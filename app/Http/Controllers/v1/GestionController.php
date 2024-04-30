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

class GestionController extends Controller
{
    use JWTTrait;

    protected $internal;

    public function __construct(Internal $internal)
    {
        $this->internal = $internal;
    }

    public function updateHorariosGestion(Request $request, $gestion_id){

		$arribo_idc                        = array_get($request, 'arribo_idc');
		$etv_hora_apertura_boveda          = array_get($request, 'etv_hora_apertura_boveda');
		$etv_hora_modo_supervisor          = array_get($request, 'etv_hora_modo_supervisor');
		$now                               = Carbon::now('America/Mexico_City')->subHour();
		$atm_queda_en_servicio             = array_get($request, 'atm_queda_en_servicio');
		$status_finalizacion_del_odt       = array_get($request, 'status_finalizacion_del_odt');
		$detalle_de_desviaciones           = array_get($request, 'detalle_de_desviaciones');
		$pieza_por_la_cual_quedo_pendiente = array_get($request, 'pieza_por_la_cual_quedo_pendiente');
		$especifica_la_otra_pieza          = array_get($request, 'especifica_la_otra_pieza');
		
		$arribo_interventor               = array_get($request, 'arribo_interventor');
		$empresa_dio_acceso               = array_get($request, 'empresa_dio_acceso');
		$etv_hora_consulta_administrativa = array_get($request, 'etv_hora_consulta_administrativa');
		$horario_pactado_de_regreso_etv   = array_get($request, 'horario_pactado_de_regreso_etv');
		$horario_llegada_de_regreso_etv   = array_get($request, 'horario_llegada_de_regreso_etv');
		$hora_termino_de_la_visita        = array_get($request, 'hora_termino_de_la_visita');
		
		$arribo_inmuebles                 = array_get($request, 'arribo_inmuebles');
		$arribo_cableado                  = array_get($request, 'arribo_cableado');
		$arribo_comunicaciones            = array_get($request, 'arribo_comunicaciones');

		$aplica_la_asistencia_de_un_interventor_gestion = array_get($request, 'aplica_la_asistencia_de_un_interventor_gestion');
		$etv_fecha_confirmacion_cita_gestion            = array_get($request, 'etv_fecha_confirmacion_cita_gestion');
		$empresa_de_inmuebles_gestion                   = array_get($request, 'empresa_de_inmuebles_gestion');
		$empresa_de_cableado_gestion                    = array_get($request, 'empresa_de_cableado_gestion');
		$empresa_de_comunicaciones_gestion              = array_get($request, 'empresa_de_comunicaciones_gestion');

		$se_requiere_reprogramacion = array_get($request, 'se_requiere_reprogramacion');
		$fecha_reprogramacion_etv   = array_get($request, 'fecha_reprogramacion_etv');
		$hora_llegada_etv           = array_get($request, 'hora_llegada_etv');
		$hora_fin_servicio          = array_get($request, 'hora_fin_servicio');
		$horario_termino_mantto_idc = array_get($request, 'horario_termino_mantto_idc');
		$comentarios_desviacion_etv = array_get($request, 'comentarios_desviacion_etv');

		$currentDate = now();
		$currentDate = date('Y-m-d');

		if($hora_llegada_etv != null || $hora_llegada_etv != '' ){
			$hora_llegada_etv = $currentDate . ' ' . $hora_llegada_etv;
		}else{
			$hora_llegada_etv = null;
		}

		if($hora_fin_servicio != null || $hora_fin_servicio != '' ){
			$hora_fin_servicio = $currentDate . ' ' . $hora_fin_servicio;
		}else{
			$hora_fin_servicio = null;
		}

		if($horario_termino_mantto_idc != null || $horario_termino_mantto_idc != '' ){
			$horario_termino_mantto_idc = $currentDate . ' ' . $horario_termino_mantto_idc;
		}else{
			$horario_termino_mantto_idc = null;
		}

        DB::table('gestion')->where('id', $gestion_id)->update(array(
			'arribo_idc'                        => $arribo_idc,
			'etv_hora_apertura_boveda'          => $etv_hora_apertura_boveda,
			'etv_hora_modo_supervisor'          => $etv_hora_modo_supervisor,
			'updated_at'                        => $now,
			'atm_queda_en_servicio'             => $atm_queda_en_servicio,
			'status_finalizacion_del_odt'       => $status_finalizacion_del_odt,
			'detalle_de_desviaciones'           => $detalle_de_desviaciones,
			'pieza_por_la_cual_quedo_pendiente' => $pieza_por_la_cual_quedo_pendiente,
			'especifica_la_otra_pieza'          => $especifica_la_otra_pieza,
			
			'arribo_interventor'                => $arribo_interventor,
			'empresa_dio_acceso'                => $empresa_dio_acceso,
			'etv_hora_consulta_administrativa'  => $etv_hora_consulta_administrativa,
			'horario_pactado_de_regreso_etv'    => $horario_pactado_de_regreso_etv,
			'horario_llegada_de_regreso_etv'    => $horario_llegada_de_regreso_etv,
			'hora_termino_de_la_visita'         => $hora_termino_de_la_visita,
			
			'arribo_inmuebles'                  => $arribo_inmuebles,
			'arribo_cableado'                   => $arribo_cableado,
			'arribo_comunicaciones'             => $arribo_comunicaciones,

			'se_requiere_reprogramacion' => $se_requiere_reprogramacion,
			'fecha_reprogramacion_etv'   => $fecha_reprogramacion_etv,
			'hora_llegada_etv'           => $hora_llegada_etv,
			'hora_fin_servicio'          => $hora_fin_servicio,
			'horario_termino_mantto_idc' => $horario_termino_mantto_idc,
			'comentarios_desviacion_etv' => $comentarios_desviacion_etv
        ));

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha editado un horario en la gestion ' . $gestion_id,
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info actualizada" , 
        ]);
    }

    public function updateEstatusGestion(Request $request, $gestion_id){

    	$status_visita = array_get($request, 'status_visita');
    	$now           = Carbon::now('America/Mexico_City');

        DB::table('gestion')->where('id', $gestion_id)->update(array(
			'status_visita'                     => $status_visita,
			'updated_at'                        => $now,
        ));

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha editado un estatus de visita es la gestion ' . $gestion_id,
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info actualizada" , 
        ]);
    }

    public function getStatusVisita(){

        $data = DB::table('status_visita')->get();
        
        return JsonResponse::singleResponse(["message" => "Info encontrada" , 
          "Data" => $data, 
        ]);
    }
    public function getTipoVisitas( $tipo_visita){

        $data = DB::table('status_visita')->where('tipo_visita',$tipo_visita)->get();
        
        return JsonResponse::singleResponse(["message" => "Info encontrada" , 
          "Data" => $data, 
        ]);
    }

    public function getPiezaPendiente(){

        $data = DB::table('piezas_pendientes')->get();
        
        return JsonResponse::singleResponse(["message" => "Info encontrada" , 
          "Data" => $data, 
        ]);
    }

    public function createGestion(Request $request){

		$atm                               = array_get($request, 'atm');
		$que_se_mide                       = array_get($request, 'que_se_mide');
		$nombre_del_gestor                 = array_get($request, 'nombre_del_gestor');
		$status_visita                     = array_get($request, 'status_visita');
		$arribo_interventor                = array_get($request, 'arribo_interventor');
		$arribo_idc                        = array_get($request, 'arribo_idc');
		$empresa_dio_acceso                = array_get($request, 'empresa_dio_acceso');
		$nombre_ing_dedicado_critico       = array_get($request, 'nombre_ing_dedicado_critico');
		$asistio_ing_dedicado              = array_get($request, 'asistio_ing_dedicado');
		$nombre_ing_sustituto              = array_get($request, 'nombre_ing_sustituto');
		$etv_hora_apertura_boveda          = array_get($request, 'etv_hora_apertura_boveda');
		$etv_hora_modo_supervisor          = array_get($request, 'etv_hora_modo_supervisor');
		$etv_hora_consulta_administrativa  = array_get($request, 'etv_hora_consulta_administrativa');
		$arribo_inmuebles                  = array_get($request, 'arribo_inmuebles');
		$nombre_tecnico_inmuebles          = array_get($request, 'nombre_tecnico_inmuebles');
		$arribo_cableado                   = array_get($request, 'arribo_cableado');
		$nombre_tecnico_cableado           = array_get($request, 'nombre_tecnico_cableado');
		$arribo_comunicaciones             = array_get($request, 'arribo_comunicaciones');
		$hora_termino_de_la_visita         = array_get($request, 'hora_termino_de_la_visita');
		$atm_queda_en_servicio             = array_get($request, 'atm_queda_en_servicio');
		$status_finalizacion_del_odt       = array_get($request, 'status_finalizacion_del_odt');
		$detalle_de_desviaciones           = array_get($request, 'detalle_de_desviaciones');
		$pieza_por_la_cual_quedo_pendiente = array_get($request, 'pieza_por_la_cual_quedo_pendiente');
		$especifica_la_otra_pieza          = array_get($request, 'especifica_la_otra_pieza');
		$comentarios                       = array_get($request, 'comentarios');
		$alta_datos_odt_id                 = array_get($request, 'alta_datos_odt_id');
		$now                               = Carbon::now('America/Mexico_City');

		$aplica_la_asistencia_de_un_interventor_gestion = array_get($request, 'aplica_la_asistencia_de_un_interventor_gestion');
		$etv_fecha_confirmacion_cita_gestion            = array_get($request, 'etv_fecha_confirmacion_cita_gestion');
		$empresa_de_inmuebles_gestion                   = array_get($request, 'empresa_de_inmuebles_gestion');
		$empresa_de_cableado_gestion                    = array_get($request, 'empresa_de_cableado_gestion');
		$empresa_de_comunicaciones_gestion              = array_get($request, 'empresa_de_comunicaciones_gestion');

		$horario_pactado_de_regreso_etv  = array_get($request, 'horario_pactado_de_regreso_etv');
		$horario_llegada_de_regreso_etv  = array_get($request, 'horario_llegada_de_regreso_etv');

		$se_requiere_reprogramacion = array_get($request, 'se_requiere_reprogramacion');
		$fecha_reprogramacion_etv   = array_get($request, 'fecha_reprogramacion_etv');
		$hora_llegada_etv           = array_get($request, 'hora_llegada_etv');
		$hora_fin_servicio          = array_get($request, 'hora_fin_servicio');
		$horario_termino_mantto_idc = array_get($request, 'horario_termino_mantto_idc');
		$comentarios_desviacion_etv = array_get($request, 'comentarios_desviacion_etv');

		$currentDate = now();
		$currentDate = date('Y-m-d');

		if($hora_llegada_etv != null || $hora_llegada_etv != '' ){
			$hora_llegada_etv = $currentDate . ' ' . $hora_llegada_etv;
		}else{
			$hora_llegada_etv = null;
		}

		if($hora_fin_servicio != null || $hora_fin_servicio != '' ){
			$hora_fin_servicio = $currentDate . ' ' . $hora_fin_servicio;
		}else{
			$hora_fin_servicio = null;
		}

		if($horario_termino_mantto_idc != null || $horario_termino_mantto_idc != '' ){
			$horario_termino_mantto_idc = $currentDate . ' ' . $horario_termino_mantto_idc;
		}else{
			$horario_termino_mantto_idc = null;
		}

		if($status_visita == 'BAJA DEFINITIVA' || $status_visita == 'BAJA TEMPORAL' || $status_visita == 'CAMBIO DE PROVEEDOR' || 
                    $status_visita == 'CANCELADO ETV' || $status_visita == 'CANCELADO IDC' || $status_visita == 'CANCELADO IDC Y ETV' ||
                    $status_visita == 'CANCELADO SE' || $status_visita == 'DESASTRES NATURALES' || $status_visita == 'EN REUBICACION' || 
                    $status_visita == 'SIN ACCESO POR CLIENTE' || $status_visita == 'SIN ACCESO POR ETV' || 
                    $status_visita == 'SIN ACCESO POR INTERVENTOR' || $status_visita == 'SIN ACCESO POR IDC' || $status_visita == 'RENOVACION TECNOLOGICA' 
                    || $status_visita == 'VANDALISMO MENOR' || $status_visita == 'VANDALISMO MAYOR'){

			if($status_finalizacion_del_odt && $comentarios){
				$vgeneral = true;
			}else{
				$vgeneral = false;
			}

		}else{
			$vgeneral = true;
		}


		if($status_visita == 'NO LLEGO ETV'){

			if($aplica_la_asistencia_de_un_interventor_gestion== 'SI, CON ALCANCE' || $aplica_la_asistencia_de_un_interventor_gestion == 'SI, FUERA DE ALCANCE'){
				if($arribo_interventor && $arribo_idc && $empresa_dio_acceso && $nombre_ing_dedicado_critico && $asistio_ing_dedicado && $nombre_ing_sustituto && $status_finalizacion_del_odt && $comentarios){
					$vno_llego_etv = true;
				}else{
					$vno_llego_etv = false;
				}
			}else{
				if($arribo_idc && $empresa_dio_acceso && $nombre_ing_dedicado_critico && $asistio_ing_dedicado && $nombre_ing_sustituto && $status_finalizacion_del_odt && $comentarios){
					$vno_llego_etv = true;
				}else{
					$vno_llego_etv = false;
				}
			}
		}else{
			$vno_llego_etv = true;
		}

		if($status_visita == 'NO LLEGO INTERVENTOR'){
			if($arribo_idc && $empresa_dio_acceso && $nombre_ing_dedicado_critico && $asistio_ing_dedicado && $nombre_ing_sustituto && $etv_hora_apertura_boveda && $etv_hora_modo_supervisor && $etv_hora_consulta_administrativa && $hora_termino_de_la_visita && $atm_queda_en_servicio && $status_finalizacion_del_odt && $comentarios ){
					$vno_llego_interventor=true;
			}else{
					$vno_llego_interventor=false;
			}	
		}else{
			$vno_llego_interventor=true;
		}

		if($status_visita == 'NO LLEGO INTERVENTOR' && $vno_llego_interventor){
			if($empresa_de_inmuebles_gestion=="SI"){
				if(!$arribo_inmuebles || !$nombre_tecnico_inmuebles){
					$vno_llego_interventor=false;
				}
			}
		}
		if($status_visita == 'NO LLEGO INTERVENTOR' && $vno_llego_interventor){
			if($empresa_de_cableado_gestion=="SI"){
				if(!$arribo_cableado || !$nombre_tecnico_cableado){
					$vno_llego_interventor=false;
				}
			}
		}
		if($status_visita == 'NO LLEGO INTERVENTOR' && $vno_llego_interventor){
			if($empresa_de_comunicaciones_gestion=="SI"){
				if(!$arribo_comunicaciones){
					$vno_llego_interventor=false;
				}
			}
		}
		if ($status_visita == 'NO LLEGO INTERVENTOR' && $vno_llego_interventor){
			if($status_finalizacion_del_odt=='NO SE FINALIZA, PENDIENTE POR PIEZA SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR PIEZA NO SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR CARGA DE SW' || $status_finalizacion_del_odt == 'NO SE FINALIZA, FALLA NO SOLUCIONADA' || $status_finalizacion_del_odt == 'NO SE FINALIZA, POR DOTACION'){
				if(!$detalle_de_desviaciones){
					$vno_llego_interventor=false;
				}
			}
		}

		if ($status_visita == 'NO LLEGO INTERVENTOR' && $vno_llego_interventor){
			if($status_finalizacion_del_odt=='NO SE FINALIZA, PENDIENTE POR PIEZA SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR PIEZA NO SOLICTADA EN ANALISIS'){
				if(!$pieza_por_la_cual_quedo_pendiente || !$especifica_la_otra_pieza){
					$vno_llego_interventor=false;
				}
			}
		}

		if($status_visita == 'NO LLEGO IDC'){
			if($etv_hora_apertura_boveda && $etv_hora_modo_supervisor && $etv_hora_consulta_administrativa && $hora_termino_de_la_visita && $atm_queda_en_servicio && $status_finalizacion_del_odt && $comentarios ){
					$vno_llego_idc=true;
			}else{
					$vno_llego_idc=false;
			}	
		}else{
			$vno_llego_idc=true;
		}
		if($status_visita == 'NO LLEGO IDC' && $vno_llego_idc){
			if($empresa_de_inmuebles_gestion=="SI"){
				if(!$arribo_inmuebles || !$nombre_tecnico_inmuebles){
					$vno_llego_idc=false;
				}
			}
		}
		if($status_visita == 'NO LLEGO IDC' && $vno_llego_idc){
			if($empresa_de_cableado_gestion=="SI"){
				if(!$arribo_cableado || !$nombre_tecnico_cableado){
					$vno_llego_idc=false;
				}
			}
		}
		if($status_visita == 'NO LLEGO IDC' && $vno_llego_idc){
			if($empresa_de_comunicaciones_gestion=="SI"){
				if(!$arribo_comunicaciones){
					$vno_llego_idc=false;
				}
			}
		}
		if ($status_visita == 'NO LLEGO IDC' && $vno_llego_idc){
			if($status_finalizacion_del_odt=='NO SE FINALIZA, PENDIENTE POR PIEZA SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR PIEZA NO SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR CARGA DE SW' || $status_finalizacion_del_odt == 'NO SE FINALIZA, FALLA NO SOLUCIONADA' || $status_finalizacion_del_odt == 'NO SE FINALIZA, POR DOTACION'){
				if(!$detalle_de_desviaciones){
					$vno_llego_idc=false;
				}
			}
		}

		if ($status_visita == 'NO LLEGO IDC' && $vno_llego_idc){
			if($status_finalizacion_del_odt=='NO SE FINALIZA, PENDIENTE POR PIEZA SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR PIEZA NO SOLICTADA EN ANALISIS'){
				if(!$pieza_por_la_cual_quedo_pendiente || !$especifica_la_otra_pieza){
					$vno_llego_idc=false;
				}
			}
		}

		if($status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA INMUEBLES 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CABLEADO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || $status_visita == 'VISITA COMUNICACIONES 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 3' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3'){
			//quitamos arribo_idc
			/*if($arribo_idc && $empresa_dio_acceso && $hora_termino_de_la_visita && $atm_queda_en_servicio && $status_finalizacion_del_odt && $comentarios ){
					$vvisitaN=true;
			}else{
					$vvisitaN=false;
			}*/
			if($empresa_dio_acceso && $hora_termino_de_la_visita && $atm_queda_en_servicio && $status_finalizacion_del_odt && $comentarios ){
					$vvisitaN=true;
			}else{
					$vvisitaN=false;
			}	
		}else{
			$vvisitaN=true;
		}
		if(($status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA INMUEBLES 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CABLEADO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || $status_visita == 'VISITA COMUNICACIONES 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 3' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3') && $vvisitaN){
			if($empresa_de_inmuebles_gestion=="SI"){
				if(!$arribo_inmuebles || !$nombre_tecnico_inmuebles){
					$vvisitaN=false;
				}
			}
		}
		if(($status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA INMUEBLES 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CABLEADO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || $status_visita == 'VISITA COMUNICACIONES 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 3' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3') && $vvisitaN){
			if($empresa_de_cableado_gestion=="SI"){
				if(!$arribo_cableado || !$nombre_tecnico_cableado){
					$vvisitaN=false;
				}
			}
		}
		if(($status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA INMUEBLES 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CABLEADO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || $status_visita == 'VISITA COMUNICACIONES 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 3' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3') && $vvisitaN){
			if($empresa_de_comunicaciones_gestion=="SI"){
				if(!$arribo_comunicaciones){
					$vvisitaN=false;
				}
			}
		}
		if (($status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA INMUEBLES 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CABLEADO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || $status_visita == 'VISITA COMUNICACIONES 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 3' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3') && $vvisitaN){
			if($status_finalizacion_del_odt=='NO SE FINALIZA, PENDIENTE POR PIEZA SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR PIEZA NO SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR CARGA DE SW' || $status_finalizacion_del_odt == 'NO SE FINALIZA, FALLA NO SOLUCIONADA' || $status_finalizacion_del_odt == 'NO SE FINALIZA, POR DOTACION'){
				if(!$detalle_de_desviaciones){
					$vvisitaN=false;
				}
			}
		}

		if (($status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA INMUEBLES 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CABLEADO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || $status_visita == 'VISITA COMUNICACIONES 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 3' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3') && $vvisitaN){
			if($status_finalizacion_del_odt=='NO SE FINALIZA, PENDIENTE POR PIEZA SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR PIEZA NO SOLICTADA EN ANALISIS'){
				if(!$pieza_por_la_cual_quedo_pendiente || !$especifica_la_otra_pieza){
					$vvisitaN=false;
				}
			}
		}
		if (($status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA INMUEBLES 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CABLEADO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || $status_visita == 'VISITA COMUNICACIONES 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 3' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3') && $vvisitaN){
       		if($aplica_la_asistencia_de_un_interventor_gestion== 'SI, CON ALCANCE' || $aplica_la_asistencia_de_un_interventor_gestion == 'SI, FUERA DE ALCANCE'){
				if(!$arribo_interventor ){
					$vvisitaN=false;
				}
			}
		}

		if($vgeneral && $vno_llego_idc && $vno_llego_interventor && $vno_llego_etv && $vvisitaN){
			$status = 'Cierres';
		}else{
			$status = 'Gestion Pendiente';
		}

        $data_id = DB::table('gestion')->insertGetId(array(
			'atm'                               => $atm,
			'que_se_mide'                       => $que_se_mide,
			'nombre_del_gestor'                 => $nombre_del_gestor,
			'status_visita'                     => $status_visita,
			'arribo_interventor'                => $arribo_interventor,
			'arribo_idc'                        => $arribo_idc,
			'empresa_dio_acceso'                => $empresa_dio_acceso,
			'nombre_ing_dedicado_critico'       => $nombre_ing_dedicado_critico,
			'asistio_ing_dedicado'              => $asistio_ing_dedicado,
			'nombre_ing_sustituto'              => $nombre_ing_sustituto,
			'etv_hora_apertura_boveda'          => $etv_hora_apertura_boveda,
			'etv_hora_modo_supervisor'          => $etv_hora_modo_supervisor,
			'etv_hora_consulta_administrativa'  => $etv_hora_consulta_administrativa,
			'arribo_inmuebles'                  => $arribo_inmuebles,
			'nombre_tecnico_inmuebles'          => $nombre_tecnico_inmuebles,
			'arribo_cableado'                   => $arribo_cableado,
			'nombre_tecnico_cableado'           => $nombre_tecnico_cableado,
			'arribo_comunicaciones'             => $arribo_comunicaciones,
			'hora_termino_de_la_visita'         => $hora_termino_de_la_visita,
			'atm_queda_en_servicio'             => $atm_queda_en_servicio,
			'status_finalizacion_del_odt'       => $status_finalizacion_del_odt,
			'detalle_de_desviaciones'           => $detalle_de_desviaciones,
			'pieza_por_la_cual_quedo_pendiente' => $pieza_por_la_cual_quedo_pendiente,
			'especifica_la_otra_pieza'          => $especifica_la_otra_pieza,
			'comentarios'                       => $comentarios,

			'se_requiere_reprogramacion' => $se_requiere_reprogramacion,
			'fecha_reprogramacion_etv'   => $fecha_reprogramacion_etv,
			'hora_llegada_etv'           => $hora_llegada_etv,
			'hora_fin_servicio'          => $hora_fin_servicio,
			'horario_termino_mantto_idc' => $horario_termino_mantto_idc,
			'comentarios_desviacion_etv' => $comentarios_desviacion_etv,


			'user_name'                         => Auth::user()->name,
			'status'                            => $status,
			'horario_pactado_de_regreso_etv'    => $horario_pactado_de_regreso_etv,
			'horario_llegada_de_regreso_etv'    => $horario_llegada_de_regreso_etv,
			'created_at'                        => $now,
			'updated_at'                        => $now,
        ));

        //

        $item_caso = DB::select("SELECT caso from alta_datos_odt where id = $alta_datos_odt_id ");
        $caso = $item_caso[0]->caso;

		if (
			$status_visita == 'VISITA IDC 1' ||
			$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' ||
			$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' ||
			$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' ||
			$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' ||
			$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' ||
			$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' ||
			$status_visita == 'VISITA IDC 2' ||
			$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' ||
			$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' ||
			$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' ||
			$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' ||
			$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5'  ||
			$status_visita == 'VISITA IDC 3' ||
			$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' ||
			$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' ||
			$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' ||
			$status_visita == 'VISITA IDC 4' ||
			$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' ||
			$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' ||
			$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' ||
			$status_visita == 'VISITA IDC 5' ||
			$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' ||
			$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' ||
			$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || 
			$status_visita == 'VISITA INMUEBLES 1' || 
			$status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || 
			$status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || 
			$status_visita == 'VISITA INMUEBLES 2' || 
			$status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || 
			$status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || 
			$status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || 
			$status_visita == 'VISITA CABLEADO 1' || 
			$status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || 
			$status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || 
			$status_visita == 'VISITA CABLEADO 2' || 
			$status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || 
			$status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || 
			$status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || 
			$status_visita == 'VISITA COMUNICACIONES 1' || 
			$status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || 
			$status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || 
			$status_visita == 'VISITA COMUNICACIONES 2' || 
			$status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || 
			$status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || 
			$status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || 
			$status_visita == 'VISITA CONJUNTA 1' || 
			$status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || 
			$status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || 
			$status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || 
			$status_visita == 'VISITA CONJUNTA 2' || 
			$status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || 
			$status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || 
			$status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || 
			$status_visita == 'VISITA CONJUNTA 3' || 
			$status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || 
			$status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || 
			$status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || 
			$status_visita == 'VISITA CONJUNTA SIN IDC 1' || 
			$status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || 
			$status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || 
			$status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || 
			$status_visita == 'VISITA CONJUNTA SIN IDC 2' || 
			$status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || 
			$status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || 
			$status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || 
			$status_visita == 'VISITA CONJUNTA SIN IDC 3' || 
			$status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || 
			$status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || 
			$status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3'
		){
			$existe = DB::select("SELECT status_visita from gestion where id in 
			(select gestion_id   from alta_datos_odt where caso ='$caso' and date_format(fecha_seleccion,'%Y-%m')=date_format(NOW(),'%Y-%m')) and status_visita ='$status_visita';");
		}else{
			$existe=null;
		}
        
        if (!$existe){
        
	        DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
				'status'				   => $status,
				'gestion_id'			   => $data_id,
				'updated_at'               => $now,
	        ));

	        if($status_finalizacion_del_odt == 'NO SE REALIZO INTERVENCION'){
	        	$status = 'Completado';
				 DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
					'status'				   => $status,
					'updated_at'               => $now
		        ));

			}
		}else{
			return JsonResponse::errorResponse("No es posible guardar la gestion, el estatus de la visita se ha registrado anteriormente", 404);
		}
        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha creado un nuevo item en la tabla gestion',
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info insertada" , 
          "Data" => $data_id, 
        ]);
    }

    public function updateGestion(Request $request, $gestion_id){

		$atm                               = array_get($request, 'atm');
		$que_se_mide                       = array_get($request, 'que_se_mide');
		$nombre_del_gestor                 = array_get($request, 'nombre_del_gestor');
		$status_visita                     = array_get($request, 'status_visita');
		$arribo_interventor                = array_get($request, 'arribo_interventor');
		$arribo_idc                        = array_get($request, 'arribo_idc');
		$empresa_dio_acceso                = array_get($request, 'empresa_dio_acceso');
		$nombre_ing_dedicado_critico       = array_get($request, 'nombre_ing_dedicado_critico');
		$asistio_ing_dedicado              = array_get($request, 'asistio_ing_dedicado');
		$nombre_ing_sustituto              = array_get($request, 'nombre_ing_sustituto');
		$etv_hora_apertura_boveda          = array_get($request, 'etv_hora_apertura_boveda');
		$etv_hora_modo_supervisor          = array_get($request, 'etv_hora_modo_supervisor');
		$etv_hora_consulta_administrativa  = array_get($request, 'etv_hora_consulta_administrativa');
		$arribo_inmuebles                  = array_get($request, 'arribo_inmuebles');
		$nombre_tecnico_inmuebles          = array_get($request, 'nombre_tecnico_inmuebles');
		$arribo_cableado                   = array_get($request, 'arribo_cableado');
		$nombre_tecnico_cableado           = array_get($request, 'nombre_tecnico_cableado');
		$arribo_comunicaciones             = array_get($request, 'arribo_comunicaciones');
		$hora_termino_de_la_visita         = array_get($request, 'hora_termino_de_la_visita');
		$atm_queda_en_servicio             = array_get($request, 'atm_queda_en_servicio');
		$status_finalizacion_del_odt       = array_get($request, 'status_finalizacion_del_odt');
		$detalle_de_desviaciones           = array_get($request, 'detalle_de_desviaciones');
		$pieza_por_la_cual_quedo_pendiente = array_get($request, 'pieza_por_la_cual_quedo_pendiente');
		$especifica_la_otra_pieza          = array_get($request, 'especifica_la_otra_pieza');
		$comentarios                       = array_get($request, 'comentarios');
		$alta_datos_odt_id                 = array_get($request, 'alta_datos_odt_id');
		$now                               = Carbon::now('America/Mexico_City');

		$aplica_la_asistencia_de_un_interventor_gestion = array_get($request, 'aplica_la_asistencia_de_un_interventor_gestion');
		$etv_fecha_confirmacion_cita_gestion            = array_get($request, 'etv_fecha_confirmacion_cita_gestion');
		$empresa_de_inmuebles_gestion                   = array_get($request, 'empresa_de_inmuebles_gestion');
		$empresa_de_cableado_gestion                    = array_get($request, 'empresa_de_cableado_gestion');
		$empresa_de_comunicaciones_gestion              = array_get($request, 'empresa_de_comunicaciones_gestion');

		$horario_pactado_de_regreso_etv  = array_get($request, 'horario_pactado_de_regreso_etv');
		$horario_llegada_de_regreso_etv  = array_get($request, 'horario_llegada_de_regreso_etv');

		$se_requiere_reprogramacion = array_get($request, 'se_requiere_reprogramacion');
		$fecha_reprogramacion_etv   = array_get($request, 'fecha_reprogramacion_etv');
		$hora_llegada_etv           = array_get($request, 'hora_llegada_etv');
		$hora_fin_servicio          = array_get($request, 'hora_fin_servicio');
		$horario_termino_mantto_idc = array_get($request, 'horario_termino_mantto_idc');
		$comentarios_desviacion_etv = array_get($request, 'comentarios_desviacion_etv');

		$currentDate = now();
		$currentDate = date('Y-m-d');

		if($hora_llegada_etv != null || $hora_llegada_etv != '' ){
			$hora_llegada_etv = $currentDate . ' ' . $hora_llegada_etv;
		}else{
			$hora_llegada_etv = null;
		}

		if($hora_fin_servicio != null || $hora_fin_servicio != '' ){
			$hora_fin_servicio = $currentDate . ' ' . $hora_fin_servicio;
		}else{
			$hora_fin_servicio = null;
		}

		if($horario_termino_mantto_idc != null || $horario_termino_mantto_idc != '' ){
			$horario_termino_mantto_idc = $currentDate . ' ' . $horario_termino_mantto_idc;
		}else{
			$horario_termino_mantto_idc = null;
		}

		if($status_visita == 'BAJA DEFINITIVA' || $status_visita == 'BAJA TEMPORAL' || $status_visita == 'CAMBIO DE PROVEEDOR' || 
                    $status_visita == 'CANCELADO ETV' || $status_visita == 'CANCELADO IDC' || $status_visita == 'CANCELADO IDC Y ETV' ||
                    $status_visita == 'CANCELADO SE' || $status_visita == 'DESASTRES NATURALES' || $status_visita == 'EN REUBICACION' || 
                    $status_visita == 'SIN ACCESO POR CLIENTE' || $status_visita == 'SIN ACCESO POR ETV' || 
                    $status_visita == 'SIN ACCESO POR INTERVENTOR' || $status_visita == 'SIN ACCESO POR IDC' || $status_visita == 'RENOVACION TECNOLOGICA' 
                    || $status_visita == 'VANDALISMO MENOR' || $status_visita == 'VANDALISMO MAYOR'){

			if($status_finalizacion_del_odt && $comentarios){
				$vgeneral = true;
			}else{
				$vgeneral = false;
			}

		}else{
			$vgeneral = true;
		}


		if($status_visita == 'NO LLEGO ETV'){

			if($aplica_la_asistencia_de_un_interventor_gestion== 'SI, CON ALCANCE' || $aplica_la_asistencia_de_un_interventor_gestion == 'SI, FUERA DE ALCANCE'){
				if($arribo_interventor && $arribo_idc && $empresa_dio_acceso && $nombre_ing_dedicado_critico && $asistio_ing_dedicado && $nombre_ing_sustituto && $status_finalizacion_del_odt && $comentarios){
					$vno_llego_etv = true;
				}else{
					$vno_llego_etv = false;
				}
			}else{
				if($arribo_idc && $empresa_dio_acceso && $nombre_ing_dedicado_critico && $asistio_ing_dedicado && $nombre_ing_sustituto && $status_finalizacion_del_odt && $comentarios){
					$vno_llego_etv = true;
				}else{
					$vno_llego_etv = false;
				}
			}
		}else{
			$vno_llego_etv = true;
		}

		if($status_visita == 'NO LLEGO INTERVENTOR'){
			if($arribo_idc && $empresa_dio_acceso && $nombre_ing_dedicado_critico && $asistio_ing_dedicado && $nombre_ing_sustituto && $etv_hora_apertura_boveda && $etv_hora_modo_supervisor && $etv_hora_consulta_administrativa && $hora_termino_de_la_visita && $atm_queda_en_servicio && $status_finalizacion_del_odt && $comentarios ){
					$vno_llego_interventor=true;
			}else{
					$vno_llego_interventor=false;
			}	
		}else{
			$vno_llego_interventor=true;
		}

		if($status_visita == 'NO LLEGO INTERVENTOR' && $vno_llego_interventor){
			if($empresa_de_inmuebles_gestion=="SI"){
				if(!$arribo_inmuebles || !$nombre_tecnico_inmuebles){
					$vno_llego_interventor=false;
				}
			}
		}
		if($status_visita == 'NO LLEGO INTERVENTOR' && $vno_llego_interventor){
			if($empresa_de_cableado_gestion=="SI"){
				if(!$arribo_cableado || !$nombre_tecnico_cableado){
					$vno_llego_interventor=false;
				}
			}
		}
		if($status_visita == 'NO LLEGO INTERVENTOR' && $vno_llego_interventor){
			if($empresa_de_comunicaciones_gestion=="SI"){
				if(!$arribo_comunicaciones){
					$vno_llego_interventor=false;
				}
			}
		}
		if ($status_visita == 'NO LLEGO INTERVENTOR' && $vno_llego_interventor){
			if($status_finalizacion_del_odt=='NO SE FINALIZA, PENDIENTE POR PIEZA SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR PIEZA NO SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR CARGA DE SW' || $status_finalizacion_del_odt == 'NO SE FINALIZA, FALLA NO SOLUCIONADA' || $status_finalizacion_del_odt == 'NO SE FINALIZA, POR DOTACION'){
				if(!$detalle_de_desviaciones){
					$vno_llego_interventor=false;
				}
			}
		}

		if ($status_visita == 'NO LLEGO INTERVENTOR' && $vno_llego_interventor){
			if($status_finalizacion_del_odt=='NO SE FINALIZA, PENDIENTE POR PIEZA SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR PIEZA NO SOLICTADA EN ANALISIS'){
				if(!$pieza_por_la_cual_quedo_pendiente || !$especifica_la_otra_pieza){
					$vno_llego_interventor=false;
				}
			}
		}

		if($status_visita == 'NO LLEGO IDC'){
			if($etv_hora_apertura_boveda && $etv_hora_modo_supervisor && $etv_hora_consulta_administrativa && $hora_termino_de_la_visita && $atm_queda_en_servicio && $status_finalizacion_del_odt && $comentarios ){
					$vno_llego_idc=true;
			}else{
					$vno_llego_idc=false;
			}	
		}else{
			$vno_llego_idc=true;
		}
		if($status_visita == 'NO LLEGO IDC' && $vno_llego_idc){
			if($empresa_de_inmuebles_gestion=="SI"){
				if(!$arribo_inmuebles || !$nombre_tecnico_inmuebles){
					$vno_llego_idc=false;
				}
			}
		}
		if($status_visita == 'NO LLEGO IDC' && $vno_llego_idc){
			if($empresa_de_cableado_gestion=="SI"){
				if(!$arribo_cableado || !$nombre_tecnico_cableado){
					$vno_llego_idc=false;
				}
			}
		}
		if($status_visita == 'NO LLEGO IDC' && $vno_llego_idc){
			if($empresa_de_comunicaciones_gestion=="SI"){
				if(!$arribo_comunicaciones){
					$vno_llego_idc=false;
				}
			}
		}
		if ($status_visita == 'NO LLEGO IDC' && $vno_llego_idc){
			if($status_finalizacion_del_odt=='NO SE FINALIZA, PENDIENTE POR PIEZA SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR PIEZA NO SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR CARGA DE SW' || $status_finalizacion_del_odt == 'NO SE FINALIZA, FALLA NO SOLUCIONADA' || $status_finalizacion_del_odt == 'NO SE FINALIZA, POR DOTACION'){
				if(!$detalle_de_desviaciones){
					$vno_llego_idc=false;
				}
			}
		}

		if ($status_visita == 'NO LLEGO IDC' && $vno_llego_idc){
			if($status_finalizacion_del_odt=='NO SE FINALIZA, PENDIENTE POR PIEZA SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR PIEZA NO SOLICTADA EN ANALISIS'){
				if(!$pieza_por_la_cual_quedo_pendiente || !$especifica_la_otra_pieza){
					$vno_llego_idc=false;
				}
			}
		}

		if($status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA INMUEBLES 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CABLEADO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || $status_visita == 'VISITA COMUNICACIONES 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 3' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3'){
			//quitamos arribo_idc
			/*if($arribo_idc && $empresa_dio_acceso && $hora_termino_de_la_visita && $atm_queda_en_servicio && $status_finalizacion_del_odt && $comentarios ){
					$vvisitaN=true;
			}else{
					$vvisitaN=false;
			}*/
			if($empresa_dio_acceso && $hora_termino_de_la_visita && $atm_queda_en_servicio && $status_finalizacion_del_odt && $comentarios ){
					$vvisitaN=true;
			}else{
					$vvisitaN=false;
			}	
		}else{
			$vvisitaN=true;
		}
		if(($status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA INMUEBLES 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CABLEADO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || $status_visita == 'VISITA COMUNICACIONES 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 3' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3') && $vvisitaN){
			if($empresa_de_inmuebles_gestion=="SI"){
				if(!$arribo_inmuebles || !$nombre_tecnico_inmuebles){
					$vvisitaN=false;
				}
			}
		}
		if(($status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA INMUEBLES 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CABLEADO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || $status_visita == 'VISITA COMUNICACIONES 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 3' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3') && $vvisitaN){
			if($empresa_de_cableado_gestion=="SI"){
				if(!$arribo_cableado || !$nombre_tecnico_cableado){
					$vvisitaN=false;
				}
			}
		}
		if(($status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA INMUEBLES 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CABLEADO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || $status_visita == 'VISITA COMUNICACIONES 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 3' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3') && $vvisitaN){
			if($empresa_de_comunicaciones_gestion=="SI"){
				if(!$arribo_comunicaciones){
					$vvisitaN=false;
				}
			}
		}
		if (($status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA INMUEBLES 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CABLEADO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || $status_visita == 'VISITA COMUNICACIONES 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 3' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3') && $vvisitaN){
			if($status_finalizacion_del_odt=='NO SE FINALIZA, PENDIENTE POR PIEZA SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR PIEZA NO SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR CARGA DE SW' || $status_finalizacion_del_odt == 'NO SE FINALIZA, FALLA NO SOLUCIONADA' || $status_finalizacion_del_odt == 'NO SE FINALIZA, POR DOTACION'){
				if(!$detalle_de_desviaciones){
					$vvisitaN=false;
				}
			}
		}
		

		if (($status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA INMUEBLES 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CABLEADO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || $status_visita == 'VISITA COMUNICACIONES 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 3' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3') && $vvisitaN){
			if($status_finalizacion_del_odt=='NO SE FINALIZA, PENDIENTE POR PIEZA SOLICTADA EN ANALISIS' || $status_finalizacion_del_odt == 'NO SE FINALIZA, PENDIENTE POR PIEZA NO SOLICTADA EN ANALISIS'){
				if(!$pieza_por_la_cual_quedo_pendiente){
					if ($pieza_por_la_cual_quedo_pendiente=='OTRA' && $especifica_la_otra_pieza==null){
						$vvisitaN=false;
					}					
				}

			}
		}
		if (($status_visita == 'VISITA IDC 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 4' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 1, SEGUIMIENTO 6' || 	$status_visita == 'VISITA IDC 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 2, SEGUIMIENTO 4' || 	$status_visita == 'VISITAIDC 2, SEGUIMIENTO 5' || 	$status_visita == 'VISITA IDC 3' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 3, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 4' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 4, SEGUIMIENTO 3' || 	$status_visita == 'VISITA IDC 5' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 1' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 2' || 	$status_visita == 'VISITA IDC 5, SEGUIMIENTO 3' || $status_visita == 'VISITA INMUEBLES 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA INMUEBLES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CABLEADO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CABLEADO 2, SEGUIMIENTO 3' || $status_visita == 'VISITA COMUNICACIONES 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 1, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 1' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 2' || $status_visita == 'VISITA COMUNICACIONES 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA 3' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA 3, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 1, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 2, SEGUIMIENTO 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 1' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 2' || $status_visita == 'VISITA CONJUNTA SIN IDC 3, SEGUIMIENTO 3') && $vvisitaN){
       		if($aplica_la_asistencia_de_un_interventor_gestion== 'SI, CON ALCANCE' || $aplica_la_asistencia_de_un_interventor_gestion == 'SI, FUERA DE ALCANCE'){
				if(!$arribo_interventor ){
					$vvisitaN=false;
				}
			}
		}
	

		if($vgeneral && $vno_llego_idc && $vno_llego_interventor && $vno_llego_etv && $vvisitaN){
			$status = 'Cierres';
		}else{
			$status = 'Gestion Pendiente';
		}

        DB::table('gestion')->where('id', $gestion_id)->update(array(
			'atm'                               => $atm,
			'que_se_mide'                       => $que_se_mide,
			'nombre_del_gestor'                 => $nombre_del_gestor,
			'status_visita'                     => $status_visita,
			'arribo_interventor'                => $arribo_interventor,
			'arribo_idc'                        => $arribo_idc,
			'empresa_dio_acceso'                => $empresa_dio_acceso,
			'nombre_ing_dedicado_critico'       => $nombre_ing_dedicado_critico,
			'asistio_ing_dedicado'              => $asistio_ing_dedicado,
			'nombre_ing_sustituto'              => $nombre_ing_sustituto,
			'etv_hora_apertura_boveda'          => $etv_hora_apertura_boveda,
			'etv_hora_modo_supervisor'          => $etv_hora_modo_supervisor,
			'etv_hora_consulta_administrativa'  => $etv_hora_consulta_administrativa,
			'arribo_inmuebles'                  => $arribo_inmuebles,
			'nombre_tecnico_inmuebles'          => $nombre_tecnico_inmuebles,
			'arribo_cableado'                   => $arribo_cableado,
			'nombre_tecnico_cableado'           => $nombre_tecnico_cableado,
			'arribo_comunicaciones'             => $arribo_comunicaciones,
			'hora_termino_de_la_visita'         => $hora_termino_de_la_visita,
			'atm_queda_en_servicio'             => $atm_queda_en_servicio,
			'status_finalizacion_del_odt'       => $status_finalizacion_del_odt,
			'detalle_de_desviaciones'           => $detalle_de_desviaciones,
			'pieza_por_la_cual_quedo_pendiente' => $pieza_por_la_cual_quedo_pendiente,
			'especifica_la_otra_pieza'          => $especifica_la_otra_pieza,
			'comentarios'                       => $comentarios,
			'user_name'                         => Auth::user()->name,
			'status'                            => $status,
			'horario_pactado_de_regreso_etv'    => $horario_pactado_de_regreso_etv,
			'horario_llegada_de_regreso_etv'    => $horario_llegada_de_regreso_etv,
			'se_requiere_reprogramacion' => $se_requiere_reprogramacion,
			'fecha_reprogramacion_etv'   => $fecha_reprogramacion_etv,
			'hora_llegada_etv'           => $hora_llegada_etv,
			'hora_fin_servicio'          => $hora_fin_servicio,
			'horario_termino_mantto_idc' => $horario_termino_mantto_idc,
			'comentarios_desviacion_etv' => $comentarios_desviacion_etv,
			//'created_at'                        => $now,
			'updated_at'                        => $now,
        ));

        DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
			'status'				   => $status,
			'gestion_id'			   => $gestion_id,
			'updated_at'               => $now,
        ));

        if($status_finalizacion_del_odt == 'NO SE REALIZO INTERVENCION'){
        	$status = 'Completado';
			 DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
				'status'				   => $status,
				'updated_at'               => $now
	        ));
		}


        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha editado una gestion en la tabla gestion con id ' . $gestion_id ,
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info insertada" , 
          //"Data" => $data_id, 
        ]);
    }

    public function getGestionById($gestion_id){

        $data = DB::table('gestion')->where('id', $gestion_id)->get();
        
        return JsonResponse::singleResponse(["message" => "Info encontrada" , 
          "Data" => $data, 
        ]);
    }

    public function downloadReporteGestion($fecha_inicio, $fecha_fin){

        $now = Carbon::now('America/Mexico_City');

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha descargado un excel de la tabla gestion',
            'created_at'    => $now,
            'updated_at'    => $now
        ));

      //aqui empieza el ciclo

        $data = DB::table('gestion')
                    ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
                    ->get();
        
        $tot_record_found = 0;
        if ($data != null || $data != '') {
            $tot_record_found = 1;

            $CsvData = array('ATM, NOMBRE DEL GESTOR, STATUS VISITA, ARRIBO INTERVENTOR, ARRIBO IDC, EMPRESA DIO ACCESO, NOMBRE ING DEDICADO CRITICO, ASISTIO ING DEDICADO, NOMBRE ING SUSTITUTO, ETV HORA APERTURA BOVEDA, ETV HORA MODO SUPERVISOR, ETV HORA CONSULTA ADMINISTRATIVA, ARRIBO INMUEBLES, OMBRE TECNICO INMUEBLES, ARRIBO CABLEADO, NOMBRE TECNICO CABLEADO, ARRIBO COMUNICACIONES, HORA TERMINO DE LA VISITA, ATM QUEDA EN SERVICIO, STATUS FINALIZACION DEL ODT, DETALLE DE DESVIACIONES, PIEZA POR LA CUAL QUEDO PENDIENTE, ESPECIFICA LA OTRA PIEZA, COMENTARIOS, USUARIO, FECHA DE CREACION');
            foreach ($data as $value) {

				$atm                               = str_replace(',', '', $value->atm);
				$nombre_del_gestor                 = str_replace(',', '', $value->nombre_del_gestor);
				$status_visita                     = str_replace(',', '', $value->status_visita);
				$arribo_interventor                = str_replace(',', '', $value->arribo_interventor);
				$arribo_idc                        = str_replace(',', '', $value->arribo_idc);
				$empresa_dio_acceso                = str_replace(',', '', $value->empresa_dio_acceso);
				$nombre_ing_dedicado_critico       = str_replace(',', '', $value->nombre_ing_dedicado_critico);
				$asistio_ing_dedicado              = str_replace(',', '', $value->asistio_ing_dedicado);
				$nombre_ing_sustituto              = str_replace(',', '', $value->nombre_ing_sustituto);
				$etv_hora_apertura_boveda          = str_replace(',', '', $value->etv_hora_apertura_boveda);
				$etv_hora_modo_supervisor          = str_replace(',', '', $value->etv_hora_modo_supervisor);
				$etv_hora_consulta_administrativa  = str_replace(',', '', $value->etv_hora_consulta_administrativa);
				$arribo_inmuebles                  = str_replace(',', '', $value->arribo_inmuebles);
				$nombre_tecnico_inmuebles          = str_replace(',', '', $value->nombre_tecnico_inmuebles);
				$arribo_cableado                   = str_replace(',', '', $value->arribo_cableado);
				$nombre_tecnico_cableado           = str_replace(',', '', $value->nombre_tecnico_cableado);
				$arribo_comunicaciones             = str_replace(',', '', $value->arribo_comunicaciones);
				$hora_termino_de_la_visita         = str_replace(',', '', $value->hora_termino_de_la_visita);
				$atm_queda_en_servicio             = str_replace(',', '', $value->atm_queda_en_servicio);
				$status_finalizacion_del_odt       = str_replace(',', '', $value->status_finalizacion_del_odt);
				$detalle_de_desviaciones           = str_replace(',', '', $value->detalle_de_desviaciones);
				$pieza_por_la_cual_quedo_pendiente = str_replace(',', '', $value->pieza_por_la_cual_quedo_pendiente);
				$especifica_la_otra_pieza          = str_replace(',', '', $value->especifica_la_otra_pieza);
				$comentarios                       = str_replace(',', '', $value->comentarios);
				$user_name                         = str_replace(',', '', $value->user_name);
				$created_at                        = str_replace(',', '', $value->created_at);
              

                $CsvData[] = $atm . ',' . $nombre_del_gestor . ',' . $status_visita . ',' . $arribo_interventor . ',' . $arribo_idc . ',' . $empresa_dio_acceso . ',' . $nombre_ing_dedicado_critico . ',' . $asistio_ing_dedicado . ',' . $nombre_ing_sustituto . ',' . $etv_hora_apertura_boveda . ',' . $etv_hora_modo_supervisor . ',' . $etv_hora_consulta_administrativa . ',' . $arribo_inmuebles . ',' . $nombre_tecnico_inmuebles . ',' . $arribo_cableado . ',' . $nombre_tecnico_cableado . ',' . $arribo_comunicaciones . ',' . $hora_termino_de_la_visita . ',' . $atm_queda_en_servicio . ',' . $status_finalizacion_del_odt . ',' . $detalle_de_desviaciones . ',' . $pieza_por_la_cual_quedo_pendiente . ',' . $especifica_la_otra_pieza  . ',' . $comentarios . ',' . $user_name . ',' . $created_at;
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