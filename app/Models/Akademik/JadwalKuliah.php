<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasLogAktivitas;

use App\Models\Akademik\MataKuliah;
use App\Models\Dosen;
use App\Models\Ruang;
use App\Models\Akademik\JenisKelas;
use App\Models\Akademik\WaktuKuliah;
use App\Models\Akademik\Kelas;
use App\Models\StudentTask;
use App\Models\AbsensiMahasiswa;
use App\Models\Nilai;
use App\Models\Akademik\TahunAkademik;

class JadwalKuliah extends Model
{
    use HasFactory, SoftDeletes, HasLogAktivitas;

    protected $guarded = [];

    // Accessors
    public function getDaysIdAttribute($value)
    {
        $daysids = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => "Jum'at",
            6 => 'Sabtu',
        ];
        return $daysids[$value] ?? 'Unknown';
    }
    public function getRawDaysIdAttribute()
    {
        return $this->attributes['days_id'] ?? null;
    }

    public function getMethIdAttribute($value)
    {
        $methids = [
            0 => 'Tatap Muka',
            1 => 'Teleconference',
        ];
        return $methids[$value] ?? 'Unknown';
    }
    public function getRawMethIdAttribute()
    {
        return $this->attributes['meth_id'] ?? null;
    }

    public function getPertIdAttribute($value)
    {
        $pertids = array_combine(range(1,16), array_map(fn($n) => "Pertemuan $n", range(1,16)));
        return $pertids[$value] ?? 'Unknown';
    }
    public function getRawPertIdAttribute()
    {
        return $this->attributes['pert_id'] ?? null;
    }

    // RELATIONSHIPS

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'matkul_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'jadwal_kuliah_kelas', 'jadwal_kuliah_id', 'kelas_id')->withTimestamps();
    }

    public function ruang()
    {
        return $this->belongsTo(Ruang::class, 'ruang_id');
    }

    public function jenisKelas()
    {
        return $this->belongsTo(JenisKelas::class, 'jenis_kelas_id');
    }

    public function waktuKuliah()
    {
        return $this->belongsToMany(WaktuKuliah::class, 'jadwal_kuliah_waktu', 'jadwal_kuliah_id', 'waktu_kuliah_id')->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(StudentTask::class, 'jadkul_id');
    }

    public function absensi()
    {
        return $this->hasMany(AbsensiMahasiswa::class, 'jadkul_code', 'code');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'jadwal_kuliah_id', 'id');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

        public function absensiStatus()
    {
        return $this->hasMany(\App\Models\AbsensiStatus::class, 'jadkul_code', 'code');
    }
    public function krs()
    {
        return $this->hasMany(\App\Models\Akademik\Krs::class, 'jadwal_id');
    }

}
