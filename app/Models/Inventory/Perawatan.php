<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


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
        'pic',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'perawatan_user')
                    ->withTimestamps();
    }

    public function noSeri()
    {
        return $this->belongsTo(NoSeri::class, 'no_seri_id');
    }
}
