<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Mahasiswa;
use App\Models\Akademik\TahunAkademik;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik dasar
        $totalMahasiswa = DB::table('mahasiswas')->count();
        $totalMatkul    = DB::table('mata_kuliahs')->count();
        $totalDosen     = DB::table('dosens')->count();
        $totalJadwal    = DB::table('jadwal_kuliahs')->whereNull('deleted_at')->count();

        // Aktivitas user (opsional)
        $recentActivities = DB::table('log_aktivitas')->orderByDesc('created_at')->limit(10)->get();

        // Tahun akademik aktif
        $currentTahunAkademik = TahunAkademik::where('status', 'Aktif')->first();

        // Semua mahasiswa aktif dengan relasi program studi
        $mahasiswas = Mahasiswa::where('type', 1)->with('programStudi')->get();

        return view('central.back-content', [
            'totalMahasiswa' => $totalMahasiswa,
            'totalMatkul' => $totalMatkul,
            'totalDosen' => $totalDosen,
            'totalJadwal' => $totalJadwal,
            'recentActivities' => $recentActivities,
            'user' => auth()->user(),
            'mahasiswas' => $mahasiswas,
            'currentTahunAkademikId' => $currentTahunAkademik->id ?? null,
        ]);
    }

    public function getChartData()
    {
        $data = DB::table('mahasiswas')
            ->join('program_studis', 'mahasiswas.prodi_id', '=', 'program_studis.id')
            ->select('program_studis.name as prodi', DB::raw('COUNT(mahasiswas.id) as total'))
            ->groupBy('program_studis.name')
            ->get();

        return response()->json($data);
    }
}
