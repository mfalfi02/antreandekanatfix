<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Admin;

class UserController extends Controller
{
    // Tampilkan semua pengguna
    public function index() {
        $admins = Admin::all();
        $dosens = Dosen::all();
        $mahasiswas = Mahasiswa::all();
        return view('admin.users.index', compact('admins','dosens','mahasiswas'));
    }

    // Form tambah user
    public function create() {
        return view('admin.users.create');
    }

    // Simpan user
   public function store(Request $request) {
    // Validasi umum
    $request->validate([
        'role' => 'required|in:Admin,Dosen,Mahasiswa',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:6|confirmed',
        'status' => 'nullable|string',
        'nim' => 'nullable|string|max:20|unique:mahasiswas,nim',
        'kode_dosen' => 'nullable|string|max:20|unique:dosens,kode_dosen',
        'room' => 'nullable|string|max:50',
        'phone' => 'nullable|string|max:20',
        'dosen_role' => 'nullable|string|max:50',
    ]);

    $role = $request->role;

    if($role === 'Admin'){
        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    } elseif($role === 'Dosen'){
        Dosen::create([
            'kode_dosen' => $request->kode_dosen,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status ?? 'Aktif',
            'role' => $request->dosen_role ?? 'Lainnya',
            'room' => $request->room,
            'phone' => $request->phone,
        ]);
    } elseif($role === 'Mahasiswa'){
        Mahasiswa::create([
            'nim' => $request->nim,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',
            'status' => $request->status ?? 'Aktif',
        ]);
    }

    return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan!');
}

    // Form edit user
    public function edit($role, $id) {
        if($role === 'Admin') {
            $user = Admin::findOrFail($id);
        } elseif($role === 'Dosen') {
            $user = Dosen::where('kode_dosen', $id)->firstOrFail();
        } elseif($role === 'Mahasiswa') {
            $user = Mahasiswa::where('nim', $id)->firstOrFail();
        } else abort(404);

        return view('admin.users.edit', compact('user','role'));
    }

    // Update user
    public function update(Request $request, $role, $id) {
        $data = $request->all();
        if(isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if($role === 'Admin'){
            $user = Admin::findOrFail($id);
            $user->update($data);
        } elseif($role === 'Dosen'){
            $user = Dosen::where('kode_dosen', $id)->firstOrFail();
            $user->update($data);
        } elseif($role === 'Mahasiswa'){
            $user = Mahasiswa::where('nim', $id)->firstOrFail();
            $user->update($data);
        }

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diupdate!');
    }

    // Hapus user
    public function destroy($role, $id) {
        if($role === 'Admin') Admin::findOrFail($id)->delete();
        elseif($role === 'Dosen') Dosen::where('kode_dosen', $id)->delete();
        elseif($role === 'Mahasiswa') Mahasiswa::where('nim', $id)->delete();
        else abort(404);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus!');
    }
}
