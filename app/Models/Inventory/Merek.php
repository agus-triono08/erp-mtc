<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merek extends Model
{
    use HasFactory;

    protected $table = 'merek'; // tambahkan atribut $table di sini

    protected $fillable = [
        'kode_merek',
        'nama_merek',
    ];

    // public function kategoriMerek()
    // {
    //     return $this->hasMany(KategoriMerek::class);
    // }

    public function kategori()
    {
        return $this->belongsToMany(Kategori::class, 'kategori_merek');
    }

    public function tipe()
    {
        return $this->hasManyThrough(Tipe::class, KategoriMerek::class, 'merek_id', 'kategori_merek_id');
    }

}
