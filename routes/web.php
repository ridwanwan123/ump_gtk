<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\AbsensiPegawaiController;
use App\Http\Controllers\Admin\UserManagementController;
// use App\Http\Controllers\Admin\MadrasahController;

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
    | Dashboard
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    | Pegawai
    */

    // Semua user login bisa melihat data
    Route::resource('pegawai', PegawaiController::class)
        ->only(['index', 'show']);

    Route::get('pegawai/export', [PegawaiController::class, 'export'])
        ->name('pegawai.export');

    // Operator boleh edit/update
    Route::middleware('role:operator|superadmin')->group(function () {
        Route::resource('pegawai', PegawaiController::class)
            ->only(['edit', 'update']);
    });

    // Hanya superadmin boleh tambah & hapus
    Route::middleware('role:superadmin')->group(function () {
        Route::resource('pegawai', PegawaiController::class)
            ->only(['create', 'store', 'destroy']);
    });


    /*
    | Absensi Pegawai
    */

    // Semua user bisa lihat absensi
    Route::resource('absensi', AbsensiPegawaiController::class)
        ->only(['index']);

    Route::get('absensi/export', [AbsensiPegawaiController::class, 'export'])
        ->name('absensi.export');

    // Operator boleh input absensi
    Route::middleware('role:operator')->group(function () {
        Route::resource('absensi', AbsensiPegawaiController::class)
            ->only(['create', 'store', 'update']);
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
        Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.updateRole');
    });




/*
| Ubah Password (semua user)
*/
Route::get('/ubah-password', [UserManagementController::class, 'editPassword'])
    ->name('auth.ubah_password');

Route::post('/ubah-password', [UserManagementController::class, 'updatePassword'])
    ->name('auth.ubah_password.update');