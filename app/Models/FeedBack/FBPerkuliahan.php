<?php

namespace App\Models\FeedBack;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Akademik\JadwalKuliah;


class FBPerkuliahan extends Model
{
    use HasFactory;

    // Field yang bisa diisi mass-assignment
    protected $fillable = [
        'fb_users_code',
        'fb_jakul_code',
        'fb_code',
        'fb_score',
        'fb_reason',
    ];

    // Relasi ke jadwal kuliah
    public function jadwal()
    {
        return $this->belongsTo(JadwalKuliah::class, 'fb_jakul_code', 'code');
    }
}
