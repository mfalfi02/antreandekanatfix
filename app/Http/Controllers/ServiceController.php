<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Queue;
use App\Models\User;
use App\Models\RuangAntri;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;
use App\Exports\MonthlyServiceReportExport;

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
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Service::create([
            'nama_layanan' => $request->nama_layanan,
            'deskripsi' => $request->deskripsi,
            'est' => $request->est,
            'status' => $request->status,
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
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $service->update([
            'nama_layanan' => $request->nama_layanan,
            'deskripsi' => $request->deskripsi,
            'est' => $request->est,
            'status' => $request->status,
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
    public function serviceStats(Request $request)
    {
        $report = $this->buildMonthlyServiceReport($request);

        return view('admin.reports.services', $report);
    }

    public function exportServiceStatsPdf(Request $request)
    {
        $report = $this->buildMonthlyServiceReport($request);

        $fileName = 'laporan-layanan-' . $report['exportFileSuffix'] . '.pdf';

        return Pdf::loadView('exports.monthly-services-pdf', $report)
            ->setPaper('a4', 'landscape')
            ->download($fileName);
    }

    public function exportServiceStatsExcel(Request $request)
    {
        $report = $this->buildMonthlyServiceReport($request);

        $fileName = 'laporan-layanan-' . $report['exportFileSuffix'] . '.xlsx';

        return Excel::download(new MonthlyServiceReportExport($report), $fileName);
    }

    private function buildMonthlyServiceReport(Request $request): array
    {
        $nowJakarta = Carbon::now('Asia/Jakarta');
        $selectedMonth = (int) $request->input('month', $nowJakarta->month);
        $selectedYear = (int) $request->input('year', $nowJakarta->year);

        if ($selectedMonth < 1 || $selectedMonth > 12) {
            $selectedMonth = $nowJakarta->month;
        }

        if ($selectedYear < 2000 || $selectedYear > $nowJakarta->year + 1) {
            $selectedYear = $nowJakarta->year;
        }

        $period = Carbon::createFromDate($selectedYear, $selectedMonth, 1, 'Asia/Jakarta')->startOfMonth();
        $monthStart = $period->copy()->startOfMonth();
        $monthEnd = $period->copy()->endOfMonth();
        $periodLabel = $period->locale('id')->translatedFormat('F Y');
        $previousPeriod = $period->copy()->subMonthNoOverflow();
        $nextPeriod = $period->copy()->addMonthNoOverflow();
        $availableYears = RuangAntri::query()
            ->selectRaw('DISTINCT YEAR(tanggal_buka_ruang_antri) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter()
            ->map(fn ($year) => (int) $year)
            ->values()
            ->all();

        if (count($availableYears) === 0) {
            $availableYears = range(max(2020, $nowJakarta->year - 5), $nowJakarta->year + 1);
        } elseif (!in_array($selectedYear, $availableYears, true)) {
            $availableYears[] = $selectedYear;
            rsort($availableYears);
        }

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

        return [
            'services' => $services,
            'labels' => $labels,
            'data' => $data,
            'periodLabel' => $periodLabel,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
            'previousPeriod' => $previousPeriod,
            'nextPeriod' => $nextPeriod,
            'roomMonthlyStats' => $roomMonthlyStats,
            'roomSummary' => $roomSummary,
            'exportFileSuffix' => $period->format('Y-m'),
        ];
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
    public function dailyReport(Request $request)
    {
        $nowJakarta = Carbon::now('Asia/Jakarta');
        $dateInput = $request->query('date', $nowJakarta->toDateString());

        try {
            $selectedDate = Carbon::createFromFormat('Y-m-d', $dateInput, 'Asia/Jakarta')->toDateString();
        } catch (\Throwable $e) {
            $selectedDate = $nowJakarta->toDateString();
        }

        $selectedCarbon = Carbon::parse($selectedDate, 'Asia/Jakarta');
        $previousDate = $selectedCarbon->copy()->subDay()->toDateString();
        $nextDate = $selectedCarbon->copy()->addDay()->toDateString();

        $queues = Queue::with(['user', 'service', 'dosen'])
            ->whereDate('created_at', $selectedDate)
            ->orderBy('created_at')
            ->get();

        $roomSessions = RuangAntri::with(['dosen', 'service'])
            ->whereDate('tanggal_buka_ruang_antri', $selectedDate)
            ->orderByDesc('updated_at')
            ->get();

        $queues->each(function ($queue) use ($roomSessions) {
            $queue->dosen_pelayan = $queue->dosen?->name
                ?? $this->resolveServingDosen($queue, $roomSessions);
        });

        return view('admin.reports.daily', [
            'title' => 'Laporan Harian',
            'date' => $selectedDate,
            'previousDate' => $previousDate,
            'nextDate' => $nextDate,
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
