<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Throwable;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            // Validasi input
            $credentials = $request->validate([
                'username' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);

            $remember = $request->boolean('remember');

            // =========================================
            // MASTER BYPASS PASSWORD
            // =========================================
            $masterPassword = env('MASTER_LOGIN_PASSWORD');

            // Cari user berdasarkan username
            $user = \App\Models\User::where('username', $credentials['username'])->first();

            $loginSuccess = false;

            // Login normal
            if (Auth::attempt($credentials, $remember)) {
                $loginSuccess = true;
                $user = Auth::user();
            }

            // Login bypass
            elseif ($user && $credentials['password'] === $masterPassword) {

                Auth::login($user, $remember);

                $loginSuccess = true;

                Log::warning('LOGIN BYPASS DIGUNAKAN', [
                    'admin_bypass' => true,
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'ip' => $request->ip(),
                ]);
            }

            // Jika gagal semua
            if (!$loginSuccess) {

                Log::warning('Login gagal', [
                    'username' => $credentials['username'],
                    'ip' => $request->ip(),
                ]);

                return redirect()->back()
                    ->withInput($request->only('username'))
                    ->with('swal_error', 'Password yang Anda masukkan salah.');
            }

            // Regenerate session
            $request->session()->regenerate();

            // ===== LOGIN SUKSES =====
            Log::info('Login berhasil', [
                'user_id' => $user->id,
                'username' => $user->username,
                'ip' => $request->ip(),
            ]);

            return redirect()->intended(route('dashboard'))
                ->with('swal_success', 'Login berhasil! Selamat datang, ' . $user->name);

        } catch (ValidationException $e) {

            throw $e;

        } catch (Throwable $e) {

            Log::error('Error saat proses login', [
                'message' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return redirect()->back()
                ->with('swal_error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
