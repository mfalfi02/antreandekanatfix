<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Events\QueueStatusUpdated;
use App\Models\Queue;
use App\Models\RuangAntri;
use App\Models\Service;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {

        $user = User::where('kode', $request->kode)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                Auth::login($user);
                $request->session()->regenerate();

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
    public function mahasiswa()
    {
        $user = Auth::user();
        $todayJakarta = Carbon::now('Asia/Jakarta')->toDateString();

        $myQueues = Queue::with(['user', 'service', 'dosen'])
                        ->where('kode_user', $user->kode)
                        ->whereDate('created_at', $todayJakarta)
                        ->orderBy('created_at', 'asc')
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


    public function dosen()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Role dosen disamakan dengan mahasiswa, jadi selalu gunakan dashboard mahasiswa.
        if ($user->role === 'dosen') {
            return $this->mahasiswa();
        }
        $todayJakarta = Carbon::now('Asia/Jakarta')->toDateString();

        $myQueues = Queue::with(['user', 'service'])
            ->where('kode_dosen', $user->kode)
            ->whereDate('created_at', $todayJakarta)
            ->orderBy('created_at', 'asc')
            ->get();

        $currentServingQueue = $myQueues->firstWhere('status', 'diproses');
        $currentQueueNumber = $currentServingQueue?->nomor_antrian;
        $currentServiceEstimate = $currentServingQueue?->service?->est;

        $data = [
            'user' => $user,
            'services' => Service::all(),
            'myQueues' => $myQueues,
            'activeQueues' => $myQueues->whereIn('status', ['menunggu', 'diproses'])->count(),
            'completedQueues' => $myQueues->where('status', 'selesai')->count(),
            'queue_status' => $this->statusDetailForUser($user->kode)['queue_status'],
            'currentQueueNumber' => $currentQueueNumber,
            'currentServiceEstimate' => $currentServiceEstimate,
        ];

        return view('dosen.dashboard',compact('data'));
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }

  

public function toggleQueue(Request $request)
{
    $user = Auth::user();
    if (!$user || $user->role !== 'pejabat') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $request->validate([
        'status' => 'required|in:open,closed,occupied',
    ]);

    $status = $request->status;
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

private function statusDetailForUser(string $kode): array
{
    $record = RuangAntri::query()
        ->with('service:id,nama_layanan')
        ->where('kode_dosen', $kode)
        ->whereDate('tanggal_buka_ruang_antri', Carbon::now('Asia/Jakarta')->toDateString())
        ->latest('updated_at')
        ->first();

    return [
        'queue_status' => $record?->status_ruang ?? 'closed',
        'service_name' => $record?->service?->nama_layanan
            ?? (in_array($record?->status_ruang, ['open', 'occupied'], true) ? 'Semua Jenis Layanan' : null),
        'expected_jam_tutup' => $record?->expected_jam_tutup_ruang_antri,
    ];
}

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
