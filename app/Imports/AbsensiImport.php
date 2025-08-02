<?php

namespace App\Imports;

use App\Models\AbsensiMahasiswa;
use App\Models\Mahasiswa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AbsensiImport implements ToCollection
{
    protected $jadkulCode;
    protected $pertemuan;

    public function __construct($jadkulCode, $pertemuan)
    {
        $this->jadkulCode = $jadkulCode;
        $this->pertemuan = $pertemuan;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row) {
            // Format kolom: NIM | Status
            $nim = trim($row[0]);
            $statusText = trim($row[1]);

            $mahasiswa = Mahasiswa::where('numb_nim', $nim)->first();
            if (!$mahasiswa) {
                continue; // skip kalau NIM tidak ditemukan
            }

            $mapStatus = [
                'Hadir' => 'H',
                'Sakit' => 'S',
                'Izin' => 'I',
                'Alpha' => 'A',
            ];

            $statusCode = $mapStatus[$statusText] ?? null;

            if (!$statusCode) {
                continue; // skip status tidak valid
            }

            AbsensiMahasiswa::updateOrCreate(
                [
                    'jadkul_code' => $this->jadkulCode,
                    'author_id' => $mahasiswa->id,
                    'pertemuan' => $this->pertemuan,
                ],
                [
                    'absen_type' => $statusCode,
                    'absen_date' => now()->toDateString(),
                    'absen_time' => now()->toTimeString(),
                    'absen_desc' => 'Diimport via Excel',
                    'absen_proof' => '-',
                ]
            );
        }
    }
}
