<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriMerek extends Model
{
    use HasFactory;

    protected $table = 'kategori_merek'; // tambahkan atribut $table di sini

    protected $fillable = [
        'merek_id',
        'kategori_id',
    ];

    public function merek(): BelongsTo
    {
        return $this->belongsTo(Merek::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }
}
