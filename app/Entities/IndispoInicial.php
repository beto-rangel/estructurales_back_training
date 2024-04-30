<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use DB;

class IndispoInicial extends Model
{
    protected $table = 'indispo_inicial';

    protected $fillable = [
        'atm_funcionalidad',
        'atm',
        'mantenimiento_tecnico',
        'recuperacion_manual',
        'funcionalidad',
        'fecha_indisponibilidad',
        'user_name',
        'created_at',
        'updated_at'
    ];

}
