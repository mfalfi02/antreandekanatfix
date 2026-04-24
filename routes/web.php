<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
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


// Landing page publik yang mengarah ke display antrean atau login sistem.
Route::get('/', function () {
    return view('auth.welcome');
})->name('welcome');


// Route::get('/display',function() {
//     return view ('display.index');})->name('display');
// Route::get('/display/queues', [DisplayController::class, 'queues'])->name('display.queues');
// Route::get('/api/display/active-pejabat', [DisplayController::class, 'activePejabat']);
// Route::get('/api/display/queues', [DisplayController::class, 'queues']);
// Display publik bersifat read-only dan mengandalkan endpoint JSON untuk update realtime.
Route::get('/display', [DisplayController::class, 'index'])->name('display');
Route::get('/display/refresh', [DisplayController::class, 'refresh'])->name('display.refresh');
Route::get('/display/queues', [DisplayController::class, 'queues'])->name('display.queues');

// Autentikasi dasar untuk masuk dan keluar dari sistem.
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/admin', function (Request $request) {
    // Dashboard admin mengumpulkan metrik inti, lalu menormalkan periode bulan/tahun yang dipilih user.
    $setting = SystemSetting::firstOrCreate(
        ['id' => 1],
        [
            'queue_status' => 'closed',
            'radius_meters' => 300,
        ]
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
        ->pluck('tanggal_buka_ruang_antri')
        ->filter()
        ->map(fn ($date) => Carbon::parse($date, 'Asia/Jakarta')->year)
        ->unique()
        ->sortDesc()
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
        'locationSetting' => $setting,
    ]);
})->name('adm');

// Simpan titik pusat geofence yang dipakai untuk memvalidasi lokasi saat buka/tarik antrean.
Route::post('/admin/location', function (Request $request) {
    if (!auth()->check() || auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized');
    }

    // Jika kolom geofence belum ada di database lama, buat dulu supaya simpan lokasi tidak gagal.
    if (!Schema::hasColumn('system_settings', 'center_latitude') || !Schema::hasColumn('system_settings', 'center_longitude') || !Schema::hasColumn('system_settings', 'radius_meters')) {
        Schema::table('system_settings', function (Illuminate\Database\Schema\Blueprint $table) {
            if (!Schema::hasColumn('system_settings', 'center_latitude')) {
                $table->decimal('center_latitude', 10, 7)->nullable()->after('queue_status');
            }
            if (!Schema::hasColumn('system_settings', 'center_longitude')) {
                $table->decimal('center_longitude', 10, 7)->nullable()->after('center_latitude');
            }
            if (!Schema::hasColumn('system_settings', 'radius_meters')) {
                $table->unsignedInteger('radius_meters')->default(300)->after('center_longitude');
            }
        });
    }

    $request->validate([
        'center_latitude' => 'required|numeric|between:-90,90',
        'center_longitude' => 'required|numeric|between:-180,180',
        'radius_meters' => 'required|integer|min:1|max:5000',
    ]);

    $setting = SystemSetting::firstOrCreate(
        ['id' => 1],
        [
            'queue_status' => 'closed',
            'radius_meters' => 300,
        ]
    );

    $setting->update([
        'center_latitude' => $request->center_latitude,
        'center_longitude' => $request->center_longitude,
        'radius_meters' => $request->radius_meters,
    ]);

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'message' => 'Pengaturan lokasi antrean berhasil disimpan.',
            'setting' => $setting->fresh(),
        ]);
    }

    return redirect()->route('adm')->with('success', 'Pengaturan lokasi antrean berhasil disimpan.');
})->name('adm.location.update');

// Route::get('/dosen',function() {
//     return view ('dosen.dashboard');})->name('dsn');
// Semua route operasional dilindungi middleware agar hanya user yang sudah login yang bisa mengaksesnya.
Route::middleware(['ceklogin'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dosen'])->name('dsn');
    Route::get('/mahasiswa',[AuthController::class, 'mahasiswa'])->name('mhs');

    // Queue lifecycle: join, buka/tutup ruang, panggil, dan selesaikan antrean.
    Route::post('/queue/join', [DashboardController::class, 'joinQueue'])->name('queue.join');
    Route::post('/queue/toggle', [QueueController::class, 'toggleQueue'])->name('queue.toggle');
    Route::post('/queue/{queue}/call', [QueueController::class, 'callQueue'])->name('queue.call');
    Route::post('/queue/{queue}/complete', [QueueController::class, 'completeQueue'])->name('queue.complete');
    Route::get('/queue/status', [QueueController::class, 'status'])->name('queue.status');
    Route::get('/queue/my', [QueueController::class, 'myQueues'])->name('queue.my');
});

// Manajemen master data pengguna dan layanan.
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

// Reports harian dan bulanan dipisah agar query dan export lebih mudah dipelihara.
Route::get('/admin/reports/daily', [ServiceController::class, 'dailyReport'])->name('reports.daily');
Route::get('/admin/reports/services', [ServiceController::class, 'serviceStats'])->name('reports.services');
Route::get('/admin/reports/services/export/pdf', [ServiceController::class, 'exportServiceStatsPdf'])->name('reports.services.pdf');
Route::get('/admin/reports/services/export/excel', [ServiceController::class, 'exportServiceStatsExcel'])->name('reports.services.excel');
Route::get('/admin/reports/rekap', [ServiceController::class, 'rekapReport'])->name('reports.rekap');
