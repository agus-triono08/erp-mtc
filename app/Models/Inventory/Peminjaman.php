<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = [
        'no_peminjaman',
        'users_id',
        'tools_id',
        'tgl_pinjam',
        'tgl_kembali',
        'detail_peminjaman',
        'alasan_penolakan',
        'deskripsi_cek',
        'tgl_cek',
        'total',
        'status',
        'status_kondisi',
    ];

    public function tools() : BelongsTo
    {
        return $this->belongsTo(Tools::class, 'tools_id', 'id');
    }

    public function users() : BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function noSeri()
    {
        return $this->belongsToMany(NoSeri::class, 'peminjaman_no_seri', 'peminjaman_id', 'no_seri_id')
            ->withPivot('created_at') // Jika kamu ingin mengakses data dari pivot
            ->withTimestamps();
    }

    public function logs()
    {
        return $this->hasMany(PeminjamanLog::class);
    }

    public function perubahan()
    {
        return $this->hasMany(PerubahanPeminjaman::class);
    }

}
