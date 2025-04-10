<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoSeri extends Model
{
    use HasFactory;

    protected $table = 'no_seri';

    protected $fillable = [
        'layout_id',
        'tools_id',
        'no_seri',
        'no_seri_default',
        'harga',
        'tanggal_masuk',
        'tanggal_kondisi',
        'kondisi',
        'kondisi_after',
    ];

    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    public function tools(): BelongsTo
    {
        return $this->belongsTo(Tools::class, 'tools_id', 'id');
    }
}
