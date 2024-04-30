<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use DB;

class IndispoSucursalCr extends Model
{
    protected $table = 'indispo_sucursal_cr';

    protected $fillable = [
        'cr',
        'dispensador',
        'receptor',
        'reciclador',
        'dispensador_receptor_suma',
        'dispensador_receptor_promedio',
        'user_name',
        'created_at',
        'updated_at'
    ];

}
