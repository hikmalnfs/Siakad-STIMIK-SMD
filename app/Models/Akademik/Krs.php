<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Database\Eloquent\SoftDeletes;

class Krs extends Model
{
    use SoftDeletes;

    protected $table = 'krs';

    protected $fillable = [
        'users_id',
        'jadwal_id',
        'tahun_akademik_id',
        'status',
        'nilai_angka',
        'nilai_huruf',
        'keterangan', // pastikan kolom ini ada di database jika digunakan untuk penolakan
    ];

    /**
     * Relasi ke jadwal kuliah
     */
    public function jadwal()
    {
        return $this->belongsTo(JadwalKuliah::class, 'jadwal_id');
    }

    /**
     * Relasi ke mahasiswa
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'users_id');
    }

    /**
     * Relasi ke tahun akademik
     */
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    public function wali()
    {
        return $this->belongsTo(Dosen::class, 'wali_id');
    }

    
}
