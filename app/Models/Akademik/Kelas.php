<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;

use App\Models\Akademik\ProgramStudi;
use App\Models\Akademik\TahunAkademik;
use App\Models\Akademik\JenisKelas;
use App\Models\Mahasiswa;

class Kelas extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'kelas';

    // Gunakan guarded kosong agar mass assignment aman
    protected $guarded = [];

    /**
     * Relasi ke Tahun Akademik
     */
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'taka_id');
    }

    /**
     * Relasi ke Program Studi utama
     */
    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_id');
    }

    /**
     * Relasi ke Jenis Kelas
     */
    public function jenisKelas()
    {
        return $this->belongsTo(JenisKelas::class);
    }

    /**
     * Relasi ke Ketua kelas (Mahasiswa)
     */
    public function ketua()
    {
        return $this->belongsTo(Mahasiswa::class, 'ketua_id');
    }

    /**
     * Relasi ke Program Studi (alternatif, proku)
     */
    public function proku()
    {
        return $this->belongsTo(ProgramStudi::class, 'proku_id');
    }

    /**
     * Alias dari programStudi (bisa gunakan pstudi)
     */
    public function pstudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_id');
    }

    /**
     * Relasi ke banyak Mahasiswa
     */
    public function mahasiswas()
    {
        return $this->hasMany(\App\Models\Mahasiswa::class, 'kelas_id');
    }
        public function jadwalKuliahs()
    {
        return $this->belongsToMany(
            \App\Models\Akademik\JadwalKuliah::class,
            'jadwal_kuliah_kelas',
            'kelas_id',
            'jadwal_kuliah_id'
        )->withTimestamps();
        }
        public function mahasiswasMany()
    {
        return $this->belongsToMany(
            \App\Models\Mahasiswa::class,
            'kelas_mahasiswa',     // nama tabel pivot
            'kelas_id',            // foreign key di pivot mengacu ke kelas
            'mahasiswa_id'         // foreign key di pivot mengacu ke mahasiswa
        )->withTimestamps();
    }
    public function wali()
    {
        return $this->belongsTo(\App\Models\Dosen::class, 'wali_id', 'id');
    }

    }
