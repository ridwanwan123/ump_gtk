<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\AbsensiPegawaiController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Admin\MadrasahController;

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
    Route::resource('absensi', AbsensiPegawaiController::class)
        ->only(['index', 'store', 'update']);
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

        Route::resource('users', UserManagementController::class);
        Route::resource('madrasah', MadrasahController::class);
    });
