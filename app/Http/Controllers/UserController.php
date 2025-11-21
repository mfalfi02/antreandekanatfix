<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create'); // halaman form tambah user
    }

    public function store(Request $request)
    {
        // validasi input
       $request->validate([
        'kode' => 'required|unique:users,kode',
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'role' => 'required|in:admin,dosen,mahasiswa,pejabat',
        'password' => 'required|string|min:6',
        'jabatan' => 'nullable|string|max:255',
        'ruangan' => 'nullable|string|max:255',
    ]);

        // simpan user baru
        User::create([
        'kode' => $request->kode,
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
        'password' => bcrypt($request->password),
        'jabatan' => $request->jabatan,
        'ruangan' => $request->ruangan,
    ]);

    return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
}

   public function edit($kode)
    {
        $user = User::where('kode', $kode)->firstOrFail();
        return view('admin.users.edit', compact('user'));
    }

    // Update user
   public function update(Request $request, $kode)
{
    $user = User::where('kode', $kode)->firstOrFail();

    $request->validate([
        'kode' => 'required|string|unique:users,kode,' . $user->kode . ',kode',
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|unique:users,email,' . $user->kode . ',kode',
        'role' => 'required|in:admin,dosen,mahasiswa,pejabat',
        'jabatan' => 'nullable|string|max:255',
        'status' => 'required|in:aktif,nonaktif',
        'password' => 'nullable|string|min:6',
    ]);

    $user->kode = $request->kode;
    $user->name = $request->name;
    $user->email = $request->email;
    $user->role = $request->role;
    $user->jabatan = $request->jabatan;
    $user->status = $request->status;

    if ($request->password) {
        $user->password = bcrypt($request->password);
    }

    $user->save();

    return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui!');
}

    public function destroy($kode)
    {
        $user = User::where('kode', $kode)->firstOrFail();
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}
