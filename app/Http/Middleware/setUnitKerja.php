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
                    $user->unit_kerja // id madrasah
                );
            }
        }

        return $next($request);
    }
}
