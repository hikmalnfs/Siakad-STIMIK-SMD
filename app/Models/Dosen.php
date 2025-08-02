<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;
use App\Models\Akademik\MataKuliah;

class Dosen extends Authenticatable
{
    use HasFactory, SoftDeletes, HasLogAktivitas;

    protected $guarded = [];

    // Accessor untuk kolom 'type' (misal 0 atau 1)
    public function getTypeAttribute($value)
    {
        $types = [
            0 => 'Dosen Non-Aktif',
            1 => 'Dosen Aktif',
        ];

        return $types[$value] ?? 'Unknown';
    }

    // Accessor untuk kolom 'dsn_stat' yang bernilai string seperti 'aktif'/'nonaktif'
    public function getDsnStatAttribute($value)
    {
        $dsnstats = [
            'aktif' => 'Dosen Aktif',
            'nonaktif' => 'Dosen Tidak Aktif',
        ];

        $key = strtolower(trim($value));
        return $dsnstats[$key] ?? 'Unknown';
    }

    // Akses nilai asli kolom 'type' tanpa accessor
    public function getRawTypeAttribute()
    {
        return $this->attributes['type'];
    }

    // Akses nilai asli kolom 'dsn_stat' tanpa accessor
    public function getRawDsnStatAttribute()
    {
        return $this->attributes['dsn_stat'];
    }

    // Accessor untuk foto profil, default diarahkan ke gambar default
    public function getPhotoAttribute($value)
    {
        return $value === 'default.jpg'
            ? asset('storage/images/profile/default.jpg')
            : asset('storage/images/profile/' . $value);
    }

    // Accessor untuk nomor WA dengan format Indonesia (mengganti '0' awal dengan '62')
    public function getWaPhoneAttribute()
    {
        if (!empty($this->phone)) {
            return preg_replace('/^0/', '62', $this->phone);
        }

        return null;
    }

    // Prefix routing berdasarkan type (angka 0/1)
    public function getPrefixAttribute()
    {
        $prefixes = [
            0 => 'dosen-nonaktif.',
            1 => 'dosen.',
        ];

        return $prefixes[$this->attributes['type']] ?? 'unknown';
    }

    // RELASI Mahasiswa Bimbingan menggunakan kolom 'wali' sebagai foreign key
// Mahasiswa bimbingan (kolom wali_id di mahasiswas mengarah ke dosens.id)

    // Kelas yang diawali dosen sebagai wali (kolom wali_id di kelas mengarah ke dosens.id)
    public function waliKelas()
    {
        return $this->belongsTo(\App\Models\Akademik\Kelas::class, 'wali'); // FK di dosen
    }

    // Relasi mahasiswa bimbingan (wali dosen)
    public function mahasiswaBimbingan()
    {
        return $this->hasMany(\App\Models\Mahasiswa::class, 'wali_id');
    }

        public function matkuls()
    {
        return $this->hasMany(MataKuliah::class, 'dosen_id'); // atau relasi sesuai database kamu
    }


}
