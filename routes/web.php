<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\AbsensiPegawaiController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Admin\MadrasahController;

/*
|--------------------------------------------------------------------------|
| ROOT                                                                      |
|--------------------------------------------------------------------------|
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------|
| AUTH                                                                      |
|--------------------------------------------------------------------------|
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------|
| MAIN APP (SEMUA USER LOGIN)                                               |
|--------------------------------------------------------------------------|
*/
Route::middleware(['auth', 'set.unit'])->group(function () {

    /*
    | Dashboard (SATU SAJA)
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    | Pegawai (RESTFUL)
    | Superadmin: semua
    | Bendahara: otomatis difilter unit_kerja
    */
    // Semua orang bisa lihat index & show
    Route::get('pegawai/export', [PegawaiController::class, 'export'])
        ->name('pegawai.export');

    Route::resource('pegawai', PegawaiController::class)
        ->only(['index', 'show', 'create']);

    // Hanya superadmin bisa create, store, edit, update, destroy
    Route::middleware('role:superadmin')->group(function () {
        Route::resource('pegawai', PegawaiController::class)
            ->only(['create', 'store', 'edit', 'update', 'destroy']);
    });

    /*
    | Absensi Pegawai (RESTFUL)
    */
    // Semua orang bisa melihat data absensi dan ekspor (untuk superadmin)
    Route::get('absensi/export', [AbsensiPegawaiController::class, 'export'])
        ->name('absensi.export');

    // Semua orang bisa melihat absensi
    Route::resource('absensi', AbsensiPegawaiController::class)
        ->only(['index', 'show', 'create']);

    // Hanya bendahara yang bisa membuat dan mengupdate absensi
    Route::middleware('role:bendahara')->group(function () {
        Route::get('absensi/create', [AbsensiPegawaiController::class, 'create'])->name('absensi.create');
        Route::post('absensi/store', [AbsensiPegawaiController::class, 'store'])->name('absensi.store');
        Route::put('absensi/update/{absensiPegawai}', [AbsensiPegawaiController::class, 'update'])->name('absensi.update');
    });

    // Superadmin hanya bisa melihat dan ekspor data absensi
    Route::middleware('role:superadmin')->group(function () {
        Route::get('absensi/export', [AbsensiPegawaiController::class, 'export'])->name('absensi.export');
    });
});

/*
|--------------------------------------------------------------------------|
| ADMIN AREA (KHUSUS SUPERADMIN)                                           |
|--------------------------------------------------------------------------|
*/
Route::prefix('admin')
    ->middleware(['auth', 'role:superadmin'])
    ->name('admin.')
    ->group(function () {

        Route::resource('users', UserManagementController::class);
        Route::resource('madrasah', MadrasahController::class);
    });
