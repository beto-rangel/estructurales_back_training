<?php

namespace App\Imports;

use App\Entities\DetalleFallas;
use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DetalleFallasImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        /*return new Isman([
            //
        ]);*/

        $now = Carbon::now('America/Mexico_City');

        if ($row['fechafallainicio']) {
            $n = $row['fechafallainicio'];
            $pattern = "#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#";
            $validate = preg_match($pattern, $n);
            
            if($validate == 0){
                $n = null;
            }else{
                $ano = substr($n, 6, 4);
                $mes = substr($n, 3, 2);
                $dia = substr($n, 0, 2);

                $n = $ano . '-' . $mes . '-' . $dia;

                $n = Carbon::parse($n)->format('Y-m-d');
            }
            $row['fechafallainicio'] = $n;
        }

        if ($row['fechafallafin']) {
            $n = $row['fechafallafin'];
            $pattern = "#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#";
            $validate = preg_match($pattern, $n);
            
            if($validate == 0){
                $n = null;
            }else{
                $ano = substr($n, 6, 4);
                $mes = substr($n, 3, 2);
                $dia = substr($n, 0, 2);

                $n = $ano . '-' . $mes . '-' . $dia;

                $n = Carbon::parse($n)->format('Y-m-d');
            }
            $row['fechafallafin'] = $n;
        }

        if ($row['zg_informacionalfallas']) {
            $n = $row['zg_informacionalfallas'];
            $pattern = "#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#";
            $validate = preg_match($pattern, $n);
            
            if($validate == 0){
                $n = null;
            }else{
                $ano = substr($n, 6, 4);
                $mes = substr($n, 3, 2);
                $dia = substr($n, 0, 2);

                $n = $ano . '-' . $mes . '-' . $dia;

                $n = Carbon::parse($n)->format('Y-m-d');
            }
            $row['zg_informacionalfallas'] = $n;
        }

        \App\Entities\DetalleFallas::updateOrCreate([
            //Add unique field combo to match here
            //For example, perhaps you only want one entry per user:
            '__IDAtm'                                  => $row['idatm'],
            ],[
            'Sitio'                                    => $row['sitio'] ?? null,
            'Division'                                 => $row['division'] ?? null,
            'TipoSitio'                                => $row['tipositio'] ?? null,
            'MarcaModelo'                              => $row['marcamodelo'] ?? null,
            'FallaTipo'                                => $row['fallatipo'] ?? null,
            'FechaFallaInicio'                         => $row['fechafallainicio'] ?? null,
            'FechaFallaFin'                            => $row['fechafallafin'] ?? null,
            'FallaDuracion'                            => $row['falladuracion'] ?? null,
            'FallaImpacto'                             => $row['fallaimpacto'] ?? null,
            'FallaTicket'                              => $row['fallaticket'] ?? null,
            'Estado'                                   => $row['estado'] ?? null,
            'Cr'                                       => $row['cr'] ?? null,
            'ContribuyenteVcHabil'                     => $row['contribuyentevchabil'] ?? null,
            'ContribuyenteSubcausaVcHabil'             => $row['contribuyentesubcausavchabil'] ?? null,
            'ImpactoVcHabil'                           => $row['impactovchabil'] ?? null,
            'ContribuyenteVncHabilVcSinAcceso'         => $row['contribuyentevnchabilvcsinacceso'] ?? null,
            'ContribuyenteSubcausaVncHabilVcSinAcceso' => $row['contribuyentesubcausavnchabilvcsinacceso'] ?? null,
            'ImpactoVcSinAcceso'                       => $row['impactovcsinacceso'] ?? null,
            'ImpactoVnc'                               => $row['impactovnc'] ?? null,
            'ContribuyenteDiasInhabiles'               => $row['contribuyentediasinhabiles'] ?? null,
            'ContribuyenteSubcausaDiasInhabiles'       => $row['contribuyentesubcausadiasinhabiles'] ?? null,
            'ImpactoDiasInhabilesVc'                   => $row['impactodiasinhabilesvc'] ?? null,
            'ImpactoDiasInhabilesVnc'                  => $row['impactodiasinhabilesvnc'] ?? null,
            'VentanaInicio'                            => $row['ventanainicio'] ?? null,
            'VentanaFIn'                               => $row['ventanafin'] ?? null,
            'Vc'                                       => $row['vc'] ?? null,
            'Vnc'                                      => $row['vnc'] ?? null,
            'CodigoFalla'                              => $row['codigofalla'] ?? null,
            'FauitId'                                  => $row['fauitid'] ?? null,
            'Generacion'                               => $row['generacion'] ?? null,
            'CreadoPor'                                => $row['creadopor'] ?? null,
            'Version'                                  => $row['version'] ?? null,
            'NumSerie'                                 => $row['numserie'] ?? null,
            'FuncionalidadAutoservicio'                => $row['funcionalidadautoservicio'] ?? null,
            'zg_InformacionAlFallas'                   => $row['zg_informacionalfallas'] ?? null,
            'MES'                                      => $row['mes'] ?? null,
            'ANIO'                                     => $row['anio'] ?? null,
            
            'user_name'                                => Auth::user()->name,
            'created_at'                               => $now,
            'updated_at'                               => $now,
            ]);
    }
}
