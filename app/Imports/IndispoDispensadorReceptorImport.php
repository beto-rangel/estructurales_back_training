<?php

namespace App\Imports;

use App\Entities\IndispoDispensadorMasReceptor;
use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IndispoDispensadorReceptorImport implements ToModel, WithHeadingRow
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

        if ($row['atm']) {
            $n = $row['atm'];
            $n = str_replace("%", "", $row['atm']);
            $row['atm'] = $n;
        }
        if ($row['dispensador']) {
            $n = $row['dispensador'];
            $n = str_replace("%", "", $row['dispensador']);
            $row['dispensador'] = $n;
        }
        if ($row['receptor']) {
            $n = $row['receptor'];
            $n = str_replace("%", "", $row['receptor']);
            $row['receptor'] = $n;
        }
        if ($row['reciclador']) {
            $n = $row['reciclador'];
            $n = str_replace("%", "", $row['reciclador']);
            $row['reciclador'] = $n;
        }
        if ($row['dispensador_receptor_suma']) {
            $n = $row['dispensador_receptor_suma'];
            $n = str_replace("%", "", $row['dispensador_receptor_suma']);
            $row['dispensador_receptor_suma'] = $n;
        }
        if ($row['dispensador_receptor_promedio']) {
            $n = $row['dispensador_receptor_promedio'];
            $n = str_replace("%", "", $row['dispensador_receptor_promedio']);
            $row['dispensador_receptor_promedio'] = $n;
        }
    
        return \App\Entities\IndispoDispensadorMasReceptor::updateOrCreate([
            //Add unique field combo to match here
            //For example, perhaps you only want one entry per user:
            'atm'    => $row['atm'],
        ],[
            'dispensador'                   => $row['dispensador'] ?? null,
            'receptor'                      => $row['receptor'] ?? null,
            'reciclador'                    => $row['reciclador'] ?? null,
            'dispensador_receptor_suma'     => $row['dispensador_receptor_suma'] ?? null,
            'dispensador_receptor_promedio' => $row['dispensador_receptor_promedio'] ?? null,
            'user_name'                     => Auth::user()->name,
            'created_at'                    => $now,
            'updated_at'                    => $now,
        ]); 
    }
}
