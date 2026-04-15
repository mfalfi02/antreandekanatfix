<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Models\RuangAntri;

use App\Models\Queue;
use App\Models\Service;
use App\Models\SystemSetting;
use App\Models\User;


Route::get('/', function () {
    return view('auth.welcome');
})->name('welcome');


// Route::get('/display',function() {
//     return view ('display.index');})->name('display');
// Route::get('/display/queues', [DisplayController::class, 'queues'])->name('display.queues');
// Route::get('/api/display/active-pejabat', [DisplayController::class, 'activePejabat']);
// Route::get('/api/display/queues', [DisplayController::class, 'queues']);
Route::get('/display', [DisplayController::class, 'index'])->name('display');
Route::get('/display/refresh', [DisplayController::class, 'refresh'])->name('display.refresh');
Route::get('/display/queues', [DisplayController::class, 'queues'])->name('display.queues');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/admin', function (Request $request) {
    $setting = SystemSetting::firstOrCreate(
        ['id' => 1],
        ['queue_status' => 'closed']
    );

    $now = Carbon::now('Asia/Jakarta');
    $selectedMonth = (int) $request->query('month', $now->month);
    $selectedYear = (int) $request->query('year', $now->year);

    if ($selectedMonth < 1 || $selectedMonth > 12) {
        $selectedMonth = $now->month;
    }
    if ($selectedYear < 2000 || $selectedYear > 2100) {
        $selectedYear = $now->year;
    }

    $availableDashboardYears = RuangAntri::query()
        ->selectRaw('DISTINCT YEAR(tanggal_buka_ruang_antri) as year')
        ->orderBy('year', 'desc')
        ->pluck('year')
        ->filter()
        ->map(fn ($year) => (int) $year)
        ->values()
        ->all();

    if (count($availableDashboardYears) === 0) {
        $availableDashboardYears = [$now->year];
    }

    if (!in_array($selectedYear, $availableDashboardYears, true)) {
        $availableDashboardYears[] = $selectedYear;
        rsort($availableDashboardYears);
    }

    $data =[
                'totalUsers' => User::count(),
                'activeQueues' => Queue::where('status', 'aktif')->count(),
                'totalServices' => Service::count(),
                'completedQueues' => Queue::where('status', 'selesai')->count(),
                'users' => User::all(),
                'service' => Service::all(),
                'queue_status' => $setting->queue_status,
            ];
    $dashboardMonthLabel = match ($selectedMonth) {
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
        default => '-',
    };

    return view('admin.dashboard', [
        'data' => $data,
        'dashboardMonth' => $selectedMonth,
        'dashboardYear' => $selectedYear,
        'dashboardPeriodLabel' => $dashboardMonthLabel . ' ' . $selectedYear,
        'availableDashboardYears' => $availableDashboardYears,
    ]);
})->name('adm');

// Route::get('/dosen',function() {
//     return view ('dosen.dashboard');})->name('dsn');
Route::middleware(['ceklogin'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dosen'])->name('dsn');
    Route::get('/mahasiswa',[AuthController::class, 'mahasiswa'])->name('mhs');

    Route::post('/queue/join', [DashboardController::class, 'joinQueue'])->name('queue.join');
    Route::post('/queue/toggle', [QueueController::class, 'toggleQueue'])->name('queue.toggle');
    Route::post('/queue/{queue}/call', [QueueController::class, 'callQueue'])->name('queue.call');
    Route::post('/queue/{queue}/complete', [QueueController::class, 'completeQueue'])->name('queue.complete');
    Route::get('/queue/status', [QueueController::class, 'status'])->name('queue.status');
    Route::get('/queue/my', [QueueController::class, 'myQueues'])->name('queue.my');
});

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');

// Edit (tampilkan form) -> GET
Route::get('/users/{kode}/edit', [UserController::class, 'edit'])->name('users.edit');

// Update (simpan perubahan) -> PUT
Route::put('/users/{kode}', [UserController::class, 'update'])->name('users.update');

// Hapus -> DELETE
Route::delete('/users/{kode}', [UserController::class, 'destroy'])->name('users.destroy');

// CRUD Service
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

// Laporan Statistik Service
Route::get('/reports/services', [ServiceController::class, 'serviceStats'])->name('services.stats');

// Reports
Route::get('/admin/reports/daily', [ServiceController::class, 'dailyReport'])->name('reports.daily');
Route::get('/admin/reports/services', [ServiceController::class, 'serviceStats'])->name('reports.services');
Route::get('/admin/reports/services/export/pdf', [ServiceController::class, 'exportServiceStatsPdf'])->name('reports.services.pdf');
Route::get('/admin/reports/services/export/excel', [ServiceController::class, 'exportServiceStatsExcel'])->name('reports.services.excel');
Route::get('/admin/reports/rekap', [ServiceController::class, 'rekapReport'])->name('reports.rekap');
