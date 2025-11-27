<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // VALIDASI INPUT
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->filled('remember');

        try {

            Log::info('Percobaan login', [
                'username' => $credentials['username'],
                'ip' => $request->ip(),
                'time' => now(),
            ]);

            // Coba login
            if (Auth::attempt([
                'username' => $credentials['username'],
                'password' => $credentials['password']
            ], $remember)) {

                $request->session()->regenerate();

                $user = Auth::user();
                $roles = $user->getRoleNames();

                Log::info('Login berhasil', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'roles' => $roles,
                    'ip' => $request->ip(),
                    'time' => now(),
                ]);

                // REDIRECT BERDASARKAN ROLE
                if ($user->hasRole('superadmin')) {
                    Log::info('Redirect berdasarkan role', ['role' => 'superadmin']);
                    return redirect()->intended(route('admin.dashboard'));
                }

                if ($user->hasRole('bendahara')) {
                    Log::info('Redirect berdasarkan role', ['role' => 'bendahara']);
                    return redirect()->intended(route('bendahara.dashboard'));
                }

                Log::info('Redirect default user tanpa role khusus');
                return redirect()->intended('/home');
            }

            // Jika password salah
            Log::warning('Login gagal - password salah', [
                'username' => $credentials['username'],
                'ip' => $request->ip(),
                'time' => now(),
            ]);

            throw ValidationException::withMessages([
                'username' => [trans('auth.failed')],
            ]);

        } catch (\Exception $e) {

            Log::error('Error login tidak terduga', [
                'username' => $credentials['username'] ?? null,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'time' => now(),
            ]);

            throw $e;
        }
    }

    // logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}