<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErrorActivity extends Model
{
    use HasFactory;
    
    protected $table = 'error_activity';

    protected $fillable = [
        'error_id',
        'waktu_mulai',
        'waktu_selesai',
        'pic',
        'detail_perbaikan',
        'kondisi',
        'changed_at',
    ];

    public function error(): BelongsTo
    {
        return $this->belongsTo(Error::class, 'error_id', 'id');
    }
}
