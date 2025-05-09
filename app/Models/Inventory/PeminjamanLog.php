<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeminjamanLog extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_log';

    protected $fillable = [
        'peminjaman_id',
        'old_status',
        'new_status',
        'changed_at',
        'changed_by',
    ];

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }
}
