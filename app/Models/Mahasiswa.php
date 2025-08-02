<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\HasLogAktivitas;
use App\Models\Akademik\Kelas;
use App\Models\Akademik\TahunAkademik;
use App\Models\Akademik\ProgramStudi;
use App\Models\Dosen;

class Mahasiswa extends Authenticatable
{
    use HasFactory, SoftDeletes, HasLogAktivitas;

    protected $table = 'mahasiswas';
    protected $guarded = [];

    // Accessor type mahasiswa
    public function getTypeAttribute($value)
    {
        $types = [
            0 => 'Calon Mahasiswa Baru',
            1 => 'Mahasiswa Aktif',
            2 => 'Mahasiswa Non-Aktif',
            3 => 'Mahasiswa Alumni',
        ];
        return $types[$value] ?? 'Unknown';
    }

    // Accessor photo default
    public function getPhotoAttribute($value)
    {
        return $value == 'default.jpg'
            ? asset('storage/images/profile/default.jpg')
            : asset('storage/images/profile/' . $value);
    }

    public function getRawTypeAttribute()
    {
        return $this->attributes['type'] ?? null;
    }

    // Nomor WA (62...)
    public function getWaPhoneAttribute()
    {
        if ($this->phone) {
            return preg_replace('/^0/', '62', $this->phone);
        }
        return null;
    }

    // Prefix route
    public function getPrefixAttribute()
    {
        $prefixes = [
            0 => 'camaba.',
            1 => 'mahasiswa.',
            2 => 'mahasiswa-nonaktif.',
            3 => 'alumni.',
        ];
        return $prefixes[$this->attributes['type']] ?? 'unknown';
    }

    // Status mahasiswa
    public function getMhsStatAttribute($value)
    {
        $mhsstats = [
            0 => 'Calon Mahasiswa',
            1 => 'Mahasiswa Aktif',
            2 => 'Mahasiswa Non-Aktif',
            3 => 'Mahasiswa Alumni',
        ];
        return $mhsstats[$value] ?? 'Unknown';
    }

    public function getRawMhsStatAttribute()
    {
        return $this->attributes['mhs_stat'] ?? null;
    }

    // Agama
    public function getAgamaAttribute($value)
    {
        $mhsrelis = [
            0 => 'Belum Memilih',
            1 => 'Agama Islam',
            2 => 'Agama Kristen Katholik',
            3 => 'Agama Kristen Protestan',
            4 => 'Agama Hindu',
            5 => 'Agama Buddha',
            6 => 'Agama Konghuchu',
            7 => 'Kepercayaan Lainnya',
        ];
        return $mhsrelis[$value] ?? 'Unknown';
    }

    public function getRawMhsReliAttribute()
    {
        return $this->attributes['mhs_reli'] ?? null;
    }

    public function getMhsReliTextAttribute()
    {
        $value = $this->attributes['mhs_reli'] ?? null;
        $mhsrelis = [
            1 => 'Agama Islam',
            2 => 'Agama Kristen Katholik',
            3 => 'Agama Kristen Protestan',
            4 => 'Agama Hindu',
            5 => 'Agama Buddha',
            6 => 'Agama Konghuchu',
            7 => 'Kepercayaan Lainnya',
        ];
        return $mhsrelis[$value] ?? 'Unknown';
    }

    // Nomor telepon dengan kode negara
    public function getMhsPhoneAttribute($value)
    {
        if (strpos($value, '0') === 0) {
            return '62' . substr($value, 1);
        }
        return $value;
    }

    // Relasi kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi tahun akademik
    public function taka()
    {
        return $this->belongsTo(TahunAkademik::class, 'taka_id');
    }

    // Relasi KRS
    public function krs()
    {
        return $this->hasMany(\App\Models\Akademik\Krs::class, 'users_id', 'id',);
    }

    // Relasi program studi
    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'pstudi_id', 'id');
    }

        // Relasi dosen wali via kelas.wali ke dosens.id
    public function dosenWali()
    {
        return $this->belongsTo(Dosen::class, 'wali_id', 'id');
    }

    public function kelasMany()
    {
        return $this->belongsToMany(
            \App\Models\Akademik\Kelas::class,
            'kelas_mahasiswa',     // nama tabel pivot
            'mahasiswa_id',        // foreign key di pivot mengacu ke mahasiswa
            'kelas_id'             // foreign key di pivot mengacu ke kelas
        )->withTimestamps();
    }
    // Mahasiswa.php
    public function prodi()
    {
        return $this->belongsTo(\App\Models\Akademik\ProgramStudi::class, 'prodi_id');
    }
        public function khs()
    {
        return $this->hasMany(\App\Models\Akademik\Khs::class, 'mahasiswa_id');
    }

}
