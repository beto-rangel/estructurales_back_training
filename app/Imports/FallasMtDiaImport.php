<?php

namespace App\Imports;

use App\Entities\FallasMtAlDia;
use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FallasMtDiaImport implements ToModel, WithHeadingRow
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
        
       if ($row['dia_10']) {
            $trim_n = $row['dia_10'];
            $n = str_replace("#", "", $row['dia_10']);
            $row['dia_10'] = $n;

        }
        if ($row['dia_11']) {
            $trim_n = $row['dia_11'];
            $n = str_replace("#", "", $row['dia_11']);
            $row['dia_11'] = $n;
        }
        if ($row['dia_12']) {
            $trim_n = $row['dia_12'];
            $n = str_replace("#", "", $row['dia_12']);
            $row['dia_12'] = $n;
        }
    
        return \App\Entities\FallasMtDia::updateOrCreate([
            //Add unique field combo to match here
            //For example, perhaps you only want one entry per user:
            'atm'        => $row['atm'],
            ],[
            'atm'        => $row['atm'] ?? null,
            'dia_1'      => $row['dia_1'] ?? null,
            'dia_2'      => $row['dia_2'] ?? null,
            'dia_3'      => $row['dia_3'] ?? null,
            'dia_4'      => $row['dia_4'] ?? null,
            'dia_5'      => $row['dia_5'] ?? null,
            'dia_6'      => $row['dia_6'] ?? null,
            'dia_7'      => $row['dia_7'] ?? null,
            'dia_8'      => $row['dia_8'] ?? null,
            'dia_9'      => $row['dia_9'] ?? null,
            'dia_10'     => $row['dia_10'] ?? null,
            'dia_11'     => $row['dia_11'] ?? null,
            'dia_12'     => $row['dia_12'] ?? null,
            'dia_13'     => $row['dia_13'] ?? null,
            'dia_14'     => $row['dia_14'] ?? null,
            'dia_15'     => $row['dia_15'] ?? null,
            'dia_16'     => $row['dia_16'] ?? null,
            'dia_17'     => $row['dia_17'] ?? null,
            'dia_18'     => $row['dia_18'] ?? null,
            'dia_19'     => $row['dia_19'] ?? null,
            'dia_20'     => $row['dia_20'] ?? null,
            'dia_21'     => $row['dia_21'] ?? null,
            'dia_22'     => $row['dia_22'] ?? null,
            'dia_23'     => $row['dia_23'] ?? null,
            'dia_24'     => $row['dia_24'] ?? null,
            'dia_25'     => $row['dia_25'] ?? null,
            'dia_26'     => $row['dia_26'] ?? null,
            'dia_27'     => $row['dia_27'] ?? null,
            'dia_28'     => $row['dia_28'] ?? null,
            'dia_29'     => $row['dia_29'] ?? null,
            'dia_30'     => $row['dia_30'] ?? null,
            'dia_31'     => $row['dia_31'] ?? null,
            'user_name'  => Auth::user()->name,
            'created_at' => $now,
            'updated_at' => $now,
        ]); 
    }
}
