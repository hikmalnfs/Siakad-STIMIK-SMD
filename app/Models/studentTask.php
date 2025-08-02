<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTask extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function jadkul()
    {
        return $this->belongsTo(\App\Models\Akademik\JadwalKuliah::class, 'jadkul_id');
    }

    public function dosen()
    {
        return $this->belongsTo(\App\Models\Dosen::class, 'dosen_id');
    }

    public function scores()
    {
        return $this->hasMany(StudentScore::class, 'stask_id');
    }
}
