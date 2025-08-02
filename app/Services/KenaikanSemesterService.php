<?php

namespace App\Services;

use App\Models\Akademik\Krs;

class KenaikanSemesterService
{
    /**
     * Konversi nilai huruf ke bobot angka IP.
     * 
     * @param string|null $nilaiHuruf
     * @return float
     */
    public static function konversiNilaiHurufKeBobot(?string $nilaiHuruf): float
    {
        $nilaiMap = [
            'A'  => 4.0,
            'A-' => 3.7,
            'B+' => 3.3,
            'B'  => 3.0,
            'B-' => 2.7,
            'C+' => 2.3,
            'C'  => 2.0,
            'C-' => 1.7,
            'D'  => 1.0,
            'E'  => 0.0,
            'T'  => 0.0, // Tidak lulus / tidak ikut ujian
        ];

        return $nilaiMap[$nilaiHuruf] ?? 0.0;
    }

    /**
     * Hitung total SKS dan IP semester mahasiswa pada tahun akademik tertentu.
     *
     * @param int $mahasiswaId
     * @param int $tahunAkademikId
     * @return array ['jumlah_sks' => int, 'ip_semester' => float]
     */
    public static function hitungIpsks(int $mahasiswaId, int $tahunAkademikId): array
    {
        $krsSemester = Krs::with(['jadwal.mataKuliah'])
            ->where('users_id', $mahasiswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->get();

        $totalSks = 0;
        $totalBobotNilai = 0;

        foreach ($krsSemester as $krs) {
            $nilaiHuruf = $krs->nilai_huruf;
            $bobot = self::konversiNilaiHurufKeBobot($nilaiHuruf);
            $sks = $krs->jadwal->mataKuliah->sks ?? 0;

            // Hitung hanya jika nilai bobot dan sks valid (lulus)
            if ($bobot > 0 && $sks > 0) {
                $totalSks += $sks;
                $totalBobotNilai += ($bobot * $sks);
            }
        }

        $ipSemester = $totalSks > 0 ? round($totalBobotNilai / $totalSks, 2) : 0.0;

        return [
            'jumlah_sks' => $totalSks,
            'ip_semester' => $ipSemester,
        ];
    }

    /**
     * Cek apakah mahasiswa memenuhi syarat kenaikan semester berdasarkan
     * SKS minimum dan IP minimum.
     *
     * @param int $mahasiswaId
     * @param int $tahunAkademikId
     * @param int $skmMin Minimal SKS yang harus diambil (default 12)
     * @param float $ipMin Minimal IP semester (default 2.0)
     * @return array ['naik' => bool, 'jumlah_sks' => int, 'ip_semester' => float]
     */
    public static function cekKenaikanSemester(int $mahasiswaId, int $tahunAkademikId, int $skmMin = 12, float $ipMin = 2.0): array
    {
        $hasil = self::hitungIpsks($mahasiswaId, $tahunAkademikId);

        $naik = ($hasil['jumlah_sks'] >= $skmMin) && ($hasil['ip_semester'] >= $ipMin);

        return [
            'naik' => $naik,
            'jumlah_sks' => $hasil['jumlah_sks'],
            'ip_semester' => $hasil['ip_semester'],
        ];
    }
}
