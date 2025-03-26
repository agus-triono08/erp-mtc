<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    use HasFactory;

    protected $table = 'jenis'; // tambahkan atribut $table di sini

    protected $fillable = [
        'kode_jenis',
        'nama_jenis',
    ];
}
