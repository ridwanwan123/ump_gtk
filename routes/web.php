<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\RekapHonorController;
use App\Http\Controllers\AbsensiPegawaiController;
use App\Http\Controllers\PengusulanPegawaiController;
use App\Http\Controllers\PenonaktifanPegawaiController;
use App\Http\Controllers\Admin\UserManagementController;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| MAIN APP (SEMUA USER LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'set.unit'])->group(function () {

    /*
    | Dashboard
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Pegawai
    |--------------------------------------------------------------------------
    */
    Route::get('pegawai/export', [PegawaiController::class, 'export'])
        ->name('pegawai.export');

    Route::resource('pegawai', PegawaiController::class);

    /*
    |--------------------------------------------------------------------------
    | Absensi Pegawai
    |--------------------------------------------------------------------------
    */
    Route::get('absensi/export', [AbsensiPegawaiController::class, 'export'])
        ->name('absensi.export');

    Route::resource('absensi', AbsensiPegawaiController::class);

    /*
    |--------------------------------------------------------------------------
    | Pengusulan Pegawai
    |--------------------------------------------------------------------------
    */
    Route::prefix('pengusulan-pegawai')->name('pengusulan-pegawai.')->group(function () {
        Route::post('{pegawai}/terima_pengusulan_pegawai', [PengusulanPegawaiController::class, 'terima_pengusulan_pegawai'])->name('terima_pengusulan_pegawai');
    });

    Route::resource('pengusulan-pegawai', PengusulanPegawaiController::class);

    /*
    |--------------------------------------------------------------------------
    | Penonaktifan Pegawai
    |--------------------------------------------------------------------------
    */
    Route::prefix('penonaktifan-pegawai')->name('penonaktifan-pegawai.')->group(function () {
        Route::post('{pegawai}/proses', [PenonaktifanPegawaiController::class, 'pengajuan_nonaktif_pegawai'])->name('proses');
        Route::post('{pegawai}/nonaktif', [PenonaktifanPegawaiController::class, 'terima_nonaktif_pegawai'])->name('nonaktif');
        Route::post('{pegawai}/tolak', [PenonaktifanPegawaiController::class, 'tolak_nonaktif_pegawai'])->name('tolak');
    });

    // baru resource di bawah
    Route::resource('penonaktifan-pegawai', PenonaktifanPegawaiController::class);
    
    Route::resource('rekap-honor', RekapHonorController::class);
    
});

/*
|--------------------------------------------------------------------------
| ADMIN AREA (KHUSUS SUPERADMIN)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'role:superadmin'])
    ->name('admin.')
    ->group(function () {
        Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.updateRole');
    });

/*
|--------------------------------------------------------------------------
| Ubah Password (SEMUA USER)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/ubah-password', [UserManagementController::class, 'editPassword'])
        ->name('auth.ubah_password');

    Route::post('/ubah-password', [UserManagementController::class, 'updatePassword'])
        ->name('auth.ubah_password.update');
});