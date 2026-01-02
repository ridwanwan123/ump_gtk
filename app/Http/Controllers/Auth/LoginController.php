<?php

namespace App\Http\Controllers\Auth;

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
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return redirect()->back()
                ->withInput($request->only('username'))
                ->with('swal_error', 'Username atau password salah');
        }

        Log::info('Percobaan login', [
            'username' => $credentials['username'],
            'ip' => $request->ip(),
            'time' => now(),
        ]);

        if (!Auth::attempt($credentials, $remember)) {
            Log::warning('Login gagal', [
                'username' => $credentials['username'],
                'ip' => $request->ip(),
            ]);

            // Return dengan flash message error ke session
            return redirect()->back()
                ->withInput($request->only('username'))
                ->with('swal_error', __('auth.failed'));
        }

        $request->session()->regenerate();

        $user = Auth::user();

        Log::info('Login berhasil', [
            'user_id' => $user->id,
            'username' => $user->username,
            'roles' => $user->getRoleNames(),
            'ip' => $request->ip(),
        ]);

        // Kirim flash message sukses
        return redirect()->intended(route('dashboard'))
            ->with('swal_success', 'Login berhasil! Selamat datang, ' . $user->name);
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