<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permintaan extends Model
{
    use HasFactory;

    protected $table = 'permintaan';

    protected $fillable = [
        'no_permintaan',
        'users_id',
        'tools_id',
        'tgl_permintaan',
        'alasan_penolakan',
        'detail_permintaan',
        'total',
        'status',
        'status_kondisi',
    ];

    public function tools() : BelongsTo
    {
        return $this->belongsTo(Tools::class, 'tools_id', 'id');
    }

    public function noSeri()
    {
        return $this->belongsToMany(NoSeri::class, 'permintaan_no_seri', 'permintaan_id', 'no_seri_id')
            ->withPivot('created_at') // Jika kamu ingin mengakses data dari pivot
            ->withTimestamps();
    }

    public function logs()
    {
        return $this->hasMany(PermintaanLog::class);
    }
}
