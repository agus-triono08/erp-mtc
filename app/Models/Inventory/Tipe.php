<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsTo(KategoriMerek::class);
    }
}
