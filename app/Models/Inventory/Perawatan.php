<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perawatan extends Model
{

    protected $table = 'perawatan';

    use HasFactory;

    protected $fillable = [
        'no_perawatan',
        'users_id',
        'no_seri_id',
        'status',
        'detail_perawatan',
        'tgl_perawatan',
        'tgl_mulai_perawatan',
        'tgl_selesai_perawatan',
        'waktu_perawatan',
        'waktu_mulai',
        'waktu_selesai',
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function noSeri()
    {
        return $this->belongsTo(NoSeri::class, 'no_seri_id');
    }
}
