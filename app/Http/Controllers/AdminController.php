<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Queue;
use App\Models\Service;
use App\Models\Dosen;
use App\Models\Mahasiswa;

class AdminController extends Controller
{
    public function index() {
        $admins = Admin::all();
        return view('admin.dashboard', compact('admins'));
    }

    public function create() {
        return view('admin.users.create');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Admin berhasil ditambahkan!');
    }

    public function edit(Admin $admin) {
        return view('admin.users.edit', compact('admin'));
    }

    public function update(Request $request, Admin $admin) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;
        if ($request->password) $admin->password = Hash::make($request->password);
        $admin->save();

        return redirect()->route('admin.users.index')->with('success', 'Admin berhasil diupdate!');
    }

    public function destroy(Admin $admin) {
        $admin->delete();
        return redirect()->route('admin.users.index')->with('success', 'Admin berhasil dihapus!');
    }

    public function dailyReport() {
    $completedToday = Queue::whereDate('updated_at', today())->where('status', 'Selesai')->count();
    $activeQueues = Queue::where('status', 'Aktif')->count();
    return view('admin.reports.daily', compact('completedToday', 'activeQueues'));
}

public function serviceStats() {
    $services = Service::withCount('queues')->get(); // hitung jumlah antrean per layanan
    return view('admin.reports.services', compact('services'));
}

public function rekapReport() {
    $totalUsers = Admin::count() + Dosen::count() + Mahasiswa::count();
    $serviceCategories = Service::count();
    $completedToday = Queue::whereDate('updated_at', today())->where('status', 'Selesai')->count();
    return view('admin.reports.rekap', compact('totalUsers', 'serviceCategories', 'completedToday'));
}

}
