<?php

namespace App\Imports;

use App\Entities\IndispoAlDia;
use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IndispoAlDiaImport implements ToModel, WithHeadingRow
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

        if ($row['atm_funcionalidad']) {
            $n = $row['atm_funcionalidad'];
            $n = str_replace("%", "", $row['atm_funcionalidad']);
            $row['ATM_FUNCIONALIDAD'] = $n;
        }
        
        if ($row['mes_anterior_6']) {
            $n = $row['mes_anterior_6'];
            $n = str_replace("%", "", $row['mes_anterior_6']);
            $row['MES_ANTERIOR_6'] = $n;
        }
        if ($row['mes_anterior_5']) {
            $n = $row['mes_anterior_5'];
            $n = str_replace("%", "", $row['mes_anterior_5']);
            $row['MES_ANTERIOR_5'] = $n;
        }
        if ($row['mes_anterior_4']) {
            $n = $row['mes_anterior_4'];
            $n = str_replace("%", "", $row['mes_anterior_4']);
            $row['MES_ANTERIOR_4'] = $n;
        }
        if ($row['mes_anterior_3']) {
            $n = $row['mes_anterior_3'];
            $n = str_replace("%", "", $row['mes_anterior_3']);
            $row['MES_ANTERIOR_3'] = $n;
        }
        if ($row['mes_anterior_2']) {
            $n = $row['mes_anterior_2'];
            $n = str_replace("%", "", $row['mes_anterior_2']);
            $row['MES_ANTERIOR_2'] = $n;
        }
        if ($row['mes_anterior_1']) {
            $n = $row['mes_anterior_1'];
            $n = str_replace("%", "", $row['mes_anterior_1']);
            $row['MES_ANTERIOR_1'] = $n;
        }
        if ($row['ind_acum_actual']) {
            $n = $row['ind_acum_actual'];
            $n = str_replace("%", "", $row['ind_acum_actual']);
            $row['IND_ACUM_ACTUAL'] = $n;
        }
        if ($row['dia_1']) {
            $n = $row['dia_1'];
            $n = str_replace("%", "", $row['dia_1']);
            $row['DIA_1'] = $n;
        }
        if ($row['dia_2']){
            $n = $row['dia_2'];
            $n = str_replace("%", "", $row['dia_2']);
            $row['DIA_2'] = $n;
        }
        if ($row['dia_3']){
            $n = $row['dia_3'];
            $n = str_replace("%", "", $row['dia_3']);
            $row['DIA_3'] = $n;
        }
        if ($row['dia_4']){
            $n = $row['dia_4'];
            $n = str_replace("%", "", $row['dia_4']);
            $row['DIA_4'] = $n;
        }
        if ($row['dia_5']){
            $n = $row['dia_5'];
            $n = str_replace("%", "", $row['dia_5']);
            $row['DIA_5'] = $n;
        }
        if ($row['dia_6']){
            $n = $row['dia_6'];
            $n = str_replace("%", "", $row['dia_6']);
            $row['DIA_6'] = $n;
        }
        if ($row['dia_7']){
            $n = $row['dia_7'];
            $n = str_replace("%", "", $row['dia_7']);
            $row['DIA_7'] = $n;
        }
        if ($row['dia_8']){
            $n = $row['dia_8'];
            $n = str_replace("%", "", $row['dia_8']);
            $row['DIA_8'] = $n;
        }
        if ($row['dia_9']){
            $n = $row['dia_9'];
            $n = str_replace("%", "", $row['dia_9']);
            $row['DIA_9'] = $n;
        }
        if ($row['dia_10']){
            $trim_n = $row['dia_10'];
            $n = str_replace("%", "", $row['dia_10']);
            $row['DIA_10'] = $n;

        }
        if ($row['dia_11']){
            $trim_n = $row['dia_11'];
            $n = str_replace("%", "", $row['dia_11']);
            $row['DIA_11'] = $n;
        }
        if ($row['dia_12']){
            $trim_n = $row['dia_12'];
            $n = str_replace("%", "", $row['dia_12']);
            $row['DIA_12'] = $n;
        }
        if ($row['dia_13']){
            $n = $row['dia_13'];
            $n = str_replace("%", "", $row['dia_13']);
            $row['DIA_13'] = $n;
        }
        if ($row['dia_14']){
            $n = $row['dia_14'];
            $n = str_replace("%", "", $row['dia_14']);
            $row['DIA_14'] = $n;
        }
        if ($row['dia_15']){
            $n = $row['dia_15'];
            $n = str_replace("%", "", $row['dia_15']);
            $row['DIA_15'] = $n;
        }
        if ($row['dia_16']){
            $n = $row['dia_16'];
            $n = str_replace("%", "", $row['dia_16']);
            $row['DIA_16'] = $n;
        }
        if ($row['dia_17']){
            $n = $row['dia_17'];
            $n = str_replace("%", "", $row['dia_17']);
            $row['DIA_17'] = $n;
        }
        if ($row['dia_18']){
            $n = $row['dia_18'];
            $n = str_replace("%", "", $row['dia_18']);
            $row['DIA_18'] = $n;
        }
        if ($row['dia_19']){
            $n = $row['dia_19'];
            $n = str_replace("%", "", $row['dia_19']);
            $row['DIA_19'] = $n;
        }
        if ($row['dia_20']){
            $n = $row['dia_20'];
            $n = str_replace("%", "", $row['dia_20']);
            $row['DIA_20'] = $n;
        }
        if ($row['dia_21']){
            $n = $row['dia_21'];
            $n = str_replace("%", "", $row['dia_21']);
            $row['DIA_21'] = $n;
        }
        if ($row['dia_22']){
            $n = $row['dia_22'];
            $n = str_replace("%", "", $row['dia_22']);
            $row['DIA_22'] = $n;
        }
        if ($row['dia_23']){
            $n = $row['dia_23'];
            $n = str_replace("%", "", $row['dia_23']);
            $row['DIA_23'] = $n;
        }
        if ($row['dia_24']){
            $n = $row['dia_24'];
            $n = str_replace("%", "", $row['dia_24']);
            $row['DIA_24'] = $n;
        }
        if ($row['dia_25']){
            $n = $row['dia_25'];
            $n = str_replace("%", "", $row['dia_25']);
            $row['DIA_25'] = $n;
        }
        if ($row['dia_26']){
            $n = $row['dia_26'];
            $n = str_replace("%", "", $row['dia_26']);
            $row['DIA_26'] = $n;
        }
        if ($row['dia_27']){
            $n = $row['dia_27'];
            $n = str_replace("%", "", $row['dia_27']);
            $row['DIA_27'] = $n;
        }
        if ($row['dia_28']){
            $n = $row['dia_28'];
            $n = str_replace("%", "", $row['dia_28']);
            $row['DIA_28'] = $n;
        }
        if ($row['dia_29']){
            $n = $row['dia_29'];
            $n = str_replace("%", "", $row['dia_29']);
            $row['DIA_29'] = $n;
        }
        if ($row['dia_30']){
            $n = $row['dia_30'];
            $n = str_replace("%", "", $row['dia_30']);
            $row['DIA_30'] = $n;
        }
        if ($row['dia_31']){
            $n = $row['dia_31'];
            $n = str_replace("%", "", $row['dia_31']);
            $row['DIA_31'] = $n;
        }
        if ($row['aplicativo']){
            $n = $row['aplicativo'];
            $n = str_replace("%", "", $row['aplicativo']);
            $row['APLICATIVO'] = $n;
        }
        if ($row['comunicaciones']){
            $n = $row['comunicaciones'];
            $n = str_replace("%", "", $row['comunicaciones']);
            $row['COMUNICACIONES'] = $n;
        }
        if ($row['cambio_planeado']){
            $n = $row['cambio_planeado'];
            $n = str_replace("%", "", $row['cambio_planeado']);
            $row['CAMBIO_PLANEADO'] = $n;
        }
        if ($row['infraestructura_central']){
            $n = $row['infraestructura_central'];
            $n = str_replace("%", "", $row['infraestructura_central']);
            $row['INFRAESTRUCTURA_CENTRAL'] = $n;
        }
        if ($row['mantenimiento_tecnico']){
            $n = $row['mantenimiento_tecnico'];
            $n = str_replace("%", "", $row['mantenimiento_tecnico']);
            $row['MANTENIMIENTO_TECNICO'] = $n;
        }
        if ($row['ra1_no_comunicaciones']){
            $n = $row['ra1_no_comunicaciones'];
            $n = str_replace("%", "", $row['ra1_no_comunicaciones']);
            $row['RA1_NO_COMUNICACIONES'] = $n;
        }
        if ($row['recuperacion_manual']){
            $n = $row['recuperacion_manual'];
            $n = str_replace("%", "", $row['recuperacion_manual']);
            $row['RECUPERACION_MANUAL'] = $n;
        }
        if ($row['seguridad']){
            $n = $row['seguridad'];
            $n = str_replace("%", "", $row['seguridad']);
            $row['SEGURIDAD'] = $n;
        }
        if ($row['sin_efectivo']){
            $n = $row['sin_efectivo'];
            $n = str_replace("%", "", $row['sin_efectivo']);
            $row['SIN_EFECTIVO'] = $n;
        }
        if ($row['suministro']){
            $n = $row['suministro'];
            $n = str_replace("%", "", $row['suministro']);
            $row['SUMINISTRO'] = $n;
        }
        if ($row['suministro_empresa']){
            $n = $row['suministro_empresa'];
            $n = str_replace("%", "", $row['suministro_empresa']);
            $row['SUMINISTRO_EMPRESA'] = $n;
        }
        if ($row['suminstro_inmuebles']){
            $n = $row['suminstro_inmuebles'];
            $n = str_replace("%", "", $row['suminstro_inmuebles']);
            $row['SUMINSTRO_INMUEBLES'] = $n;
        }
        if ($row['saturacion_de_efectivo']){
            $n = $row['saturacion_de_efectivo'];
            $n = str_replace("%", "", $row['saturacion_de_efectivo']);
            $row['SATURACION_DE_EFECTIVO'] = $n;
        }
        if ($row['funcionalidad']){
            $n = $row['funcionalidad'];
            $n = str_replace("%", "", $row['funcionalidad']);
            $row['FUNCIONALIDAD'] = $n;
        }
        if ($row['atm']){
            $n = $row['atm'];
            $n = str_replace("%", "", $row['atm']);
            $row['ATM'] = $n;
        }
        if ($row['indisponibilidad']) {
            $n = $row['indisponibilidad'];

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
            
            $row['INDISPONIBILIDAD'] = $n;
        }
    
        return \App\Entities\IndispoAlDia::updateOrCreate([
            //Add unique field combo to match here
            //For example, perhaps you only want one entry per user:
            'ATM_FUNCIONALIDAD'    => $row['ATM_FUNCIONALIDAD'],
        ],[
            'ATM'                     => $row['ATM'] ?? null,
            'MES_ANTERIOR_6'          => $row['MES_ANTERIOR_6'] ?? null,
            'MES_ANTERIOR_5'          => $row['MES_ANTERIOR_5'] ?? null,
            'MES_ANTERIOR_4'          => $row['MES_ANTERIOR_4'] ?? null,
            'MES_ANTERIOR_3'          => $row['MES_ANTERIOR_3'] ?? null,
            'MES_ANTERIOR_2'          => $row['MES_ANTERIOR_2'] ?? null,
            'MES_ANTERIOR_1'          => $row['MES_ANTERIOR_1'] ?? null,
            'IND_ACUM_ACTUAL'         => $row['IND_ACUM_ACTUAL'] ?? null,
            'DIA_1'                   => $row['DIA_1'] ?? null,
            'DIA_2'                   => $row['DIA_2'] ?? null,
            'DIA_3'                   => $row['DIA_3'] ?? null,
            'DIA_4'                   => $row['DIA_4'] ?? null,
            'DIA_5'                   => $row['DIA_5'] ?? null,
            'DIA_6'                   => $row['DIA_6'] ?? null,
            'DIA_7'                   => $row['DIA_7'] ?? null,
            'DIA_8'                   => $row['DIA_8'] ?? null,
            'DIA_9'                   => $row['DIA_9'] ?? null,
            'DIA_10'                  => $row['DIA_10'] ?? null,
            'DIA_11'                  => $row['DIA_11'] ?? null,
            'DIA_12'                  => $row['DIA_12'] ?? null,
            'DIA_13'                  => $row['DIA_13'] ?? null,
            'DIA_14'                  => $row['DIA_14'] ?? null,
            'DIA_15'                  => $row['DIA_15'] ?? null,
            'DIA_16'                  => $row['DIA_16'] ?? null,
            'DIA_17'                  => $row['DIA_17'] ?? null,
            'DIA_18'                  => $row['DIA_18'] ?? null,
            'DIA_19'                  => $row['DIA_19'] ?? null,
            'DIA_20'                  => $row['DIA_20'] ?? null,
            'DIA_21'                  => $row['DIA_21'] ?? null,
            'DIA_22'                  => $row['DIA_22'] ?? null,
            'DIA_23'                  => $row['DIA_23'] ?? null,
            'DIA_24'                  => $row['DIA_24'] ?? null,
            'DIA_25'                  => $row['DIA_25'] ?? null,
            'DIA_26'                  => $row['DIA_26'] ?? null,
            'DIA_27'                  => $row['DIA_27'] ?? null,
            'DIA_28'                  => $row['DIA_28'] ?? null,
            'DIA_29'                  => $row['DIA_29'] ?? null,
            'DIA_30'                  => $row['DIA_30'] ?? null,
            'DIA_31'                  => $row['DIA_31'] ?? null,
            'APLICATIVO'              => $row['APLICATIVO'] ?? null,
            'COMUNICACIONES'          => $row['COMUNICACIONES'] ?? null,
            'CAMBIO_PLANEADO'         => $row['CAMBIO_PLANEADO'] ?? null,
            'INFRAESTRUCTURA_CENTRAL' => $row['INFRAESTRUCTURA_CENTRAL'] ?? null,
            'MANTENIMIENTO_TECNICO'   => $row['MANTENIMIENTO_TECNICO'] ?? null,
            'RA1_NO_COMUNICACIONES'   => $row['RA1_NO_COMUNICACIONES'] ?? null,
            'RECUPERACION_MANUAL'     => $row['RECUPERACION_MANUAL'] ?? null,
            'SEGURIDAD'               => $row['SEGURIDAD'] ?? null,
            'SIN_EFECTIVO'            => $row['SIN_EFECTIVO'] ?? null,
            'SUMINISTRO'              => $row['SUMINISTRO'] ?? null,
            'SUMINISTRO_EMPRESA'      => $row['SUMINISTRO_EMPRESA'] ?? null,
            'SUMINSTRO_INMUEBLES'     => $row['SUMINSTRO_INMUEBLES'] ?? null,
            'SATURACION_DE_EFECTIVO'  => $row['SATURACION_DE_EFECTIVO'] ?? null,
            'FUNCIONALIDAD'           => $row['FUNCIONALIDAD'] ?? null,
            'INDISPONIBILIDAD'        => $row['INDISPONIBILIDAD'] ?? null,
            'user_name'               => Auth::user()->name,
            'created_at'              => $now,
            'updated_at'              => $now,
        ]); 
    }
}
