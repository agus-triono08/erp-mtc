<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Karyawan;


class Perawatan extends Model
{

    protected $connection = 'mysql'; // Tambahkan ini
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

    // public function Karyawan() {
    //     return $this->belongsTo(Karyawan::class);
    // }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'erp_mtc.perawatan_user', 'perawatan_id', 'user_id')
                    ->withTimestamps();
    }

    // public function users()
    // {
    //     return $this->belongsTo(user::class, 'pic', 'id');
    // }

    public function noSeri()
    {
        return $this->belongsTo(NoSeri::class, 'no_seri_id');
    }
}
