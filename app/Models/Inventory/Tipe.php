<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tipe extends Model
{
    use HasFactory;

    protected $table = 'tipe'; // tambahkan atribut $table di sini

    protected $fillable = [
        'kategori_merek_id',
        'kode_tipe',
        'nama_tipe',
    ];

    public function kategorimerek(): BelongsTo
    {
        return $this->belongsTo(KategoriMerek::class, 'kategori_merek_id', 'id');
    }

    // public function merek() {
    //     return $this->hasOneThrough(Merek::class, KategoriMerek::class, 'merek_id', 'id');
    // }

    // public function kategori()
    // {
    //     return $this->hasOneThrough(Kategori::class, KategoriMerek::class, 'kategori_id', 'id');
    // }
}
