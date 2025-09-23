<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    // Menampilkan semua service
    public function index()
    {
        $services = Service::all();
        return view('admin.services.index', compact('services'));
    }

    // Form tambah service
    public function create()
    {
        return view('admin.services.create'); // blade untuk form tambah service
    }

    // Simpan service baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'est_time' => 'required|string|max:50',
        ]);

        Service::create($request->only('name', 'description', 'est_time'));

        return redirect()->route('services.index')->with('success', 'Service berhasil ditambahkan!');
    }

    // Form edit service
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    // Update service
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'est_time' => 'required|string|max:50',
        ]);

        $service->update($request->only('name', 'description', 'est_time'));

        return redirect()->route('services.index')->with('success', 'Service berhasil diperbarui!');
    }

    // Hapus service
    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service berhasil dihapus!');
    }

    public function serviceStats() {
    $services = Service::withCount('queues')->get();

    // Siapkan data untuk chart
    $labels = $services->pluck('name');
    $data = $services->pluck('queues_count');

    return view('admin.reports.services', compact('services', 'labels', 'data'));
}

}
