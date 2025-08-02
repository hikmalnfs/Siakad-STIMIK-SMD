<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
protected function redirectTo(Request $request): ?string
{
    if (! $request->expectsJson()) {
        if ($request->is('admin/*')) {
            return route('admin.auth-signin-page');
        } elseif ($request->is('dosen/*')) {
            return route('dosen.auth-signin-page');
        } elseif ($request->is('mahasiswa/*')) {
            return route('mahasiswa.auth-signin-page');
        }

        return route('auth.render-signin'); // fallback untuk umum
    }

    return null;
}

}
