<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rusak extends Model
{
    use HasFactory;

    protected $table = 'rusak';

    protected $fillable = [
        'no_kerusakan',
        'users_id',
        'no_seri_id',
        'tgl_kerusakan',
        'kondisi',
        'detail_kerusakan',
        'status',
    ];

    public function noSeri() : BelongsTo
    {
        return $this->belongsTo(NoSeri::class, 'no_seri_id', 'id');
    }
}
