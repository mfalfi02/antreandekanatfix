<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\RuangAntri;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Carbon;

class DisplayController extends Controller
{
    public function index()
    {
        $setting = SystemSetting::firstOrCreate(
            ['id' => 1],
            ['queue_status' => 'closed']
        );

        return view('display.index', [
            'queue_status' => $setting->queue_status,
        ]);
    }

    public function queues()
    {
        $todayJakarta = Carbon::now('Asia/Jakarta')->toDateString();

        $queues = Queue::with(['user', 'service', 'dosen'])
            ->whereDate('created_at', $todayJakarta)
            ->get()
            ->map(function ($q) {
            return [
                'id' => $q->id,
                'status' => $q->status,
                'dosen' => $q->dosen ? ['kode' => $q->dosen->kode, 'name' => $q->dosen->name] : null,
                'user' => $q->user ? ['kode' => $q->user->kode, 'name' => $q->user->name] : null,
                'service' => $q->service ? ['id' => $q->service->id, 'nama_layanan' => $q->service->nama_layanan] : null,
            ];
        });

        $activeStaff = RuangAntri::with(['dosen', 'service'])
            ->whereDate('tanggal_buka_ruang_antri', $todayJakarta)
            ->whereIn('status_ruang', ['open', 'occupied'])
            ->orderByDesc('updated_at')
            ->get()
            ->unique('kode_dosen')
            ->values()
            ->map(function ($item) {
                return [
                    'kode' => $item->kode_dosen,
                    'name' => $item->dosen->name ?? '-',
                    'jabatan' => $item->dosen->jabatan ?? '-',
                    'ruangan' => $item->dosen->ruangan ?? '-',
                    'service' => $item->service ? [
                        'id' => $item->service->id,
                        'nama_layanan' => $item->service->nama_layanan,
                    ] : [
                        'id' => null,
                        'nama_layanan' => 'Semua Jenis Layanan',
                    ],
                    'waktu' => [
                        'expected_jam_tutup' => $item->expected_jam_tutup_ruang_antri,
                    ],
                    'queue_status' => $item->status_ruang,
                    'queue_status_label' => match ($item->status_ruang) {
                        'open' => 'Antrean Dibuka',
                        'occupied' => 'Melayani',
                        default => 'Antrean Ditutup',
                    },
                ];
            });

        $dosenQueueSummary = User::query()
            ->where('role', 'pejabat')
            ->where('status', 'aktif')
            ->get(['kode', 'name', 'jabatan', 'ruangan'])
            ->map(function ($dosen) use ($todayJakarta) {
                $todayQueues = Queue::query()
                    ->where('kode_dosen', $dosen->kode)
                    ->whereDate('created_at', $todayJakarta);

                $lastNumber = (clone $todayQueues)->max('nomor_antrian');
                $currentServing = (clone $todayQueues)
                    ->where('status', 'diproses')
                    ->orderBy('updated_at')
                    ->first();
                $waitingCount = (clone $todayQueues)->where('status', 'menunggu')->count();
                $totalCount = (clone $todayQueues)->count();
                $waitingMahasiswaCount = (clone $todayQueues)
                    ->where('status', 'menunggu')
                    ->whereHas('user', function ($query) {
                        $query->where('role', 'mahasiswa');
                    })
                    ->count();
                $totalMahasiswaCount = (clone $todayQueues)
                    ->whereHas('user', function ($query) {
                        $query->where('role', 'mahasiswa');
                    })
                    ->count();

                return [
                    'kode' => $dosen->kode,
                    'name' => $dosen->name,
                    'jabatan' => $dosen->jabatan,
                    'ruangan' => $dosen->ruangan,
                    'nomor_saat_ini' => $currentServing?->nomor_antrian,
                    'nomor_terakhir' => $lastNumber,
                    'menunggu' => $waitingCount,
                    'total_hari_ini' => $totalCount,
                    'menunggu_mahasiswa' => $waitingMahasiswaCount,
                    'total_hari_ini_mahasiswa' => $totalMahasiswaCount,
                ];
            })
            ->sortBy('name')
            ->values();

        return response()->json([
            'queues' => $queues,
            'active_staff' => $activeStaff,
            'dosen_queue_summary' => $dosenQueueSummary,
        ]);
    }
    public function refresh()
    {
        $todayJakarta = Carbon::now('Asia/Jakarta')->toDateString();
        $queues = Queue::with('service', 'user')
            ->whereDate('created_at', $todayJakarta)
            ->get();
        return response()->json($queues);
    }
}
