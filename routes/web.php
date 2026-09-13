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

// Jalur publik: landing page dan display yang bisa dibuka tanpa login.
Route::get('/', function () {
    return view('auth.welcome');
})->name('welcome');

Route::get('/display', [DisplayController::class, 'index'])->name('display');
Route::get('/display/refresh', [DisplayController::class, 'refresh'])->name('display.refresh');
Route::get('/display/queues', [DisplayController::class, 'queues'])->name('display.queues');

// Jalur autentikasi untuk masuk dan keluar dari sistem.
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])
    ->middleware('signed')
    ->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Dashboard admin: ringkasan sistem, pengaturan lokasi, dan respons JSON saat dibutuhkan.
Route::get('/admin', function () {
    $setting = SystemSetting::firstOrCreate(
        ['id' => 1],
        [
            'queue_status' => 'closed',
            'radius_meters' => 300,
        ]
    );

    $data = [
        'totalUsers' => User::count(),
        'activeQueues' => Queue::where('status', 'aktif')->count(),
        'totalServices' => Service::count(),
        'completedQueues' => Queue::where('status', 'selesai')->count(),
        'users' => User::all(),
        'service' => Service::all(),
        'queue_status' => $setting->queue_status,
    ];

    return view('admin.dashboard', [
        'data' => $data,
        'locationSetting' => $setting,
    ]);
})->name('adm');

Route::post('/admin/location', function (Request $request) {
    if (!auth()->check() || auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized');
    }

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

// Alur yang harus login terlebih dahulu sebelum mengakses dashboard dan operasi antrean.
Route::middleware(['auth'])->group(function () {
    Route::get('/mahasiswa', [AuthController::class, 'mahasiswa'])
        ->name('mhs');

    Route::get('/dashboard', [AuthController::class, 'dosen'])
        ->name('dsn');

    Route::post('/queue/join', [DashboardController::class, 'joinQueue'])
        ->name('queue.join');
    Route::post('/queue/toggle', [QueueController::class, 'toggleQueue'])
        ->name('queue.toggle');
    Route::post('/queue/{queue}/call', [QueueController::class, 'callQueue'])
        ->name('queue.call');
    Route::post('/queue/{queue}/complete', [QueueController::class, 'completeQueue'])
        ->name('queue.complete');
    Route::get('/queue/status', [QueueController::class, 'status'])
        ->name('queue.status');
    Route::get('/queue/my', [QueueController::class, 'myQueues'])
        ->name('queue.my');
});

// Master data pengguna untuk admin.
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');

Route::get('/users/{kode}/edit', [UserController::class, 'edit'])->name('users.edit');

Route::put('/users/{kode}', [UserController::class, 'update'])->name('users.update');

Route::delete('/users/{kode}', [UserController::class, 'destroy'])->name('users.destroy');

// Master data layanan yang dipakai di antrean, dashboard, dan laporan.
Route::middleware(['auth', 'cekrole:admin,pejabat'])->group(function () {
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
});

Route::middleware(['auth', 'cekrole:admin'])->group(function () {
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
});

// Laporan layanan untuk ringkasan statistik dan distribusi data.
Route::get('/reports/services', [ServiceController::class, 'serviceStats'])->name('services.stats');

// Laporan admin untuk observasi harian, rekapan, dan export PDF/Excel.
Route::get('/admin/reports/daily', [ServiceController::class, 'dailyReport'])->name('reports.daily');
Route::get('/admin/reports/services', [ServiceController::class, 'serviceStats'])->name('reports.services');
Route::get('/admin/reports/services/export/pdf', [ServiceController::class, 'exportServiceStatsPdf'])->name('reports.services.pdf');
Route::get('/admin/reports/services/export/excel', [ServiceController::class, 'exportServiceStatsExcel'])->name('reports.services.excel');
Route::get('/admin/reports/rekap', [ServiceController::class, 'rekapReport'])->name('reports.rekap');
