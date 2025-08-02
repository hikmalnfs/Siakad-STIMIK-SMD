<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class MhsAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $expectedStatus): Response
    {
        if (auth()->guard('mahasiswa')->check()) {
            $user = auth()->guard('mahasiswa')->user();

            // Ganti dari mhs_stat ke status
            $status = $user->status ?? null;

            Log::info('Middleware MhsAccess:', [
                'user_email' => $user->email,
                'mhs_stat_raw' => $status,
                'expectedStatus' => $expectedStatus,
            ]);

            if (!$status) {
                Log::error('Mahasiswa tidak memiliki status', [
                    'user_email' => $user->email,
                ]);
                return redirect()->route('error.access');
            }

            if (Str::lower(trim($status)) === Str::lower(trim($expectedStatus))) {
                return $next($request);
            }

            Log::warning('Middleware MhsAccess blocked access', [
                'user_email' => $user->email,
                'mhs_stat' => $status,
                'expectedStatus' => $expectedStatus,
            ]);

            return redirect()->route('error.access');
        }

        return redirect()->route('auth.render-signin');

    }
}
