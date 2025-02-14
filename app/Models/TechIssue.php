<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechIssue extends Model
{
    use HasFactory;
    protected $fillable = [
            'email',
            'nama',
            'jabatan',
            'bagian',
            'jenis_sistem',
            'jenis_permintaan',
            'keterangan_permasalahan',
            'lampiran',
            'waktu_kebutuhan',
    ];
}
