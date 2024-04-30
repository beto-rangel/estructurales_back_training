<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use DB;

class IndispoDispensadorMasReceptor extends Model
{
    protected $table = 'indispo_dispensador_mas_receptor';

    protected $fillable = [
        'atm',
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
