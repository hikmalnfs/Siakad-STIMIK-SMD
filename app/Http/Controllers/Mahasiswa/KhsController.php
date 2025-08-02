<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Nilai;
use App\Models\Akademik\TahunAkademik;
use Barryvdh\DomPDF\Facade\Pdf;

class KhsController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $semesterFilter = request('semester');

        $nilai = Nilai::with([
                'jadwalKuliah.tahunAkademik',
                'jadwalKuliah.mataKuliah',
                'mataKuliah',
            ])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->when($semesterFilter, function ($query) use ($semesterFilter) {
                $query->whereHas('jadwalKuliah.mataKuliah', function ($q) use ($semesterFilter) {
                    $q->where('semester', $semesterFilter);
                });
            })
            ->get();

        $groupedNilai = $nilai->groupBy(function ($item) {
            return optional($item->jadwalKuliah->tahunAkademik)->name ?? 'Unknown';
        });

        $ipk_total_bobot = 0;
        $ipk_total_sks = 0;
        $ips_per_semester = [];

        foreach ($groupedNilai as $semester => $items) {
            $semester_sks = 0;
            $semester_bobot = 0;

            foreach ($items as $item) {
                $matkul = $item->jadwalKuliah->mataKuliah ?? $item->mataKuliah;
                $sks = optional($matkul)->bsks ?? 0;
                $bobot = $item->bobot ?? $this->getBobotDariNilaiAkhir($item->nilai_akhir ?? 0);

                $semester_sks += $sks;
                $semester_bobot += $sks * $bobot;

                $ipk_total_sks += $sks;
                $ipk_total_bobot += $sks * $bobot;

                $item->bobot = $bobot;
            }

            $ips_per_semester[$semester] = [
                'ips' => ($semester_sks > 0) ? round($semester_bobot / $semester_sks, 2) : 0,
                'sks' => $semester_sks,
                'bobot' => $semester_bobot,
            ];
        }

        $ipk = ($ipk_total_sks > 0) ? round($ipk_total_bobot / $ipk_total_sks, 2) : 0;

        return view('mahasiswa.khs.khs-index', compact(
            'nilai',
            'groupedNilai',
            'ips_per_semester',
            'ipk',
            'ipk_total_sks',
            'mahasiswa',
            'semesterFilter'
        ));
    }

    public function cetakPdf()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $semesterFilter = request('semester');

        $nilai = Nilai::with([
                'jadwalKuliah.tahunAkademik',
                'jadwalKuliah.mataKuliah',
                'mataKuliah',
            ])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->when($semesterFilter, function ($query) use ($semesterFilter) {
                $query->whereHas('jadwalKuliah.mataKuliah', function ($q) use ($semesterFilter) {
                    $q->where('semester', $semesterFilter);
                });
            })
            ->get();

        $data = $nilai; // ⬅ FIX: alias untuk dipakai di blade
        $groupedNilai = $nilai->groupBy(function ($item) {
            return optional($item->jadwalKuliah->tahunAkademik)->name ?? 'Unknown';
        });

        $ipk_total_bobot = 0;
        $ipk_total_sks = 0;
        $ips_per_semester = [];

        foreach ($groupedNilai as $semester => $items) {
            $semester_sks = 0;
            $semester_bobot = 0;

            foreach ($items as $item) {
                $matkul = $item->jadwalKuliah->mataKuliah ?? $item->mataKuliah;
                $sks = optional($matkul)->bsks ?? 0;
                $bobot = $item->bobot ?? $this->getBobotDariNilaiAkhir($item->nilai_akhir ?? 0);

                $semester_sks += $sks;
                $semester_bobot += $sks * $bobot;

                $ipk_total_sks += $sks;
                $ipk_total_bobot += $sks * $bobot;

                $item->bobot = $bobot;
            }

            $ips_per_semester[$semester] = [
                'ips' => ($semester_sks > 0) ? round($semester_bobot / $semester_sks, 2) : 0,
                'sks' => $semester_sks,
                'bobot' => $semester_bobot,
            ];
        }

        $ipk = ($ipk_total_sks > 0) ? round($ipk_total_bobot / $ipk_total_sks, 2) : 0;
        $semester = $groupedNilai->keys()->last() ?? 'Ganjil';
        $tahunAkademik = optional($nilai->first()->jadwalKuliah->tahunAkademik ?? null);

        $pdf = Pdf::loadView('mahasiswa.khs.khs-pdf', compact(
            'data',              // ⬅ PENTING
            'nilai',
            'groupedNilai',
            'ips_per_semester',
            'ipk',
            'ipk_total_sks',
            'mahasiswa',
            'semesterFilter',
            'semester',
            'tahunAkademik'
        ))->setPaper('A4', 'portrait');

        return $pdf->stream('KHS_' . $mahasiswa->name . '_Semester_' . ($semesterFilter ?: 'Semua') . '.pdf');
    }

    private function getBobotDariNilaiAkhir($nilai)
    {
        if ($nilai >= 93) return 4.00;
        if ($nilai >= 90) return 4.00;
        if ($nilai >= 87) return 3.70;
        if ($nilai >= 83) return 3.30;
        if ($nilai >= 80) return 3.00;
        if ($nilai >= 77) return 2.70;
        if ($nilai >= 73) return 2.30;
        if ($nilai >= 70) return 2.00;
        if ($nilai >= 67) return 1.70;
        if ($nilai >= 63) return 1.30;
        if ($nilai >= 60) return 1.00;
        return 0.00;
    }
}
