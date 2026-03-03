<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:users,kode',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|in:admin,dosen,mahasiswa,pejabat',
            'password' => 'required|string|min:6|confirmed',
            'jabatan' => 'nullable|string|max:255',
            'ruangan' => 'nullable|string|max:255|required_if:role,pejabat',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        User::create([
            'kode' => $validated['kode'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => bcrypt($validated['password']),
            'jabatan' => in_array($validated['role'], ['dosen', 'pejabat'], true) ? ($validated['jabatan'] ?? null) : null,
            'ruangan' => $validated['role'] === 'pejabat' ? ($validated['ruangan'] ?? null) : null,
            'status' => $validated['status'],
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

        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'kode')->ignore($user->kode, 'kode'),
            ],
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->kode, 'kode'),
            ],
            'role' => 'required|in:admin,dosen,mahasiswa,pejabat',
            'jabatan' => 'nullable|string|max:255',
            'ruangan' => 'nullable|string|max:255|required_if:role,pejabat',
            'status' => 'required|in:aktif,nonaktif',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->kode = $validated['kode'];
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->jabatan = in_array($validated['role'], ['dosen', 'pejabat'], true) ? ($validated['jabatan'] ?? null) : null;
        $user->ruangan = $validated['role'] === 'pejabat' ? ($validated['ruangan'] ?? null) : null;
        $user->status = $validated['status'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
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
