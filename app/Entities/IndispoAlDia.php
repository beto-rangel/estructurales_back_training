<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use DB;

class IndispoAlDia extends Model
{
    protected $table = 'indisp_al_dia';

    protected $fillable = [
        'ATM_FUNCIONALIDAD',
        'ATM',
        'MES_ANTERIOR_6',
        'MES_ANTERIOR_5',
        'MES_ANTERIOR_4',
        'MES_ANTERIOR_3',
        'MES_ANTERIOR_2',
        'MES_ANTERIOR_1',
        'IND_ACUM_ACTUAL',
        'DIA_1',
        'DIA_2',
        'DIA_3',
        'DIA_4',
        'DIA_5',
        'DIA_6',
        'DIA_7',
        'DIA_8',
        'DIA_9',
        'DIA_10',
        'DIA_11',
        'DIA_12',
        'DIA_13',
        'DIA_14',
        'DIA_15',
        'DIA_16',
        'DIA_17',
        'DIA_18',
        'DIA_19',
        'DIA_20',
        'DIA_21',
        'DIA_22',
        'DIA_23',
        'DIA_24',
        'DIA_25',
        'DIA_26',
        'DIA_27',
        'DIA_28',
        'DIA_29',
        'DIA_30',
        'DIA_31',
        'APLICATIVO',
        'COMUNICACIONES',
        'CAMBIO_PLANEADO',
        'INFRAESTRUCTURA_CENTRAL',
        'MANTENIMIENTO_TECNICO',
        'RA1_NO_COMUNICACIONES',
        'RECUPERACION_MANUAL',
        'SEGURIDAD',
        'SIN_EFECTIVO',
        'SUMINISTRO',
        'SUMINISTRO_EMPRESA',
        'SUMINSTRO_INMUEBLES',
        'SATURACION_DE_EFECTIVO',
        'FUNCIONALIDAD',
        'INDISPONIBILIDAD',
        'user_name',
        'created_at',
        'updated_at'
    ];

}
