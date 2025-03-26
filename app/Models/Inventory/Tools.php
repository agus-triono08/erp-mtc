<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tools extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_id',
        'kode',
        'nama',
        'stok_awal',
        'stok_akhir',
        'unit',
        'harga_total',
        'pembelian',
        'sumber',
        'vendor',
        'fungsi',
        'deskripsi',
        'gambar',
        'jadwal_perawatan',
    ];

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(Jenis::class);
    }
}
