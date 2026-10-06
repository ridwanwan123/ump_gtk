<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetUnitKerja
{
    public function handle(Request $request, Closure $next)
    {
        app()->instance('current_madrasah_id', null);

        if (auth()->check()) {
            $user = auth()->user();

            if (!$user->hasRole('superadmin')) {
                app()->instance(
                    'current_madrasah_id',
                    // 0 = tidak cocok dengan madrasah mana pun, supaya operator
                    // yang belum punya unit kerja tidak melihat data semua madrasah
                    $user->unit_kerja ?? 0 // id madrasah
                );
            }
        }

        return $next($request);
    }
}
