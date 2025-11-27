<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('superadmin')) {
            return redirect()->route('admin.dashboard');
        }
        if (auth()->user()->hasRole('bendahara')) {
            return redirect()->route('bendahara.dashboard');
        }
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    // tampilkan form login (view Anda)
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    // proses login
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

// logout (hanya untuk user yg ter-auth)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// DASHBOARD ROUTES
Route::get('/admin/dashboard', [DashboardController::class, 'index'])
    ->name('admin.dashboard')
    ->middleware(['auth', 'role:superadmin']);

Route::get('/bendahara/dashboard', [DashboardController::class, 'index'])
    ->name('bendahara.dashboard')
    ->middleware(['auth', 'role:bendahara']);