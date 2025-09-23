<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // pastikan file blade ada
    }

    public function login(Request $request)
{
    $credentials = $request->only('identifier', 'password');

    // Cek Admin
    if (Auth::guard('admin')->attempt([
        'email' => $request->identifier,
        'password' => $request->password
    ])) {
        return redirect()->route('dashboard.admin');
    }

    // Cek Dosen
    if (Auth::guard('dosen')->attempt([
        'kode_dosen' => $request->identifier,
        'password' => $request->password
    ])) {
        return redirect()->route('dosen.dashboard'); // 👈 arahkan ke dashboard dosen
    }

    // Cek Mahasiswa
    if (Auth::guard('mahasiswa')->attempt([
        'nim' => $request->identifier,
        'password' => $request->password
    ])) {
        return redirect()->route('mahasiswa.dashboard');
    }

    return back()->withErrors(['login' => 'Email/NIM/Kode Dosen atau password salah!']);
}

    public function logout()
    {
        Auth::logout();
        return redirect()->route('logout');
    }
}
