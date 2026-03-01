<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Queue;
use App\Models\User;
use App\Models\RuangAntri;
use Illuminate\Support\Carbon;

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
        $nowJakarta = Carbon::now('Asia/Jakarta');
        $monthStart = $nowJakarta->copy()->startOfMonth();
        $monthEnd = $nowJakarta->copy()->endOfMonth();

        $services = Service::query()
            ->leftJoin('queues', 'services.id', '=', 'queues.service_id')
            ->select('services.id', 'services.nama_layanan')
            ->selectRaw('COUNT(DISTINCT queues.kode_user) as mahasiswa_count')
            ->where(function ($query) use ($monthStart, $monthEnd) {
                $query->whereBetween('queues.created_at', [$monthStart, $monthEnd])
                    ->orWhereNull('queues.created_at');
            })
            ->groupBy('services.id', 'services.nama_layanan')
            ->orderBy('services.nama_layanan')
            ->get();

        $labels = $services->pluck('nama_layanan');
        $data = $services->pluck('mahasiswa_count');
        $periodLabel = $nowJakarta->translatedFormat('F Y');

        $roomMonthlyStats = RuangAntri::query()
            ->leftJoin('users', 'ruang_antri.kode_dosen', '=', 'users.kode')
            ->selectRaw('ruang_antri.kode_dosen')
            ->selectRaw('COALESCE(users.name, ruang_antri.kode_dosen) as dosen_name')
            ->selectRaw('COUNT(*) as total_sesi')
            ->selectRaw('SUM(CASE WHEN ruang_antri.jam_buka_ruang_antri IS NOT NULL THEN 1 ELSE 0 END) as total_buka')
            ->selectRaw("SUM(CASE WHEN ruang_antri.jam_tutup_ruang_antri IS NOT NULL OR ruang_antri.status_ruang = 'closed' THEN 1 ELSE 0 END) as total_tutup")
            ->whereBetween('ruang_antri.tanggal_buka_ruang_antri', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->groupBy('ruang_antri.kode_dosen', 'users.name')
            ->orderByDesc('total_sesi')
            ->get();

        $roomMonthlySessions = RuangAntri::query()
            ->leftJoin('users', 'ruang_antri.kode_dosen', '=', 'users.kode')
            ->selectRaw('ruang_antri.kode_dosen')
            ->selectRaw('COALESCE(users.name, ruang_antri.kode_dosen) as dosen_name')
            ->selectRaw('ruang_antri.tanggal_buka_ruang_antri')
            ->selectRaw('ruang_antri.jam_buka_ruang_antri')
            ->selectRaw('ruang_antri.jam_tutup_ruang_antri')
            ->selectRaw('ruang_antri.updated_at')
            ->whereBetween('ruang_antri.tanggal_buka_ruang_antri', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->orderBy('ruang_antri.tanggal_buka_ruang_antri')
            ->orderBy('ruang_antri.jam_buka_ruang_antri')
            ->get()
            ->groupBy('kode_dosen');

        $roomMonthlyStats = $roomMonthlyStats->map(function ($item) use ($roomMonthlySessions) {
            $sessions = $roomMonthlySessions->get($item->kode_dosen, collect());
            $item->open_schedules = $sessions
                ->filter(fn ($row) => !empty($row->jam_buka_ruang_antri))
                ->map(function ($row) {
                    $tgl = Carbon::parse($row->tanggal_buka_ruang_antri)
                        ->locale('id')
                        ->translatedFormat('d M Y');
                    return $tgl . ' • ' . $row->jam_buka_ruang_antri . ' WIB';
                })
                ->unique()
                ->values();
            $item->close_schedules = $sessions
                ->filter(fn ($row) => !empty($row->jam_tutup_ruang_antri))
                ->map(function ($row) {
                    $tgl = $row->updated_at
                        ? Carbon::parse($row->updated_at)->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y')
                        : Carbon::parse($row->tanggal_buka_ruang_antri)->locale('id')->translatedFormat('d M Y');
                    return $tgl . ' • ' . $row->jam_tutup_ruang_antri . ' WIB';
                })
                ->unique()
                ->values();

            return $item;
        });

        $roomSummary = [
            'total_sesi' => (int) $roomMonthlyStats->sum('total_sesi'),
            'total_buka' => (int) $roomMonthlyStats->sum('total_buka'),
            'total_tutup' => (int) $roomMonthlyStats->sum('total_tutup'),
            'total_dosen' => (int) $roomMonthlyStats->count(),
        ];

        return view('admin.reports.services', compact(
            'services',
            'labels',
            'data',
            'periodLabel',
            'roomMonthlyStats',
            'roomSummary'
        ));
    }

    // Rekap laporan umum
    public function rekapReport()
    {
        $totalUsers = User::count();
        $serviceCategories = Service::count();
        $completedToday = Queue::whereDate('updated_at', today())
                                ->where('status', 'Selesai')
                                ->count();
        $totalRuangAntriToday = RuangAntri::whereDate('tanggal_buka_ruang_antri', today())->count();
        $activeRuangAntriNow = RuangAntri::whereDate('tanggal_buka_ruang_antri', today())
            ->whereIn('status_ruang', ['open', 'occupied'])
            ->count();

        return view('admin.reports.rekap', compact(
            'totalUsers',
            'serviceCategories',
            'completedToday',
            'totalRuangAntriToday',
            'activeRuangAntriNow'
        ));
    }

    // Laporan harian antrean
    public function dailyReport()
    {
        $today = now()->toDateString();
        $queues = Queue::with(['user', 'service', 'dosen'])
            ->whereDate('created_at', $today)
            ->orderBy('created_at')
            ->get();

        $roomSessions = RuangAntri::with(['dosen', 'service'])
            ->whereDate('tanggal_buka_ruang_antri', $today)
            ->orderByDesc('updated_at')
            ->get();

        $queues->each(function ($queue) use ($roomSessions) {
            $queue->dosen_pelayan = $queue->dosen?->name
                ?? $this->resolveServingDosen($queue, $roomSessions);
        });

        return view('admin.reports.daily', [
            'title' => 'Laporan Harian',
            'date' => $today,
            'queues' => $queues,
            'roomSessions' => $roomSessions,
        ]);
    }

    private function resolveServingDosen(Queue $queue, $roomSessions): string
    {
        if (!$queue->service_id) {
            return '-';
        }

        $queueTime = optional($queue->created_at)->setTimezone('Asia/Jakarta')->format('H:i:s');
        if (!$queueTime) {
            return '-';
        }

        $match = $roomSessions
            ->filter(function ($room) use ($queue, $queueTime) {
                if ((int) $room->service_id !== (int) $queue->service_id) {
                    return false;
                }

                $open = $room->jam_buka_ruang_antri ?? $room->expected_jam_buka_ruang_antri;
                $close = $room->jam_tutup_ruang_antri;
                if (!$open || $open > $queueTime) {
                    return false;
                }

                if ($close && $close < $queueTime) {
                    return false;
                }

                return true;
            })
            ->sortByDesc('jam_buka_ruang_antri')
            ->first();

        if ($match && $match->dosen?->name) {
            return $match->dosen->name;
        }

        $fallback = $roomSessions
            ->where('service_id', $queue->service_id)
            ->sortByDesc('updated_at')
            ->first();

        return $fallback?->dosen?->name ?? '-';
    }
}
