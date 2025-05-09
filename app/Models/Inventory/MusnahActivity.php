<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusnahActivity extends Model
{
    use HasFactory;

    protected $table = 'musnah_activity';

    protected $fillable = [
        'musnah_id',
        'dokumen_pemusnahan',
        'berita_acara',
        'detail_pemusnahan',
        'status',
        'changed_at',
    ];

    public function musnah(): BelongsTo
    {
        return $this->belongsTo(Musnah::class, 'musnah_id', 'id');
    }
}
