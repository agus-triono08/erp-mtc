<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori'; // tambahkan atribut $table di sini

    protected $fillable = [
        'jenis_id',
        'kode_kategori',
        'nama_kategori',
    ];

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(Jenis::class);
    }
}
