<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class CheckUser
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $userType): Response
    {
        // Cek user dari guard mana saja (default, dosen, mahasiswa)
        $user = Auth::user() 
            ?? Auth::guard('dosen')->user() 
            ?? Auth::guard('mahasiswa')->user();

        if (!$user) {
            // Belum login di semua guard, redirect ke login
            return redirect()->route('auth.render-signin');
        }

        // Pastikan properti type ada dan cocok
        if (isset($user->type) && $user->type === $userType) {
            return $next($request);
        }

        // Jika tipe user salah, beri alert dan redirect ke halaman sebelumnya atau home
        Alert::error('Akses Ditolak', 'Kamu tidak diizinkan masuk');

        // Jika ada halaman sebelumnya, redirect back, kalau tidak redirect ke home
        return redirect()->back()->withInput();
    }
}
