<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoSeriLog extends Model
{
    use HasFactory;

    protected $table = 'no_seri_log';

    protected $fillable = [
        'no_seri_id',
        'old_kondisi',
        'new_kondisi',
        'changed_at',
        'changed_by',
    ];

    public function noSeri(): BelongsTo
    {
        return $this->belongsTo(NoSeri::class);
    }

}
