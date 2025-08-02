<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;


class DsnAccess
{
    public function handle(Request $request, Closure $next, $expectedStatus): Response
    {
        if (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();

            Log::info('Middleware DsnAccess:', [
                'user_email' => $user->email,
                'dsn_stat_raw' => $user->raw_dsn_stat,
                'dsn_stat_accessor' => $user->dsn_stat,
                'expectedStatus' => $expectedStatus,
            ]);

            // Gunakan accessor, bukan raw
            if (strtolower(trim($user->dsn_stat)) === strtolower(trim($expectedStatus))) {
                return $next($request);
            }

            Log::warning('Middleware DsnAccess blocked access', [
                'user_email' => $user->email,
                'dsn_stat_accessor' => $user->dsn_stat,
                'expectedStatus' => $expectedStatus,
            ]);

            return redirect()->route('error.access');
        }

        return redirect()->route('dosen.auth-signin-page');
    }
}
