<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hilang extends Model
{
    use HasFactory;

    protected $table = 'hilang';

    protected $fillable = [
        'no_kehilangan',
        'users_id',
        'no_seri_id',
        'tgl_kehilangan',
        'kondisi',
        'detail_hilang',
        'status',
    ];

    public function noSeri() : BelongsTo
    {
        return $this->belongsTo(NoSeri::class, 'no_seri_id', 'id');
    }
}
