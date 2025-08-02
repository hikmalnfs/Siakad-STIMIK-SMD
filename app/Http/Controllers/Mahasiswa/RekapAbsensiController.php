<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Akademik\JadwalKuliah;
use App\Models\AbsensiMahasiswa;
use App\Models\AbsensiStatus;

class RekapAbsensiController extends Controller
{
    public function index()
    {
        $mahasiswaId = Auth::guard('mahasiswa')->id();

        $jadwalKuliah = JadwalKuliah::with([
            'mataKuliah:id,name',
            'dosen:id,name',
        ])
        ->whereHas('krs', function ($query) use ($mahasiswaId) {
            $query->where('users_id', $mahasiswaId);
        })
        ->get();

        $rekap = $jadwalKuliah->map(function ($jadwal) use ($mahasiswaId) {
            $absensi = AbsensiMahasiswa::where('author_id', $mahasiswaId)
                ->where('jadkul_code', $jadwal->code)
                ->get();

            $absensiPerPertemuan = [];
            $prioritas = [
                'hadir' => 4,
                'izin' => 3,
                'sakit' => 2,
                'alpa' => 1,
                '-' => 0,
            ];

            foreach ($absensi as $row) {
                $absenType = strtolower(trim($row->absen_type));
                $pertemuan = $row->pertemuan;

                if (!isset($absensiPerPertemuan[$pertemuan]) || $prioritas[$absenType] > $prioritas[$absensiPerPertemuan[$pertemuan]]) {
                    $absensiPerPertemuan[$pertemuan] = $absenType;
                }
            }

            for ($i = 1; $i <= 16; $i++) {
                if (!isset($absensiPerPertemuan[$i])) {
                    $absensiPerPertemuan[$i] = '-';
                }
            }

            $absensiStatus = AbsensiStatus::where('jadkul_code', $jadwal->code)
                ->orderBy('pertemuan')
                ->get();

            $keteranganPertemuan = [];
            for ($i = 1; $i <= 16; $i++) {
                $status = $absensiStatus->firstWhere('pertemuan', $i);
                $keteranganPertemuan[$i] = $status ? date('d M', strtotime($status->tanggal ?? now())) : '';
            }

            $jumlahHadir = collect($absensiPerPertemuan)
                ->filter(fn($val) => $val === 'hadir')
                ->count();

            $totalPertemuan = 16;
            $persentase = $totalPertemuan > 0 ? round(($jumlahHadir / $totalPertemuan) * 100, 2) : 0;

            return [
                'jadwal' => $jadwal,
                'mata_kuliah' => $jadwal->mataKuliah->name ?? '-',
                'sks' => $jadwal->bsks ?? 0,
                'dosen' => $jadwal->dosen->name ?? '-',
                'absensiPerPertemuan' => $absensiPerPertemuan,
                'keterangan_pertemuan' => $keteranganPertemuan,
                'jumlah_hadir' => $jumlahHadir,
                'persentase' => $persentase,
                'status' => $persentase >= 75 ? 'Memenuhi' : 'Belum Memenuhi',
            ];
        });

        return view('mahasiswa.pages.mhs-jadkul-rekap', compact('rekap'));
    }
}
