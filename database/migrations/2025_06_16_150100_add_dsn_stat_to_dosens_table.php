<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UserAccess
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $guards = ['mahasiswa', 'dosen', 'web']; // Sesuaikan jika ada guard lain
        $user = null;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                break;
            }
        }

        if ($user && in_array($user->role ?? $user->mhs_stat ?? $user->dsn_stat ?? null, $roles)) {
            return $next($request);
        }

        return redirect()->route('error.access');
    }
}
