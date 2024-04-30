<?php

namespace App\Imports;

use App\Entities\IndispoInicial;
use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IndispoInicialImport implements ToModel, WithHeadingRow
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

        if ($row['mantenimiento_tecnico']) {
            $n = $row['mantenimiento_tecnico'];
            $n = str_replace("%", "", $row['mantenimiento_tecnico']);
            $row['mantenimiento_tecnico'] = $n;
        }
        if ($row['recuperacion_manual']) {
            $n = $row['recuperacion_manual'];
            $n = str_replace("%", "", $row['recuperacion_manual']);
            $row['recuperacion_manual'] = $n;
        }

        if ($row['fecha_indisponibilidad']) {
            $n = $row['fecha_indisponibilidad'];

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
            
            $row['fecha_indisponibilidad'] = $n;
        }
    
        return \App\Entities\IndispoInicial::updateOrCreate([
            //Add unique field combo to match here
            //For example, perhaps you only want one entry per user:
            'atm_funcionalidad'    => $row['atm_funcionalidad'],
        ],[
            'atm'                    => $row['atm'] ?? null,
            'mantenimiento_tecnico'  => $row['mantenimiento_tecnico'] ?? null,
            'recuperacion_manual'    => $row['recuperacion_manual'] ?? null,
            'funcionalidad'          => $row['funcionalidad'] ?? null,
            'fecha_indisponibilidad' => $row['fecha_indisponibilidad'] ?? null,
            'user_name'              => Auth::user()->name,
            'created_at'             => $now,
            'updated_at'             => $now,
        ]);
    }
}
