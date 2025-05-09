<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanLog extends Model
{
    use HasFactory;

    protected $table = 'permintaan_log';

    protected $fillable = [
        'permintaan_id',
        'old_status',
        'new_status',
        'changed_at',
        'changed_by',
    ];

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class);
    }
}
