<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Nilai extends Model
{
    use SoftDeletes;

    protected $table = 'nilai'; // sesuaikan dengan nama tabel di database

    protected $guarded = [];

    
    public function jadwalKuliah()
    {
        return $this->belongsTo(\App\Models\Akademik\JadwalKuliah::class, 'jadwal_kuliah_id', 'id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(\App\Models\Mahasiswa::class, 'mahasiswa_id');
    }
        public function mataKuliah()
    {
        return $this->belongsTo(\App\Models\Akademik\MataKuliah::class, 'matkul_id');
    }

}
