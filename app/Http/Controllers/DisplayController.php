<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\RuangAntri;
use App\Models\Service;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Carbon;

class DisplayController extends Controller
{
    /**
     * Mengarahkan browser ke halaman display publik dengan status antrean global.
     */
    public function index()
    {
        $setting = SystemSetting::firstOrCreate(
            ['id' => 1],
            [
                'queue_status' => 'closed',
                'radius_meters' => 300,
            ]
        );

        return view('display.index', [
            'queue_status' => $setting->queue_status,
        ]);
    }

    /**
     * Mengembalikan JSON data antrean, pejabat aktif, dan ringkasan ke layar display.
     */
    public function queues()
    {
        // Data ini mengarah ke display/index dan dipakai polling atau websocket untuk merender layar publik.
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

        // Nama layanan di-cache dulu supaya payload aktif staff bisa disusun tanpa query berulang.
        $serviceNameMap = Service::query()->pluck('nama_layanan', 'id');

        // Semua pejabat aktif tetap tampil di layar, lalu statusnya mengikuti sesi ruang terbaru hari ini.
        $todayRooms = RuangAntri::with(['dosen', 'service'])
            ->whereDate('tanggal_buka_ruang_antri', $todayJakarta)
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy('kode_dosen');

        $activeStaff = User::query()
            ->where('role', 'pejabat')
            ->where('status', 'aktif')
            ->get(['kode', 'name', 'jabatan', 'ruangan'])
            ->map(function ($dosen) use ($todayRooms, $serviceNameMap) {
                $item = $todayRooms->get($dosen->kode)?->first();

                $servicePayload = [
                    'id' => null,
                    'ids' => [],
                    'nama_layanan' => 'Antrean Ditutup',
                ];

                if ($item) {
                    $serviceIds = is_array($item->service_ids) ? $item->service_ids : [];
                    $serviceIds = array_values(array_unique(array_map('intval', array_filter(
                        $serviceIds,
                        fn ($id) => $id !== null && $id !== ''
                    ))));

                    if (count($serviceIds) === 0 && $item->service_id) {
                        $serviceIds = [(int) $item->service_id];
                    }

                    if (count($serviceIds) > 0) {
                        $serviceNames = collect($serviceIds)
                            ->map(fn (int $id) => $serviceNameMap->get($id))
                            ->filter()
                            ->values();

                        $servicePayload = [
                            'id' => count($serviceIds) === 1 ? $serviceIds[0] : null,
                            'ids' => $serviceIds,
                            'nama_layanan' => $serviceNames->implode(', '),
                        ];
                    } else {
                        $servicePayload = [
                            'id' => null,
                            'ids' => [],
                            'nama_layanan' => 'Semua Jenis Layanan',
                        ];
                    }
                }

                return [
                    'kode' => $dosen->kode,
                    'name' => $dosen->name ?? '-',
                    'jabatan' => $dosen->jabatan ?? '-',
                    'ruangan' => $dosen->ruangan ?? '-',
                    'service' => $servicePayload,
                    'waktu' => [
                        'expected_jam_buka' => $item?->expected_jam_buka_ruang_antri,
                        'expected_jam_tutup' => $item?->expected_jam_tutup_ruang_antri,
                        'jam_buka' => $item?->jam_buka_ruang_antri,
                        'jam_tutup' => $item?->jam_tutup_ruang_antri,
                    ],
                    'queue_status' => $item?->status_ruang ?? 'closed',
                    'queue_status_label' => match ($item?->status_ruang ?? 'closed') {
                        'open' => 'Antrean Dibuka',
                        'occupied' => 'Melayani',
                        default => 'Antrean Ditutup',
                    },
                ];
            })
            ->sortBy('name')
            ->values();

        // Ringkasan per pejabat dipakai untuk panel statistik di display publik.
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

    /**
     * Mengembalikan JSON ringkas untuk refresh data antrean tanpa payload tambahan.
     */
    public function refresh()
    {
        $todayJakarta = Carbon::now('Asia/Jakarta')->toDateString();
        $queues = Queue::with('service', 'user')
            ->whereDate('created_at', $todayJakarta)
            ->get();
        return response()->json($queues);
    }
}
