<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HilangActivityProses extends Model
{
    use HasFactory;

    protected $table = 'hilang_activity_proses';

    protected $fillable = [
        'hilang_id',
        'no_seri_old',
        'no_seri_new',
        'tgl_penggantian',
        'harga',
        'status',
        'changed_at',
    ];

    public function hilang(): BelongsTo
    {
        return $this->belongsTo(Hilang::class, 'hilang_id', 'id');
    }
}
