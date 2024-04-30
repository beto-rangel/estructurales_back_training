<?php

namespace App\Http\Controllers\v1;
use App\helpers\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Traits\JWTTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Eloquent\InternalEventRepository as Internal;
use Carbon\Carbon;
use Mail;
use Excel;

class PlaneacionController extends Controller
{
    use JWTTrait;

    protected $internal;

    public function __construct(Internal $internal)
    {
        $this->internal = $internal;
    }

    public function getTipoIngenieria(){

        $data = DB::table('tipo_ingenierias')->get();
        
        return JsonResponse::singleResponse(["message" => "Info encontrada" , 
          "Data" => $data, 
        ]);
    }

    public function getNombreByIdcAndDivision($idc, $division, $tipo_de_ingenieria){

        $data = DB::table('nombres_idc')->where('idc', $idc)->where('division', $division)->where('tipo_de_ingenieria', $tipo_de_ingenieria)->get();
        
        return JsonResponse::singleResponse(["message" => "Info encontrada" , 
          "Data" => $data, 
        ]);
    }

    public function getInterventorByDivision($division){

        $data = DB::table('nombres_interventores')->where('division', $division)->get();
        
        return JsonResponse::singleResponse(["message" => "Info encontrada" , 
          "Data" => $data, 
        ]);
    }

    public function getEmpresaByDivision($division){

        $data = DB::table('empresas_inmuebles')->where('division', $division)->get();
        
        return JsonResponse::singleResponse(["message" => "Info encontrada" , 
          "Data" => $data, 
        ]);
    }

    public function getPlaneacionById($planeacion_id){

        $data = DB::table('planeacion')->where('id', $planeacion_id)->get();
        
        return JsonResponse::singleResponse(["message" => "Info encontrada" , 
          "Data" => $data, 
        ]);
    }

    public function createPlaneacion(Request $request){

        $atm                      = array_get($request, 'atm');
        $que_se_mide              = array_get($request, 'que_se_mide');
        $fecha_de_planeacion      = array_get($request, 'fecha_de_planeacion');
        $idc_fecha_solicitud_cita = array_get($request, 'idc_fecha_solicitud_cita');
        $fecha_de_cita            = array_get($request, 'fecha_de_cita');
        $alta_datos_odt_id        = array_get($request, 'alta_datos_odt_id');
        $now                      = Carbon::now('America/Mexico_City');

        if($idc_fecha_solicitud_cita == null || $fecha_de_cita == null){
            $status = 'Planeacion Pendiente';

            $data_id = DB::table('planeacion')->insertGetId(array(
                'atm'                      => $atm,
                'responsable_planeacion'   => Auth::user()->name,
                'fecha_de_planeacion'      => $fecha_de_planeacion,
                'idc_fecha_solicitud_cita' => $idc_fecha_solicitud_cita,
                'fecha_de_cita'            => $fecha_de_cita,
                'user_name'                => Auth::user()->name,
                'status'                   => $status,
                'created_at'               => $now,
                'updated_at'               => $now,
            ));

            DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
                'status'                   => $status,
                'planeacion_id'            => $data_id,
                'updated_at'               => $now,
            ));
        }else{
           $data_id = DB::table('planeacion')->insertGetId(array(
                'atm'                      => $atm,
                'responsable_planeacion'   => Auth::user()->name,
                'fecha_de_planeacion'      => $fecha_de_planeacion,
                'idc_fecha_solicitud_cita' => $idc_fecha_solicitud_cita,
                'fecha_de_cita'            => $fecha_de_cita,
                'user_name'                => Auth::user()->name,
                'status'                   => 'Analisis',
                'created_at'               => $now,
                'updated_at'               => $now,
            ));

            DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
                'status'                   => 'Analisis',
                'planeacion_id'            => $data_id,
                'updated_at'               => $now,
            )); 

            $existe = DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->whereIn('universo_mes_actual', ['MANTENIMIENTO PREVENTIVO ANUAL', 'MANTENIMIENTO TRANSACCIONALIDAD','MANTENIMIENTO ACLARACIONES','MANTENIMIENTO PREDICTIVO','MANTENIMIENTO PREVENTIVO NOTAS ACUMULADAS'])->first();

            if($existe != null || $existe != ''){
                $universo_mes_actual=$existe->universo_mes_actual;
                $analisis_id = DB::table('analisis')->insertGetId(array(
                    //'atm'                                      => $atm,
                    'nombre_responsable'                       => Auth::user()->name,
                    'nombre_analista'                          => Auth::user()->name,
                    'fecha_de_analisis'                        => $now,
                    'aplican_acciones_de_idc'                  => 'SI' ,
                    'acciones_a_realizar_idc'                  => $universo_mes_actual,
                    'mant_modulo_dispensador'                  => 'SI',
                    'cambio_presentador_stacker'               => 'NO',
                    'cambio_cosumibles'                        => 'NO',
                    'cambio_pick_picker_estractor'             => 'NO',
                    'cambio_caseteros'                         => 'NO',
                    'otro_1_dispensador'                       => 'NO',
                    'mant_modulo_idc_cpu'                      => 'SI',
                    'cambio_dd'                                => 'NO',
                    'cambio_cpu'                               => 'NO',
                    'mant_modulo_lectora'                      => 'SI',
                    'cambio_lectora'                           => 'NO',
                    'mant_modulo_impresora'                    => 'SI',
                    'cambio_impresora'                         => 'NO',
                    'fuente_de_poder'                          => 'NO',
                    'teclado_teclado_lateral_touch_screen'     => 'NO',
                    'hopper'                                   => 'NO',
                    'monitor_pantalla'                         => 'NO',
                    'fascia'                                   => 'NO',
                    'se_requiere_planchado_de_sw'              => 'NO',
                    'aplican_acciones_de_inmuebles'            => 'NO',
                    'aplican_acciones_de_cableado'             => 'NO',
                    'aplican_acciones_de_comunicaciones'       => 'NO',
                    'aplica_la_asistencia_de_un_interventor'   => 'NO',
                    'status'                                   => 'Planeacion',
                    'atm'                                      => $existe->atm,
                    'tipo_de_visita'                           => 'VISITA_IDC_ETV',
                    'user_name'                                => Auth::user()->name,
                    'created_at'                               => $now,
                    'updated_at'                               => $now,
                ));

                DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
                    'status'                   => 'Planeacion',
                    'analisis_id'              => $analisis_id,
                    'updated_at'               => $now,
                ));          

            }

        }

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha creado un nuevo item en la tabla planeacion',
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info insertada" , 
          "Data" => $data_id, 
        ]);
    }

    public function updatePlaneacionPendiente(Request $request, $planeacion_id){

        $atm                      = array_get($request, 'atm');
        $que_se_mide              = array_get($request, 'que_se_mide');
        $fecha_de_planeacion      = array_get($request, 'fecha_de_planeacion');
        $idc_fecha_solicitud_cita = array_get($request, 'idc_fecha_solicitud_cita');
        $fecha_de_cita            = array_get($request, 'fecha_de_cita');
        $alta_datos_odt_id        = array_get($request, 'alta_datos_odt_id');
        $alta_datos_odt_id        = $alta_datos_odt_id * 1;
        $now                      = Carbon::now('America/Mexico_City');

        if($idc_fecha_solicitud_cita == null || $fecha_de_cita == null){
            $status = 'Planeacion Pendiente';

            $data_id = DB::table('planeacion')->where('id', $planeacion_id)->update(array(
                'atm'                      => $atm,
                'responsable_planeacion'   => Auth::user()->name,
                'fecha_de_planeacion'      => $fecha_de_planeacion,
                'idc_fecha_solicitud_cita' => $idc_fecha_solicitud_cita,
                'fecha_de_cita'            => $fecha_de_cita,
                'user_name'                => Auth::user()->name,
                'status'                   => $status,
                'updated_at'               => $now,
            ));

            DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
                'status'                   => $status,
                //'planeacion_id'            => $planeacion_id,
                'updated_at'               => $now,
            ));
        }else{
            $data_id = DB::table('planeacion')->where('id', $planeacion_id)->update(array(
                'atm'                      => $atm,
                'responsable_planeacion'   => Auth::user()->name,
                'fecha_de_planeacion'      => $fecha_de_planeacion,
                'idc_fecha_solicitud_cita' => $idc_fecha_solicitud_cita,
                'fecha_de_cita'            => $fecha_de_cita,
                'user_name'                => Auth::user()->name,
                'status'                   => 'Analisis',
                'updated_at'               => $now,
            ));

            DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
                'status'                   => 'Analisis',
                //'planeacion_id'            => $planeacion_id,
                'updated_at'               => $now,
            )); 


            $existe = DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->where('universo_mes_actual', 'MANTENIMIENTO PREVENTIVO ANUAL')->first();

            if($existe != null || $existe != ''){
                $analisis_id = DB::table('analisis')->insertGetId(array(
                    'atm'                                      => $atm,
                    'nombre_responsable'                       => Auth::user()->name,
                    'nombre_analista'                          => Auth::user()->name,
                    'fecha_de_analisis'                        => $now,
                    'aplican_acciones_de_idc'                  => 'SI' ,
                    'acciones_a_realizar_idc'                  => 'MANTENIMIENTO PREVENTIVO ANUAL',
                    'mant_modulo_dispensador'                  => 'SI',
                    'cambio_presentador_stacker'               => 'NO',
                    'cambio_cosumibles'                        => 'NO',
                    'cambio_pick_picker_estractor'             => 'NO',
                    'cambio_caseteros'                         => 'NO',
                    'otro_1_dispensador'                       => 'NO',
                    'mant_modulo_idc_cpu'                      => 'SI',
                    'cambio_dd'                                => 'NO',
                    'cambio_cpu'                               => 'NO',
                    'mant_modulo_lectora'                      => 'SI',
                    'cambio_lectora'                           => 'NO',
                    'mant_modulo_impresora'                    => 'SI',
                    'cambio_impresora'                         => 'NO',
                    'fuente_de_poder'                          => 'NO',
                    'teclado_teclado_lateral_touch_screen'     => 'NO',
                    'hopper'                                   => 'NO',
                    'monitor_pantalla'                         => 'NO',
                    'fascia'                                   => 'NO',
                    'se_requiere_planchado_de_sw'              => 'NO',
                    'aplican_acciones_de_inmuebles'            => 'NO',
                    'aplican_acciones_de_cableado'             => 'NO',
                    'aplican_acciones_de_comunicaciones'       => 'NO',
                    'aplica_la_asistencia_de_un_interventor'   => 'NO',
                    'status'                                   => 'Planeacion',
                    'user_name'                                => Auth::user()->name,
                    'created_at'                               => $now,
                    'updated_at'                               => $now,
                ));

                DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
                    'status'                   => 'Planeacion',
                    'analisis_id'              => $analisis_id,
                    'updated_at'               => $now,
                ));          

            }
   
        }

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha actualizado un nuevo item en la tabla planeacion con id ' . $planeacion_id ,
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info insertada" , 
          "Data" => $data_id, 
        ]);
    }

    public function updatePlaneacion(Request $request, $planeacion_id){

        $atm                                   = array_get($request, 'atm');
        $que_se_mide                           = array_get($request, 'que_se_mide');
        $responsable_planeacion                = array_get($request, 'responsable_planeacion');
        $fecha_de_planeacion                   = array_get($request, 'fecha_de_planeacion');
        $idc_fecha_solicitud_cita              = array_get($request, 'idc_fecha_solicitud_cita');
        $idc_fecha_confirmacion_cita           = array_get($request, 'idc_fecha_confirmacion_cita');
        $tipo_de_ingenieria                    = array_get($request, 'tipo_de_ingenieria');
        $nombres_del_personal_a_asistir_de_idc = array_get($request, 'nombres_del_personal_a_asistir_de_idc');
        $etv_fecha_solicitud_cita              = array_get($request, 'etv_fecha_solicitud_cita');
        $etv_fecha_confirmacion_cita           = array_get($request, 'etv_fecha_confirmacion_cita');
        $empresa_de_inmuebles                  = array_get($request, 'empresa_de_inmuebles');
        $personal_de_inmuebles                 = array_get($request, 'personal_de_inmuebles');
        $empresa_de_cableado                   = array_get($request, 'empresa_de_cableado');
        $personal_de_cableado                  = array_get($request, 'personal_de_cableado');
        $empresa_de_comunicaciones             = array_get($request, 'empresa_de_comunicaciones');
        $personal_de_comunicaciones            = array_get($request, 'personal_de_comunicaciones');
        $nombre_interventor                    = array_get($request, 'nombre_interventor');
        $fecha_de_cita                         = array_get($request, 'fecha_de_cita');
        $ticket_ob                             = array_get($request, 'ticket_ob');
        $ticket_remedy                         = array_get($request, 'ticket_remedy');
        $tas_idc                               = array_get($request, 'tas_idc');
        $tas_etv                               = array_get($request, 'tas_etv');
        $tas_seguimiento                       = array_get($request, 'tas_seguimiento');
        $tas_seguimiento2                      = array_get($request, 'tas_seguimiento2');
        $tas_seguimiento3                      = array_get($request, 'tas_seguimiento3');
        $tas_seguimiento4                      = array_get($request, 'tas_seguimiento4');
        $folio_inmuebles                       = array_get($request, 'folio_inmuebles');
        $alta_datos_odt_id                     = array_get($request, 'alta_datos_odt_id');
        $alta_datos_odt_id                     = $alta_datos_odt_id * 1;
        $now                                   = Carbon::now('America/Mexico_City');

        $aplican_acciones_idc               = array_get($request, 'aplican_acciones_idc');
        $aplican_acciones_de_inmuebles      = array_get($request, 'aplican_acciones_de_inmuebles');
        $aplican_acciones_de_cableado       = array_get($request, 'aplican_acciones_de_cableado');
        $aplican_acciones_de_comunicaciones = array_get($request, 'aplican_acciones_de_comunicaciones');
        $interventor                        = array_get($request, 'interventor');
        $local                              = array_get($request, 'local');

        /*if($aplican_acciones_idc == "SI"){
            if($idc_fecha_confirmacion_cita  && $tipo_de_ingenieria && $nombres_del_personal_a_asistir_de_idc && $etv_fecha_solicitud_cita && $etv_fecha_confirmacion_cita 
                && $fecha_de_cita && $ticket_ob && $ticket_remedy && $tas_idc){
                $global = true;
            }else{
                $global = false;
            }   
        }else{
            $global = true;
        }*/

        if($aplican_acciones_idc == "SI"){
            if($idc_fecha_confirmacion_cita  && $tipo_de_ingenieria && $nombres_del_personal_a_asistir_de_idc && $fecha_de_cita && $ticket_ob && $ticket_remedy && $tas_idc){
                $global = true;
            }else{
                $global = false;
            }   
        }else{
            $global = true;
        } 

        if($aplican_acciones_de_inmuebles == "SI"){
            if( $empresa_de_inmuebles && $personal_de_inmuebles && $folio_inmuebles ){
                $inmuebles = true;
            }else{  
                $inmuebles = false; 
            }
        }else{
           $inmuebles = true;
        }

        if($aplican_acciones_de_cableado == "SI"){
            if( $empresa_de_cableado && $personal_de_cableado ){
                $cableado = true;
            }else{  
                $cableado = false; 
            }
        }else{
           $cableado = true;
        }

        if($aplican_acciones_de_comunicaciones == "SI"){
            if( $empresa_de_comunicaciones && $personal_de_comunicaciones ){
                $comunicaciones = true;
            }else{  
                $comunicaciones = false; 
            }
        }else{
           $comunicaciones = true;
        }

        if($interventor == 'SI, CON ALCANCE' || $interventor == 'SI, FUERA DE ALCANCE' ){
            if($nombre_interventor){
                $int = true;
            }else{
                $int = false;
            }
        }else{
            $int = true;
        }

        /*if($local == 'REM' ){
            if($tas_etv && $tas_seguimiento){
                $local_local = true;
            }else{
                $local_local = false;
            }
        }else{
            $local_local = true;
        }*/

        if ($global && $inmuebles && $cableado && $comunicaciones && $int){
            $status = 'Gestion';
        }else{
            $status = 'Planeacion Pendiente Fin';
        }

        $data_id = DB::table('planeacion')->where('id', $planeacion_id)->update(array(
            //'atm'                                   => $atm,
            //'responsable_planeacion'                => $responsable_planeacion,
            //'fecha_de_planeacion'                   => $fecha_de_planeacion,
            //'idc_fecha_solicitud_cita'              => $idc_fecha_solicitud_cita,
            'idc_fecha_confirmacion_cita'           => $idc_fecha_confirmacion_cita,
            'tipo_de_ingenieria'                    => $tipo_de_ingenieria,
            'nombres_del_personal_a_asistir_de_idc' => $nombres_del_personal_a_asistir_de_idc,
            'etv_fecha_solicitud_cita'              => $etv_fecha_solicitud_cita,
            'etv_fecha_confirmacion_cita'           => $etv_fecha_confirmacion_cita,
            'empresa_de_inmuebles'                  => $empresa_de_inmuebles,
            'personal_de_inmuebles'                 => $personal_de_inmuebles,
            'empresa_de_cableado'                   => $empresa_de_cableado,
            'personal_de_cableado'                  => $personal_de_cableado,
            'empresa_de_comunicaciones'             => $empresa_de_comunicaciones,
            'personal_de_comunicaciones'            => $personal_de_comunicaciones,
            'nombre_interventor'                    => $nombre_interventor,
            'fecha_de_cita'                         => $fecha_de_cita,
            'ticket_ob'                             => $ticket_ob,
            'ticket_remedy'                         => $ticket_remedy,
            'tas_idc'                               => $tas_idc,
            'tas_etv'                               => $tas_etv,
            'tas_seguimiento'                       => $tas_seguimiento,
            'tas_seguimiento2'                      => $tas_seguimiento2,
            'tas_seguimiento3'                      => $tas_seguimiento3,
            'tas_seguimiento4'                      => $tas_seguimiento4,
            'folio_inmuebles'                       => $folio_inmuebles,
            'user_name'                             => Auth::user()->name,
            'status'                                => $status,
            'created_at'                            => $now,
            'updated_at'                            => $now,
        ));

        DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
            'status'                   => $status,
            //'planeacion_id'            => $planeacion_id,
            'updated_at'               => $now,
        ));


        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha actualizado un nuevo item en la tabla planeacion con id ' . $planeacion_id ,
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info insertada" , 
          "Data" => $data_id, 
        ]);
    }

    public function downloadReportePlaneacion($fecha_inicio, $fecha_fin){

        $now = Carbon::now('America/Mexico_City');

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha descargado un excel de la tabla planeacion',
            'created_at'    => $now,
            'updated_at'    => $now
        ));

      //aqui empieza el ciclo

        $data = DB::table('planeacion')
                    ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
                    ->get();
        
        $tot_record_found = 0;
        if ($data != null || $data != '') {
            $tot_record_found = 1;

            $CsvData = array('ATM, RESPONSABLE PLANEACION, FECHA DE PLANEACION, IDC FECHA SOLICITUD CITA, IDC FECHA CONFIRMACION CITA, TIPO DE INGENIERIA, NOMBRES DEL PERSONAL A ASISTIR DE IDC, ETV FECHA SOLICITUD CITA, ETV FECHA CONFIRMACION CITA, EMPRESA DE INMUEBLES, PERSONAL DE INMUEBLES, EMPRESA DE CABLEADO, PERSONAL DE CABLEADO, EMPRESA DE COMUNICACIONES, PERSONAL DE COMUNICACIONES, FECHA DE CITA, TICKET OB, TICKET SERVICE NOW, TAS IDC, TAS ETV, TAS SEGUIMIENTO, TAS SEGUIMIENTO2, TAS SEGUIMIENTO3, TAS SEGUIMIENTO4, FOLIO INMUEBLES, NOMBRE DE INTERVENTOR, USUARIO, FECHA DE CREACION');
            foreach ($data as $value) {

                $atm                                   = str_replace(',', '', $value->atm);
                $responsable_planeacion                = str_replace(',', '', $value->responsable_planeacion);
                $fecha_de_planeacion                   = str_replace(',', '', $value->fecha_de_planeacion);
                $idc_fecha_solicitud_cita              = str_replace(',', '', $value->idc_fecha_solicitud_cita);
                $idc_fecha_confirmacion_cita           = str_replace(',', '', $value->idc_fecha_confirmacion_cita);
                $tipo_de_ingenieria                    = str_replace(',', '', $value->tipo_de_ingenieria);
                $nombres_del_personal_a_asistir_de_idc = str_replace(',', ' ', $value->nombres_del_personal_a_asistir_de_idc);
                $etv_fecha_solicitud_cita              = str_replace(',', '', $value->etv_fecha_solicitud_cita);
                $etv_fecha_confirmacion_cita           = str_replace(',', '', $value->etv_fecha_confirmacion_cita);
                $empresa_de_inmuebles                  = str_replace(',', '', $value->empresa_de_inmuebles);
                $personal_de_inmuebles                 = str_replace(',', '', $value->personal_de_inmuebles);
                $empresa_de_cableado                   = str_replace(',', '', $value->empresa_de_cableado);
                $personal_de_cableado                  = str_replace(',', '', $value->personal_de_cableado);
                $empresa_de_comunicaciones             = str_replace(',', '', $value->empresa_de_comunicaciones);
                $personal_de_comunicaciones            = str_replace(',', '', $value->personal_de_comunicaciones);
                $fecha_de_cita                         = str_replace(',', '', $value->fecha_de_cita);
                $ticket_ob                             = str_replace(',', '', $value->ticket_ob);
                $ticket_remedy                         = str_replace(',', '', $value->ticket_remedy);
                $tas_idc                               = str_replace(',', '', $value->tas_idc);
                $tas_etv                               = str_replace(',', '', $value->tas_etv);
                $tas_seguimiento                       = str_replace(',', '', $value->tas_seguimiento);
                $tas_seguimiento2                       = str_replace(',', '', $value->tas_seguimiento2);
                $tas_seguimiento3                       = str_replace(',', '', $value->tas_seguimiento3);
                $tas_seguimiento4                       = str_replace(',', '', $value->tas_seguimiento4);
                $folio_inmuebles                       = str_replace(',', '', $value->folio_inmuebles);
                $nombre_interventor                    = str_replace(',', '', $value->nombre_interventor);
                $user_name                             = str_replace(',', '', $value->user_name);
                $created_at                            = str_replace(',', '', $value->created_at);

                $CsvData[] = $atm . ',' . $responsable_planeacion . ',' . $fecha_de_planeacion . ',' . $idc_fecha_solicitud_cita . ',' . $idc_fecha_confirmacion_cita . ',' . $tipo_de_ingenieria . ',' . $nombres_del_personal_a_asistir_de_idc . ',' . $etv_fecha_solicitud_cita . ',' . $etv_fecha_confirmacion_cita . ',' . $empresa_de_inmuebles . ',' . $personal_de_inmuebles . ',' . $empresa_de_cableado . ',' . $personal_de_cableado . ',' . $empresa_de_comunicaciones . ',' . $personal_de_comunicaciones . ',' . $fecha_de_cita . ',' . $ticket_ob . ',' . $ticket_remedy . ',' . $tas_idc . ',' . $tas_etv . ',' . $tas_seguimiento  . ',' . $tas_seguimiento2  . ',' . $tas_seguimiento3  . ',' . $tas_seguimiento4 . ',' . $folio_inmuebles . ',' . $nombre_interventor  . ',' . $user_name . ',' . $created_at;
            }

            $filename = date('Y-m-d') . ".csv";
            $file_path = base_path() . '/' . $filename;
            $file = fopen($file_path, "w+");
            foreach ($CsvData as $exp_data) {
                fputcsv($file, explode(',', $exp_data));
            }
            fclose($file);

            $headers = ['Content-Encoding'    => 'UTF-8',
                        'Content-Type'        => 'application/csv; charset=UTF-8'
                        ];

            return response()->download($file_path, $filename, $headers);
        }

      // hasta aqui acaba hacia arriba

      //return view('download', ['record_found' => $tot_record_found]);

    }

    public function updateFechaCita(Request $request, $planeacion_id){

        $fecha_de_cita = array_get($request, 'fecha_de_cita');
        $now           = Carbon::now('America/Mexico_City');

        DB::table('planeacion')->where('id', $planeacion_id)->update(array(
            'fecha_de_cita'            => $fecha_de_cita,
            'user_name'                => Auth::user()->name,
            'updated_at'               => $now,
        ));

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha actualizado fecha de inicio item en la tabla planeacion con id ' . $planeacion_id ,
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info insertada" , 
        ]);
    }

    public function updateTicketSeguimiento(Request $request, $planeacion_id){

        $ticket_ob       = array_get($request, 'ticket_ob');
        $ticket_remedy   = array_get($request, 'ticket_remedy');
        $tas_idc         = array_get($request, 'tas_idc');
        $tas_etv         = array_get($request, 'tas_etv');
        $tas_seguimiento = array_get($request, 'tas_seguimiento');
        $tas_seguimiento2 = array_get($request, 'tas_seguimiento2');
        $tas_seguimiento3 = array_get($request, 'tas_seguimiento3');
        $tas_seguimiento4 = array_get($request, 'tas_seguimiento4');
        $folio_inmuebles = array_get($request, 'folio_inmuebles');
        $now             = Carbon::now('America/Mexico_City');

        DB::table('planeacion')->where('id', $planeacion_id)->update(array(
            'ticket_ob'       => $ticket_ob,
            'ticket_remedy'   => $ticket_remedy,
            'tas_idc'         => $tas_idc,
            'tas_etv'         => $tas_etv,
            'tas_seguimiento' => $tas_seguimiento,
            'tas_seguimiento2' => $tas_seguimiento2,
            'tas_seguimiento3' => $tas_seguimiento3,
            'tas_seguimiento4' => $tas_seguimiento4,
            'folio_inmuebles' => $folio_inmuebles,
            'user_name'       => Auth::user()->name,
            'updated_at'      => $now,
        ));

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha actualizado ticket de seguimiento en la tabla planeacion con id ' . $planeacion_id ,
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info insertada" , 
        ]);
    }
}