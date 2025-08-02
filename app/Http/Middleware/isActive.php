<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
{
    $guards = ['web', 'dosen', 'mahasiswa'];

    foreach ($guards as $guard) {
        if (auth()->guard($guard)->check()) {
            $user = auth()->guard($guard)->user();

            if ($user && $user->status == 1) {
                return $next($request);
            }

            return redirect()->route('error.access');
        }
    }

    // Kalau tidak login di semua guard, arahkan ke halaman login default
    return redirect()->route('auth.render-signin');
}

}
