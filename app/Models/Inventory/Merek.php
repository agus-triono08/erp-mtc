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
}
