<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use App\Models\Dosen;

class IsDosenWali
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd($request);
        // Debugging: log untuk mengecek nilai session is_dosen_wali
        Log::info('Session is_dosen_wali:', ['is_dosen_wali' => session('is_dosen_wali')]);

        $dosen = Auth::guard("dosen")->user()->id;

        // Periksa apakah session 'is_dosen_wali' ada dan bernilai true
        if (Dosen::find($dosen)->wali == NULL) {
            // Tampilkan alert jika bukan Dosen Wali
            Alert::error('Akses ditolak', 'Anda bukan Dosen Wali, tidak memiliki akses ke fitur ini.');

            // Arahkan pengguna ke halaman home dosen
            return redirect()->route('dosen.home-index');
        }

        // Jika Dosen Wali, lanjutkan request
        return $next($request);
    }
}
