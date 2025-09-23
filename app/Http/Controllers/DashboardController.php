<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Service;
use App\Models\Queue;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Dashboard Admin
    public function admin()
    {
        $totalUsers = Admin::count() + Mahasiswa::count() + Dosen::count();
        $activeQueues = Queue::where('status', 'Menunggu')->count();
        $completedToday = Queue::whereDate('created_at', now())->where('status','Selesai')->count();
        $serviceCategories = Service::count();

        $admins = Admin::all();
        $mahasiswas = Mahasiswa::all();
        $dosens = Dosen::all();
        $services = Service::all();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeQueues',
            'completedToday',
            'serviceCategories',
            'admins',
            'mahasiswas',
            'dosens',
            'services'
        ));
    }

    // Dashboard Mahasiswa
    public function mahasiswa($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        // Queue milik mahasiswa
        $myQueues = Queue::where('nim', $mahasiswa->nim)->get();
        $activeQueues = $myQueues->where('status', 'Menunggu')->count();
        $completedQueues = $myQueues->where('status', 'Selesai')->count();

        $services = Service::all(); // bisa dipakai untuk pilihan service

        return view('mahasiswa.dashboard', compact(
            'mahasiswa',
            'myQueues',
            'activeQueues',
            'completedQueues',
            'services'
        ));
    }

    // Dashboard Dosen
    public function dosen()
{
    $dosen = Auth::guard('dosen')->user(); // pakai guard dosen jika ada
    $myQueues = Queue::where('kode_dosen', $dosen->id)->get();
    $activeQueues = $myQueues->where('status', 'Menunggu')->count();
    $completedQueues = $myQueues->where('status', 'Selesai')->count();
    $services = Service::all();

    return view('dosen.dashboard', compact(
        'dosen',
        'myQueues',
        'activeQueues',
        'completedQueues',
        'services'
    ));
}
}