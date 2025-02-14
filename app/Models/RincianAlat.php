<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RincianAlat extends Model
{
    protected $fillable = ['kode_alat', 'brand', 'kode_rincian_alat', 'jumlah', 'kondisi', 'gambar'];

    public function alat()
    {
        return $this->belongsTo(Alat::class);
    }

    protected static function booted()
    {
        static::saved(function ($rincianAlat) {
            $rincianAlat->alat->updateStok();
        });

        static::deleted(function ($rincianAlat) {
            $rincianAlat->alat->updateStok();
        });
    }
}