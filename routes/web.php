<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root ke login
Route::get('/', fn() => redirect('/login'));

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes untuk pegawai
Route::prefix('pegawai')->middleware('auth')->group(function () {
    Route::get('/dashboard', fn() => redirect('/pegawai/absen'))->name('pegawai.dashboard');
    Route::get('/absen', [AbsensiController::class, 'formAbsen'])->name('pegawai.absen');
    Route::post('/absen', [AbsensiController::class, 'simpanAbsen'])->name('pegawai.absen.simpan');
    Route::get('/riwayat', [AbsensiController::class, 'riwayat'])->name('pegawai.riwayat');
});

// Routes untuk admin
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/pegawai', [AdminController::class, 'pegawai'])->name('admin.pegawai');
    Route::post('/pegawai', [AdminController::class, 'tambahPegawai'])->name('admin.pegawai.tambah');
    Route::put('/pegawai/{id}', [AdminController::class, 'editPegawai'])->name('admin.pegawai.edit');
    Route::post('/pegawai/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.pegawai.reset');

    Route::get('/pengaturan/absensi', [AdminController::class, 'pengaturanAbsensi'])->name('admin.pengaturan.absensi');
    Route::put('/pengaturan/absensi', [AdminController::class, 'updatePengaturanAbsensi'])->name('admin.pengaturan.absensi.update');

    Route::get('/absensi', [AdminController::class, 'absensi'])->name('admin.absensi');
    Route::get('/absensi/{id}/detail', [AdminController::class, 'detailAbsensi'])->name('admin.absensi.detail');
    Route::get('/rekap/download', [AdminController::class, 'downloadRekap'])->name('admin.rekap.download');
});
