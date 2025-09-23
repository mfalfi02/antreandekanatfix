<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Auth;



// use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('auth.welcome');
});


Route::get('/login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class,'login'])->name('login.submit');
Route::post('/logout', [LoginController::class,'logout'])->name('logout');



Route::post('/login', [LoginController::class, 'login'])->name('login.submit');


// Admin dashboard
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});

// ===== Dosen =====
Route::middleware(['auth:dosen'])->group(function () {
    Route::get('/dosen/dashboard', [DosenController::class, 'index'])->name('dosen.dashboard');
});

// ===== Mahasiswa =====
Route::middleware(['auth:mahasiswa'])->group(function () {
    Route::get('/mahasiswa/dashboard', [MahasiswaController::class, 'index'])->name('mahasiswa.dashboard');
});






Route::get('/display', [DisplayController::class, 'index'])->name('display');

// Route::resource('admin', AdminController::class);
// Route::resource('dosen', DosenController::class);
// Route::resource('mahasiswa', MahasiswaController::class);


// routes/web.php


// Dashboard Dosen
// Route::middleware(['auth:dosen'])->group(function () {
//     Route::get('/dosen/dashboard', [DashboardController::class, 'dosen'])
//         ->name('dashboard.dosen'); // <-- sesuaikan dengan redirect
// });

// Route::middleware(['auth:mahasiswa'])->group(function () {
//     Route::get('/mahasiswa/dashboard', [DashboardController::class, 'mahasiswa'])
//         ->name('dashboard.mahasiswa');
// });


// Halaman dashboard
Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('dashboard.admin');

// CRUD User (Admin, Dosen, Mahasiswa) lewat UserController
Route::prefix('admin/users')->group(function() {
    // Tampilkan semua pengguna
    Route::get('/', [UserController::class, 'index'])->name('users.index');

    // Form tambah pengguna
    Route::get('/create', [UserController::class, 'create'])->name('users.create');

    // Simpan pengguna baru
    Route::post('/', [UserController::class, 'store'])->name('users.store');

    // Form edit pengguna
    Route::get('/{role}/{id}/edit', [UserController::class, 'edit'])->name('users.edit');

    // Update pengguna
    Route::put('/{role}/{id}', [UserController::class, 'update'])->name('users.update');

    // Hapus pengguna
 
    Route::delete('/{role}/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/admin/users/{role}/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::delete('/admin/users/{role}/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});





Route::middleware(['auth:admin'])->group(function(){
    Route::get('/admin/dashboard',[DashboardController::class,'admin'])->name('dashboard.admin');
    
    Route::get('/admin/services/create',[ServiceController::class,'create'])->name('auth.admin.services.create');
    Route::post('/admin/services',[ServiceController::class,'store'])->name('services.store');
    Route::get('/admin/services/{service}/edit',[ServiceController::class,'edit'])->name('services.edit');
    Route::put('/admin/services/{service}',[ServiceController::class,'update'])->name('services.update');
    Route::delete('/admin/services/{service}',[ServiceController::class,'destroy'])->name('services.destroy');
});

Route::resource('services', ServiceController::class);




// Reports
Route::get('/admin/reports/daily', [AdminController::class, 'dailyReport'])->name('reports.daily');
Route::get('/admin/reports/services', [AdminController::class, 'serviceStats'])->name('reports.services');
Route::get('/admin/reports/rekap', [AdminController::class, 'rekapReport'])->name('reports.rekap');
