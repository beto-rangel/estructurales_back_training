<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use DB;

class DetalleFallas extends Model
{
    protected $table = 'detalle_fallas';

    protected $fillable = [
        '__IDAtm',
'Sitio',
'Division',
'TipoSitio',
'MarcaModelo',
'FallaTipo',
'FechaFallaInicio',
'FechaFallaFin',
'FallaDuracion',
'FallaImpacto',
'FallaTicket',
'Estado',
'Cr',
'ContribuyenteVcHabil',
'ContribuyenteSubcausaVcHabil',
'ImpactoVcHabil',
'ContribuyenteVncHabilVcSinAcceso',
'ContribuyenteSubcausaVncHabilVcSinAcceso',
'ImpactoVcSinAcceso',
'ImpactoVnc',
'ContribuyenteDiasInhabiles',
'ContribuyenteSubcausaDiasInhabiles',
'ImpactoDiasInhabilesVc',
'ImpactoDiasInhabilesVnc',
'VentanaInicio',
'VentanaFIn',
'Vc',
'Vnc',
'CodigoFalla',
'FauitId',
'Generacion',
'CreadoPor',
'Version',
'NumSerie',
'FuncionalidadAutoservicio',
'zg_InformacionAlFallas',
'MES',
'ANIO',
'user_name',
'created_at',
'updated_at'


    ];

}
