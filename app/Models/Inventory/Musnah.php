<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Musnah extends Model
{
    use HasFactory;

    protected $table = 'musnah';

    protected $fillable = [
        'no_pemusnahan',
        'users_id',
        'no_seri_id',
        'tgl_pemusnahan',
        'kondisi',
        'detail_pemusnahan',
        'status',
    ];

    public function noSeri() : BelongsTo
    {
        return $this->belongsTo(NoSeri::class, 'no_seri_id', 'id');
    }
}
