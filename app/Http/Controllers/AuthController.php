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


/**
 * Mengatur autentikasi, pengalihan dashboard berdasarkan role, dan beberapa endpoint legacy yang masih dipakai UI.
 */
class AuthController extends Controller
{
    /**
     * Mengarahkan user ke form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memvalidasi kredensial, membuat sesi login, lalu mengarahkan user ke dashboard sesuai role.
     */
    public function login(Request $request)
    {
        // Kredensial login diikat ke kode user karena itu identitas utama di sistem ini.
        $user = User::where('kode', $request->kode)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                Auth::login($user);
                $request->session()->regenerate();

                // Alur keluar dari login bergantung pada role agar tiap user masuk ke dashboard yang tepat.
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

    /**
     * Mengirim user pengantre ke dashboard mahasiswa dan menyertakan data antrean yang dipakai UI.
     */
    public function mahasiswa()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'admin') {
            return redirect()->route('adm');
        }

        // Data yang diambil di sini mengarah ke view mahasiswa/dashboard untuk menampilkan antrean hari ini dan histori.
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

        // Status pejabat dikompilasi dulu agar view bisa merender kartu status tanpa query tambahan.
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

        // Semua data dikirim ke view mahasiswa/dashboard sebagai satu payload agar rendering lebih sederhana.
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

    /**
     * Mengirim pejabat ke dashboard dosen dan mengarahkan role lain kembali ke dashboard pengantre.
     */
    public function dosen()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'admin') {
            return redirect()->route('adm');
        }

        if ($user->role !== 'pejabat') {
            return $this->mahasiswa();
        }
        // Data ini mengarah ke view dosen/dashboard untuk menampilkan antrean aktif dan riwayat hari ini.
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

        // Dashboard dosen butuh antrean yang sedang diproses agar kartu nomor aktif bisa ditampilkan.
        $currentServingQueue = $myQueues->firstWhere('status', 'diproses');
        $currentQueueNumber = $currentServingQueue?->nomor_antrian;
        $currentServiceEstimate = $currentServingQueue?->service?->est;

        // Payload dikirim utuh ke view dosen/dashboard supaya seluruh komponen bisa membaca state yang sama.
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

    /**
     * Mengakhiri sesi login dan mengembalikan user ke halaman login.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }

    /**
     * Endpoint legacy untuk mengubah status ruang pejabat dan mengirim JSON ke client lama yang masih memakainya.
     */
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

        // Legacy toggle ini tetap divalidasi ke geofence supaya perilakunya tidak berbeda dari alur utama.
        $status = $request->status;
        if (in_array($status, ['open', 'occupied'], true)) {
            if ($response = app(GeofenceService::class)->validateRequest($request, 'membuka antrean')) {
                return $response;
            }
        }
        // Hasil akhirnya tetap berupa pembaruan record ruang yang kemudian dibroadcast ke listener realtime.
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

    /**
     * Mengambil status ruang pejabat yang dipakai dashboard untuk menampilkan badge, layanan, dan jam perkiraan.
     */
    private function statusDetailForUser(string $kode): array
    {
        // Query ini mengarah ke view dashboard agar status ruang bisa dirender tanpa query tambahan di client.
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

    /**
     * Menentukan status global gabungan dari seluruh pejabat aktif untuk UI ringkasan.
     */
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
