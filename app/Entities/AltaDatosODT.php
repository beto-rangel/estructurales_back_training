<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use DB;

class AltaDatosODT extends Model
{
    protected $table = 'alta_datos_odt';

    protected $fillable = [
        'atm',
        'que_se_mide',
        'fecha_seleccion',
        'indispo_de_seleccion',
        'total_transacciones',
        'universo_mes_actual',
        'caso_inicial',
        'fecha_escalado_dar',
        'fecha_escalado_banca',
        'user_name',
        'created_at',
        'updated_at'
    ];

}
