<?php

namespace App\Exports;

use App\Models\AbsensiMahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbsensiExport implements FromCollection, WithHeadings
{
    protected $jadkulCode;
    protected $pertemuan;

    public function __construct($jadkulCode, $pertemuan = null)
    {
        $this->jadkulCode = $jadkulCode;
        $this->pertemuan = $pertemuan;
    }

    public function collection()
    {
        $query = AbsensiMahasiswa::where('jadkul_code', $this->jadkulCode);

        if ($this->pertemuan !== null) {
            $query->where('pertemuan', $this->pertemuan);
        }

        // Ambil data absensi + relasi mahasiswa
        return $query->with('mahasiswa:id,name,numb_nim')
            ->get()
            ->map(function ($item) {
                return [
                    'NIM' => $item->mahasiswa->numb_nim ?? '-',
                    'Nama Mahasiswa' => $item->mahasiswa->name ?? '-',
                    'Pertemuan' => $item->pertemuan,
                    'Status' => $this->convertStatusCodeToText($item->absen_type),
                    'Tanggal' => $item->absen_date,
                    'Waktu' => $item->absen_time,
                ];
            });
    }

    public function headings(): array
    {
        return ['NIM', 'Nama Mahasiswa', 'Pertemuan', 'Status', 'Tanggal', 'Waktu'];
    }

    protected function convertStatusCodeToText($code)
    {
        return match ($code) {
            'H' => 'Hadir',
            'S' => 'Sakit',
            'I' => 'Izin',
            'A' => 'Alpha',
            default => 'Belum Absen',
        };
    }
}
