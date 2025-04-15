<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
    use HasFactory;

    protected $fillable = ['ruang', 'rak', 'lantai', 'koordinat'];

    public function noSeri()
    {
        return $this->hasMany(Tools::class);
    }
}
