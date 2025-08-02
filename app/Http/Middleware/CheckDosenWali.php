<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDosenWali
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            // Cek kolom 'wali' == 1 (dosen wali)
            if ($user->wali == 1) {
                return $next($request);
            }
        }

        abort(403, 'Akses hanya untuk Dosen Wali');
    }
}
