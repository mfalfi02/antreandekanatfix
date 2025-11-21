<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Queue;
use App\Models\User;

class ServiceController extends Controller
{
    // Menampilkan semua layanan
    public function index()
    {
        $services = Service::all();
        return view('admin.services.index', compact('services'));
    }

    // Form tambah layanan
    public function create()
    {
        return view('admin.services.create');
    }

    // Simpan layanan baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'est' => 'required|integer|min:1', // estimasi waktu dalam menit
        ]);

        Service::create([
            'nama_layanan' => $request->nama_layanan,
            'deskripsi' => $request->deskripsi,
            'est' => $request->est,
        ]);

        return redirect()->route('services.index')->with('success', 'Layanan berhasil ditambahkan!');
    }

    // Form edit layanan
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    // Update layanan
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'est' => 'required|integer|min:1',
        ]);

        $service->update([
            'nama_layanan' => $request->nama_layanan,
            'deskripsi' => $request->deskripsi,
            'est' => $request->est,
        ]);

        return redirect()->route('services.index')->with('success', 'Layanan berhasil diperbarui!');
    }

    // Hapus layanan
    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Layanan berhasil dihapus!');
    }

    // Statistik layanan (jumlah antrean per layanan)
    public function serviceStats()
    {
        $services = Service::withCount('queues')->get();

        $labels = $services->pluck('nama_layanan');
        $data = $services->pluck('queues_count');

        return view('admin.reports.services', compact('services', 'labels', 'data'));
    }

    // Rekap laporan umum
    public function rekapReport()
    {
        $totalUsers = User::count();
        $serviceCategories = Service::count();
        $completedToday = Queue::whereDate('updated_at', today())
                                ->where('status', 'Selesai')
                                ->count();

        return view('admin.reports.rekap', compact('totalUsers', 'serviceCategories', 'completedToday'));
    }

    // Laporan harian antrean
    public function dailyReport()
    {
        $today = now()->toDateString();
        $queues = Queue::whereDate('created_at', $today)->get();

        return view('admin.reports.daily', [
            'title' => 'Laporan Harian',
            'date' => $today,
            'queues' => $queues
        ]);
    }
}
