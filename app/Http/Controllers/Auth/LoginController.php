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

            // ===== GAGAL LOGIN =====
            if (!Auth::attempt($credentials, $remember)) {
                Log::warning('Login gagal', [
                    'username' => $credentials['username'],
                    'ip' => $request->ip(),
                ]);

                return redirect()->back()
                    ->withInput($request->only('username'))
                    ->with('swal_error', __('auth.failed'));
            }

            // Regenerate session setelah login sukses
            $request->session()->regenerate();

            $user = Auth::user();

            // ===== LOGIN SUKSES =====
            Log::info('Login berhasisl', [
                'user_id' => $user->id,
                'username' => $user->username,
                'ip' => $request->ip(),
            ]);

            return redirect()->intended(route('dashboard'))
                ->with('swal_success', 'Login berhasil! Selamat datang, ' . $user->name);

        } catch (ValidationException $e) {
            // Validasi gagal (biasanya tidak perlu log error)
            throw $e;

        } catch (Throwable $e) {
            // ===== ERROR TIDAK TERDUGA =====
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
