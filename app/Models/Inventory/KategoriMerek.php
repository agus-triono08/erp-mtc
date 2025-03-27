<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KategoriMerek extends Model
{
    use HasFactory;

    protected $table = 'kategori_merek'; // tambahkan atribut $table di sini

    protected $fillable = [
        'merek_id',
        'kategori_id',
    ];

    public function tipe()
    {
        return $this->hasOne(Tipe::class, 'kategori_merek_id');
    }

    public function merek(): BelongsTo
    {
        return $this->belongsTo(Merek::class, 'merek_id', 'id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'id');
    }
}
