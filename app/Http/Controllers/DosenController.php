<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Dosen;

class DosenController extends Controller
{
   public function index() {
    $dosens = Dosen::all();
    return view('dosen.dashboard', compact('dosens'));
}
    public function create() {
        return view('admin.users.create');
    }

    public function store(Request $request) {
        $request->validate([
            'kode_dosen' => 'required|string|unique:dosens,kode_dosen',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:dosens,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|string'
        ]);

        Dosen::create([
            'kode_dosen' => $request->kode_dosen,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'Lainnya',
            'room' => $request->room,
            'phone' => $request->phone,
            'status' => $request->status ?? 'Aktif',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Dosen berhasil ditambahkan!');
    }

    public function edit(Dosen $dosen) {
        return view('admin.users.edit', compact('dosen'));
    }

    public function update(Request $request, Dosen $dosen) {
        $data = $request->all();
        if(isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $dosen->update($data);
        return redirect()->route('admin.users.index')->with('success', 'Dosen berhasil diupdate!');
    }

    public function destroy(Dosen $dosen) {
        $dosen->delete();
        return redirect()->route('admin.users.index')->with('success', 'Dosen berhasil dihapus!');
    }
}
