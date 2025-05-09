<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerubahanPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'perubahan_peminjaman';

    protected $fillable = [
        'no_perubahan',
        'peminjaman_id',
        'keterangan_perubahan',
        'alasan_penolakan',
        'status',
        'tgl_kembali',
    ];

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function noSeri()
    {
        return $this->peminjaman->noSeri;
    }

}
