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
        'waktu_perawatan',
        'jumlah_orang_perawatan',
        'note_perubahan_jadwal',
    ];

    public function jenis()
    {
        return $this->belongsTo(Jenis::class);
    }

    public function noSeri()
    {
        return $this->hasMany(NoSeri::class);
    }

    public function layout()
    {
        return $this->belongsTo(Layout::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function tipe()
    {
        return $this->belongsTo(Tipe::class);
    }

}
