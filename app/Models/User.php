<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;
    protected $connection = 'erp';
    protected $fillable = [
        'divisi_id',
        'jabatan_id',
        'username',
        'password',
        'nama',
        'foto',
        'status',
        'url',
    ];

    protected $hidden = [
        'password',
    ];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(DivisiErp::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function Karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
    
}
