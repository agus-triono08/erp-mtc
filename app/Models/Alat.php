<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_alat',
        'merek_alat',
        'gambar',
        'tanggal_masuk',
        'lokasi_penyimpanan',
        'kondisi',
        'status',
        'stok',
        'deskripsi',
        'kode_alat',
    ];
    
    public function rincianAlat()
    {
        return $this->hasMany(RincianAlat::class, 'kode_alat', 'kode_alat');
    }

    public function updateStok()
    {
        $this->stok = $this->rincianAlat()->sum('jumlah');
        $this->save();
    }
}
