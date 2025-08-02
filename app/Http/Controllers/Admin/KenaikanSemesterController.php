<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;
use App\Models\Akademik\TahunAkademik;
use App\Models\Akademik\Kelas;  // Pastikan import kelas model
use App\Services\KenaikanSemesterService;

class KenaikanSemesterController extends Controller
{
    /**
     * Tampilkan dashboard kenaikan semester mahasiswa.
     */
    public function index()
    {
        $user = Auth::user();
        $spref = $user ? $user->prefix : '';
        $currentTahunAkademik = TahunAkademik::where('status', 'Aktif')->first();

        // Eager load relasi programStudi dan kelas (jika ada relasi kelas)
        $mahasiswas = Mahasiswa::with(['programStudi', 'kelas'])->get();

        // Ambil data unik untuk filter dropdown
        // $angkatanList = Mahasiswa::select('angkatan')
        //     ->whereNotNull('angkatan')
        //     ->distinct()
        //     ->orderBy('angkatan', 'asc')
        //     ->pluck('angkatan');
        $angkatanList = collect([]); // kosong jika kolom angkatan memang tidak ada


        $semesterList = Mahasiswa::select('semester')
            ->whereNotNull('semester')
            ->distinct()
            ->orderBy('semester', 'asc')
            ->pluck('semester');

        $kelasList = Kelas::orderBy('name', 'asc')->pluck('name');

        return view('master.akademik.kenaikan-semester.index', [
            'mahasiswas' => $mahasiswas,
            'currentTahunAkademik' => $currentTahunAkademik,
            'currentTahunAkademikId' => $currentTahunAkademik->id ?? null,
            'pages' => 'Kenaikan Semester',
            'menus' => 'Akademik',
            'academy' => config('app.name', 'SIAKAD'),
            'user' => $user,
            'spref' => $spref,
            'angkatanList' => $angkatanList,
            'semesterList' => $semesterList,
            'kelasList' => $kelasList,
        ]);
    }

    /**
     * Naikkan semester satu mahasiswa secara manual.
     */
    public function naik(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        $kenaikanService = app(KenaikanSemesterService::class);
        $berhasil = $kenaikanService->prosesNaikSemester($mahasiswa);

        if ($berhasil) {
            return redirect()->back()->with('success', 'Mahasiswa berhasil dinaikkan ke semester berikutnya.');
        }

        return redirect()->back()->with('error', 'Mahasiswa tidak memenuhi syarat kenaikan semester.');
    }

    /**
     * Proses massal naik semester semua mahasiswa.
     */
    public function naikMassal()
    {
        $kenaikanService = app(KenaikanSemesterService::class);
        $kenaikanService->prosesNaikSemesterSemua();

        return redirect()->back()->with('success', 'Proses kenaikan semester massal telah selesai.');
    }
}
