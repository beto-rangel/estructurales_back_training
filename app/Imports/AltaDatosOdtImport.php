<?php

namespace App\Imports;

use App\Entities\AltaDatosODT;
use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AltaDatosOdtImport implements ToModel, WithHeadingRow
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

        if($row['atm']){
            $row['atm'] = str_pad($row['atm'], 4, "0", STR_PAD_LEFT);

            $quesemedira = DB::table('siga')
                        ->where('pk_autoservicios_id', '=', $row['atm'])
                        ->first();

            $row['d_tipo'] = $quesemedira->d_tipo;

            if($row['d_tipo'] == 'AUTOBANCO' || $row['d_tipo'] == 'DISPENSADOR'){
              $row['que_se_mide'] = 'DISPENSADOR';
            }

            if($row['d_tipo'] == 'PRACTDUAL' || $row['d_tipo'] == 'PRACTICAJA'){
              $row['que_se_mide'] = 'RECEPTOR';
            }

            if($row['d_tipo'] == 'RECICLADOR'){
              $row['que_se_mide'] = 'RECICLADOR';
            }

            if($row['d_tipo'] == 'NA'){
              $row['que_se_mide'] = '';
            }

        }

        if ($row['fecha_seleccion']) {
            $n = $row['fecha_seleccion'];

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
            
            $row['fecha_seleccion'] = $n;
        }

        if ($row['fecha_escalado_dar']) {
            $n = $row['fecha_escalado_dar'];

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
            
            $row['fecha_escalado_dar'] = $n;
        }

        if ($row['fecha_escalado_banca']) {
            $n = $row['fecha_escalado_banca'];

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
            
            $row['fecha_escalado_banca'] = $n;
        }

        if ($row['indispo_de_seleccion']) {
            $n = $row['indispo_de_seleccion'];
            $n = str_replace("%", "", $row['indispo_de_seleccion']);
            $row['indispo_de_seleccion'] = $n;
        }
        if ($row['total_transacciones']) {
            $n = $row['total_transacciones'];
            $n = str_replace(",", "", $row['total_transacciones']);
            $row['total_transacciones'] = $n;
        }
         $existe = DB::table('alta_datos_odt')
                ->where('atm', $row['atm'])
                ->whereNotIn('status', ['Completado'])
                ->where('fecha_seleccion', '>', DB::raw('DATE_ADD(NOW(), INTERVAL -61 DAY)'))
                ->get();

        //$existe = DB::table('alta_datos_odt')->where('atm', $row['atm'])->get();
        $existe = $existe->count();

        //DB::select("INSERT INTO `casos` (`atm`, `estatus`) VALUES ( LPAD('" . $row['atm']  . "',4,'0000'), 'NO')");

        if($existe == 0 ){

            DB::select("CALL insertaCaso(
                '" .$row['prioridad']. "', 
                '" .$row['ano_del_caso']. "', 
                '" .$row['mes_del_caso']. "', 
                '" .$row['grupo']. "', 
                '" .$row['atm']  . "' , 
                '" .$row['que_se_mide']. "' , 
                '" .$row['fecha_seleccion']. "' ,
                '" .$row['indispo_de_seleccion']. "' ,
                '" .$row['universo_mes_actual']. "',
                '" .$row['caso_inicial']. "' ,
                '" .$row['fecha_escalado_dar']. "' ,
                '" .$row['fecha_escalado_banca']. "' ,
                '" .Auth::user()->name. "' ,
                '" .$row['total_transacciones']. "',    
                '" .$row['no_odt']. "'                                     
            )");
        }
    }
}
