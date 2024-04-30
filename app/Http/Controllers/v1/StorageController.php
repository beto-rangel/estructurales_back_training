<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;

use App\helpers\JsonResponse;
use App\Http\Requests;
use App\Http\Controllers\Controller;
//use Zipper;
use DB;

use Carbon\Carbon;

class StorageController extends Controller
{

    public function save(Request $request)
    {
        try{

            $name    = $request->input('name');

            $seccion         = $request->input('seccion');
            $namecarpeta_ano = $request->input('año');
            $namecarpeta_mes = $request->input('mes');

            $carpeta1 =  $seccion;

            $carpetaC =  $carpeta1;

            /*if (!file_exists('storage/'.$seccion)) {
                mkdir('storage/'.$seccion,  0777, true);
            }

            if (!file_exists('storage/'.$carpeta1)) {
                mkdir('storage/'.$carpeta1,  0777, true);
            }

            if (!file_exists('storage/'.$carpetaC)) {
                mkdir('storage/'.$carpetaC,  0777, true);
            }*/
            //obtenemos el campo file definido en el formulario
            $file = $request->file('file');

            if (!isset($name))
                $name = $file->getClientOriginalName();
            //obtenemos el nombre del archivo


            //indicamos que queremos guardar un nuevo archivo en el disco local
            \Storage::disk('local')->put($carpetaC.'/'.$name, \File::get($file));
            //\Storage::move('old/file1.jpg', 'new/file1.jpg');

            return JsonResponse::singleResponse(["message" => "El archivo ha sido guardado exitosamente"
            ], 200);
        }catch (\Exception $e)
        {
            return JsonResponse::errorResponse("Imposible guardar  el archivo", 500);
        }
    }

}