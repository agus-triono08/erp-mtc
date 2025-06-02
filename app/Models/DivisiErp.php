<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivisiErp extends Model
{
    use HasFactory;
    protected $connection = 'erp';
    protected $table = 'divisi';

}
