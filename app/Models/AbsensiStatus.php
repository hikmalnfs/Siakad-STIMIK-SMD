<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiStatus extends Model
{
    protected $table = 'absensi_statuses';

    protected $fillable = [
        'jadkul_code',
        'pertemuan',
        'is_active',
        'status',
    ];

    public function jadwal()
    {
        return $this->belongsTo(\App\Models\Akademik\JadwalKuliah::class, 'jadkul_code', 'code');
    }

    public function absensiMahasiswa()
    {
        return $this->hasMany(\App\Models\AbsensiMahasiswa::class, 'jadkul_code', 'jadkul_code')
            ->whereColumn('pertemuan', 'absensi_statuses.pertemuan');
    }
}
