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

class AnalisisController extends Controller
{
    use JWTTrait;

    protected $internal;

    public function __construct(Internal $internal)
    {
        $this->internal = $internal;
    }

    public function createAnalisis(Request $request){

        $atm                                      = array_get($request, 'atm');
        $que_se_mide                              = array_get($request, 'que_se_mide');
        $responsable_atm                          = array_get($request, 'responsable_atm');
        $nombre_analista                          = array_get($request, 'nombre_analista');
        $fecha_de_analisis                        = array_get($request, 'fecha_de_analisis');
        $aplican_acciones_de_idc                  = array_get($request, 'aplican_acciones_de_idc');
        $acciones_a_realizar_idc                  = array_get($request, 'acciones_a_realizar_idc');
        $mant_modulo_dispensador                  = array_get($request, 'mant_modulo_dispensador');
        $cambio_presentador_stacker               = array_get($request, 'cambio_presentador_stacker');
        $cambio_cosumibles                        = array_get($request, 'cambio_cosumibles');
        $cambio_pick_picker_estractor             = array_get($request, 'cambio_pick_picker_estractor');
        $cambio_caseteros                         = array_get($request, 'cambio_caseteros');
        $otro_1_dispensador                       = array_get($request, 'otro_1_dispensador');
        $nombre_pieza_otro_1_dispensador          = array_get($request, 'nombre_pieza_otro_1_dispensador');
        $otro_2_dispensador                       = array_get($request, 'otro_2_dispensador');
        $nombre_pieza_otro_2_dispensador          = array_get($request, 'nombre_pieza_otro_2_dispensador');
        $mant_modulo_aceptador                    = array_get($request, 'mant_modulo_aceptador');
        $cambio_escrow                            = array_get($request, 'cambio_escrow');
        $cambio_validador                         = array_get($request, 'cambio_validador');
        $cambio_shutter_cash_slot                 = array_get($request, 'cambio_shutter_cash_slot');
        $cambio_tarjeta_controladora              = array_get($request, 'cambio_tarjeta_controladora');
        $otro_1_aceptador                         = array_get($request, 'otro_1_aceptador');
        $nombre_pieza_otro_1_aceptador            = array_get($request, 'nombre_pieza_otro_1_aceptador');
        $otro_2_aceptador                         = array_get($request, 'otro_2_aceptador');
        $nombre_pieza_otro_2_aceptador            = array_get($request, 'nombre_pieza_otro_2_aceptador');
        $mant_modulo_idc_cpu                      = array_get($request, 'mant_modulo_idc_cpu');
        $cambio_dd                                = array_get($request, 'cambio_dd');
        $cambio_cpu                               = array_get($request, 'cambio_cpu');
        $mant_modulo_lectora                      = array_get($request, 'mant_modulo_lectora');
        $cambio_lectora                           = array_get($request, 'cambio_lectora');
        $mant_modulo_impresora                    = array_get($request, 'mant_modulo_impresora');
        $cambio_impresora                         = array_get($request, 'cambio_impresora');
        $fuente_de_poder                          = array_get($request, 'fuente_de_poder');
        $teclado_teclado_lateral_touch_screen     = array_get($request, 'teclado_teclado_lateral_touch_screen');
        $hopper                                   = array_get($request, 'hopper');
        $monitor_pantalla                         = array_get($request, 'monitor_pantalla');
        $fascia                                   = array_get($request, 'fascia');
        $se_requiere_planchado_de_sw              = array_get($request, 'se_requiere_planchado_de_sw');
        $ver_requerida                            = array_get($request, 'ver_requerida');
        $aplican_acciones_de_inmuebles            = array_get($request, 'aplican_acciones_de_inmuebles');
        $acciones_a_realizar_inmuebles            = array_get($request, 'acciones_a_realizar_inmuebles');
        $revision_correcion_de_voltajes           = array_get($request, 'revision_correcion_de_voltajes');
        $mejoramiento_de_imagen_limpieza          = array_get($request, 'mejoramiento_de_imagen_limpieza');
        $revision_correccion_instalacion_de_aa    = array_get($request, 'revision_correccion_instalacion_de_aa');
        $requiere_ups                             = array_get($request, 'requiere_ups');
        $aplican_acciones_de_cableado             = array_get($request, 'aplican_acciones_de_cableado');
        $acciones_a_realizar_cableado             = array_get($request, 'acciones_a_realizar_cableado');
        $correcciones_red_interna                 = array_get($request, 'correcciones_red_interna');
        $revision_de_cableado_y_status_de_equipos = array_get($request, 'revision_de_cableado_y_status_de_equipos');
        $retiro_y_o_limpieza_de_equipos           = array_get($request, 'retiro_y_o_limpieza_de_equipos');
        $aplican_acciones_de_comunicaciones       = array_get($request, 'aplican_acciones_de_comunicaciones');
        $acciones_a_realizar_comunicaciones       = array_get($request, 'acciones_a_realizar_comunicaciones');
        $revision_enlace_equipo                   = array_get($request, 'revision_enlace_equipo');
        $prueba_de_calidad                        = array_get($request, 'prueba_de_calidad');
        $aplica_la_asistencia_de_un_interventor   = array_get($request, 'aplica_la_asistencia_de_un_interventor');
        $alta_datos_odt_id                        = array_get($request, 'alta_datos_odt_id');
        $alta_datos_odt_id                        = $alta_datos_odt_id * 1;
        $tipo_de_visita                           = array_get($request, 'tipo_de_visita');
        $now                                      = Carbon::now('America/Mexico_City');

        /*if($aplican_acciones_de_inmuebles == 'SI' 
            && $aplican_acciones_de_cableado == 'SI' 
            && $aplican_acciones_de_comunicaciones == 'SI'){
            $tipo_de_visita      = 'VISITA_IDC_ETV_INMUEBLES_CABLEADO_COMUNICACIONES';
        }
        if($aplican_acciones_de_inmuebles == 'SI' 
            && $aplican_acciones_de_cableado == 'SI' 
            && ($aplican_acciones_de_comunicaciones == 'NO' || $aplican_acciones_de_comunicaciones == '')){
            $tipo_de_visita      = 'VISITA_IDC_ETV_INMUEBLES_CABLEADO';
        }
        if($aplican_acciones_de_inmuebles == 'SI' 
            && ($aplican_acciones_de_cableado == 'NO' || $aplican_acciones_de_cableado == '') 
            && ($aplican_acciones_de_comunicaciones == 'NO' || $aplican_acciones_de_comunicaciones == '')){
            $tipo_de_visita      = 'VISITA_IDC_ETV_INMUEBLES';
        }
        if($aplican_acciones_de_inmuebles == 'SI' 
            && ($aplican_acciones_de_cableado == 'NO' || $aplican_acciones_de_cableado == '') 
            && $aplican_acciones_de_comunicaciones == 'SI'){
            $tipo_de_visita      = 'VISITA_IDC_ETV_INMUEBLES_COMUNICACIONES';
        }
        if(($aplican_acciones_de_inmuebles == 'NO' || $aplican_acciones_de_inmuebles == '') 
            && $aplican_acciones_de_cableado == 'SI' 
            && $aplican_acciones_de_comunicaciones == 'SI'){
            $tipo_de_visita      = 'VISITA_IDC_ETV_CABLEADO_COMUNICACIONES';
        }
        if(($aplican_acciones_de_inmuebles == 'NO' || $aplican_acciones_de_inmuebles == '') 
            && $aplican_acciones_de_cableado == 'SI' 
            && ($aplican_acciones_de_comunicaciones == 'NO' || $aplican_acciones_de_comunicaciones == '')){
            $tipo_de_visita      = 'VISITA_IDC_ETV_CABLEADO';
        }
        if(($aplican_acciones_de_inmuebles == 'NO' || $aplican_acciones_de_inmuebles == '') 
            && ($aplican_acciones_de_cableado == 'NO' || $aplican_acciones_de_cableado == 'NO') 
            && $aplican_acciones_de_comunicaciones == 'SI'){
            $tipo_de_visita      = 'VISITA_IDC_ETV_COMUNICACIONES';
        }	*/

        if($aplican_acciones_de_idc=='SI'){
            if($aplican_acciones_de_idc && $mant_modulo_idc_cpu && $cambio_dd && $mant_modulo_lectora && $cambio_lectora && $mant_modulo_impresora
            && $cambio_impresora && $fuente_de_poder && $teclado_teclado_lateral_touch_screen && $hopper && $monitor_pantalla && $fascia && $se_requiere_planchado_de_sw && $aplican_acciones_de_inmuebles && $aplican_acciones_de_cableado && $aplican_acciones_de_comunicaciones
            && $aplica_la_asistencia_de_un_interventor){
            $status = 'Planeacion';
            }else{
                $status = 'Analisis Pendiente';
            }

        }else{
            $status ='Planeacion';
        }

        $data_id = DB::table('analisis')->insertGetId(array(
            'atm'                                      => $atm,
            'nombre_responsable'                       => $responsable_atm,
            'nombre_analista'                          => $nombre_analista,
            'fecha_de_analisis'                        => $fecha_de_analisis,
            'aplican_acciones_de_idc'                  => $aplican_acciones_de_idc,
            'acciones_a_realizar_idc'                  => $acciones_a_realizar_idc,
            'mant_modulo_dispensador'                  => $mant_modulo_dispensador,
            'cambio_presentador_stacker'               => $cambio_presentador_stacker,
            'cambio_cosumibles'                        => $cambio_cosumibles,
            'cambio_pick_picker_estractor'             => $cambio_pick_picker_estractor,
            'cambio_caseteros'                         => $cambio_caseteros,
            'otro_1_dispensador'                       => $otro_1_dispensador,
            'nombre_pieza_otro_1_dispensador'          => $nombre_pieza_otro_1_dispensador,
            'otro_2_dispensador'                       => $otro_2_dispensador,
            'nombre_pieza_otro_2_dispensador'          => $nombre_pieza_otro_2_dispensador,
            'mant_modulo_aceptador'                    => $mant_modulo_aceptador,
            'cambio_escrow'                            => $cambio_escrow,
            'cambio_validador'                         => $cambio_validador,
            'cambio_shutter_cash_slot'                 => $cambio_shutter_cash_slot,
            'cambio_tarjeta_controladora'              => $cambio_tarjeta_controladora,
            'otro_1_aceptador'                         => $otro_1_aceptador,
            'nombre_pieza_otro_1_aceptador'            => $nombre_pieza_otro_1_aceptador,
            'otro_2_aceptador'                         => $otro_2_aceptador,
            'nombre_pieza_otro_2_aceptador'            => $nombre_pieza_otro_2_aceptador,
            'mant_modulo_idc_cpu'                      => $mant_modulo_idc_cpu,
            'cambio_dd'                                => $cambio_dd,
            'cambio_cpu'                               => $cambio_cpu,
            'mant_modulo_lectora'                      => $mant_modulo_lectora,
            'cambio_lectora'                           => $cambio_lectora,
            'mant_modulo_impresora'                    => $mant_modulo_impresora,
            'cambio_impresora'                         => $cambio_impresora,
            'fuente_de_poder'                          => $fuente_de_poder,
            'teclado_teclado_lateral_touch_screen'     => $teclado_teclado_lateral_touch_screen,
            'hopper'                                   => $hopper,
            'monitor_pantalla'                         => $monitor_pantalla,
            'fascia'                                   => $fascia,
            'se_requiere_planchado_de_sw'              => $se_requiere_planchado_de_sw,
            'ver_requerida'                            => $ver_requerida,
            'aplican_acciones_de_inmuebles'            => $aplican_acciones_de_inmuebles,
            'acciones_a_realizar_inmuebles'            => $acciones_a_realizar_inmuebles,
            'revision_correcion_de_voltajes'           => $revision_correcion_de_voltajes,
            'mejoramiento_de_imagen_limpieza'          => $mejoramiento_de_imagen_limpieza,
            'revision_correccion_instalacion_de_aa'    => $revision_correccion_instalacion_de_aa,
            'requiere_ups'                             => $requiere_ups,
            'aplican_acciones_de_cableado'             => $aplican_acciones_de_cableado,
            'acciones_a_realizar_cableado'             => $acciones_a_realizar_cableado,
            'correcciones_red_interna'                 => $correcciones_red_interna,
            'revision_de_cableado_y_status_de_equipos' => $revision_de_cableado_y_status_de_equipos,
            'retiro_y_o_limpieza_de_equipos'           => $retiro_y_o_limpieza_de_equipos,
            'aplican_acciones_de_comunicaciones'       => $aplican_acciones_de_comunicaciones,
            'acciones_a_realizar_comunicaciones'       => $acciones_a_realizar_comunicaciones,
            'revision_enlace_equipo'                   => $revision_enlace_equipo,
            'prueba_de_calidad'                        => $prueba_de_calidad,
            'aplica_la_asistencia_de_un_interventor'   => $aplica_la_asistencia_de_un_interventor,
            'tipo_de_visita'                           => $tipo_de_visita,
            'status'                                   => $status,
            'user_name'                                => Auth::user()->name,
            'created_at'                               => $now,
            'updated_at'                               => $now,
        ));

        DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
			'status'				   => $status,
			'analisis_id'			   => $data_id,
			'updated_at'               => $now,
        ));


        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha creado un nuevo item en la tabla analisis con id ' . $data_id ,
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info insertada" , 
          "Data" => $data_id, 
        ]);
    }

    public function getAnalisisById($analisis_id){

        $data = DB::table('analisis')->where('id', $analisis_id)->get();
        
        return JsonResponse::singleResponse(["message" => "Info encontrada" , 
          "Data" => $data, 
        ]);
    }

    public function updateAnalisis(Request $request, $analisis_id){

        $atm                                      = array_get($request, 'atm');
        $que_se_mide                              = array_get($request, 'que_se_mide');
        //$responsable_planeacion                   = array_get($request, 'responsable_planeacion');
        $nombre_analista                          = array_get($request, 'nombre_analista');
        $fecha_de_analisis                        = array_get($request, 'fecha_de_analisis');
        $aplican_acciones_de_idc                  = array_get($request, 'aplican_acciones_de_idc');
        $acciones_a_realizar_idc                  = array_get($request, 'acciones_a_realizar_idc');
        $mant_modulo_dispensador                  = array_get($request, 'mant_modulo_dispensador');
        $cambio_presentador_stacker               = array_get($request, 'cambio_presentador_stacker');
        $cambio_cosumibles                        = array_get($request, 'cambio_cosumibles');
        $cambio_pick_picker_estractor             = array_get($request, 'cambio_pick_picker_estractor');
        $cambio_caseteros                         = array_get($request, 'cambio_caseteros');
        $otro_1_dispensador                       = array_get($request, 'otro_1_dispensador');
        $nombre_pieza_otro_1_dispensador          = array_get($request, 'nombre_pieza_otro_1_dispensador');
        $otro_2_dispensador                       = array_get($request, 'otro_2_dispensador');
        $nombre_pieza_otro_2_dispensador          = array_get($request, 'nombre_pieza_otro_2_dispensador');
        $mant_modulo_aceptador                    = array_get($request, 'mant_modulo_aceptador');
        $cambio_escrow                            = array_get($request, 'cambio_escrow');
        $cambio_validador                         = array_get($request, 'cambio_validador');
        $cambio_shutter_cash_slot                 = array_get($request, 'cambio_shutter_cash_slot');
        $cambio_tarjeta_controladora              = array_get($request, 'cambio_tarjeta_controladora');
        $otro_1_aceptador                         = array_get($request, 'otro_1_aceptador');
        $nombre_pieza_otro_1_aceptador            = array_get($request, 'nombre_pieza_otro_1_aceptador');
        $otro_2_aceptador                         = array_get($request, 'otro_2_aceptador');
        $nombre_pieza_otro_2_aceptador            = array_get($request, 'nombre_pieza_otro_2_aceptador');
        $mant_modulo_idc_cpu                      = array_get($request, 'mant_modulo_idc_cpu');
        $cambio_dd                                = array_get($request, 'cambio_dd');
        $cambio_cpu                               = array_get($request, 'cambio_cpu');
        $mant_modulo_lectora                      = array_get($request, 'mant_modulo_lectora');
        $cambio_lectora                           = array_get($request, 'cambio_lectora');
        $mant_modulo_impresora                    = array_get($request, 'mant_modulo_impresora');
        $cambio_impresora                         = array_get($request, 'cambio_impresora');
        $fuente_de_poder                          = array_get($request, 'fuente_de_poder');
        $teclado_teclado_lateral_touch_screen     = array_get($request, 'teclado_teclado_lateral_touch_screen');
        $hopper                                   = array_get($request, 'hopper');
        $monitor_pantalla                         = array_get($request, 'monitor_pantalla');
        $fascia                                   = array_get($request, 'fascia');
        $se_requiere_planchado_de_sw              = array_get($request, 'se_requiere_planchado_de_sw');
        $ver_requerida                            = array_get($request, 'ver_requerida');
        $aplican_acciones_de_inmuebles            = array_get($request, 'aplican_acciones_de_inmuebles');
        $acciones_a_realizar_inmuebles            = array_get($request, 'acciones_a_realizar_inmuebles');
        $revision_correcion_de_voltajes           = array_get($request, 'revision_correcion_de_voltajes');
        $mejoramiento_de_imagen_limpieza          = array_get($request, 'mejoramiento_de_imagen_limpieza');
        $revision_correccion_instalacion_de_aa    = array_get($request, 'revision_correccion_instalacion_de_aa');
        $requiere_ups                             = array_get($request, 'requiere_ups');
        $aplican_acciones_de_cableado             = array_get($request, 'aplican_acciones_de_cableado');
        $acciones_a_realizar_cableado             = array_get($request, 'acciones_a_realizar_cableado');
        $correcciones_red_interna                 = array_get($request, 'correcciones_red_interna');
        $revision_de_cableado_y_status_de_equipos = array_get($request, 'revision_de_cableado_y_status_de_equipos');
        $retiro_y_o_limpieza_de_equipos           = array_get($request, 'retiro_y_o_limpieza_de_equipos');
        $aplican_acciones_de_comunicaciones       = array_get($request, 'aplican_acciones_de_comunicaciones');
        $acciones_a_realizar_comunicaciones       = array_get($request, 'acciones_a_realizar_comunicaciones');
        $revision_enlace_equipo                   = array_get($request, 'revision_enlace_equipo');
        $prueba_de_calidad                        = array_get($request, 'prueba_de_calidad');
        $aplica_la_asistencia_de_un_interventor   = array_get($request, 'aplica_la_asistencia_de_un_interventor');
        $alta_datos_odt_id                        = array_get($request, 'alta_datos_odt_id');
        $alta_datos_odt_id                        = $alta_datos_odt_id * 1;
        $tipo_de_visita                           = array_get($request, 'tipo_de_visita');
        $now                                      = Carbon::now('America/Mexico_City');

      /*  if($aplican_acciones_de_inmuebles == 'SI' 
            && $aplican_acciones_de_cableado == 'SI' 
            && $aplican_acciones_de_comunicaciones == 'SI'){
            $tipo_de_visita      = 'VISITA_IDC_ETV_INMUEBLES_CABLEADO_COMUNICACIONES';
        }
        if($aplican_acciones_de_inmuebles == 'SI' 
            && $aplican_acciones_de_cableado == 'SI' 
            && ($aplican_acciones_de_comunicaciones == 'NO' || $aplican_acciones_de_comunicaciones == '')){
            $tipo_de_visita      = 'VISITA_IDC_ETV_INMUEBLES_CABLEADO';
        }
        if($aplican_acciones_de_inmuebles == 'SI' 
            && ($aplican_acciones_de_cableado == 'NO' || $aplican_acciones_de_cableado == '') 
            && ($aplican_acciones_de_comunicaciones == 'NO' || $aplican_acciones_de_comunicaciones == '')){
            $tipo_de_visita      = 'VISITA_IDC_ETV_INMUEBLES';
        }
        if($aplican_acciones_de_inmuebles == 'SI' 
            && ($aplican_acciones_de_cableado == 'NO' || $aplican_acciones_de_cableado == '') 
            && $aplican_acciones_de_comunicaciones == 'SI'){
            $tipo_de_visita      = 'VISITA_IDC_ETV_INMUEBLES_COMUNICACIONES';
        }
        if(($aplican_acciones_de_inmuebles == 'NO' || $aplican_acciones_de_inmuebles == '') 
            && $aplican_acciones_de_cableado == 'SI' 
            && $aplican_acciones_de_comunicaciones == 'SI'){
            $tipo_de_visita      = 'VISITA_IDC_ETV_CABLEADO_COMUNICACIONES';
        }
        if(($aplican_acciones_de_inmuebles == 'NO' || $aplican_acciones_de_inmuebles == '') 
            && $aplican_acciones_de_cableado == 'SI' 
            && ($aplican_acciones_de_comunicaciones == 'NO' || $aplican_acciones_de_comunicaciones == '')){
            $tipo_de_visita      = 'VISITA_IDC_ETV_CABLEADO';
        }
        if(($aplican_acciones_de_inmuebles == 'NO' || $aplican_acciones_de_inmuebles == '') 
            && ($aplican_acciones_de_cableado == 'NO' || $aplican_acciones_de_cableado == 'NO') 
            && $aplican_acciones_de_comunicaciones == 'SI'){
            $tipo_de_visita      = 'VISITA_IDC_ETV_COMUNICACIONES';
        }   */

         if($aplican_acciones_de_idc=='SI'){
            if($aplican_acciones_de_idc && $mant_modulo_idc_cpu && $cambio_dd && $mant_modulo_lectora && $cambio_lectora && $mant_modulo_impresora
            && $cambio_impresora && $fuente_de_poder && $teclado_teclado_lateral_touch_screen && $hopper && $monitor_pantalla && $fascia && $se_requiere_planchado_de_sw && $aplican_acciones_de_inmuebles && $aplican_acciones_de_cableado && $aplican_acciones_de_comunicaciones
            && $aplica_la_asistencia_de_un_interventor){
            $status = 'Planeacion';
            }else{
                $status = 'Analisis Pendiente';
            }

        }else{
            $status ='Planeacion';
        }


        DB::table('analisis')->where('id', $analisis_id)->update(array(
            'atm'                                      => $atm,
            //'nombre_responsable'                       => $responsable_planeacion,
            'nombre_analista'                          => $nombre_analista,
            'fecha_de_analisis'                        => $fecha_de_analisis,
            'aplican_acciones_de_idc'                  => $aplican_acciones_de_idc,
            'acciones_a_realizar_idc'                  => $acciones_a_realizar_idc,
            'mant_modulo_dispensador'                  => $mant_modulo_dispensador,
            'cambio_presentador_stacker'               => $cambio_presentador_stacker,
            'cambio_cosumibles'                        => $cambio_cosumibles,
            'cambio_pick_picker_estractor'             => $cambio_pick_picker_estractor,
            'cambio_caseteros'                         => $cambio_caseteros,
            'otro_1_dispensador'                       => $otro_1_dispensador,
            'nombre_pieza_otro_1_dispensador'          => $nombre_pieza_otro_1_dispensador,
            'otro_2_dispensador'                       => $otro_2_dispensador,
            'nombre_pieza_otro_2_dispensador'          => $nombre_pieza_otro_2_dispensador,
            'mant_modulo_aceptador'                    => $mant_modulo_aceptador,
            'cambio_escrow'                            => $cambio_escrow,
            'cambio_validador'                         => $cambio_validador,
            'cambio_shutter_cash_slot'                 => $cambio_shutter_cash_slot,
            'cambio_tarjeta_controladora'              => $cambio_tarjeta_controladora,
            'otro_1_aceptador'                         => $otro_1_aceptador,
            'nombre_pieza_otro_1_aceptador'            => $nombre_pieza_otro_1_aceptador,
            'otro_2_aceptador'                         => $otro_2_aceptador,
            'nombre_pieza_otro_2_aceptador'            => $nombre_pieza_otro_2_aceptador,
            'mant_modulo_idc_cpu'                      => $mant_modulo_idc_cpu,
            'cambio_dd'                                => $cambio_dd,
            'cambio_cpu'                               => $cambio_cpu,
            'mant_modulo_lectora'                      => $mant_modulo_lectora,
            'cambio_lectora'                           => $cambio_lectora,
            'mant_modulo_impresora'                    => $mant_modulo_impresora,
            'cambio_impresora'                         => $cambio_impresora,
            'fuente_de_poder'                          => $fuente_de_poder,
            'teclado_teclado_lateral_touch_screen'     => $teclado_teclado_lateral_touch_screen,
            'hopper'                                   => $hopper,
            'monitor_pantalla'                         => $monitor_pantalla,
            'fascia'                                   => $fascia,
            'se_requiere_planchado_de_sw'              => $se_requiere_planchado_de_sw,
            'ver_requerida'                            => $ver_requerida,
            'aplican_acciones_de_inmuebles'            => $aplican_acciones_de_inmuebles,
            'acciones_a_realizar_inmuebles'            => $acciones_a_realizar_inmuebles,
            'revision_correcion_de_voltajes'           => $revision_correcion_de_voltajes,
            'mejoramiento_de_imagen_limpieza'          => $mejoramiento_de_imagen_limpieza,
            'revision_correccion_instalacion_de_aa'    => $revision_correccion_instalacion_de_aa,
            'requiere_ups'                             => $requiere_ups,
            'aplican_acciones_de_cableado'             => $aplican_acciones_de_cableado,
            'acciones_a_realizar_cableado'             => $acciones_a_realizar_cableado,
            'correcciones_red_interna'                 => $correcciones_red_interna,
            'revision_de_cableado_y_status_de_equipos' => $revision_de_cableado_y_status_de_equipos,
            'retiro_y_o_limpieza_de_equipos'           => $retiro_y_o_limpieza_de_equipos,
            'aplican_acciones_de_comunicaciones'       => $aplican_acciones_de_comunicaciones,
            'acciones_a_realizar_comunicaciones'       => $acciones_a_realizar_comunicaciones,
            'revision_enlace_equipo'                   => $revision_enlace_equipo,
            'prueba_de_calidad'                        => $prueba_de_calidad,
            'aplica_la_asistencia_de_un_interventor'   => $aplica_la_asistencia_de_un_interventor,
            'tipo_de_visita'                           => $tipo_de_visita,
            'status'                                   => $status,
            'user_name'                                => Auth::user()->name,
            //'created_at'                               => $now,
            'updated_at'                               => $now,
        ));

        DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
            'status'                   => $status,
            'analisis_id'              => $analisis_id,
            'updated_at'               => $now,
        ));


        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha actualizado un item en la tabla analisis con id ' . $analisis_id ,
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info actualizada" , 
        ]);
    }

    public function downloadReporteAnalisis($fecha_inicio, $fecha_fin){

        $now = Carbon::now('America/Mexico_City');

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha descargado un excel de la tabla analisis',
            'created_at'    => $now,
            'updated_at'    => $now
        ));

      //aqui empieza el ciclo

        $data = DB::table('analisis')
                    ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
                    ->get();
        
        $tot_record_found = 0;
        if ($data != null || $data != '') {
            $tot_record_found = 1;

            $CsvData = array('ATM, NOMBRE RESPONSABLE, NOMBRE ANALISTA, FECHA DE ANALISIS, APLICAN ACCIONES DE IDC, ACCIONES A REALIZAR IDC, MANT MODULO DISPENSADOR, CAMBIO PRESENTADOR STACKER, CAMBIO CONSUMIBLES, CAMBIO PICK PICKER ESTRACTOR, CAMBIO CASETEROS, OTRO 1 DISPENSADOR, NOMBRE OTRO 1 DISPENSADOR, OTRO 2 DISPENSADOR, NOMBRE OTRO 2 DISPENSADOR, MANT MODULO ACEPTADOR, CAMBIO ESCROW, CAMBIO VALIDADOR, CAMBIO SHUTTER CASH SLOT, CAMBIO TARJETA CONTROLADORA, OTRO 1 ACEPTADOR, NOMBRE OTRO 1 ACEPTADOR, OTRO 2 ACEPTADOR, NOMBRE OTRO 2 ACEPTADOR, MANT MODULO IDC CPU, CAMBIO DD, CAMBIO CPU, MANT MODULO LECTORA, MANT MODULO IMPRESORA, CAMBIO IMPRESORA, FUENTE DE PODER, TECLADO TECLADO LATERAL TOUCH SCREEN, HOPPER, MONITOR DE PANTALLA, FASCIA, SE REQUIERE PLANCHADO DE SW, VERSION REQUERIDA, APLICAN ACCIONES DE INMUEBLES, ACCIONES A REALIZAR INMUEBLES, REVISION CORRECION DE VOLTAJES, MEJORAMIENTO DE IMAGEN LIMPIEZA, REVISION CORRECCION INSTALACION DE AA, REQUIERE UPS, APLICAN ACCIONES DE CABLEADO, ACCIONES A REALIZAR CABLEADO, CORRECCIONES RED INTERNA, REVISION DE CABLEADO Y STATUS DE EQUIPOS, RETIRO Y O LIMPIEZA DE EQUIPOS, APLICAN ACCIONES DE COMUNICACIONES, ACCIONES A REALIZAR COMUNICACIONES, REVISION ENLACE EQUIPO, PRUEBA DE CALIDAD, APLICA LA ASISTENCIA DE UN INTERVENTOR, TIPO DE VISITA, USUARIO, FECHA DE CREACION');
            foreach ($data as $value) {

                $atm                                      = str_replace(',', '', $value->atm);
                $nombre_responsable                       = str_replace(',', '', $value->nombre_responsable);
                $nombre_analista                          = str_replace(',', '', $value->nombre_analista);
                $fecha_de_analisis                        = str_replace(',', '', $value->fecha_de_analisis);
                $aplican_acciones_de_idc                  = str_replace(',', '', $value->aplican_acciones_de_idc);
                $acciones_a_realizar_idc                  = str_replace(',', '', $value->acciones_a_realizar_idc);
                $mant_modulo_dispensador                  = str_replace(',', '', $value->mant_modulo_dispensador);
                $cambio_presentador_stacker               = str_replace(',', '', $value->cambio_presentador_stacker);
                $cambio_cosumibles                        = str_replace(',', '', $value->cambio_cosumibles);
                $cambio_pick_picker_estractor             = str_replace(',', '', $value->cambio_pick_picker_estractor);
                $cambio_caseteros                         = str_replace(',', '', $value->cambio_caseteros);
                $otro_1_dispensador                       = str_replace(',', '', $value->otro_1_dispensador);
                $nombre_pieza_otro_1_dispensador          = str_replace(',', '', $value->nombre_pieza_otro_1_dispensador);
                $otro_2_dispensador                       = str_replace(',', '', $value->otro_2_dispensador);
                $nombre_pieza_otro_2_dispensador          = str_replace(',', '', $value->nombre_pieza_otro_2_dispensador);
                $mant_modulo_aceptador                    = str_replace(',', '', $value->mant_modulo_aceptador);
                $cambio_escrow                            = str_replace(',', '', $value->cambio_escrow);
                $cambio_validador                         = str_replace(',', '', $value->cambio_validador);
                $cambio_shutter_cash_slot                 = str_replace(',', '', $value->cambio_shutter_cash_slot);
                $cambio_tarjeta_controladora              = str_replace(',', '', $value->cambio_tarjeta_controladora);
                $otro_1_aceptador                         = str_replace(',', '', $value->otro_1_aceptador);
                $nombre_pieza_otro_1_aceptador            = str_replace(',', '', $value->nombre_pieza_otro_1_aceptador);
                $otro_2_aceptador                         = str_replace(',', '', $value->otro_2_aceptador);
                $nombre_pieza_otro_2_aceptador            = str_replace(',', '', $value->nombre_pieza_otro_2_aceptador);
                $mant_modulo_idc_cpu                      = str_replace(',', '', $value->mant_modulo_idc_cpu);
                $cambio_dd                                = str_replace(',', '', $value->cambio_dd);
                $cambio_cpu                               = str_replace(',', '', $value->cambio_cpu);
                $mant_modulo_lectora                      = str_replace(',', '', $value->mant_modulo_lectora);
                $cambio_lectora                           = str_replace(',', '', $value->cambio_lectora);
                $mant_modulo_impresora                    = str_replace(',', '', $value->mant_modulo_impresora);
                $cambio_impresora                         = str_replace(',', '', $value->cambio_impresora);
                $fuente_de_poder                          = str_replace(',', '', $value->fuente_de_poder);
                $teclado_teclado_lateral_touch_screen     = str_replace(',', '', $value->teclado_teclado_lateral_touch_screen);
                $hopper                                   = str_replace(',', '', $value->hopper);
                $monitor_pantalla                         = str_replace(',', '', $value->monitor_pantalla);
                $fascia                                   = str_replace(',', '', $value->fascia);
                $se_requiere_planchado_de_sw              = str_replace(',', '', $value->se_requiere_planchado_de_sw);
                $ver_requerida                            = str_replace(',', '', $value->ver_requerida);
                $aplican_acciones_de_inmuebles            = str_replace(',', '', $value->aplican_acciones_de_inmuebles);
                $acciones_a_realizar_inmuebles            = str_replace(',', '', $value->acciones_a_realizar_inmuebles);
                $revision_correcion_de_voltajes           = str_replace(',', '', $value->revision_correcion_de_voltajes);
                $mejoramiento_de_imagen_limpieza          = str_replace(',', '', $value->mejoramiento_de_imagen_limpieza);
                $revision_correccion_instalacion_de_aa    = str_replace(',', '', $value->revision_correccion_instalacion_de_aa);
                $requiere_ups                             = str_replace(',', '', $value->requiere_ups);
                $aplican_acciones_de_cableado             = str_replace(',', '', $value->aplican_acciones_de_cableado);
                $acciones_a_realizar_cableado             = str_replace(',', '', $value->acciones_a_realizar_cableado);
                $correcciones_red_interna                 = str_replace(',', '', $value->correcciones_red_interna);
                $revision_de_cableado_y_status_de_equipos = str_replace(',', '', $value->revision_de_cableado_y_status_de_equipos);
                $retiro_y_o_limpieza_de_equipos           = str_replace(',', '', $value->retiro_y_o_limpieza_de_equipos);
                $aplican_acciones_de_comunicaciones       = str_replace(',', '', $value->aplican_acciones_de_comunicaciones);
                $acciones_a_realizar_comunicaciones       = str_replace(',', '', $value->acciones_a_realizar_comunicaciones);
                $revision_enlace_equipo                   = str_replace(',', '', $value->revision_enlace_equipo);
                $prueba_de_calidad                        = str_replace(',', '', $value->prueba_de_calidad);
                $aplica_la_asistencia_de_un_interventor   = str_replace(',', '', $value->aplica_la_asistencia_de_un_interventor);
                $tipo_de_visita                           = str_replace(',', '', $value->tipo_de_visita);
                $user_name                                = str_replace(',', '', $value->user_name);
                $created_at                               = str_replace(',', '', $value->created_at);
              

                $CsvData[] = $atm . ',' . $nombre_responsable . ',' . $nombre_analista . ',' . $fecha_de_analisis . ',' . $aplican_acciones_de_idc . ',' . $acciones_a_realizar_idc . ',' . $mant_modulo_dispensador . ',' . $cambio_presentador_stacker . ',' . $cambio_cosumibles . ',' . $cambio_pick_picker_estractor . ',' . $cambio_caseteros . ',' . $otro_1_dispensador . ',' . $nombre_pieza_otro_1_dispensador . ',' . $otro_2_dispensador . ',' . $nombre_pieza_otro_2_dispensador . ',' . $mant_modulo_aceptador . ',' . $cambio_escrow . ',' . $cambio_validador . ',' . $cambio_shutter_cash_slot . ',' . $cambio_tarjeta_controladora . ',' . $otro_1_aceptador . ',' . $nombre_pieza_otro_1_aceptador . ',' . $otro_2_aceptador  . ',' . $nombre_pieza_otro_2_aceptador . ',' . $mant_modulo_idc_cpu . ',' . $cambio_dd . ',' . $cambio_cpu . ',' . $mant_modulo_lectora . ',' . $mant_modulo_impresora . ',' . $cambio_impresora . ',' . $fuente_de_poder . ',' . $teclado_teclado_lateral_touch_screen . ',' . $hopper . ',' . $monitor_pantalla . ',' . $fascia . ',' . $se_requiere_planchado_de_sw . ',' . $ver_requerida . ',' . $aplican_acciones_de_inmuebles . ',' . $acciones_a_realizar_inmuebles . ',' . $revision_correcion_de_voltajes . ',' . $mejoramiento_de_imagen_limpieza . ',' . $revision_correccion_instalacion_de_aa . ',' . $requiere_ups . ',' . $aplican_acciones_de_cableado . ',' . $acciones_a_realizar_cableado . ',' . $correcciones_red_interna  . ',' . $revision_de_cableado_y_status_de_equipos . ',' . $retiro_y_o_limpieza_de_equipos . ',' . $aplican_acciones_de_comunicaciones . ',' . $acciones_a_realizar_comunicaciones . ',' . $revision_enlace_equipo . ',' . $prueba_de_calidad . ',' . $aplica_la_asistencia_de_un_interventor . ',' . $tipo_de_visita . ',' . $user_name . ',' . $created_at;
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

    public function exportExcel()
    {
        /** Fuente de Datos Eloquent */
        $data = \App\Entities\InternalEvent::select('evento as goku')->get();
        $data1 = \App\Entities\InternalEvent::select('id as goku_id')->get();
        /** Creamos nuestro archivo Excel */
        Excel::create('internal', function ($excel) use ($data, $data1) {
            /** Creamos una hoja */
            //$excel->sheet('Hoja Uno', function ($sheet) use ($data) {
                /**
                 * Insertamos los datos en la hoja con el método with/fromArray
                 * Parametros: (

                 * Datos,
                 * Valores del encabezado de la columna,
                 * Celda de Inicio,
                 * Comparación estricta de los valores del encabezado
                 * Impresión de los encabezados

                 * )*/
            //    $sheet->with($data, null , 'A1', false, false);
            //});

            $excel->sheet('Sheetname', function($sheet) use ($data, $data1) {

                $sheet->fromArray(array(
                    array($data, $data1),
                ));

            });


           
            /** Descargamos nuestro archivo pasandole la extensión deseada (xls, xlsx, csv) */
        })->download('csv');
    }
}