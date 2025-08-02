<?php
// app/Models/AbsensiMahasiswa.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mahasiswa;
use App\Models\Akademik\JadwalKuliah;

class AbsensiMahasiswa extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Akses label status absen dari kode singkat
     */
    public function getAbsenTypeAttribute($value)
    {
        $absentypes = [
            'H' => 'Hadir',
            'S' => 'Sakit',
            'I' => 'Izin',
            'A' => 'Alpa',
        ];

        return $absentypes[$value] ?? 'Unknown';
    }

    /**
     * Ambil kode status absen apa adanya tanpa mapping
     */
    public function getRawAbsenTypeAttribute()
    {
        return $this->attributes['absen_type'] ?? null;
    }

    /**
     * Relasi ke Mahasiswa (author)
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'author_id');
    }

    /**
     * Relasi ke JadwalKuliah berdasarkan kode jadwal
     */
    public function jadkul()
    {
        return $this->belongsTo(JadwalKuliah::class, 'jadkul_code', 'code');
    }
}
