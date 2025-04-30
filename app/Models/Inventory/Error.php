<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Error extends Model
{
    use HasFactory;

    protected $table = 'error';

    protected $fillable = [
        'no_perbaikan',
        'users_id',
        'no_seri_id',
        'tgl_perbaikan',
        'kondisi',
        'detail_perbaikan',
        'status',
    ];

    public function noSeri() :BelongsTo 
    {
        return $this->belongsTo(NoSeri::class, 'no_seri_id', 'id');
    }
}
