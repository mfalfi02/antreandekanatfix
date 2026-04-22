<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Events\QueueStatusUpdated;
use App\Models\Queue;
use App\Models\RuangAntri;
use App\Models\Service;
use App\Services\GeofenceService;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    // Tampilkan form login.
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login berdasarkan kode dan password.
    public function login(Request $request)
    {
        // Cari user berdasarkan kode unik yang dipakai sebagai identitas login.
        $user = User::where('kode', $request->kode)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                Auth::login($user);
                $request->session()->regenerate();

                // Arahkan user ke dashboard sesuai role masing-masing.
                if ($user->role === 'admin') {
                    return redirect()->route('adm');
                } elseif ($user->role === 'pejabat') {
                    return redirect()->route('dsn');
                } elseif (in_array($user->role, ['dosen', 'mahasiswa'])) {
                    return redirect()->route('mhs');
                }

                return redirect()->route('login')->with('error', 'Role tidak dikenali.');
            }

            return back()->with('error', 'Kode atau password salah!');

     }
    // Dashboard mahasiswa juga dipakai untuk user pengantre non-pejabat.
    public function mahasiswa()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'admin') {
            return redirect()->route('adm');
        }

        // Ambil antrean hari ini dan histori lama untuk dashboard pengantre.
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $myQueues = Queue::with(['user', 'service', 'dosen'])
                        ->where('kode_user', $user->kode)
                        ->whereDate('created_at', $today)
                        ->latest('created_at')
                        ->get();
        $historyQueues = Queue::with(['user', 'service', 'dosen'])
                        ->where('kode_user', $user->kode)
                        ->whereDate('created_at', '<', $today)
                        ->latest('created_at')
                        ->get();

        $pejabat = User::where('role', 'pejabat')->where('status', 'aktif')->get();
        $pejabatStatuses = [];
        $pejabatServices = [];
        $pejabatExpectedClose = [];
        foreach ($pejabat as $item) {
            $detail = $this->statusDetailForUser($item->kode);
            $pejabatStatuses[$item->kode] = $detail['queue_status'];
            $pejabatServices[$item->kode] = $detail['service_name'];
            $pejabatExpectedClose[$item->kode] = $detail['expected_jam_tutup'];
        }

        $data = [
            'user' => $user,
            'allUsers' => User::all(),
            'services' => Service::all(),
            'myQueues' => $myQueues,
            'historyQueues' => $historyQueues,
            'activeQueues' => $myQueues->where('status', 'menunggu')->count(),
            'completedQueues' => $myQueues->where('status', 'selesai')->count(),
            'pejabat' => $pejabat,
            'pejabat_statuses' => $pejabatStatuses,
            'pejabat_services' => $pejabatServices,
            'pejabat_expected_close' => $pejabatExpectedClose,
            'queue_status' => $this->globalStatusFromStatuses($pejabatStatuses),
        ];

        return view('mahasiswa.dashboard', compact('data'));
    }

    // Dashboard dosen hanya valid untuk pejabat; role lain dialihkan ke dashboard pengantre.
    public function dosen()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'admin') {
            return redirect()->route('adm');
        }

        // Selain pejabat diperlakukan sebagai pengantre.
        if ($user->role !== 'pejabat') {
            return $this->mahasiswa();
        }
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $myQueues = Queue::with(['user', 'service'])
            ->where('kode_dosen', $user->kode)
            ->whereDate('created_at', $today)
            ->latest('created_at')
            ->get();
        $historyQueues = Queue::with(['user', 'service'])
            ->where('kode_dosen', $user->kode)
            ->whereDate('created_at', '<', $today)
            ->latest('created_at')
            ->get();

        $currentServingQueue = $myQueues->firstWhere('status', 'diproses');
        $currentQueueNumber = $currentServingQueue?->nomor_antrian;
        $currentServiceEstimate = $currentServingQueue?->service?->est;

        $data = [
            'user' => $user,
            'services' => Service::all(),
            'myQueues' => $myQueues,
            'historyQueues' => $historyQueues,
            'activeQueues' => $myQueues->whereIn('status', ['menunggu', 'diproses'])->count(),
            'completedQueues' => $myQueues->where('status', 'selesai')->count(),
            'queue_status' => $this->statusDetailForUser($user->kode)['queue_status'],
            'currentQueueNumber' => $currentQueueNumber,
            'currentServiceEstimate' => $currentServiceEstimate,
        ];

        return view('dosen.dashboard',compact('data'));
    }

    // Hapus sesi login dan bersihkan session user.
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }

    // Toggle ruang antrean versi legacy yang masih dipakai di beberapa route.
    public function toggleQueue(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'pejabat') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:open,closed,occupied',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        $status = $request->status;
        // Toggle legacy ini tetap memakai validasi lokasi agar perilakunya sejajar dengan controller utama.
        if (in_array($status, ['open', 'occupied'], true)) {
            if ($response = app(GeofenceService::class)->validateRequest($request, 'membuka antrean')) {
                return $response;
            }
        }
        $ruang = RuangAntri::firstOrNew([
            'kode_dosen' => $user->kode,
            'tanggal_buka_ruang_antri' => now()->toDateString(),
        ]);

        $ruang->status_ruang = $status;
        if (in_array($status, ['open', 'occupied'], true)) {
            if (!$ruang->jam_buka_ruang_antri) {
                $ruang->jam_buka_ruang_antri = now()->format('H:i:s');
            }
            $ruang->jam_tutup_ruang_antri = null;
        } else {
            $ruang->jam_tutup_ruang_antri = now()->format('H:i:s');
        }
        $ruang->save();

        // Broadcast juga status ini supaya listener realtime tetap bergerak.
        try {
            event(new QueueStatusUpdated($user->kode, $status));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'success' => true,
            'queue_status' => $status,
        ]);
    }

    // Ambil status detail satu pejabat untuk kebutuhan dashboard dan API.
    private function statusDetailForUser(string $kode): array
    {
        $record = RuangAntri::query()
            ->with('service:id,nama_layanan')
            ->where('kode_dosen', $kode)
            ->whereDate('tanggal_buka_ruang_antri', Carbon::now('Asia/Jakarta')->toDateString())
            ->latest('updated_at')
            ->first();

        $serviceIds = is_array($record?->service_ids) ? $record->service_ids : [];
        $serviceIds = array_values(array_unique(array_map('intval', array_filter(
            $serviceIds,
            fn ($id) => $id !== null && $id !== ''
        ))));

        $serviceName = null;
        if (count($serviceIds) > 0) {
            $servicesById = Service::query()
                ->whereIn('id', $serviceIds)
                ->get(['id', 'nama_layanan'])
                ->keyBy('id');

            $serviceName = collect($serviceIds)
                ->map(fn (int $id) => $servicesById->get($id)?->nama_layanan)
                ->filter()
                ->implode(', ');
        } elseif ($record?->service?->nama_layanan) {
            $serviceName = $record->service->nama_layanan;
        } elseif (in_array($record?->status_ruang, ['open', 'occupied'], true)) {
            $serviceName = 'Semua Jenis Layanan';
        }

        return [
            'queue_status' => $record?->status_ruang ?? 'closed',
            'service_name' => $serviceName,
            'expected_jam_tutup' => $record?->expected_jam_tutup_ruang_antri,
        ];
    }

    // Ambil status global dari semua pejabat yang aktif.
    private function globalStatusFromStatuses(array $statuses): string
    {
        if (in_array('occupied', $statuses, true)) {
            return 'occupied';
        }

        if (in_array('open', $statuses, true)) {
            return 'open';
        }

        return 'closed';
    }

}
