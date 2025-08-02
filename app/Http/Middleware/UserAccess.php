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
        $guards = ['mahasiswa', 'dosen', 'web'];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Tentukan user role/stat sesuai guard
                $userRole = null;

                // Tentukan user role/status dari properti yang tersedia
                if (property_exists($user, 'role') && !empty($user->role)) {
                    $userRole = strtolower($user->role);
                } elseif (property_exists($user, 'mhs_stat') && !empty($user->mhs_stat)) {
                    $userRole = strtolower($user->mhs_stat);
                } elseif (property_exists($user, 'dsn_stat') && !empty($user->dsn_stat)) {
                    $userRole = strtolower($user->dsn_stat);
                }

                // Normalisasi input role yang diterima untuk perbandingan
                $normalizedRoles = array_map('strtolower', $roles);

                // Jika userRole ada di daftar role yang diizinkan, lanjut
                if ($userRole !== null && in_array($userRole, $normalizedRoles)) {
                    return $next($request);
                }
            }
        }

        // Kalau tidak cocok, redirect ke halaman akses ditolak
        return redirect()->route('error.access');
    }
}
