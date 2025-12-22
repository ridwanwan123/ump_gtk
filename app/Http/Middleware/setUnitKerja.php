<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetUnitKerja
{
    public function handle(Request $request, Closure $next)
    {
        // DEFAULT: set null (penting!)
        app()->instance('current_madrasah_id', null);

        if (auth()->check()) {
            $user = auth()->user();

            // Jika bukan superadmin → set sesuai unit kerja
            if (!$user->hasRole('superadmin')) {
                app()->instance(
                    'current_madrasah_id',
                    $user->unit_kerja // id madrasah
                );
            }
        }

        return $next($request);
    }
}
