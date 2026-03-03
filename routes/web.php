<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;

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


Route::get('/admin',function() {
    $setting = SystemSetting::firstOrCreate(
        ['id' => 1],
        ['queue_status' => 'closed']
    );

    $data =[
                'totalUsers' => User::count(),
                'activeQueues' => Queue::where('status', 'aktif')->count(),
                'totalServices' => Service::count(),
                'completedQueues' => Queue::where('status', 'selesai')->count(),
                'users' => User::all(),
                'service' => Service::all(),
                'queue_status' => $setting->queue_status,
            ];
    return view ('admin.dashboard', compact('data'));})->name('adm');

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
Route::get('/admin/reports/rekap', [ServiceController::class, 'rekapReport'])->name('reports.rekap');
