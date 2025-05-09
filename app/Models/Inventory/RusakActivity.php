<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RusakActivity extends Model
{
    use HasFactory;

    protected $table = 'rusak_activity';

    protected $fillable = [
        'rusak_id',
        'waktu_mulai',
        'waktu_selesai',
        'pic',
        'detail_kerusakan',
        'kondisi',
        'status',
        'alasan_penolakan',
        'catatan',
        'changed_at',
    ];

    public function rusak(): BelongsTo
    {
        return $this->belongsTo(Rusak::class, 'rusak_id', 'id');
    }
}
