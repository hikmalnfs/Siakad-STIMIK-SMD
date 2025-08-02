<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mahasiswa;
use App\Models\Keuangan\TagihanKuliah;

class HistoryTagihan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users()
    {
        return $this->belongsTo(Mahasiswa::class, 'users_id');
    }

    public function tagihan()
    {
        return $this->belongsTo(TagihanKuliah::class, 'tagihan_code', 'code');
    }

    public function getPriceAttribute($value)
    {
        return str_replace(['Rp.', ' ', '.'], '', $value);
    }

    public function getRawPriceAttribute()
    {
        return $this->attributes['price'];
    }
}
