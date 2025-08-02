<?php
// app/Models/Khs.php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Akademik\TahunAkademik;
use App\Models\Mahasiswa;


class Khs extends Model
{
    use HasFactory;

    protected $fillable = [
        'mahasiswa_id',
        'tahun_akademik_id',
        'semester',
        'ip_semester',
        'ipk',
        'jumlah_sks',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }
}
