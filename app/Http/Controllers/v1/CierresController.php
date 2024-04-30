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

class CierresController extends Controller
{
    use JWTTrait;

    protected $internal;

    public function __construct(Internal $internal)
    {
        $this->internal = $internal;
    }

    public function createCierres(Request $request){

		$atm                                      = array_get($request, 'atm');
		$que_se_mide                              = array_get($request, 'que_se_mide');
		
		$cierre_idc                               = array_get($request, 'cierre_idc');
		$cierre_idc                               = str_replace(["\r", "\n"], "", $cierre_idc);
		$vandalismo                               = array_get($request, 'vandalismo');
		$modulo_vandalizado                       = array_get($request, 'modulo_vandalizado');
		$otro_especificar                         = array_get($request, 'otro_especificar');
		$fecha_escalamiento_dar                   = array_get($request, 'fecha_escalamiento_dar');
		
		$mant_modulo_dispensador                  = array_get($request, 'mant_modulo_dispensador');
		$cambio_presentador_stacker               = array_get($request, 'cambio_presentador_stacker');
		$cambio_consumibles                       = array_get($request, 'cambio_consumibles');
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
		$cambio_tarjeta_cotroladora               = array_get($request, 'cambio_tarjeta_cotroladora');
		$otro_1_aceptador                         = array_get($request, 'otro_1_aceptador');
		$nombre_pieza_otro_1_aceptador            = array_get($request, 'nombre_pieza_otro_1_aceptador');
		$otro_2_aceptador                         = array_get($request, 'otro_2_aceptador');
		$nombre_pieza_otro_2_aceptador            = array_get($request, 'nombre_pieza_otro_2_aceptador');
		
		$mant_modulo_cpu                          = array_get($request, 'mant_modulo_cpu');
		$cambio_dd                                = array_get($request, 'cambio_dd');
		$cambio_cpu                               = array_get($request, 'cambio_cpu');
		
		$mant_modulo_lectora                      = array_get($request, 'mant_modulo_lectora');
		$cambio_lectora                           = array_get($request, 'cambio_lectora');
		
		$mant_modulo_impresora                    = array_get($request, 'mant_modulo_impresora');
		$cambio_impresora                         = array_get($request, 'cambio_impresora');
		
		$fuente_de_poder                          = array_get($request, 'fuente_de_poder');
		$teclado_teclado_lateral_touch_screen     = array_get($request, 'teclado_teclado_lateral_touch_screen');
		$hooper                                   = array_get($request, 'hooper');
		$monitor_pantalla                         = array_get($request, 'monitor_pantalla');
		$fascia                                   = array_get($request, 'fascia');
		
		$se_realizo_planchado_sw                  = array_get($request, 'se_realizo_planchado_sw');
		$version_instalada                        = array_get($request, 'version_instalada');
		
		$checker_visible                          = array_get($request, 'checker_visible');
		$activacion_checker                       = array_get($request, 'activacion_checker');
		$nombre_activa_checker                    = array_get($request, 'nombre_activa_checker');
		$csds_visible                             = array_get($request, 'csds_visible');
		
		$cierre_inmuebles                         = array_get($request, 'cierre_inmuebles');
		$revision_correcion_de_voltajes           = array_get($request, 'revision_correcion_de_voltajes');
		$mejoramiento_de_imagen_limpieza          = array_get($request, 'mejoramiento_de_imagen_limpieza');
		$revision_correccion_instalacion_de_aa    = array_get($request, 'revision_correccion_instalacion_de_aa');
		$requiere_ups                             = array_get($request, 'requiere_ups');
		
		$cierre_cableado                          = array_get($request, 'cierre_cableado');
		$correcciones_red_interna                 = array_get($request, 'correcciones_red_interna');
		$revision_de_cableado_y_status_de_equipos = array_get($request, 'revision_de_cableado_y_status_de_equipos');
		$retiro_y_o_limpieza_de_equipos           = array_get($request, 'retiro_y_o_limpieza_de_equipos');
		
		$cierre_comunicaciones                    = array_get($request, 'cierre_comunicaciones');
		$revision_enlace_equipos                  = array_get($request, 'revision_enlace_equipos');
		$prueba_de_calidad                        = array_get($request, 'prueba_de_calidad');
		
		$cierre_interventor                       = array_get($request, 'cierre_interventor');
		
		$cuenta_con_reporte_fotografico           = array_get($request, 'cuenta_con_reporte_fotografico');
		$requiere_calcomanias                     = array_get($request, 'requiere_calcomanias');
		$requiere_mejoramiento_de_imagen          = array_get($request, 'requiere_mejoramiento_de_imagen');
		$alta_datos_odt_id                        = array_get($request, 'alta_datos_odt_id');
		$alta_datos_odt_id                        = $alta_datos_odt_id * 1;

		$aplican_acciones_de_comunicaciones_analisis     = array_get($request, 'aplican_acciones_de_comunicaciones_analisis');
		$aplican_acciones_de_cableado_analisis           = array_get($request, 'aplican_acciones_de_cableado_analisis');
		$aplican_acciones_de_inmuebles_analisis          = array_get($request, 'aplican_acciones_de_inmuebles_analisis');
		$aplica_la_asistencia_de_un_interventor_analisis = array_get($request, 'aplica_la_asistencia_de_un_interventor_analisis');

		$now                                      = Carbon::now('America/Mexico_City');

		if($cierre_idc && $vandalismo && $mant_modulo_dispensador && $cambio_presentador_stacker && $cambio_consumibles && $cambio_pick_picker_estractor && 
			$cambio_caseteros && $otro_1_dispensador && $otro_2_dispensador && $mant_modulo_cpu && $cambio_dd && $cambio_cpu && $mant_modulo_lectora 
			&& $cambio_lectora && $mant_modulo_impresora && $cambio_impresora && $fuente_de_poder && $teclado_teclado_lateral_touch_screen && $hooper 
			&& $monitor_pantalla && $fascia && $se_realizo_planchado_sw && $checker_visible && $activacion_checker && $nombre_activa_checker 
			&& $csds_visible && $cuenta_con_reporte_fotografico && $requiere_calcomanias && $requiere_mejoramiento_de_imagen){
				$vgeneral=true;
				if($vandalismo == 'SI'){
					if($modulo_vandalizado && $fecha_escalamiento_dar){
						$vgeneral=true;
					}else{
						$vgeneral=false;
					}
				}
				if($vandalismo == 'SI' && $modulo_vandalizado == 'OTRO' ){
					if($otro_especificar){
						$vgeneral=true;
					}else{
						$vgeneral=false;
					}
				}
				if($aplican_acciones_de_inmuebles_analisis == 'SI' ){
					if($cierre_inmuebles && $revision_correcion_de_voltajes && $mejoramiento_de_imagen_limpieza && $revision_correccion_instalacion_de_aa
		               && $requiere_ups){
						$vgeneral=true;
					}else{
						$vgeneral=false;
					}
				}
				if($aplican_acciones_de_cableado_analisis == 'SI' ){
					if($cierre_cableado && $correcciones_red_interna && $revision_de_cableado_y_status_de_equipos && $retiro_y_o_limpieza_de_equipos){
						$vgeneral=true;
					}else{
						$vgeneral=false;
					}
				}
				if($aplican_acciones_de_comunicaciones_analisis == 'SI' ){
					if($cierre_comunicaciones && $revision_enlace_equipos && $prueba_de_calidad){
						$vgeneral=true;
					}else{
						$vgeneral=false;
					}
				}
				/*if($aplica_la_asistencia_de_un_interventor_analisis == 'SI, CON ALCANCE' || $aplica_la_asistencia_de_un_interventor_analisis == 'SI, FUERA DE ALCANCE' ){
					if($cierre_interventor){
						$vgeneral=true;
					}else{
						$vgeneral=false;
					}
				}*/
		}else{
				$vgeneral=false;
		}
		
		if($vgeneral){
			$status = 'Completado';
		}else{
			$status = 'Cierre Pendiente';
		}

        $data_id = DB::table('cierres')->insertGetId(array(
        	'atm'                                      => $atm,
			'que_se_mide'                              => $que_se_mide,

			'cierre_idc'                               => $cierre_idc,
			'vandalismo'                               => $vandalismo,
			'modulo_vandalizado'                       => $modulo_vandalizado,
			'otro_especificar'                         => $otro_especificar,
			'fecha_escalamiento_dar'                   => $fecha_escalamiento_dar,
			
			'mant_modulo_dispensador'                  => $mant_modulo_dispensador,
			'cambio_presentador_stacker'               => $cambio_presentador_stacker,
			'cambio_consumibles'                       => $cambio_consumibles,
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
			'cambio_tarjeta_cotroladora'               => $cambio_tarjeta_cotroladora,
			'otro_1_aceptador'                         => $otro_1_aceptador,
			'nombre_pieza_otro_1_aceptador'            => $nombre_pieza_otro_1_aceptador,
			'otro_2_aceptador'                         => $otro_2_aceptador,
			'nombre_pieza_otro_2_aceptador'            => $nombre_pieza_otro_2_aceptador,
			
			'mant_modulo_cpu'                          => $mant_modulo_cpu,
			'cambio_dd'                                => $cambio_dd,
			'cambio_cpu'                               => $cambio_cpu,
			
			'mant_modulo_lectora'                      => $mant_modulo_lectora,
			'cambio_lectora'                           => $cambio_lectora,
			
			'mant_modulo_impresora'                    => $mant_modulo_impresora,
			'cambio_impresora'                         => $cambio_impresora,
			
			'fuente_de_poder'                          => $fuente_de_poder,
			'teclado_teclado_lateral_touch_screen'     => $teclado_teclado_lateral_touch_screen,
			'hooper'                                   => $hooper,
			'monitor_pantalla'                         => $monitor_pantalla,
			'fascia'                                   => $fascia,
			
			'se_realizo_planchado_sw'                  => $se_realizo_planchado_sw,
			'version_instalada'                        => $version_instalada,
			
			'checker_visible'                          => $checker_visible,
			'activacion_checker'                       => $activacion_checker,
			'nombre_activa_checker'                    => $nombre_activa_checker,
			'csds_visible'                             => $csds_visible,
			
			'cierre_inmuebles'                         => $cierre_inmuebles,
			'revision_correcion_de_voltajes'           => $revision_correcion_de_voltajes,
			'mejoramiento_de_imagen_limpieza'          => $mejoramiento_de_imagen_limpieza,
			'revision_correccion_instalacion_de_aa'    => $revision_correccion_instalacion_de_aa,
			'requiere_ups'                             => $requiere_ups,
			
			'cierre_cableado'                          => $cierre_cableado,
			'correcciones_red_interna'                 => $correcciones_red_interna,
			'revision_de_cableado_y_status_de_equipos' => $revision_de_cableado_y_status_de_equipos,
			'retiro_y_o_limpieza_de_equipos'           => $retiro_y_o_limpieza_de_equipos,
			
			'cierre_comunicaciones'                    => $cierre_comunicaciones,
			'revision_enlace_equipos'                  => $revision_enlace_equipos,
			'prueba_de_calidad'                        => $prueba_de_calidad,
			
			'cierre_interventor'                       => $cierre_interventor,
			
			'cuenta_con_reporte_fotografico'           => $cuenta_con_reporte_fotografico,
			'requiere_calcomanias'                     => $requiere_calcomanias,
			'requiere_mejoramiento_de_imagen'          => $requiere_mejoramiento_de_imagen,
			
			'user_name'                                => Auth::user()->name,
			'status'                                   => $status,
			'created_at'                               => $now,
			'updated_at'                               => $now,
        ));

        DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
			'status'     => $status,
			'cierre_id'  => $data_id,
			'updated_at' => $now,
        ));


        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha creado un nuevo item en la tabla cierres con id ' . $data_id ,
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info insertada" , 
          "Data" => $data_id, 
        ]);
    }

    public function getCierreById($cierre_id){

        $data = DB::table('cierres')->where('id', $cierre_id)->get();
        
        return JsonResponse::singleResponse(["message" => "Info encontrada" , 
          "Data" => $data, 
        ]);
    }

    public function updateCierres(Request $request, $cierre_id){

		$atm                                      = array_get($request, 'atm');
		$que_se_mide                              = array_get($request, 'que_se_mide');
		
		$cierre_idc                               = array_get($request, 'cierre_idc');
		$cierre_idc                               = str_replace(["\r", "\n"], "", $cierre_idc);
		$vandalismo                               = array_get($request, 'vandalismo');
		$modulo_vandalizado                       = array_get($request, 'modulo_vandalizado');
		$otro_especificar                         = array_get($request, 'otro_especificar');
		$fecha_escalamiento_dar                   = array_get($request, 'fecha_escalamiento_dar');
		
		$mant_modulo_dispensador                  = array_get($request, 'mant_modulo_dispensador');
		$cambio_presentador_stacker               = array_get($request, 'cambio_presentador_stacker');
		$cambio_consumibles                       = array_get($request, 'cambio_consumibles');
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
		$cambio_tarjeta_cotroladora               = array_get($request, 'cambio_tarjeta_cotroladora');
		$otro_1_aceptador                         = array_get($request, 'otro_1_aceptador');
		$nombre_pieza_otro_1_aceptador            = array_get($request, 'nombre_pieza_otro_1_aceptador');
		$otro_2_aceptador                         = array_get($request, 'otro_2_aceptador');
		$nombre_pieza_otro_2_aceptador            = array_get($request, 'nombre_pieza_otro_2_aceptador');
		
		$mant_modulo_cpu                          = array_get($request, 'mant_modulo_cpu');
		$cambio_dd                                = array_get($request, 'cambio_dd');
		$cambio_cpu                               = array_get($request, 'cambio_cpu');
		
		$mant_modulo_lectora                      = array_get($request, 'mant_modulo_lectora');
		$cambio_lectora                           = array_get($request, 'cambio_lectora');
		
		$mant_modulo_impresora                    = array_get($request, 'mant_modulo_impresora');
		$cambio_impresora                         = array_get($request, 'cambio_impresora');
		
		$fuente_de_poder                          = array_get($request, 'fuente_de_poder');
		$teclado_teclado_lateral_touch_screen     = array_get($request, 'teclado_teclado_lateral_touch_screen');
		$hooper                                   = array_get($request, 'hooper');
		$monitor_pantalla                         = array_get($request, 'monitor_pantalla');
		$fascia                                   = array_get($request, 'fascia');
		
		$se_realizo_planchado_sw                  = array_get($request, 'se_realizo_planchado_sw');
		$version_instalada                        = array_get($request, 'version_instalada');
		
		$checker_visible                          = array_get($request, 'checker_visible');
		$activacion_checker                       = array_get($request, 'activacion_checker');
		$nombre_activa_checker                    = array_get($request, 'nombre_activa_checker');
		$csds_visible                             = array_get($request, 'csds_visible');
		
		$cierre_inmuebles                         = array_get($request, 'cierre_inmuebles');
		$revision_correcion_de_voltajes           = array_get($request, 'revision_correcion_de_voltajes');
		$mejoramiento_de_imagen_limpieza          = array_get($request, 'mejoramiento_de_imagen_limpieza');
		$revision_correccion_instalacion_de_aa    = array_get($request, 'revision_correccion_instalacion_de_aa');
		$requiere_ups                             = array_get($request, 'requiere_ups');
		
		$cierre_cableado                          = array_get($request, 'cierre_cableado');
		$correcciones_red_interna                 = array_get($request, 'correcciones_red_interna');
		$revision_de_cableado_y_status_de_equipos = array_get($request, 'revision_de_cableado_y_status_de_equipos');
		$retiro_y_o_limpieza_de_equipos           = array_get($request, 'retiro_y_o_limpieza_de_equipos');
		
		$cierre_comunicaciones                    = array_get($request, 'cierre_comunicaciones');
		$revision_enlace_equipos                  = array_get($request, 'revision_enlace_equipos');
		$prueba_de_calidad                        = array_get($request, 'prueba_de_calidad');
		
		$cierre_interventor                       = array_get($request, 'cierre_interventor');
		
		$cuenta_con_reporte_fotografico           = array_get($request, 'cuenta_con_reporte_fotografico');
		$requiere_calcomanias                     = array_get($request, 'requiere_calcomanias');
		$requiere_mejoramiento_de_imagen          = array_get($request, 'requiere_mejoramiento_de_imagen');
		$alta_datos_odt_id                        = array_get($request, 'alta_datos_odt_id');
		$alta_datos_odt_id                        = $alta_datos_odt_id * 1;

		$aplican_acciones_de_comunicaciones_analisis     = array_get($request, 'aplican_acciones_de_comunicaciones_analisis');
		$aplican_acciones_de_cableado_analisis           = array_get($request, 'aplican_acciones_de_cableado_analisis');
		$aplican_acciones_de_inmuebles_analisis          = array_get($request, 'aplican_acciones_de_inmuebles_analisis');
		$aplica_la_asistencia_de_un_interventor_analisis = array_get($request, 'aplica_la_asistencia_de_un_interventor_analisis');

		$now                                      = Carbon::now('America/Mexico_City');

		if($cierre_idc && $vandalismo && $mant_modulo_dispensador && $cambio_presentador_stacker && $cambio_consumibles && $cambio_pick_picker_estractor && 
			$cambio_caseteros && $otro_1_dispensador && $otro_2_dispensador && $mant_modulo_cpu && $cambio_dd && $cambio_cpu && $mant_modulo_lectora 
			&& $cambio_lectora && $mant_modulo_impresora && $cambio_impresora && $fuente_de_poder && $teclado_teclado_lateral_touch_screen && $hooper 
			&& $monitor_pantalla && $fascia && $se_realizo_planchado_sw  && $checker_visible && $activacion_checker && $nombre_activa_checker 
			&& $csds_visible && $cuenta_con_reporte_fotografico && $requiere_calcomanias && $requiere_mejoramiento_de_imagen){
				$vgeneral=true;
				if($vandalismo == 'SI'){
					if($modulo_vandalizado && $fecha_escalamiento_dar){
						$vgeneral=true;
					}else{
						$vgeneral=false;
					}
				}
				if($vandalismo == 'SI' && $modulo_vandalizado == 'OTRO' ){
					if($otro_especificar){
						$vgeneral=true;
					}else{
						$vgeneral=false;
					}
				}
				if($aplican_acciones_de_inmuebles_analisis == 'SI' ){
					if($cierre_inmuebles && $revision_correcion_de_voltajes && $mejoramiento_de_imagen_limpieza && $revision_correccion_instalacion_de_aa
		               && $requiere_ups){
						$vgeneral=true;
					}else{
						$vgeneral=false;
					}
				}
				if($aplican_acciones_de_cableado_analisis == 'SI' ){
					if($cierre_cableado && $correcciones_red_interna && $revision_de_cableado_y_status_de_equipos && $retiro_y_o_limpieza_de_equipos){
						$vgeneral=true;
					}else{
						$vgeneral=false;
					}
				}
				if($aplican_acciones_de_comunicaciones_analisis == 'SI' ){
					if($cierre_comunicaciones && $revision_enlace_equipos && $prueba_de_calidad){
						$vgeneral=true;
					}else{
						$vgeneral=false;
					}
				}
				/*if($aplica_la_asistencia_de_un_interventor_analisis == 'SI, CON ALCANCE' || $aplica_la_asistencia_de_un_interventor_analisis == 'SI, FUERA DE ALCANCE' ){
					if($cierre_interventor){
						$vgeneral=true;
					}else{
						$vgeneral=false;
					}
				}*/
		}else{
				$vgeneral=false;
		}
		
		if($vgeneral){
			$status = 'Completado';
		}else{
			$status = 'Cierre Pendiente';
		}

        $data_id = DB::table('cierres')->where('id', $cierre_id)->update(array(
        	//'atm'                                      => $atm,
			//'que_se_mide'                              => $que_se_mide,

			'cierre_idc'                               => $cierre_idc,
			'vandalismo'                               => $vandalismo,
			'modulo_vandalizado'                       => $modulo_vandalizado,
			'otro_especificar'                         => $otro_especificar,
			'fecha_escalamiento_dar'                   => $fecha_escalamiento_dar,
			
			'mant_modulo_dispensador'                  => $mant_modulo_dispensador,
			'cambio_presentador_stacker'               => $cambio_presentador_stacker,
			'cambio_consumibles'                       => $cambio_consumibles,
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
			'cambio_tarjeta_cotroladora'               => $cambio_tarjeta_cotroladora,
			'otro_1_aceptador'                         => $otro_1_aceptador,
			'nombre_pieza_otro_1_aceptador'            => $nombre_pieza_otro_1_aceptador,
			'otro_2_aceptador'                         => $otro_2_aceptador,
			'nombre_pieza_otro_2_aceptador'            => $nombre_pieza_otro_2_aceptador,
			
			'mant_modulo_cpu'                          => $mant_modulo_cpu,
			'cambio_dd'                                => $cambio_dd,
			'cambio_cpu'                               => $cambio_cpu,
			
			'mant_modulo_lectora'                      => $mant_modulo_lectora,
			'cambio_lectora'                           => $cambio_lectora,
			
			'mant_modulo_impresora'                    => $mant_modulo_impresora,
			'cambio_impresora'                         => $cambio_impresora,
			
			'fuente_de_poder'                          => $fuente_de_poder,
			'teclado_teclado_lateral_touch_screen'     => $teclado_teclado_lateral_touch_screen,
			'hooper'                                   => $hooper,
			'monitor_pantalla'                         => $monitor_pantalla,
			'fascia'                                   => $fascia,
			
			'se_realizo_planchado_sw'                  => $se_realizo_planchado_sw,
			'version_instalada'                        => $version_instalada,
			
			'checker_visible'                          => $checker_visible,
			'activacion_checker'                       => $activacion_checker,
			'nombre_activa_checker'                    => $nombre_activa_checker,
			'csds_visible'                             => $csds_visible,
			
			'cierre_inmuebles'                         => $cierre_inmuebles,
			'revision_correcion_de_voltajes'           => $revision_correcion_de_voltajes,
			'mejoramiento_de_imagen_limpieza'          => $mejoramiento_de_imagen_limpieza,
			'revision_correccion_instalacion_de_aa'    => $revision_correccion_instalacion_de_aa,
			'requiere_ups'                             => $requiere_ups,
			
			'cierre_cableado'                          => $cierre_cableado,
			'correcciones_red_interna'                 => $correcciones_red_interna,
			'revision_de_cableado_y_status_de_equipos' => $revision_de_cableado_y_status_de_equipos,
			'retiro_y_o_limpieza_de_equipos'           => $retiro_y_o_limpieza_de_equipos,
			
			'cierre_comunicaciones'                    => $cierre_comunicaciones,
			'revision_enlace_equipos'                  => $revision_enlace_equipos,
			'prueba_de_calidad'                        => $prueba_de_calidad,
			
			'cierre_interventor'                       => $cierre_interventor,
			
			'cuenta_con_reporte_fotografico'           => $cuenta_con_reporte_fotografico,
			'requiere_calcomanias'                     => $requiere_calcomanias,
			'requiere_mejoramiento_de_imagen'          => $requiere_mejoramiento_de_imagen,
			
			'user_name'                                => Auth::user()->name,
			'status'                                   => $status,
			//'created_at'                               => $now,
			'updated_at'                               => $now,
        ));

        DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
			'status'     => $status,
			'cierre_id'  => $cierre_id,
			'updated_at' => $now,
        ));


        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha editado un item en la tabla cierres con id ' . $cierre_id ,
            'created_at'    => $now,
            'updated_at'    => $now
        ));

        return JsonResponse::singleResponse(["message" => "Info insertada" , 
          //"Data" => $data_id, 
        ]);
    }

    public function updateCierresCompletado(Request $request, $cierre_id){

    	$now = Carbon::now('America/Mexico_City')->subHour();
		$cierre_idc = array_get($request, 'cierre_idc');
		$cierre_idc = str_replace(["\r", "\n"], "", $cierre_idc);

		$data_id = DB::table('cierres')->where('id', $cierre_id)->update(array(
			'cierre_idc' => $cierre_idc,
			'updated_at' => $now,
        ));
    	$this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha editado el cierre idc en la tabla cierres con id ' . $cierre_id ,
            'created_at'    => $now,
            'updated_at'    => $now
        ));

        return JsonResponse::singleResponse(["message" => "Info insertada" , 
          //"Data" => $data_id, 
        ]);
    }

    public function downloadReporteCierres($fecha_inicio, $fecha_fin){

        $now = Carbon::now('America/Mexico_City');

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha descargado un excel de la tabla cierres',
            'created_at'    => $now,
            'updated_at'    => $now
        ));

      //aqui empieza el ciclo

        $data = DB::table('cierres')
                    ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
                    ->get();
        
        $tot_record_found = 0;
        if ($data != null || $data != '') {
            $tot_record_found = 1;

            $CsvData = array('ATM, QUE SE MIDE, MODULO VANDALIZADO, OTRO ESPECIFICAR, FECHA ESCALAMIENTO DAR, MANT MODULO DISPENSADOR,CAMBIO PRESENTADOR STACKER, CAMBIO CONSUMIBLES, CAMBIO PICK PICKER ESTRACTOR, CAMBIO CASETEROS, OTRO 1 DISPENSADOR, NOMBRE PIEZA 1 OTRO DISPENSADOR, OTRO 2 DISPENSADOR, NOMBRE PIEZA 2 OTRO DISPENSADOR, MANT MODULO ACEPTADOR, CAMBIO ESCROW, CAMBIO VALIDADOR, CAMBIO SHUTTER CASH SLOT, CAMBIO TARJETA CONTROLADORA, OTRO 1 ACEPTADOR, NOMBRE PIEZA 1 OTRO ACEPTADOR, OTRO 2 ACEPTADOR, NOMBRE PIEZA 2 OTRO ACEPTADOR, MANT MODULO CPU, CAMBIO DD, CAMBIO CPU, MANT MODULO LECTURA, CAMBIO LECTORA, MANT MODULO IMPRESORA, CAMBIO IMPRESORA, FUENTE DE PODER, TECLADO LATERAL TOUCH SCREEN, HOOPER, MONITOR PANTALLA, FASCIA, SE REALIZO PLANCHADO SW, VERSION INSTALADA, CHECKER VISIBLE, ACTIVACION CHECKER, NOMBRE ACTIVAR CHECKER, CSDS VISIBLE, CIERRE INMUEBLES, REVISION CORRECION DE VOLTAJES, MEJORAMIENTO DE IMAGEN LIMPIEZA, REVISION CORRECCION INSTALACION DE AA, REQUIERE UPS, CIERRE CABLEADO, CORRECION RED INTERNA, REVISION DE CABLEDO Y ESTATUS DE EQUIPO, RETIRO Y O ESTATUS DE EQUIPOS, CIERRE COMUNICACIONES, REVISION ENLACE DE EQUIPOS, PRUEBA DE CALIDAD, CIERRE INTERVENTOR, CUENTA CON REPORTE FOTOGRAFICO, REQUIERE CALCOMANIAS, REQUIERE MEJORAMIENTO DE IMAGEN, ESTATUS, USUARIO, FECHA DE CREACION, CIERRE IDC, VANDALISMO');
            foreach ($data as $value) {

				$atm                                      = str_replace(',', '', $value->atm);
				$que_se_mide                              = str_replace(',', '', $value->que_se_mide);
				$modulo_vandalizado                       = str_replace(',', '', $value->modulo_vandalizado);
				$otro_especificar                         = str_replace(',', '', $value->otro_especificar);
				$fecha_escalamiento_dar                   = str_replace(',', '', $value->fecha_escalamiento_dar);
				$mant_modulo_dispensador                  = str_replace(',', '', $value->mant_modulo_dispensador);
				$cambio_presentador_stacker               = str_replace(',', '', $value->cambio_presentador_stacker);
				$cambio_consumibles                       = str_replace(',', '', $value->cambio_consumibles);
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
				$cambio_tarjeta_cotroladora               = str_replace(',', '', $value->cambio_tarjeta_cotroladora);
				$otro_1_aceptador                         = str_replace(',', '', $value->otro_1_aceptador);
				$nombre_pieza_otro_1_aceptador            = str_replace(',', '', $value->nombre_pieza_otro_1_aceptador);
				$otro_2_aceptador                         = str_replace(',', '', $value->otro_2_aceptador);
				$nombre_pieza_otro_2_aceptador            = str_replace(',', '', $value->nombre_pieza_otro_2_aceptador);
				$mant_modulo_idc_cpu                      = str_replace(',', '', $value->mant_modulo_cpu);
				$cambio_dd                                = str_replace(',', '', $value->cambio_dd);
				$cambio_cpu                               = str_replace(',', '', $value->cambio_cpu);
				$mant_modulo_lectora                      = str_replace(',', '', $value->mant_modulo_lectora);
				$cambio_lectora                           = str_replace(',', '', $value->cambio_lectora);
				$mant_modulo_impresora                    = str_replace(',', '', $value->mant_modulo_impresora);
				$cambio_impresora                         = str_replace(',', '', $value->cambio_impresora);
				$fuente_de_poder                          = str_replace(',', '', $value->fuente_de_poder);
				$teclado_teclado_lateral_touch_screen     = str_replace(',', '', $value->teclado_teclado_lateral_touch_screen);
				$hopper                                   = str_replace(',', '', $value->hooper);
				$monitor_pantalla                         = str_replace(',', '', $value->monitor_pantalla);
				$fascia                                   = str_replace(',', '', $value->fascia);
				$se_requiere_planchado_de_sw              = str_replace(',', '', $value->se_realizo_planchado_sw);
				$ver_requerida                            = str_replace(',', '', $value->version_instalada);
				$checker_visible                          = str_replace(',', '', $value->checker_visible);
				$activacion_checker                       = str_replace(',', '', $value->activacion_checker);
				$nombre_activa_checker                    = str_replace(',', '', $value->nombre_activa_checker);
				$csds_visible                             = str_replace(',', '', $value->csds_visible);
				$cierre_inmuebles                         = str_replace(',', '', $value->cierre_inmuebles);
				$revision_correcion_de_voltajes           = str_replace(',', '', $value->revision_correcion_de_voltajes);
				$mejoramiento_de_imagen_limpieza          = str_replace(',', '', $value->mejoramiento_de_imagen_limpieza);
				$revision_correccion_instalacion_de_aa    = str_replace(',', '', $value->revision_correccion_instalacion_de_aa);
				$requiere_ups                             = str_replace(',', '', $value->requiere_ups);
				$cierre_cableado                          = str_replace(',', '', $value->cierre_cableado);
				$correcciones_red_interna                 = str_replace(',', '', $value->correcciones_red_interna);
				$revision_de_cableado_y_status_de_equipos = str_replace(',', '', $value->revision_de_cableado_y_status_de_equipos);
				$retiro_y_o_limpieza_de_equipos           = str_replace(',', '', $value->retiro_y_o_limpieza_de_equipos);
				$cierre_comunicaciones                    = str_replace(',', '', $value->cierre_comunicaciones);
				$revision_enlace_equipos                  = str_replace(',', '', $value->revision_enlace_equipos);
				$prueba_de_calidad                        = str_replace(',', '', $value->prueba_de_calidad);
				$cierre_interventor                       = str_replace(',', '', $value->cierre_interventor);
				$cuenta_con_reporte_fotografico           = str_replace(',', '', $value->cuenta_con_reporte_fotografico);
				$requiere_calcomanias                     = str_replace(',', '', $value->requiere_calcomanias);
				$requiere_mejoramiento_de_imagen          = str_replace(',', '', $value->requiere_mejoramiento_de_imagen);
				$status                                   = str_replace(',', '', $value->status);
				$user_name                                = str_replace(',', '', $value->user_name);
				$created_at                               = str_replace(',', '', $value->created_at);
				$cierre_idc                               = str_replace(',', '', $value->cierre_idc);
				$cierre_idc                               = str_replace(["\r", "\n"], "", $cierre_idc);
				$vandalismo                               = str_replace(',', '', $value->vandalismo);

              

                $CsvData[] = $atm . ',' . $que_se_mide . ',' . $modulo_vandalizado . ',' . $otro_especificar . ',' . $fecha_escalamiento_dar . ',' . $mant_modulo_dispensador . ',' . $cambio_presentador_stacker . ',' . $cambio_consumibles . ',' . $cambio_pick_picker_estractor . ',' . $cambio_caseteros . ',' . $otro_1_dispensador . ',' . $nombre_pieza_otro_1_dispensador . ',' . $otro_2_dispensador . ',' . $nombre_pieza_otro_2_dispensador . ',' . $mant_modulo_aceptador . ',' . $cambio_escrow . ',' . $cambio_validador . ',' . $cambio_shutter_cash_slot . ',' . $cambio_tarjeta_cotroladora . ',' . $otro_1_aceptador . ',' . $nombre_pieza_otro_1_aceptador . ',' . $otro_2_aceptador  . ',' . $nombre_pieza_otro_2_aceptador . ',' . $mant_modulo_idc_cpu . ',' . $cambio_dd . ',' . $cambio_cpu . ',' . $mant_modulo_lectora . ',' . $cambio_lectora . ',' . $mant_modulo_impresora . ',' . $cambio_impresora . ',' . $fuente_de_poder . ',' . $teclado_teclado_lateral_touch_screen . ',' . $hopper . ',' . $monitor_pantalla . ',' . $fascia . ',' . $se_requiere_planchado_de_sw . ',' . $ver_requerida . ',' . $checker_visible . ',' . $activacion_checker . ',' . $nombre_activa_checker . ',' . $csds_visible . ',' . $cierre_inmuebles . ',' . $revision_correcion_de_voltajes . ',' . $mejoramiento_de_imagen_limpieza . ',' . $revision_correccion_instalacion_de_aa . ',' . $requiere_ups  . ',' . $cierre_cableado . ',' . $correcciones_red_interna . ',' . $revision_de_cableado_y_status_de_equipos . ',' . $retiro_y_o_limpieza_de_equipos . ',' . $cierre_comunicaciones . ',' . $revision_enlace_equipos . ',' . $prueba_de_calidad . ',' . $cierre_interventor . ',' . $cuenta_con_reporte_fotografico . ',' . $requiere_calcomanias . ',' . $requiere_mejoramiento_de_imagen . ',' . $status . ',' . $user_name . ',' . $created_at . ',' . $cierre_idc . ',' . $vandalismo;
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
}