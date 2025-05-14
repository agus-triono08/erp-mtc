<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HilangActivityBaru extends Model
{
    use HasFactory;

    protected $table = 'hilang_activity_baru';

    protected $fillable = [
        'hilang_id',
        'bukti_pertanggung_jawaban',
        'alasan_penolakan',
        'status',
        'changed_at',
    ];

    public function hilang(): BelongsTo
    {
        return $this->belongsTo(Hilang::class, 'hilang_id', 'id');
    }
}
