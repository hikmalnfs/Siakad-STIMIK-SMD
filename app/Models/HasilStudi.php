<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilStudi extends Model
{
    use HasFactory;

    // Jika nama tabel sudah sesuai konvensi Laravel (hasil_studis), property ini bisa dihilangkan
    protected $table = 'hasil_studis';

    // Gunakan fillable untuk mencegah mass assignment
    protected $fillable = [
        'krs_id',
        'nilai_angka',
        'nilai_huruf',
        'nilai_index',
        'desc',
    ];

    // Relasi ke model Krs - pastikan sudah import namespace App\Models\Akademik\Krs;
    public function krs()
    {
        return $this->belongsTo(\App\Models\Akademik\Krs::class, 'krs_id');
    }
}
