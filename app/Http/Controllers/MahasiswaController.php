<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;


class MahasiswaController extends Controller
{
   public function index()
{
    $mahasiswa = Auth::guard('mahasiswa')->user(); // ambil user login
    return view('mahasiswa.dashboard', compact('mahasiswa'));
}


    public function create() {
        return view('admin.users.create');
    }

    public function store(Request $request) {
        $request->validate([
            'nim' => 'required|string|unique:mahasiswas,nim',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mahasiswas,email',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'nullable|string'
        ]);

        Mahasiswa::create([
            'nim' => $request->nim,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status ?? 'Aktif',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Mahasiswa berhasil ditambahkan!');
    }

    public function edit(Mahasiswa $mahasiswa) {
        return view('admin.users.edit', compact('mahasiswa'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa) {
        $data = $request->all();
        if(isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $mahasiswa->update($data);
        return redirect()->route('admin.users.index')->with('success', 'Mahasiswa berhasil diupdate!');
    }

    public function destroy(Mahasiswa $mahasiswa) {
        $mahasiswa->delete();
        return redirect()->route('admin.users.index')->with('success', 'Mahasiswa berhasil dihapus!');
    }
}
