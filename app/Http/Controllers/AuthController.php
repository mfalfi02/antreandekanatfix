<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Events\QueueStatusUpdated;
use App\Models\Queue;
use App\Models\Service;

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
        // Ambil user yang sedang login
        $user = Auth::user();

        // Ambil semua antrean dengan relasi user & service
        $myQueues = Queue::with(['user', 'service'])
                        ->orderBy('created_at', 'asc')
                        ->get();

        // Siapkan semua data yang dibutuhkan ke Blade
        $data = [
            'user' => $user,
            'allUsers' => User::all(),
            'services' => Service::all(),
            'myQueues' => $myQueues,
            'activeQueues' => $myQueues->where('status', 'Menunggu')->count(),
            'completedQueues' => $myQueues->where('status', 'Selesai')->count(),
            'pejabat' => User::where('role', 'pejabat')->get(),
        ];

        // Arahkan ke view mahasiswa
        return view('mahasiswa.dashboard', compact('data'));
    }


    public function dosen()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Ambil data antrean sesuai kebutuhan
        $myQueues = Queue::with(['user', 'service'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Siapkan data untuk dikirim ke blade
        $data = [
            'user' => $user,
            'services' => Service::all(),
            'myQueues' => $myQueues,
            'activeQueues' => $myQueues->where('status', 'Menunggu')->count(),
            'completedQueues' => $myQueues->where('status', 'Selesai')->count(),
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
    $user = auth()->user();

    // optional cek role
    if ($user->role !== 'pejabat') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $user->is_active_queue = !$user->is_active_queue;
    $user->save();

    // broadcast event
    broadcast(new QueueStatusUpdated($user))->toOthers();

    return response()->json([
        'is_active_queue' => $user->is_active_queue,
        'dosen' => $user->name,
    ]);
}

}
