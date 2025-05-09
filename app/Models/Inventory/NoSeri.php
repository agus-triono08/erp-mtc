<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Layout;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'reject_reason',
        'status_perubahan',
        'alasan_penolakan_perubahan',
        'tgl_perubahan',
        'tgl_pengecekan',
        'deskripsi_cek',
    ];

    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    public function tools(): BelongsTo
    {
        return $this->belongsTo(Tools::class, 'tools_id', 'id');
    }

    public function perawatan()
    {
        return $this->hasMany(Perawatan::class);
    }

    public function peminjaman()
    {
        return $this->belongsToMany(Peminjaman::class, 'peminjaman_no_seri', 'no_seri_id', 'peminjaman_id')
            ->withPivot('created_at') // Jika kamu ingin mengakses data dari pivot
            ->withTimestamps();
    }

    public function permintaan()
    {
        return $this->belongsToMany(Permintaan::class, 'permintaan_no_seri', 'no_seri_id', 'permintaan_id')
            ->withPivot('created_at') // Jika kamu ingin mengakses data dari pivot
            ->withTimestamps();
    }

    public function logs() 
    {
        return $this->hasMany(NoSeriLog::class);
    }

    public function perbaikan()
    {
        return $this->hasMany(Error::class);
    }
}
