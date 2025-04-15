<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Layout;


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

    // public function jenis()
    // {
    //     return $this->belongsTo(Jenis::class);
    // }

    // public function kategori()
    // {
    //     return $this->hasOneThrough(Kategori::class, Jenis::class, 'id', 'jenis_id', 'jenis_id', 'id');
    // }

    // public function kategoriMerek()
    // {
    //     return $this->hasOneThrough(KategoriMerek::class, Kategori::class, 'id', 'kategori_id', 'kategori_id', 'id');
    // }

    // public function merek()
    // {
    //     return $this->hasOneThrough(Merek::class, KategoriMerek::class, 'id', 'id', 'kategori_merek_id', 'merek_id');
    // }

    // public function tipe()
    // {
    //     return $this->hasOneThrough(Tipe::class, KategoriMerek::class, 'id', 'kategori_merek_id', 'kategori_merek_id', 'id');
    // }

    public function jenis()
    {
        return $this->belongsTo(Jenis::class);
    }

    // public function kategori()
    // {
    //     return $this->belongsTo(Kategori::class);
    // }

    // public function merek()
    // {
    //     return $this->belongsTo(Merek::class);
    // }

    // public function tipe()
    // {
    //     return $this->belongsTo(Tipe::class);
    // }

    public function noSeri()
    {
        return $this->hasMany(NoSeri::class);
    }

    public function layout()
    {
        return $this->belongsTo(Layout::class);
    }

}
