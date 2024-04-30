<?php

namespace App\Http\Controllers\v1;
use App\helpers\JsonResponse;
use App\Entities\Siga;
use App\Entities\IndispoAlDia;
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
use App\Jobs\SendEmailAltaDatosOdt;

class AltaDatosOdtController extends Controller
{
    use JWTTrait;

    protected $internal;

    public function __construct(Internal $internal)
    {
        $this->internal = $internal;
    }

    public function envioReporteOdt(Request $request){

        $now       = Carbon::now('America/Mexico_City');
        $para      = array_get($request, 'para');
        $copia     = array_get($request, 'copia');
        $user_name = Auth::user()->name;


        if($copia != null){
            $copia = 'marioalberto.rangel.contractor@bbva.com, marcoantonio.negrete.contractor@bbva.com, ' . $copia;
        }else{
            $copia = 'marioalberto.rangel.contractor@bbva.com, marcoantonio.negrete.contractor@bbva.com';
        }    

        DB::table('envioreportes')->insert(array(
            'para'       => $para,
            'copia'      => $copia,
            'reporte'    => 'GESTION ODT',
            'user_name'  => $user_name,
            'created_at' => $now,
            'updated_at' => $now,
        ));

        $userId = Auth::id();
        $this->internal->create(array(
            'user_id'       => $userId,
            'evento'        => 'Se ha solicitado un reporte con GESTION ODT',
            'created_at'    => $now,
            'updated_at'    => $now
        ));
    }

    public function envioReportePlantillaDrive(Request $request){

        $now       = Carbon::now('America/Mexico_City');
        $para      = array_get($request, 'para');
        $copia     = array_get($request, 'copia');
        $user_name = Auth::user()->name;


        if($copia != null){
            $copia = 'marioalberto.rangel.contractor@bbva.com, marcoantonio.negrete.contractor@bbva.com, ' . $copia;
        }else{
            $copia = 'marioalberto.rangel.contractor@bbva.com, marcoantonio.negrete.contractor@bbva.com';
        }    

        DB::table('envioreportes')->insert(array(
            'para'       => $para,
            'copia'      => $copia,
            'reporte'    => 'PLANTILLA DRIVE',
            'user_name'  => $user_name,
            'created_at' => $now,
            'updated_at' => $now,
        ));

        $userId = Auth::id();
        $this->internal->create(array(
            'user_id'       => $userId,
            'evento'        => 'Se ha solicitado un reporte con GESTION ODT',
            'created_at'    => $now,
            'updated_at'    => $now
        ));
    }

    public function getUniversoMesActual(){

        $data = DB::table('universo_mes_actual')->orderByRaw('created_at ASC')->get();

        return JsonResponse::collectionResponse($data);
    }

    public function getItemUniversoMesActual($item_id){

        $data = DB::table('universo_mes_actual')->where('id', $item_id)->get();

        return JsonResponse::singleResponse(["message" => "Info" , 
          "Data" => $data, 
        ]);
    }

    public function habilitaCierre($item_id){

        $now    = Carbon::now('America/Mexico_City')->subHour();

        $data = DB::table('alta_datos_odt')->where('id', $item_id)->update(array(
            'status'     => 'Cierres' ,
            'updated_at' => $now,
        ));

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha actualizado el id =' . $item_id . ' de la tabla alta_datos_odt, con estatus Cierres',
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info actualizada" , 
          "Data" => $data, 
        ]);
    }

    public function updateUniversoMesActual(Request $request, $item_id){

        $nombre = array_get($request, 'nombre');
        $now    = Carbon::now('America/Mexico_City');

        $data = DB::table('universo_mes_actual')->where('id', $item_id)->update(array(
            'nombre'     => $nombre,
            'updated_at' => $now,
        ));

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha actualizado el id =' . $item_id . ' de la tabla universo_mes_actual',
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info actualizada" , 
          "Data" => $data, 
        ]);
    }

    public function createUniversoMesActual(Request $request){

        $nombre = array_get($request, 'nombre');
        $now    = Carbon::now('America/Mexico_City');

        $data = DB::table('universo_mes_actual')->insert(array(
            'nombre'     => $nombre,
            'created_at' => $now,
            'updated_at' => $now,
        ));

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha creado un nuevo item en la tabla universo_mes_actual',
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info insertada" , 
          "Data" => $data, 
        ]);
    }

    public function deleteItemUniversoMesActual($item_id){

        $now    = Carbon::now('America/Mexico_City');
        DB::table('universo_mes_actual')->where('id', $item_id)->delete();

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha elminado el id =' . $item_id . ' de la tabla universo_mes_actual',
            'created_at'    => $now,
            'updated_at'    => $now
        ));

        return JsonResponse::singleResponse(["message" => "Info eliminado" , 
        ]);
    }

    public function createNewDataOdt(Request $request){

        $atm                  = array_get($request, 'atm');
        $caso                 = array_get($request, 'caso');
        $que_se_mide          = array_get($request, 'que_se_mide');
        $fecha_seleccion      = array_get($request, 'fecha_seleccion');
        $indispo_de_seleccion = array_get($request, 'indispo_de_seleccion');
        $total_transacciones  = array_get($request, 'total_transacciones');

        $grupo        = array_get($request, 'universo_mes_actual');
        $mes_del_caso = array_get($request, 'mes_del_caso');
        $ano_del_caso = array_get($request, 'ano_del_caso');
        $prioridad    = array_get($request, 'prioridad');

        $universo_mes_actual  = array_get($request, 'universo_mes_actual');
        $caso_inicial         = array_get($request, 'caso_inicial');
        $fecha_escalado_dar   = array_get($request, 'fecha_escalado_dar');
        $fecha_escalado_banca = array_get($request, 'fecha_escalado_banca');
        $now                  = Carbon::now('America/Mexico_City');
        $user_name            = Auth::user()->name;

        //$existe = DB::table('alta_datos_odt')->where('atm', $atm)->whereNotIn('status', ['Completado'])->orderBy('id', 'DESC')->get();

        $existe = DB::table('alta_datos_odt')
                ->where('atm', $atm)
                ->whereNotIn('status', ['Completado'])
                ->where('fecha_seleccion', '>', DB::raw('DATE_ADD(NOW(), INTERVAL -70 DAY)'))
                ->get();
  
        if ($existe->count() >0 ){

           
            return JsonResponse::errorResponse("No es posible crear otra ODT sin completar la anterior", 404);

             }else{

            DB::select("CALL insertaODT(
                '" . $prioridad . "' ,
                '" . $ano_del_caso . "' ,
                '" . $mes_del_caso . "' ,
                '" . $grupo . "' ,
                '" . $caso . "' , 
                '" . $atm . "' , 
                '" . $que_se_mide . "' ,
                '" . $fecha_seleccion . "' ,
                '" . $indispo_de_seleccion . "',
                '" . $universo_mes_actual . "' ,
                '" . $caso_inicial . "' ,
                '" . $fecha_escalado_dar . "' ,
                '" . $fecha_escalado_banca . "' ,
                '" . Auth::user()->name . "' ,
                '" . $total_transacciones . "'                                        
            )");

            $this->internal->create(array(
                'user_id'       => Auth::user()->name,
                'evento'        => 'Se ha creado un nuevo item en la tabla alta_datos_odt',
                'created_at'    => $now,
                'updated_at'    => $now
            ));

            return JsonResponse::singleResponse(["message" => "Info insertada" , 
              //"Data" => $data, 
            ]);
            }
        }
       
   

    public function deleteItemAltaDatosItem($item_id){
        $now = Carbon::now('America/Mexico_City');

        $llave = DB::select("SELECT concat(id,' | ','caso:',caso,' | ', 'atm:', atm,' | ', 'fecha_seleccion:', fecha_seleccion,' | ', 'created_at:', created_at,' | ', 'updated_at:', updated_at,' | ', 'planeacion:', planeacion_id,' | ', 'analisis:', analisis_id,' | ', 'gestion:', gestion_id,' | ') as llave from alta_datos_odt where id = $item_id");

         $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se intenta eliminar registro de la tabla alta_datos_odt' . $llave[0]->llave,
            'created_at'    => $now,
            'updated_at'    => $now
        ));

        DB::table('alta_datos_odt')->where('id', $item_id)->delete();

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha eliminado un registro de la tabla alta_datos_odt' . $item_id,
            'created_at'    => $now,
            'updated_at'    => $now
        ));

        return JsonResponse::singleResponse(["message" => "Info eliminado" , 
        ]);
    }

    public function getAltaDatosOdt(Request $request){

        $userId = Auth::id();
        $divisiones = DB::table('users_divisiones')
                  ->where('user_id', $userId)
                  ->select('users_divisiones.*')
                  ->get();

        $division=collect($divisiones)->implode('division_id',',');
        //var_dump($division);

        $roles = DB::table('users_rol')->where('user_id', $userId)->select('users_rol.*')->get();

        $rol=collect($roles)->implode('rol_id',',');

        $data = DB::select("SELECT 
            b.id as indisp_al_dia_id,
            b.ind_acum_actual, 
            a.atm, 
            a.que_se_mide, 
            a.fecha_seleccion, 
                        CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) as indispo_de_seleccion,
                        CAST(b.MANTENIMIENTO_TECNICO AS DECIMAL(10,4)) as mantenimiento_tecnico,
                        CAST(b.RECUPERACION_MANUAL AS DECIMAL(10,4)) as recuperacion_manual,
                        CAST(a.total_transacciones AS DECIMAL(10,0)) as total_transacciones,
                        CAST(b.MANTENIMIENTO_TECNICO AS DECIMAL(10,4))  + CAST(b.RECUPERACION_MANUAL AS DECIMAL(10,4)) as suma_mant_tecnico_recup_manual,
            a.universo_mes_actual, 
            a.caso_inicial, 
            a.fecha_escalado_dar, 
            a.fecha_escalado_banca, 
            a.user_name,
            a.gestionado,
            a.id,
            a.status,
            a.planeacion_id,
            a.analisis_id,
            a.gestion_id,
            a.cierre_id,
            c.d_local,
            c.d_division,
            c.d_idc,
            c.d_sitio,
            c.d_division,
            c.d_estatus_autoservicio,
            d.id as division_id,
            e.id AS planeacion_id_planeacion, e.responsable_planeacion AS planeacion_responsable_planeacion, e.fecha_de_planeacion AS planeacion_fecha_de_planeacion, e.idc_fecha_solicitud_cita AS planeacion_idc_fecha_solicitud_cita, e.idc_fecha_confirmacion_cita AS planeacion_idc_fecha_confirmacion_cita, e.tipo_de_ingenieria AS planeacion_tipo_de_ingenieria, e.nombres_del_personal_a_asistir_de_idc AS planeacion_nombres_del_personal_a_asistir_de_idc, e.etv_fecha_solicitud_cita AS planeacion_etv_fecha_solicitud_cita, e.etv_fecha_confirmacion_cita AS planeacion_etv_fecha_confirmacion_cita, e.empresa_de_inmuebles AS planeacion_empresa_de_inmuebles, e.personal_de_inmuebles AS planeacion_personal_de_inmuebles, e.empresa_de_cableado AS planeacion_empresa_de_cableado, e.personal_de_cableado AS planeacion_personal_de_cableado, e.empresa_de_comunicaciones AS planeacion_empresa_de_comunicaciones, e.personal_de_comunicaciones AS planeacion_personal_de_comunicaciones, e.nombre_interventor AS planeacion_nombre_interventor, e.fecha_de_cita AS planeacion_fecha_de_cita, e.ticket_ob AS planeacion_ticket_ob, e.ticket_remedy AS planeacion_ticket_remedy, e.tas_idc AS planeacion_tas_idc, e.tas_etv AS planeacion_tas_etv, e.tas_seguimiento AS planeacion_tas_seguimiento, e.folio_inmuebles AS planeacion_folio_inmuebles, e. STATUS AS planeacion_status, e.atm AS planeacion_atm, e.no_odt AS planeacion_no_odt, e.created_at AS planeacion_created_at, e.updated_at AS planeacion_updated_at, e.user_name AS planeacion_user_name, 
            f.nombre_responsable AS analisis_nombre_responsable, f.nombre_analista AS analisis_nombre_analista, f.fecha_de_analisis AS analisis_fecha_de_analisis, f.aplican_acciones_de_idc AS analisis_aplican_acciones_de_idc, f.acciones_a_realizar_idc AS analisis_acciones_a_realizar_idc, f.mant_modulo_dispensador AS analisis_mant_modulo_dispensador, f.cambio_presentador_stacker AS analisis_cambio_presentador_stacker, f.cambio_cosumibles AS analisis_cambio_cosumibles, f.cambio_pick_picker_estractor AS analisis_cambio_pick_picker_estractor, f.cambio_caseteros AS analisis_cambio_caseteros, f.otro_1_dispensador AS analisis_otro_1_dispensador, f.nombre_pieza_otro_1_dispensador AS analisis_nombre_pieza_otro_1_dispensador, f.otro_2_dispensador AS analisis_otro_2_dispensador, f.nombre_pieza_otro_2_dispensador AS analisis_nombre_pieza_otro_2_dispensador, f.mant_modulo_aceptador AS analisis_mant_modulo_aceptador, f.cambio_escrow AS analisis_cambio_escrow, f.cambio_validador AS analisis_cambio_validador, f.cambio_shutter_cash_slot AS analisis_cambio_shutter_cash_slot, f.cambio_tarjeta_controladora AS analisis_cambio_tarjeta_controladora, f.otro_1_aceptador AS analisis_otro_1_aceptador, f.nombre_pieza_otro_1_aceptador AS analisis_nombre_pieza_otro_1_aceptador, f.otro_2_aceptador AS analisis_otro_2_aceptador, f.nombre_pieza_otro_2_aceptador AS analisis_nombre_pieza_otro_2_aceptador, f.mant_modulo_idc_cpu AS analisis_mant_modulo_idc_cpu, f.cambio_dd AS analisis_cambio_dd, f.cambio_cpu AS analisis_cambio_cpu, f.mant_modulo_lectora AS analisis_mant_modulo_lectora, f.cambio_lectora AS analisis_cambio_lectora, f.mant_modulo_impresora AS analisis_mant_modulo_impresora, f.cambio_impresora AS analisis_cambio_impresora, f.fuente_de_poder AS analisis_fuente_de_poder, f.teclado_teclado_lateral_touch_screen AS analisis_teclado_teclado_lateral_touch_screen, f.hopper AS analisis_hopper, f.monitor_pantalla AS analisis_monitor_pantalla, f.fascia AS analisis_fascia, f.se_requiere_planchado_de_sw AS analisis_se_requiere_planchado_de_sw, f.ver_requerida AS analisis_ver_requerida, f.aplican_acciones_de_inmuebles AS analisis_aplican_acciones_de_inmuebles, f.acciones_a_realizar_inmuebles AS analisis_acciones_a_realizar_inmuebles, f.revision_correcion_de_voltajes AS analisis_revision_correcion_de_voltajes, f.mejoramiento_de_imagen_limpieza AS analisis_mejoramiento_de_imagen_limpieza, f.revision_correccion_instalacion_de_aa AS analisis_revision_correccion_instalacion_de_aa, f.requiere_ups AS analisis_requiere_ups, f.aplican_acciones_de_cableado AS analisis_aplican_acciones_de_cableado, f.acciones_a_realizar_cableado AS analisis_acciones_a_realizar_cableado, f.correcciones_red_interna AS analisis_correcciones_red_interna, f.revision_de_cableado_y_status_de_equipos AS analisis_revision_de_cableado_y_status_de_equipos, f.retiro_y_o_limpieza_de_equipos AS analisis_retiro_y_o_limpieza_de_equipos, f.aplican_acciones_de_comunicaciones AS analisis_aplican_acciones_de_comunicaciones, f.acciones_a_realizar_comunicaciones AS analisis_acciones_a_realizar_comunicaciones, f.revision_enlace_equipo AS analisis_revision_enlace_equipo, f.prueba_de_calidad AS analisis_prueba_de_calidad, f.aplica_la_asistencia_de_un_interventor AS analisis_aplica_la_asistencia_de_un_interventor, f.tipo_de_visita AS analisis_tipo_de_visita, f. STATUS AS analisis_status, f.atm AS analisis_atm, f.no_odt AS analisis_no_odt, f.user_name AS analisis_user_name, f.created_at AS analisis_created_at, f.updated_at AS analisis_updated_at, f.id AS analisis_id_analisis,
                        g.id as gestion_id_gestion,g.atm AS gestion_atm, g.que_se_mide AS gestion_que_se_mide, g.nombre_del_gestor AS gestion_nombre_del_gestor, g.status_visita AS gestion_status_visita, g.arribo_interventor AS gestion_arribo_interventor, g.arribo_idc AS gestion_arribo_idc, g.empresa_dio_acceso AS gestion_empresa_dio_acceso, g.nombre_ing_dedicado_critico AS gestion_nombre_ing_dedicado_critico, g.asistio_ing_dedicado AS gestion_asistio_ing_dedicado, g.nombre_ing_sustituto AS gestion_nombre_ing_sustituto, g.etv_hora_apertura_boveda AS gestion_etv_hora_apertura_boveda, g.etv_hora_modo_supervisor AS gestion_etv_hora_modo_supervisor, g.etv_hora_consulta_administrativa AS gestion_etv_hora_consulta_administrativa, g.arribo_inmuebles AS gestion_arribo_inmuebles, g.nombre_tecnico_inmuebles AS gestion_nombre_tecnico_inmuebles, g.arribo_cableado AS gestion_arribo_cableado, g.nombre_tecnico_cableado AS gestion_nombre_tecnico_cableado, g.arribo_comunicaciones AS gestion_arribo_comunicaciones, g.hora_termino_de_la_visita AS gestion_hora_termino_de_la_visita, g.atm_queda_en_servicio AS gestion_atm_queda_en_servicio, g.status_finalizacion_del_odt AS gestion_status_finalizacion_del_odt, g.detalle_de_desviaciones AS gestion_detalle_de_desviaciones, g.pieza_por_la_cual_quedo_pendiente AS gestion_pieza_por_la_cual_quedo_pendiente, g.especifica_la_otra_pieza AS gestion_especifica_la_otra_pieza, g.comentarios AS gestion_comentarios, g. STATUS AS gestion_status, g.user_name AS gestion_user_name, g.created_at AS gestion_created_at, g.updated_at AS gestion_updated_at, g.no_odt AS gestion_no_odt,
            h.id AS cierres_id_cierres, h.atm AS cierres_atm, h.que_se_mide AS cierres_que_se_mide, h.modulo_vandalizado AS cierres_modulo_vandalizado, h.otro_especificar AS cierres_otro_especificar, h.fecha_escalamiento_dar AS cierres_fecha_escalamiento_dar, h.mant_modulo_dispensador AS cierres_mant_modulo_dispensador, h.cambio_presentador_stacker AS cierres_cambio_presentador_stacker, h.cambio_consumibles AS cierres_cambio_consumibles, h.cambio_pick_picker_estractor AS cierres_cambio_pick_picker_estractor, h.cambio_caseteros AS cierres_cambio_caseteros, h.otro_1_dispensador AS cierres_otro_1_dispensador, h.nombre_pieza_otro_1_dispensador AS cierres_nombre_pieza_otro_1_dispensador, h.otro_2_dispensador AS cierres_otro_2_dispensador, h.nombre_pieza_otro_2_dispensador AS cierres_nombre_pieza_otro_2_dispensador, h.mant_modulo_aceptador AS cierres_mant_modulo_aceptador, h.cambio_escrow AS cierres_cambio_escrow, h.cambio_validador AS cierres_cambio_validador, h.cambio_shutter_cash_slot AS cierres_cambio_shutter_cash_slot, h.cambio_tarjeta_cotroladora AS cierres_cambio_tarjeta_cotroladora, h.otro_1_aceptador AS cierres_otro_1_aceptador, h.nombre_pieza_otro_1_aceptador AS cierres_nombre_pieza_otro_1_aceptador, h.otro_2_aceptador AS cierres_otro_2_aceptador, h.nombre_pieza_otro_2_aceptador AS cierres_nombre_pieza_otro_2_aceptador, h.mant_modulo_cpu AS cierres_mant_modulo_cpu, h.cambio_dd AS cierres_cambio_dd, h.cambio_cpu AS cierres_cambio_cpu, h.mant_modulo_lectora AS cierres_mant_modulo_lectora, h.cambio_lectora AS cierres_cambio_lectora, h.mant_modulo_impresora AS cierres_mant_modulo_impresora, h.cambio_impresora AS cierres_cambio_impresora, h.fuente_de_poder AS cierres_fuente_de_poder, h.teclado_teclado_lateral_touch_screen AS cierres_teclado_teclado_lateral_touch_screen, h.hooper AS cierres_hooper, h.monitor_pantalla AS cierres_monitor_pantalla, h.fascia AS cierres_fascia, h.se_realizo_planchado_sw AS cierres_se_realizo_planchado_sw, h.version_instalada AS cierres_version_instalada, h.checker_visible AS cierres_checker_visible, h.activacion_checker AS cierres_activacion_checker, h.nombre_activa_checker AS cierres_nombre_activa_checker, h.csds_visible AS cierres_csds_visible, h.cierre_inmuebles AS cierres_cierre_inmuebles, h.revision_correcion_de_voltajes AS cierres_revision_correcion_de_voltajes, h.mejoramiento_de_imagen_limpieza AS cierres_mejoramiento_de_imagen_limpieza, h.revision_correccion_instalacion_de_aa AS cierres_revision_correccion_instalacion_de_aa, h.requiere_ups AS cierres_requiere_ups, h.cierre_cableado AS cierres_cierre_cableado, h.correcciones_red_interna AS cierres_correcciones_red_interna, h.revision_de_cableado_y_status_de_equipos AS cierres_revision_de_cableado_y_status_de_equipos, h.retiro_y_o_limpieza_de_equipos AS cierres_retiro_y_o_limpieza_de_equipos, h.cierre_comunicaciones AS cierres_cierre_comunicaciones, h.revision_enlace_equipos AS cierres_revision_enlace_equipos, h.prueba_de_calidad AS cierres_prueba_de_calidad, h.cierre_interventor AS cierres_cierre_interventor, h.cuenta_con_reporte_fotografico AS cierres_cuenta_con_reporte_fotografico, h.requiere_calcomanias AS cierres_requiere_calcomanias, h.requiere_mejoramiento_de_imagen AS cierres_requiere_mejoramiento_de_imagen, h. STATUS AS cierres_status, h.user_name AS cierres_user_name, h.created_at AS cierres_created_at, h.updated_at AS cierres_updated_at, h.cierre_idc AS cierres_cierre_idc, h.vandalismo AS cierres_vandalismo,
            case 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' then 0.663 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' then 0.663
                when a.que_se_mide ='RECEPTOR'  then 0.754
                when a.que_se_mide ='RECICLADOR'  then 1.5
            end as meta,
            case 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' AND CAST(b.ind_acum_actual AS DECIMAL(10,4)) < 0.663  then 'META'
              when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' AND CAST(b.ind_acum_actual AS DECIMAL(10,4)) > 0.663  then 'FUERA META'
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' AND CAST(b.ind_acum_actual AS DECIMAL(10,4)) < 0.663  then 'META' 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' AND CAST(b.ind_acum_actual AS DECIMAL(10,4)) > 0.663  then 'FUERA META' 
                when a.que_se_mide ='RECEPTOR' AND CAST(b.ind_acum_actual AS DECIMAL(10,4)) < 0.754 then 'META'
                when a.que_se_mide ='RECEPTOR'  AND CAST(b.ind_acum_actual AS DECIMAL(10,4)) > 0.754 then 'FUERA META'
                when a.que_se_mide ='RECICLADOR' AND CAST(b.ind_acum_actual AS DECIMAL(10,4)) < 1.5 then 'META'
                when a.que_se_mide ='RECICLADOR' AND CAST(b.ind_acum_actual AS DECIMAL(10,4)) > 1.5 then 'FUERA META'
end as en_meta,a.created_at as fecha_creacion_ODT,DATEDIFF(NOW(),j.primera_falla) as dias_fuera
            FROM alta_datos_odt as a 
            LEFT JOIN indisp_al_dia as b ON a.atm=b.atm and a.que_se_mide=b.FUNCIONALIDAD 
            LEFT JOIN siga c on a.atm=c.pk_autoservicios_id
            LEFT JOIN cat_divisiones d on c.d_division=d.nombre
                        LEFT JOIN planeacion e on a.planeacion_id=e.id
                        LEFT JOIN analisis f on a.analisis_id=f.id
                        LEFT JOIN gestion g on a.gestion_id=g.id
                        LEFT JOIN cierres h on a.cierre_id=h.id
                        LEFT JOIN cat_rol_esatus i on a.`status`= i.nombre_estatus
                        LEFT JOIN (
                            select b.atm,min(created_at) as primera_falla from 
                                (select atm,created_at from gestion A where status_finalizacion_del_odt='NO SE REALIZO INTERVENCION') a
                            RIGHT JOIN
                                (select atm,max(created_at) as ultimo_exito from gestion where status_finalizacion_del_odt='SE FINALIZA OK LA INTERVENCION'  GROUP BY atm) b
                            on a.atm=b.atm where a.created_at>b.ultimo_exito GROUP BY atm
                            ) as j on a.atm=j.atm
            where  i.id in ($rol) and d.id in ($division)
                        and a.fecha_seleccion > DATE_ADD(NOW(),INTERVAL -70 DAY)
            GROUP BY a.id
            ORDER BY a.created_at DESC");

        /*foreach($data as $item){
            $item->indispo_de_seleccion = (float)($item->indispo_de_seleccion);
            $item->mantenimiento_tecnico = (float)($item->mantenimiento_tecnico);
            $item->recuperacion_manual = (float)($item->recuperacion_manual);
            $item->total_transacciones = (int)($item->total_transacciones);
            $item->suma_mant_tecnico_recup_manual = $item->mantenimiento_tecnico + $item->recuperacion_manual;
            $item->planeacion = DB::table('planeacion')->where('id', $item->planeacion_id)->get();
            $item->analisis = DB::table('analisis')->where('id', $item->analisis_id)->get();
            $item->gestion = DB::table('gestion')->where('id', $item->gestion_id)->get();
            $item->cierre = DB::table('cierres')->where('id', $item->cierre_id)->get();
        }*/

        return JsonResponse::collectionResponse($data);
    }

    public function getAltaDatosOdtPlaneacion(Request $request){

        $userId = Auth::id();
        $divisiones = DB::table('users_divisiones')
                  ->where('user_id', $userId)
                  ->select('users_divisiones.*')
                  ->get();

        $division=collect($divisiones)->implode('division_id',',');
        //var_dump($division);

        $data = DB::select("SELECT 
            b.id as indisp_al_dia_id, 
            a.atm, 
            a.que_se_mide, 
            a.fecha_seleccion, 
                        CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) as indispo_de_seleccion,
                        CAST(b.MANTENIMIENTO_TECNICO AS DECIMAL(10,4)) as mantenimiento_tecnico,
                        CAST(b.RECUPERACION_MANUAL AS DECIMAL(10,4)) as recuperacion_manual,
                        CAST(a.total_transacciones AS DECIMAL(10,0)) as total_transacciones,
                        CAST(b.MANTENIMIENTO_TECNICO AS DECIMAL(10,4))  + CAST(b.RECUPERACION_MANUAL AS DECIMAL(10,4)) as suma_mant_tecnico_recup_manual,
            a.universo_mes_actual, 
            a.caso_inicial, 
            a.fecha_escalado_dar, 
            a.fecha_escalado_banca, 
            a.user_name,
            a.gestionado,
            a.id,
            a.status,
            a.planeacion_id,
            a.analisis_id,
            a.gestion_id,
            a.cierre_id,
            c.d_local,
            c.d_division,
            c.d_idc,
            c.d_sitio,
            c.d_division,
            c.d_estatus_autoservicio,
            d.id as division_id,
            e.id AS planeacion_id_planeacion, e.responsable_planeacion AS planeacion_responsable_planeacion, e.fecha_de_planeacion AS planeacion_fecha_de_planeacion, e.idc_fecha_solicitud_cita AS planeacion_idc_fecha_solicitud_cita, e.idc_fecha_confirmacion_cita AS planeacion_idc_fecha_confirmacion_cita, e.tipo_de_ingenieria AS planeacion_tipo_de_ingenieria, e.nombres_del_personal_a_asistir_de_idc AS planeacion_nombres_del_personal_a_asistir_de_idc, e.etv_fecha_solicitud_cita AS planeacion_etv_fecha_solicitud_cita, e.etv_fecha_confirmacion_cita AS planeacion_etv_fecha_confirmacion_cita, e.empresa_de_inmuebles AS planeacion_empresa_de_inmuebles, e.personal_de_inmuebles AS planeacion_personal_de_inmuebles, e.empresa_de_cableado AS planeacion_empresa_de_cableado, e.personal_de_cableado AS planeacion_personal_de_cableado, e.empresa_de_comunicaciones AS planeacion_empresa_de_comunicaciones, e.personal_de_comunicaciones AS planeacion_personal_de_comunicaciones, e.nombre_interventor AS planeacion_nombre_interventor, e.fecha_de_cita AS planeacion_fecha_de_cita, e.ticket_ob AS planeacion_ticket_ob, e.ticket_remedy AS planeacion_ticket_remedy, e.tas_idc AS planeacion_tas_idc, e.tas_etv AS planeacion_tas_etv, e.tas_seguimiento AS planeacion_tas_seguimiento, e.folio_inmuebles AS planeacion_folio_inmuebles, e. STATUS AS planeacion_status, e.atm AS planeacion_atm, e.no_odt AS planeacion_no_odt, e.created_at AS planeacion_created_at, e.updated_at AS planeacion_updated_at, e.user_name AS planeacion_user_name, 
            f.nombre_responsable AS analisis_nombre_responsable, f.nombre_analista AS analisis_nombre_analista, f.fecha_de_analisis AS analisis_fecha_de_analisis, f.aplican_acciones_de_idc AS analisis_aplican_acciones_de_idc, f.acciones_a_realizar_idc AS analisis_acciones_a_realizar_idc, f.mant_modulo_dispensador AS analisis_mant_modulo_dispensador, f.cambio_presentador_stacker AS analisis_cambio_presentador_stacker, f.cambio_cosumibles AS analisis_cambio_cosumibles, f.cambio_pick_picker_estractor AS analisis_cambio_pick_picker_estractor, f.cambio_caseteros AS analisis_cambio_caseteros, f.otro_1_dispensador AS analisis_otro_1_dispensador, f.nombre_pieza_otro_1_dispensador AS analisis_nombre_pieza_otro_1_dispensador, f.otro_2_dispensador AS analisis_otro_2_dispensador, f.nombre_pieza_otro_2_dispensador AS analisis_nombre_pieza_otro_2_dispensador, f.mant_modulo_aceptador AS analisis_mant_modulo_aceptador, f.cambio_escrow AS analisis_cambio_escrow, f.cambio_validador AS analisis_cambio_validador, f.cambio_shutter_cash_slot AS analisis_cambio_shutter_cash_slot, f.cambio_tarjeta_controladora AS analisis_cambio_tarjeta_controladora, f.otro_1_aceptador AS analisis_otro_1_aceptador, f.nombre_pieza_otro_1_aceptador AS analisis_nombre_pieza_otro_1_aceptador, f.otro_2_aceptador AS analisis_otro_2_aceptador, f.nombre_pieza_otro_2_aceptador AS analisis_nombre_pieza_otro_2_aceptador, f.mant_modulo_idc_cpu AS analisis_mant_modulo_idc_cpu, f.cambio_dd AS analisis_cambio_dd, f.cambio_cpu AS analisis_cambio_cpu, f.mant_modulo_lectora AS analisis_mant_modulo_lectora, f.cambio_lectora AS analisis_cambio_lectora, f.mant_modulo_impresora AS analisis_mant_modulo_impresora, f.cambio_impresora AS analisis_cambio_impresora, f.fuente_de_poder AS analisis_fuente_de_poder, f.teclado_teclado_lateral_touch_screen AS analisis_teclado_teclado_lateral_touch_screen, f.hopper AS analisis_hopper, f.monitor_pantalla AS analisis_monitor_pantalla, f.fascia AS analisis_fascia, f.se_requiere_planchado_de_sw AS analisis_se_requiere_planchado_de_sw, f.ver_requerida AS analisis_ver_requerida, f.aplican_acciones_de_inmuebles AS analisis_aplican_acciones_de_inmuebles, f.acciones_a_realizar_inmuebles AS analisis_acciones_a_realizar_inmuebles, f.revision_correcion_de_voltajes AS analisis_revision_correcion_de_voltajes, f.mejoramiento_de_imagen_limpieza AS analisis_mejoramiento_de_imagen_limpieza, f.revision_correccion_instalacion_de_aa AS analisis_revision_correccion_instalacion_de_aa, f.requiere_ups AS analisis_requiere_ups, f.aplican_acciones_de_cableado AS analisis_aplican_acciones_de_cableado, f.acciones_a_realizar_cableado AS analisis_acciones_a_realizar_cableado, f.correcciones_red_interna AS analisis_correcciones_red_interna, f.revision_de_cableado_y_status_de_equipos AS analisis_revision_de_cableado_y_status_de_equipos, f.retiro_y_o_limpieza_de_equipos AS analisis_retiro_y_o_limpieza_de_equipos, f.aplican_acciones_de_comunicaciones AS analisis_aplican_acciones_de_comunicaciones, f.acciones_a_realizar_comunicaciones AS analisis_acciones_a_realizar_comunicaciones, f.revision_enlace_equipo AS analisis_revision_enlace_equipo, f.prueba_de_calidad AS analisis_prueba_de_calidad, f.aplica_la_asistencia_de_un_interventor AS analisis_aplica_la_asistencia_de_un_interventor, f.tipo_de_visita AS analisis_tipo_de_visita, f. STATUS AS analisis_status, f.atm AS analisis_atm, f.no_odt AS analisis_no_odt, f.user_name AS analisis_user_name, f.created_at AS analisis_created_at, f.updated_at AS analisis_updated_at, f.id AS analisis_id_analisis,
                        g.id as gestion_id_gestion,g.atm AS gestion_atm, g.que_se_mide AS gestion_que_se_mide, g.nombre_del_gestor AS gestion_nombre_del_gestor, g.status_visita AS gestion_status_visita, g.arribo_interventor AS gestion_arribo_interventor, g.arribo_idc AS gestion_arribo_idc, g.empresa_dio_acceso AS gestion_empresa_dio_acceso, g.nombre_ing_dedicado_critico AS gestion_nombre_ing_dedicado_critico, g.asistio_ing_dedicado AS gestion_asistio_ing_dedicado, g.nombre_ing_sustituto AS gestion_nombre_ing_sustituto, g.etv_hora_apertura_boveda AS gestion_etv_hora_apertura_boveda, g.etv_hora_modo_supervisor AS gestion_etv_hora_modo_supervisor, g.etv_hora_consulta_administrativa AS gestion_etv_hora_consulta_administrativa, g.arribo_inmuebles AS gestion_arribo_inmuebles, g.nombre_tecnico_inmuebles AS gestion_nombre_tecnico_inmuebles, g.arribo_cableado AS gestion_arribo_cableado, g.nombre_tecnico_cableado AS gestion_nombre_tecnico_cableado, g.arribo_comunicaciones AS gestion_arribo_comunicaciones, g.hora_termino_de_la_visita AS gestion_hora_termino_de_la_visita, g.atm_queda_en_servicio AS gestion_atm_queda_en_servicio, g.status_finalizacion_del_odt AS gestion_status_finalizacion_del_odt, g.detalle_de_desviaciones AS gestion_detalle_de_desviaciones, g.pieza_por_la_cual_quedo_pendiente AS gestion_pieza_por_la_cual_quedo_pendiente, g.especifica_la_otra_pieza AS gestion_especifica_la_otra_pieza, g.comentarios AS gestion_comentarios, g. STATUS AS gestion_status, g.user_name AS gestion_user_name, g.created_at AS gestion_created_at, g.updated_at AS gestion_updated_at, g.no_odt AS gestion_no_odt,
            h.id AS cierres_id_cierres, h.atm AS cierres_atm, h.que_se_mide AS cierres_que_se_mide, h.modulo_vandalizado AS cierres_modulo_vandalizado, h.otro_especificar AS cierres_otro_especificar, h.fecha_escalamiento_dar AS cierres_fecha_escalamiento_dar, h.mant_modulo_dispensador AS cierres_mant_modulo_dispensador, h.cambio_presentador_stacker AS cierres_cambio_presentador_stacker, h.cambio_consumibles AS cierres_cambio_consumibles, h.cambio_pick_picker_estractor AS cierres_cambio_pick_picker_estractor, h.cambio_caseteros AS cierres_cambio_caseteros, h.otro_1_dispensador AS cierres_otro_1_dispensador, h.nombre_pieza_otro_1_dispensador AS cierres_nombre_pieza_otro_1_dispensador, h.otro_2_dispensador AS cierres_otro_2_dispensador, h.nombre_pieza_otro_2_dispensador AS cierres_nombre_pieza_otro_2_dispensador, h.mant_modulo_aceptador AS cierres_mant_modulo_aceptador, h.cambio_escrow AS cierres_cambio_escrow, h.cambio_validador AS cierres_cambio_validador, h.cambio_shutter_cash_slot AS cierres_cambio_shutter_cash_slot, h.cambio_tarjeta_cotroladora AS cierres_cambio_tarjeta_cotroladora, h.otro_1_aceptador AS cierres_otro_1_aceptador, h.nombre_pieza_otro_1_aceptador AS cierres_nombre_pieza_otro_1_aceptador, h.otro_2_aceptador AS cierres_otro_2_aceptador, h.nombre_pieza_otro_2_aceptador AS cierres_nombre_pieza_otro_2_aceptador, h.mant_modulo_cpu AS cierres_mant_modulo_cpu, h.cambio_dd AS cierres_cambio_dd, h.cambio_cpu AS cierres_cambio_cpu, h.mant_modulo_lectora AS cierres_mant_modulo_lectora, h.cambio_lectora AS cierres_cambio_lectora, h.mant_modulo_impresora AS cierres_mant_modulo_impresora, h.cambio_impresora AS cierres_cambio_impresora, h.fuente_de_poder AS cierres_fuente_de_poder, h.teclado_teclado_lateral_touch_screen AS cierres_teclado_teclado_lateral_touch_screen, h.hooper AS cierres_hooper, h.monitor_pantalla AS cierres_monitor_pantalla, h.fascia AS cierres_fascia, h.se_realizo_planchado_sw AS cierres_se_realizo_planchado_sw, h.version_instalada AS cierres_version_instalada, h.checker_visible AS cierres_checker_visible, h.activacion_checker AS cierres_activacion_checker, h.nombre_activa_checker AS cierres_nombre_activa_checker, h.csds_visible AS cierres_csds_visible, h.cierre_inmuebles AS cierres_cierre_inmuebles, h.revision_correcion_de_voltajes AS cierres_revision_correcion_de_voltajes, h.mejoramiento_de_imagen_limpieza AS cierres_mejoramiento_de_imagen_limpieza, h.revision_correccion_instalacion_de_aa AS cierres_revision_correccion_instalacion_de_aa, h.requiere_ups AS cierres_requiere_ups, h.cierre_cableado AS cierres_cierre_cableado, h.correcciones_red_interna AS cierres_correcciones_red_interna, h.revision_de_cableado_y_status_de_equipos AS cierres_revision_de_cableado_y_status_de_equipos, h.retiro_y_o_limpieza_de_equipos AS cierres_retiro_y_o_limpieza_de_equipos, h.cierre_comunicaciones AS cierres_cierre_comunicaciones, h.revision_enlace_equipos AS cierres_revision_enlace_equipos, h.prueba_de_calidad AS cierres_prueba_de_calidad, h.cierre_interventor AS cierres_cierre_interventor, h.cuenta_con_reporte_fotografico AS cierres_cuenta_con_reporte_fotografico, h.requiere_calcomanias AS cierres_requiere_calcomanias, h.requiere_mejoramiento_de_imagen AS cierres_requiere_mejoramiento_de_imagen, h. STATUS AS cierres_status, h.user_name AS cierres_user_name, h.created_at AS cierres_created_at, h.updated_at AS cierres_updated_at, h.cierre_idc AS cierres_cierre_idc, h.vandalismo AS cierres_vandalismo,
            case 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' then 0.663 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' then 0.663
                when a.que_se_mide ='RECEPTOR'  then 0.754
                when a.que_se_mide ='RECICLADOR'  then 1.5
            end as meta,
            case 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 0.663  then 'META'
              when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 0.663  then 'FUERA META'
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 0.663  then 'META' 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 0.663  then 'FUERA META' 
                when a.que_se_mide ='RECEPTOR' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 0.754 then 'META'
                when a.que_se_mide ='RECEPTOR'  AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 0.754 then 'FUERA META'
                when a.que_se_mide ='RECICLADOR' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 1.5 then 'META'
                when a.que_se_mide ='RECICLADOR' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 1.5 then 'FUERA META'
            end as en_meta
            FROM alta_datos_odt as a 
            LEFT JOIN indisp_al_dia as b ON a.atm=b.atm and a.que_se_mide=b.FUNCIONALIDAD 
            LEFT JOIN siga c on a.atm=c.pk_autoservicios_id
            LEFT JOIN cat_divisiones d on c.d_division=d.nombre
                        LEFT JOIN planeacion e on a.planeacion_id=e.id
                        LEFT JOIN analisis f on a.analisis_id=f.id
                        LEFT JOIN gestion g on a.gestion_id=g.id
                        LEFT JOIN cierres h on a.cierre_id=h.id
            where d.id in ($division)
                        and a.fecha_seleccion > DATE_ADD(NOW(),INTERVAL -70 DAY)
                        and a.status in ( 'Planeacion', 'Planeacion Pendiente', 'Planeacion Pendiente Fin', 'Completado')
            ORDER BY a.created_at DESC");

        return JsonResponse::collectionResponse($data);
    }

    public function getAltaDatosOdtAnalisis(Request $request){

        $userId = Auth::id();
        $divisiones = DB::table('users_divisiones')
                  ->where('user_id', $userId)
                  ->select('users_divisiones.*')
                  ->get();

        $division=collect($divisiones)->implode('division_id',',');
        //var_dump($division);

        $data = DB::select("SELECT 
            b.id as indisp_al_dia_id, 
            a.atm, 
            a.que_se_mide, 
            a.fecha_seleccion, 
                        CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) as indispo_de_seleccion,
                        CAST(b.MANTENIMIENTO_TECNICO AS DECIMAL(10,4)) as mantenimiento_tecnico,
                        CAST(b.RECUPERACION_MANUAL AS DECIMAL(10,4)) as recuperacion_manual,
                        CAST(a.total_transacciones AS DECIMAL(10,0)) as total_transacciones,
                        CAST(b.MANTENIMIENTO_TECNICO AS DECIMAL(10,4))  + CAST(b.RECUPERACION_MANUAL AS DECIMAL(10,4)) as suma_mant_tecnico_recup_manual,
            a.universo_mes_actual, 
            a.caso_inicial, 
            a.fecha_escalado_dar, 
            a.fecha_escalado_banca, 
            a.user_name,
            a.gestionado,
            a.id,
            a.status,
            a.planeacion_id,
            a.analisis_id,
            a.gestion_id,
            a.cierre_id,
            c.d_local,
            c.d_division,
            c.d_idc,
            c.d_sitio,
            c.d_division,
            c.d_estatus_autoservicio,
            d.id as division_id,
            e.id AS planeacion_id_planeacion, e.responsable_planeacion AS planeacion_responsable_planeacion, e.fecha_de_planeacion AS planeacion_fecha_de_planeacion, e.idc_fecha_solicitud_cita AS planeacion_idc_fecha_solicitud_cita, e.idc_fecha_confirmacion_cita AS planeacion_idc_fecha_confirmacion_cita, e.tipo_de_ingenieria AS planeacion_tipo_de_ingenieria, e.nombres_del_personal_a_asistir_de_idc AS planeacion_nombres_del_personal_a_asistir_de_idc, e.etv_fecha_solicitud_cita AS planeacion_etv_fecha_solicitud_cita, e.etv_fecha_confirmacion_cita AS planeacion_etv_fecha_confirmacion_cita, e.empresa_de_inmuebles AS planeacion_empresa_de_inmuebles, e.personal_de_inmuebles AS planeacion_personal_de_inmuebles, e.empresa_de_cableado AS planeacion_empresa_de_cableado, e.personal_de_cableado AS planeacion_personal_de_cableado, e.empresa_de_comunicaciones AS planeacion_empresa_de_comunicaciones, e.personal_de_comunicaciones AS planeacion_personal_de_comunicaciones, e.nombre_interventor AS planeacion_nombre_interventor, e.fecha_de_cita AS planeacion_fecha_de_cita, e.ticket_ob AS planeacion_ticket_ob, e.ticket_remedy AS planeacion_ticket_remedy, e.tas_idc AS planeacion_tas_idc, e.tas_etv AS planeacion_tas_etv, e.tas_seguimiento AS planeacion_tas_seguimiento, e.folio_inmuebles AS planeacion_folio_inmuebles, e. STATUS AS planeacion_status, e.atm AS planeacion_atm, e.no_odt AS planeacion_no_odt, e.created_at AS planeacion_created_at, e.updated_at AS planeacion_updated_at, e.user_name AS planeacion_user_name, 
            f.nombre_responsable AS analisis_nombre_responsable, f.nombre_analista AS analisis_nombre_analista, f.fecha_de_analisis AS analisis_fecha_de_analisis, f.aplican_acciones_de_idc AS analisis_aplican_acciones_de_idc, f.acciones_a_realizar_idc AS analisis_acciones_a_realizar_idc, f.mant_modulo_dispensador AS analisis_mant_modulo_dispensador, f.cambio_presentador_stacker AS analisis_cambio_presentador_stacker, f.cambio_cosumibles AS analisis_cambio_cosumibles, f.cambio_pick_picker_estractor AS analisis_cambio_pick_picker_estractor, f.cambio_caseteros AS analisis_cambio_caseteros, f.otro_1_dispensador AS analisis_otro_1_dispensador, f.nombre_pieza_otro_1_dispensador AS analisis_nombre_pieza_otro_1_dispensador, f.otro_2_dispensador AS analisis_otro_2_dispensador, f.nombre_pieza_otro_2_dispensador AS analisis_nombre_pieza_otro_2_dispensador, f.mant_modulo_aceptador AS analisis_mant_modulo_aceptador, f.cambio_escrow AS analisis_cambio_escrow, f.cambio_validador AS analisis_cambio_validador, f.cambio_shutter_cash_slot AS analisis_cambio_shutter_cash_slot, f.cambio_tarjeta_controladora AS analisis_cambio_tarjeta_controladora, f.otro_1_aceptador AS analisis_otro_1_aceptador, f.nombre_pieza_otro_1_aceptador AS analisis_nombre_pieza_otro_1_aceptador, f.otro_2_aceptador AS analisis_otro_2_aceptador, f.nombre_pieza_otro_2_aceptador AS analisis_nombre_pieza_otro_2_aceptador, f.mant_modulo_idc_cpu AS analisis_mant_modulo_idc_cpu, f.cambio_dd AS analisis_cambio_dd, f.cambio_cpu AS analisis_cambio_cpu, f.mant_modulo_lectora AS analisis_mant_modulo_lectora, f.cambio_lectora AS analisis_cambio_lectora, f.mant_modulo_impresora AS analisis_mant_modulo_impresora, f.cambio_impresora AS analisis_cambio_impresora, f.fuente_de_poder AS analisis_fuente_de_poder, f.teclado_teclado_lateral_touch_screen AS analisis_teclado_teclado_lateral_touch_screen, f.hopper AS analisis_hopper, f.monitor_pantalla AS analisis_monitor_pantalla, f.fascia AS analisis_fascia, f.se_requiere_planchado_de_sw AS analisis_se_requiere_planchado_de_sw, f.ver_requerida AS analisis_ver_requerida, f.aplican_acciones_de_inmuebles AS analisis_aplican_acciones_de_inmuebles, f.acciones_a_realizar_inmuebles AS analisis_acciones_a_realizar_inmuebles, f.revision_correcion_de_voltajes AS analisis_revision_correcion_de_voltajes, f.mejoramiento_de_imagen_limpieza AS analisis_mejoramiento_de_imagen_limpieza, f.revision_correccion_instalacion_de_aa AS analisis_revision_correccion_instalacion_de_aa, f.requiere_ups AS analisis_requiere_ups, f.aplican_acciones_de_cableado AS analisis_aplican_acciones_de_cableado, f.acciones_a_realizar_cableado AS analisis_acciones_a_realizar_cableado, f.correcciones_red_interna AS analisis_correcciones_red_interna, f.revision_de_cableado_y_status_de_equipos AS analisis_revision_de_cableado_y_status_de_equipos, f.retiro_y_o_limpieza_de_equipos AS analisis_retiro_y_o_limpieza_de_equipos, f.aplican_acciones_de_comunicaciones AS analisis_aplican_acciones_de_comunicaciones, f.acciones_a_realizar_comunicaciones AS analisis_acciones_a_realizar_comunicaciones, f.revision_enlace_equipo AS analisis_revision_enlace_equipo, f.prueba_de_calidad AS analisis_prueba_de_calidad, f.aplica_la_asistencia_de_un_interventor AS analisis_aplica_la_asistencia_de_un_interventor, f.tipo_de_visita AS analisis_tipo_de_visita, f. STATUS AS analisis_status, f.atm AS analisis_atm, f.no_odt AS analisis_no_odt, f.user_name AS analisis_user_name, f.created_at AS analisis_created_at, f.updated_at AS analisis_updated_at, f.id AS analisis_id_analisis,
                        g.id as gestion_id_gestion,g.atm AS gestion_atm, g.que_se_mide AS gestion_que_se_mide, g.nombre_del_gestor AS gestion_nombre_del_gestor, g.status_visita AS gestion_status_visita, g.arribo_interventor AS gestion_arribo_interventor, g.arribo_idc AS gestion_arribo_idc, g.empresa_dio_acceso AS gestion_empresa_dio_acceso, g.nombre_ing_dedicado_critico AS gestion_nombre_ing_dedicado_critico, g.asistio_ing_dedicado AS gestion_asistio_ing_dedicado, g.nombre_ing_sustituto AS gestion_nombre_ing_sustituto, g.etv_hora_apertura_boveda AS gestion_etv_hora_apertura_boveda, g.etv_hora_modo_supervisor AS gestion_etv_hora_modo_supervisor, g.etv_hora_consulta_administrativa AS gestion_etv_hora_consulta_administrativa, g.arribo_inmuebles AS gestion_arribo_inmuebles, g.nombre_tecnico_inmuebles AS gestion_nombre_tecnico_inmuebles, g.arribo_cableado AS gestion_arribo_cableado, g.nombre_tecnico_cableado AS gestion_nombre_tecnico_cableado, g.arribo_comunicaciones AS gestion_arribo_comunicaciones, g.hora_termino_de_la_visita AS gestion_hora_termino_de_la_visita, g.atm_queda_en_servicio AS gestion_atm_queda_en_servicio, g.status_finalizacion_del_odt AS gestion_status_finalizacion_del_odt, g.detalle_de_desviaciones AS gestion_detalle_de_desviaciones, g.pieza_por_la_cual_quedo_pendiente AS gestion_pieza_por_la_cual_quedo_pendiente, g.especifica_la_otra_pieza AS gestion_especifica_la_otra_pieza, g.comentarios AS gestion_comentarios, g. STATUS AS gestion_status, g.user_name AS gestion_user_name, g.created_at AS gestion_created_at, g.updated_at AS gestion_updated_at, g.no_odt AS gestion_no_odt,
            h.id AS cierres_id_cierres, h.atm AS cierres_atm, h.que_se_mide AS cierres_que_se_mide, h.modulo_vandalizado AS cierres_modulo_vandalizado, h.otro_especificar AS cierres_otro_especificar, h.fecha_escalamiento_dar AS cierres_fecha_escalamiento_dar, h.mant_modulo_dispensador AS cierres_mant_modulo_dispensador, h.cambio_presentador_stacker AS cierres_cambio_presentador_stacker, h.cambio_consumibles AS cierres_cambio_consumibles, h.cambio_pick_picker_estractor AS cierres_cambio_pick_picker_estractor, h.cambio_caseteros AS cierres_cambio_caseteros, h.otro_1_dispensador AS cierres_otro_1_dispensador, h.nombre_pieza_otro_1_dispensador AS cierres_nombre_pieza_otro_1_dispensador, h.otro_2_dispensador AS cierres_otro_2_dispensador, h.nombre_pieza_otro_2_dispensador AS cierres_nombre_pieza_otro_2_dispensador, h.mant_modulo_aceptador AS cierres_mant_modulo_aceptador, h.cambio_escrow AS cierres_cambio_escrow, h.cambio_validador AS cierres_cambio_validador, h.cambio_shutter_cash_slot AS cierres_cambio_shutter_cash_slot, h.cambio_tarjeta_cotroladora AS cierres_cambio_tarjeta_cotroladora, h.otro_1_aceptador AS cierres_otro_1_aceptador, h.nombre_pieza_otro_1_aceptador AS cierres_nombre_pieza_otro_1_aceptador, h.otro_2_aceptador AS cierres_otro_2_aceptador, h.nombre_pieza_otro_2_aceptador AS cierres_nombre_pieza_otro_2_aceptador, h.mant_modulo_cpu AS cierres_mant_modulo_cpu, h.cambio_dd AS cierres_cambio_dd, h.cambio_cpu AS cierres_cambio_cpu, h.mant_modulo_lectora AS cierres_mant_modulo_lectora, h.cambio_lectora AS cierres_cambio_lectora, h.mant_modulo_impresora AS cierres_mant_modulo_impresora, h.cambio_impresora AS cierres_cambio_impresora, h.fuente_de_poder AS cierres_fuente_de_poder, h.teclado_teclado_lateral_touch_screen AS cierres_teclado_teclado_lateral_touch_screen, h.hooper AS cierres_hooper, h.monitor_pantalla AS cierres_monitor_pantalla, h.fascia AS cierres_fascia, h.se_realizo_planchado_sw AS cierres_se_realizo_planchado_sw, h.version_instalada AS cierres_version_instalada, h.checker_visible AS cierres_checker_visible, h.activacion_checker AS cierres_activacion_checker, h.nombre_activa_checker AS cierres_nombre_activa_checker, h.csds_visible AS cierres_csds_visible, h.cierre_inmuebles AS cierres_cierre_inmuebles, h.revision_correcion_de_voltajes AS cierres_revision_correcion_de_voltajes, h.mejoramiento_de_imagen_limpieza AS cierres_mejoramiento_de_imagen_limpieza, h.revision_correccion_instalacion_de_aa AS cierres_revision_correccion_instalacion_de_aa, h.requiere_ups AS cierres_requiere_ups, h.cierre_cableado AS cierres_cierre_cableado, h.correcciones_red_interna AS cierres_correcciones_red_interna, h.revision_de_cableado_y_status_de_equipos AS cierres_revision_de_cableado_y_status_de_equipos, h.retiro_y_o_limpieza_de_equipos AS cierres_retiro_y_o_limpieza_de_equipos, h.cierre_comunicaciones AS cierres_cierre_comunicaciones, h.revision_enlace_equipos AS cierres_revision_enlace_equipos, h.prueba_de_calidad AS cierres_prueba_de_calidad, h.cierre_interventor AS cierres_cierre_interventor, h.cuenta_con_reporte_fotografico AS cierres_cuenta_con_reporte_fotografico, h.requiere_calcomanias AS cierres_requiere_calcomanias, h.requiere_mejoramiento_de_imagen AS cierres_requiere_mejoramiento_de_imagen, h. STATUS AS cierres_status, h.user_name AS cierres_user_name, h.created_at AS cierres_created_at, h.updated_at AS cierres_updated_at, h.cierre_idc AS cierres_cierre_idc, h.vandalismo AS cierres_vandalismo,
            case 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' then 0.663 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' then 0.663
                when a.que_se_mide ='RECEPTOR'  then 0.754
                when a.que_se_mide ='RECICLADOR'  then 1.5
            end as meta,
            case 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 0.663  then 'META'
              when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 0.663  then 'FUERA META'
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 0.663  then 'META' 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 0.663  then 'FUERA META' 
                when a.que_se_mide ='RECEPTOR' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 0.754 then 'META'
                when a.que_se_mide ='RECEPTOR'  AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 0.754 then 'FUERA META'
                when a.que_se_mide ='RECICLADOR' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 1.5 then 'META'
                when a.que_se_mide ='RECICLADOR' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 1.5 then 'FUERA META'
            end as en_meta
            FROM alta_datos_odt as a 
            LEFT JOIN indisp_al_dia as b ON a.atm=b.atm and a.que_se_mide=b.FUNCIONALIDAD 
            LEFT JOIN siga c on a.atm=c.pk_autoservicios_id
            LEFT JOIN cat_divisiones d on c.d_division=d.nombre
                        LEFT JOIN planeacion e on a.planeacion_id=e.id
                        LEFT JOIN analisis f on a.analisis_id=f.id
                        LEFT JOIN gestion g on a.gestion_id=g.id
                        LEFT JOIN cierres h on a.cierre_id=h.id
            where d.id in ($division)
                        and a.fecha_seleccion > DATE_ADD(NOW(),INTERVAL -70 DAY)
                        and a.status in ( 'Analisis', 'Analisis Pendiente', 'Completado')
            ORDER BY a.created_at DESC");

        return JsonResponse::collectionResponse($data);
    }

    public function getAltaDatosOdtGestion(Request $request){

        $userId = Auth::id();
        $divisiones = DB::table('users_divisiones')
                  ->where('user_id', $userId)
                  ->select('users_divisiones.*')
                  ->get();

        $division=collect($divisiones)->implode('division_id',',');
        //var_dump($division);

        $data = DB::select("SELECT 
            b.id as indisp_al_dia_id, 
            a.atm, 
            a.que_se_mide, 
            a.fecha_seleccion, 
                        CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) as indispo_de_seleccion,
                        CAST(b.MANTENIMIENTO_TECNICO AS DECIMAL(10,4)) as mantenimiento_tecnico,
                        CAST(b.RECUPERACION_MANUAL AS DECIMAL(10,4)) as recuperacion_manual,
                        CAST(a.total_transacciones AS DECIMAL(10,0)) as total_transacciones,
                        CAST(b.MANTENIMIENTO_TECNICO AS DECIMAL(10,4))  + CAST(b.RECUPERACION_MANUAL AS DECIMAL(10,4)) as suma_mant_tecnico_recup_manual,
            a.universo_mes_actual, 
            a.caso_inicial, 
            a.fecha_escalado_dar, 
            a.fecha_escalado_banca, 
            a.user_name,
            a.gestionado,
            a.id,
            a.status,
            a.planeacion_id,
            a.analisis_id,
            a.gestion_id,
            a.cierre_id,
            c.d_local,
            c.d_division,
            c.d_idc,
            c.d_sitio,
            c.d_division,
            c.d_estatus_autoservicio,
            d.id as division_id,
            e.id AS planeacion_id_planeacion, e.responsable_planeacion AS planeacion_responsable_planeacion, e.fecha_de_planeacion AS planeacion_fecha_de_planeacion, e.idc_fecha_solicitud_cita AS planeacion_idc_fecha_solicitud_cita, e.idc_fecha_confirmacion_cita AS planeacion_idc_fecha_confirmacion_cita, e.tipo_de_ingenieria AS planeacion_tipo_de_ingenieria, e.nombres_del_personal_a_asistir_de_idc AS planeacion_nombres_del_personal_a_asistir_de_idc, e.etv_fecha_solicitud_cita AS planeacion_etv_fecha_solicitud_cita, e.etv_fecha_confirmacion_cita AS planeacion_etv_fecha_confirmacion_cita, e.empresa_de_inmuebles AS planeacion_empresa_de_inmuebles, e.personal_de_inmuebles AS planeacion_personal_de_inmuebles, e.empresa_de_cableado AS planeacion_empresa_de_cableado, e.personal_de_cableado AS planeacion_personal_de_cableado, e.empresa_de_comunicaciones AS planeacion_empresa_de_comunicaciones, e.personal_de_comunicaciones AS planeacion_personal_de_comunicaciones, e.nombre_interventor AS planeacion_nombre_interventor, e.fecha_de_cita AS planeacion_fecha_de_cita, e.ticket_ob AS planeacion_ticket_ob, e.ticket_remedy AS planeacion_ticket_remedy, e.tas_idc AS planeacion_tas_idc, e.tas_etv AS planeacion_tas_etv, e.tas_seguimiento AS planeacion_tas_seguimiento, e.folio_inmuebles AS planeacion_folio_inmuebles, e. STATUS AS planeacion_status, e.atm AS planeacion_atm, e.no_odt AS planeacion_no_odt, e.created_at AS planeacion_created_at, e.updated_at AS planeacion_updated_at, e.user_name AS planeacion_user_name, 
            f.nombre_responsable AS analisis_nombre_responsable, f.nombre_analista AS analisis_nombre_analista, f.fecha_de_analisis AS analisis_fecha_de_analisis, f.aplican_acciones_de_idc AS analisis_aplican_acciones_de_idc, f.acciones_a_realizar_idc AS analisis_acciones_a_realizar_idc, f.mant_modulo_dispensador AS analisis_mant_modulo_dispensador, f.cambio_presentador_stacker AS analisis_cambio_presentador_stacker, f.cambio_cosumibles AS analisis_cambio_cosumibles, f.cambio_pick_picker_estractor AS analisis_cambio_pick_picker_estractor, f.cambio_caseteros AS analisis_cambio_caseteros, f.otro_1_dispensador AS analisis_otro_1_dispensador, f.nombre_pieza_otro_1_dispensador AS analisis_nombre_pieza_otro_1_dispensador, f.otro_2_dispensador AS analisis_otro_2_dispensador, f.nombre_pieza_otro_2_dispensador AS analisis_nombre_pieza_otro_2_dispensador, f.mant_modulo_aceptador AS analisis_mant_modulo_aceptador, f.cambio_escrow AS analisis_cambio_escrow, f.cambio_validador AS analisis_cambio_validador, f.cambio_shutter_cash_slot AS analisis_cambio_shutter_cash_slot, f.cambio_tarjeta_controladora AS analisis_cambio_tarjeta_controladora, f.otro_1_aceptador AS analisis_otro_1_aceptador, f.nombre_pieza_otro_1_aceptador AS analisis_nombre_pieza_otro_1_aceptador, f.otro_2_aceptador AS analisis_otro_2_aceptador, f.nombre_pieza_otro_2_aceptador AS analisis_nombre_pieza_otro_2_aceptador, f.mant_modulo_idc_cpu AS analisis_mant_modulo_idc_cpu, f.cambio_dd AS analisis_cambio_dd, f.cambio_cpu AS analisis_cambio_cpu, f.mant_modulo_lectora AS analisis_mant_modulo_lectora, f.cambio_lectora AS analisis_cambio_lectora, f.mant_modulo_impresora AS analisis_mant_modulo_impresora, f.cambio_impresora AS analisis_cambio_impresora, f.fuente_de_poder AS analisis_fuente_de_poder, f.teclado_teclado_lateral_touch_screen AS analisis_teclado_teclado_lateral_touch_screen, f.hopper AS analisis_hopper, f.monitor_pantalla AS analisis_monitor_pantalla, f.fascia AS analisis_fascia, f.se_requiere_planchado_de_sw AS analisis_se_requiere_planchado_de_sw, f.ver_requerida AS analisis_ver_requerida, f.aplican_acciones_de_inmuebles AS analisis_aplican_acciones_de_inmuebles, f.acciones_a_realizar_inmuebles AS analisis_acciones_a_realizar_inmuebles, f.revision_correcion_de_voltajes AS analisis_revision_correcion_de_voltajes, f.mejoramiento_de_imagen_limpieza AS analisis_mejoramiento_de_imagen_limpieza, f.revision_correccion_instalacion_de_aa AS analisis_revision_correccion_instalacion_de_aa, f.requiere_ups AS analisis_requiere_ups, f.aplican_acciones_de_cableado AS analisis_aplican_acciones_de_cableado, f.acciones_a_realizar_cableado AS analisis_acciones_a_realizar_cableado, f.correcciones_red_interna AS analisis_correcciones_red_interna, f.revision_de_cableado_y_status_de_equipos AS analisis_revision_de_cableado_y_status_de_equipos, f.retiro_y_o_limpieza_de_equipos AS analisis_retiro_y_o_limpieza_de_equipos, f.aplican_acciones_de_comunicaciones AS analisis_aplican_acciones_de_comunicaciones, f.acciones_a_realizar_comunicaciones AS analisis_acciones_a_realizar_comunicaciones, f.revision_enlace_equipo AS analisis_revision_enlace_equipo, f.prueba_de_calidad AS analisis_prueba_de_calidad, f.aplica_la_asistencia_de_un_interventor AS analisis_aplica_la_asistencia_de_un_interventor, f.tipo_de_visita AS analisis_tipo_de_visita, f. STATUS AS analisis_status, f.atm AS analisis_atm, f.no_odt AS analisis_no_odt, f.user_name AS analisis_user_name, f.created_at AS analisis_created_at, f.updated_at AS analisis_updated_at, f.id AS analisis_id_analisis,
                        g.id as gestion_id_gestion,g.atm AS gestion_atm, g.que_se_mide AS gestion_que_se_mide, g.nombre_del_gestor AS gestion_nombre_del_gestor, g.status_visita AS gestion_status_visita, g.arribo_interventor AS gestion_arribo_interventor, g.arribo_idc AS gestion_arribo_idc, g.empresa_dio_acceso AS gestion_empresa_dio_acceso, g.nombre_ing_dedicado_critico AS gestion_nombre_ing_dedicado_critico, g.asistio_ing_dedicado AS gestion_asistio_ing_dedicado, g.nombre_ing_sustituto AS gestion_nombre_ing_sustituto, g.etv_hora_apertura_boveda AS gestion_etv_hora_apertura_boveda, g.etv_hora_modo_supervisor AS gestion_etv_hora_modo_supervisor, g.etv_hora_consulta_administrativa AS gestion_etv_hora_consulta_administrativa, g.arribo_inmuebles AS gestion_arribo_inmuebles, g.nombre_tecnico_inmuebles AS gestion_nombre_tecnico_inmuebles, g.arribo_cableado AS gestion_arribo_cableado, g.nombre_tecnico_cableado AS gestion_nombre_tecnico_cableado, g.arribo_comunicaciones AS gestion_arribo_comunicaciones, g.hora_termino_de_la_visita AS gestion_hora_termino_de_la_visita, g.atm_queda_en_servicio AS gestion_atm_queda_en_servicio, g.status_finalizacion_del_odt AS gestion_status_finalizacion_del_odt, g.detalle_de_desviaciones AS gestion_detalle_de_desviaciones, g.pieza_por_la_cual_quedo_pendiente AS gestion_pieza_por_la_cual_quedo_pendiente, g.especifica_la_otra_pieza AS gestion_especifica_la_otra_pieza, g.comentarios AS gestion_comentarios, g. STATUS AS gestion_status, g.user_name AS gestion_user_name, g.created_at AS gestion_created_at, g.updated_at AS gestion_updated_at, g.no_odt AS gestion_no_odt,
            h.id AS cierres_id_cierres, h.atm AS cierres_atm, h.que_se_mide AS cierres_que_se_mide, h.modulo_vandalizado AS cierres_modulo_vandalizado, h.otro_especificar AS cierres_otro_especificar, h.fecha_escalamiento_dar AS cierres_fecha_escalamiento_dar, h.mant_modulo_dispensador AS cierres_mant_modulo_dispensador, h.cambio_presentador_stacker AS cierres_cambio_presentador_stacker, h.cambio_consumibles AS cierres_cambio_consumibles, h.cambio_pick_picker_estractor AS cierres_cambio_pick_picker_estractor, h.cambio_caseteros AS cierres_cambio_caseteros, h.otro_1_dispensador AS cierres_otro_1_dispensador, h.nombre_pieza_otro_1_dispensador AS cierres_nombre_pieza_otro_1_dispensador, h.otro_2_dispensador AS cierres_otro_2_dispensador, h.nombre_pieza_otro_2_dispensador AS cierres_nombre_pieza_otro_2_dispensador, h.mant_modulo_aceptador AS cierres_mant_modulo_aceptador, h.cambio_escrow AS cierres_cambio_escrow, h.cambio_validador AS cierres_cambio_validador, h.cambio_shutter_cash_slot AS cierres_cambio_shutter_cash_slot, h.cambio_tarjeta_cotroladora AS cierres_cambio_tarjeta_cotroladora, h.otro_1_aceptador AS cierres_otro_1_aceptador, h.nombre_pieza_otro_1_aceptador AS cierres_nombre_pieza_otro_1_aceptador, h.otro_2_aceptador AS cierres_otro_2_aceptador, h.nombre_pieza_otro_2_aceptador AS cierres_nombre_pieza_otro_2_aceptador, h.mant_modulo_cpu AS cierres_mant_modulo_cpu, h.cambio_dd AS cierres_cambio_dd, h.cambio_cpu AS cierres_cambio_cpu, h.mant_modulo_lectora AS cierres_mant_modulo_lectora, h.cambio_lectora AS cierres_cambio_lectora, h.mant_modulo_impresora AS cierres_mant_modulo_impresora, h.cambio_impresora AS cierres_cambio_impresora, h.fuente_de_poder AS cierres_fuente_de_poder, h.teclado_teclado_lateral_touch_screen AS cierres_teclado_teclado_lateral_touch_screen, h.hooper AS cierres_hooper, h.monitor_pantalla AS cierres_monitor_pantalla, h.fascia AS cierres_fascia, h.se_realizo_planchado_sw AS cierres_se_realizo_planchado_sw, h.version_instalada AS cierres_version_instalada, h.checker_visible AS cierres_checker_visible, h.activacion_checker AS cierres_activacion_checker, h.nombre_activa_checker AS cierres_nombre_activa_checker, h.csds_visible AS cierres_csds_visible, h.cierre_inmuebles AS cierres_cierre_inmuebles, h.revision_correcion_de_voltajes AS cierres_revision_correcion_de_voltajes, h.mejoramiento_de_imagen_limpieza AS cierres_mejoramiento_de_imagen_limpieza, h.revision_correccion_instalacion_de_aa AS cierres_revision_correccion_instalacion_de_aa, h.requiere_ups AS cierres_requiere_ups, h.cierre_cableado AS cierres_cierre_cableado, h.correcciones_red_interna AS cierres_correcciones_red_interna, h.revision_de_cableado_y_status_de_equipos AS cierres_revision_de_cableado_y_status_de_equipos, h.retiro_y_o_limpieza_de_equipos AS cierres_retiro_y_o_limpieza_de_equipos, h.cierre_comunicaciones AS cierres_cierre_comunicaciones, h.revision_enlace_equipos AS cierres_revision_enlace_equipos, h.prueba_de_calidad AS cierres_prueba_de_calidad, h.cierre_interventor AS cierres_cierre_interventor, h.cuenta_con_reporte_fotografico AS cierres_cuenta_con_reporte_fotografico, h.requiere_calcomanias AS cierres_requiere_calcomanias, h.requiere_mejoramiento_de_imagen AS cierres_requiere_mejoramiento_de_imagen, h. STATUS AS cierres_status, h.user_name AS cierres_user_name, h.created_at AS cierres_created_at, h.updated_at AS cierres_updated_at, h.cierre_idc AS cierres_cierre_idc, h.vandalismo AS cierres_vandalismo,
            case 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' then 0.663 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' then 0.663
                when a.que_se_mide ='RECEPTOR'  then 0.754
                when a.que_se_mide ='RECICLADOR'  then 1.5
            end as meta,
            case 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 0.663  then 'META'
              when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 0.663  then 'FUERA META'
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 0.663  then 'META' 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 0.663  then 'FUERA META' 
                when a.que_se_mide ='RECEPTOR' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 0.754 then 'META'
                when a.que_se_mide ='RECEPTOR'  AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 0.754 then 'FUERA META'
                when a.que_se_mide ='RECICLADOR' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 1.5 then 'META'
                when a.que_se_mide ='RECICLADOR' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 1.5 then 'FUERA META'
            end as en_meta
            FROM alta_datos_odt as a 
            LEFT JOIN indisp_al_dia as b ON a.atm=b.atm and a.que_se_mide=b.FUNCIONALIDAD 
            LEFT JOIN siga c on a.atm=c.pk_autoservicios_id
            LEFT JOIN cat_divisiones d on c.d_division=d.nombre
                        LEFT JOIN planeacion e on a.planeacion_id=e.id
                        LEFT JOIN analisis f on a.analisis_id=f.id
                        LEFT JOIN gestion g on a.gestion_id=g.id
                        LEFT JOIN cierres h on a.cierre_id=h.id
            where d.id in ($division)
                        and a.fecha_seleccion > DATE_ADD(NOW(),INTERVAL -70 DAY)
                        and a.status in ( 'Gestion', 'Gestion Pendiente', 'Completado')
            ORDER BY a.created_at DESC");

        return JsonResponse::collectionResponse($data);
    }

    public function getAltaDatosOdtCierres(Request $request){

        $userId = Auth::id();
        $divisiones = DB::table('users_divisiones')
                  ->where('user_id', $userId)
                  ->select('users_divisiones.*')
                  ->get();

        $division=collect($divisiones)->implode('division_id',',');
        //var_dump($division);

        $data = DB::select("SELECT 
            b.id as indisp_al_dia_id, 
            a.atm, 
            a.que_se_mide, 
            a.fecha_seleccion, 
                        CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) as indispo_de_seleccion,
                        CAST(b.MANTENIMIENTO_TECNICO AS DECIMAL(10,4)) as mantenimiento_tecnico,
                        CAST(b.RECUPERACION_MANUAL AS DECIMAL(10,4)) as recuperacion_manual,
                        CAST(a.total_transacciones AS DECIMAL(10,0)) as total_transacciones,
                        CAST(b.MANTENIMIENTO_TECNICO AS DECIMAL(10,4))  + CAST(b.RECUPERACION_MANUAL AS DECIMAL(10,4)) as suma_mant_tecnico_recup_manual,
            a.universo_mes_actual, 
            a.caso_inicial, 
            a.fecha_escalado_dar, 
            a.fecha_escalado_banca, 
            a.user_name,
            a.gestionado,
            a.id,
            a.status,
            a.planeacion_id,
            a.analisis_id,
            a.gestion_id,
            a.cierre_id,
            c.d_local,
            c.d_division,
            c.d_idc,
            c.d_sitio,
            c.d_division,
            c.d_estatus_autoservicio,
            d.id as division_id,
            e.id AS planeacion_id_planeacion, e.responsable_planeacion AS planeacion_responsable_planeacion, e.fecha_de_planeacion AS planeacion_fecha_de_planeacion, e.idc_fecha_solicitud_cita AS planeacion_idc_fecha_solicitud_cita, e.idc_fecha_confirmacion_cita AS planeacion_idc_fecha_confirmacion_cita, e.tipo_de_ingenieria AS planeacion_tipo_de_ingenieria, e.nombres_del_personal_a_asistir_de_idc AS planeacion_nombres_del_personal_a_asistir_de_idc, e.etv_fecha_solicitud_cita AS planeacion_etv_fecha_solicitud_cita, e.etv_fecha_confirmacion_cita AS planeacion_etv_fecha_confirmacion_cita, e.empresa_de_inmuebles AS planeacion_empresa_de_inmuebles, e.personal_de_inmuebles AS planeacion_personal_de_inmuebles, e.empresa_de_cableado AS planeacion_empresa_de_cableado, e.personal_de_cableado AS planeacion_personal_de_cableado, e.empresa_de_comunicaciones AS planeacion_empresa_de_comunicaciones, e.personal_de_comunicaciones AS planeacion_personal_de_comunicaciones, e.nombre_interventor AS planeacion_nombre_interventor, e.fecha_de_cita AS planeacion_fecha_de_cita, e.ticket_ob AS planeacion_ticket_ob, e.ticket_remedy AS planeacion_ticket_remedy, e.tas_idc AS planeacion_tas_idc, e.tas_etv AS planeacion_tas_etv, e.tas_seguimiento AS planeacion_tas_seguimiento, e.folio_inmuebles AS planeacion_folio_inmuebles, e. STATUS AS planeacion_status, e.atm AS planeacion_atm, e.no_odt AS planeacion_no_odt, e.created_at AS planeacion_created_at, e.updated_at AS planeacion_updated_at, e.user_name AS planeacion_user_name, 
            f.nombre_responsable AS analisis_nombre_responsable, f.nombre_analista AS analisis_nombre_analista, f.fecha_de_analisis AS analisis_fecha_de_analisis, f.aplican_acciones_de_idc AS analisis_aplican_acciones_de_idc, f.acciones_a_realizar_idc AS analisis_acciones_a_realizar_idc, f.mant_modulo_dispensador AS analisis_mant_modulo_dispensador, f.cambio_presentador_stacker AS analisis_cambio_presentador_stacker, f.cambio_cosumibles AS analisis_cambio_cosumibles, f.cambio_pick_picker_estractor AS analisis_cambio_pick_picker_estractor, f.cambio_caseteros AS analisis_cambio_caseteros, f.otro_1_dispensador AS analisis_otro_1_dispensador, f.nombre_pieza_otro_1_dispensador AS analisis_nombre_pieza_otro_1_dispensador, f.otro_2_dispensador AS analisis_otro_2_dispensador, f.nombre_pieza_otro_2_dispensador AS analisis_nombre_pieza_otro_2_dispensador, f.mant_modulo_aceptador AS analisis_mant_modulo_aceptador, f.cambio_escrow AS analisis_cambio_escrow, f.cambio_validador AS analisis_cambio_validador, f.cambio_shutter_cash_slot AS analisis_cambio_shutter_cash_slot, f.cambio_tarjeta_controladora AS analisis_cambio_tarjeta_controladora, f.otro_1_aceptador AS analisis_otro_1_aceptador, f.nombre_pieza_otro_1_aceptador AS analisis_nombre_pieza_otro_1_aceptador, f.otro_2_aceptador AS analisis_otro_2_aceptador, f.nombre_pieza_otro_2_aceptador AS analisis_nombre_pieza_otro_2_aceptador, f.mant_modulo_idc_cpu AS analisis_mant_modulo_idc_cpu, f.cambio_dd AS analisis_cambio_dd, f.cambio_cpu AS analisis_cambio_cpu, f.mant_modulo_lectora AS analisis_mant_modulo_lectora, f.cambio_lectora AS analisis_cambio_lectora, f.mant_modulo_impresora AS analisis_mant_modulo_impresora, f.cambio_impresora AS analisis_cambio_impresora, f.fuente_de_poder AS analisis_fuente_de_poder, f.teclado_teclado_lateral_touch_screen AS analisis_teclado_teclado_lateral_touch_screen, f.hopper AS analisis_hopper, f.monitor_pantalla AS analisis_monitor_pantalla, f.fascia AS analisis_fascia, f.se_requiere_planchado_de_sw AS analisis_se_requiere_planchado_de_sw, f.ver_requerida AS analisis_ver_requerida, f.aplican_acciones_de_inmuebles AS analisis_aplican_acciones_de_inmuebles, f.acciones_a_realizar_inmuebles AS analisis_acciones_a_realizar_inmuebles, f.revision_correcion_de_voltajes AS analisis_revision_correcion_de_voltajes, f.mejoramiento_de_imagen_limpieza AS analisis_mejoramiento_de_imagen_limpieza, f.revision_correccion_instalacion_de_aa AS analisis_revision_correccion_instalacion_de_aa, f.requiere_ups AS analisis_requiere_ups, f.aplican_acciones_de_cableado AS analisis_aplican_acciones_de_cableado, f.acciones_a_realizar_cableado AS analisis_acciones_a_realizar_cableado, f.correcciones_red_interna AS analisis_correcciones_red_interna, f.revision_de_cableado_y_status_de_equipos AS analisis_revision_de_cableado_y_status_de_equipos, f.retiro_y_o_limpieza_de_equipos AS analisis_retiro_y_o_limpieza_de_equipos, f.aplican_acciones_de_comunicaciones AS analisis_aplican_acciones_de_comunicaciones, f.acciones_a_realizar_comunicaciones AS analisis_acciones_a_realizar_comunicaciones, f.revision_enlace_equipo AS analisis_revision_enlace_equipo, f.prueba_de_calidad AS analisis_prueba_de_calidad, f.aplica_la_asistencia_de_un_interventor AS analisis_aplica_la_asistencia_de_un_interventor, f.tipo_de_visita AS analisis_tipo_de_visita, f. STATUS AS analisis_status, f.atm AS analisis_atm, f.no_odt AS analisis_no_odt, f.user_name AS analisis_user_name, f.created_at AS analisis_created_at, f.updated_at AS analisis_updated_at, f.id AS analisis_id_analisis,
                        g.id as gestion_id_gestion,g.atm AS gestion_atm, g.que_se_mide AS gestion_que_se_mide, g.nombre_del_gestor AS gestion_nombre_del_gestor, g.status_visita AS gestion_status_visita, g.arribo_interventor AS gestion_arribo_interventor, g.arribo_idc AS gestion_arribo_idc, g.empresa_dio_acceso AS gestion_empresa_dio_acceso, g.nombre_ing_dedicado_critico AS gestion_nombre_ing_dedicado_critico, g.asistio_ing_dedicado AS gestion_asistio_ing_dedicado, g.nombre_ing_sustituto AS gestion_nombre_ing_sustituto, g.etv_hora_apertura_boveda AS gestion_etv_hora_apertura_boveda, g.etv_hora_modo_supervisor AS gestion_etv_hora_modo_supervisor, g.etv_hora_consulta_administrativa AS gestion_etv_hora_consulta_administrativa, g.arribo_inmuebles AS gestion_arribo_inmuebles, g.nombre_tecnico_inmuebles AS gestion_nombre_tecnico_inmuebles, g.arribo_cableado AS gestion_arribo_cableado, g.nombre_tecnico_cableado AS gestion_nombre_tecnico_cableado, g.arribo_comunicaciones AS gestion_arribo_comunicaciones, g.hora_termino_de_la_visita AS gestion_hora_termino_de_la_visita, g.atm_queda_en_servicio AS gestion_atm_queda_en_servicio, g.status_finalizacion_del_odt AS gestion_status_finalizacion_del_odt, g.detalle_de_desviaciones AS gestion_detalle_de_desviaciones, g.pieza_por_la_cual_quedo_pendiente AS gestion_pieza_por_la_cual_quedo_pendiente, g.especifica_la_otra_pieza AS gestion_especifica_la_otra_pieza, g.comentarios AS gestion_comentarios, g. STATUS AS gestion_status, g.user_name AS gestion_user_name, g.created_at AS gestion_created_at, g.updated_at AS gestion_updated_at, g.no_odt AS gestion_no_odt,
            h.id AS cierres_id_cierres, h.atm AS cierres_atm, h.que_se_mide AS cierres_que_se_mide, h.modulo_vandalizado AS cierres_modulo_vandalizado, h.otro_especificar AS cierres_otro_especificar, h.fecha_escalamiento_dar AS cierres_fecha_escalamiento_dar, h.mant_modulo_dispensador AS cierres_mant_modulo_dispensador, h.cambio_presentador_stacker AS cierres_cambio_presentador_stacker, h.cambio_consumibles AS cierres_cambio_consumibles, h.cambio_pick_picker_estractor AS cierres_cambio_pick_picker_estractor, h.cambio_caseteros AS cierres_cambio_caseteros, h.otro_1_dispensador AS cierres_otro_1_dispensador, h.nombre_pieza_otro_1_dispensador AS cierres_nombre_pieza_otro_1_dispensador, h.otro_2_dispensador AS cierres_otro_2_dispensador, h.nombre_pieza_otro_2_dispensador AS cierres_nombre_pieza_otro_2_dispensador, h.mant_modulo_aceptador AS cierres_mant_modulo_aceptador, h.cambio_escrow AS cierres_cambio_escrow, h.cambio_validador AS cierres_cambio_validador, h.cambio_shutter_cash_slot AS cierres_cambio_shutter_cash_slot, h.cambio_tarjeta_cotroladora AS cierres_cambio_tarjeta_cotroladora, h.otro_1_aceptador AS cierres_otro_1_aceptador, h.nombre_pieza_otro_1_aceptador AS cierres_nombre_pieza_otro_1_aceptador, h.otro_2_aceptador AS cierres_otro_2_aceptador, h.nombre_pieza_otro_2_aceptador AS cierres_nombre_pieza_otro_2_aceptador, h.mant_modulo_cpu AS cierres_mant_modulo_cpu, h.cambio_dd AS cierres_cambio_dd, h.cambio_cpu AS cierres_cambio_cpu, h.mant_modulo_lectora AS cierres_mant_modulo_lectora, h.cambio_lectora AS cierres_cambio_lectora, h.mant_modulo_impresora AS cierres_mant_modulo_impresora, h.cambio_impresora AS cierres_cambio_impresora, h.fuente_de_poder AS cierres_fuente_de_poder, h.teclado_teclado_lateral_touch_screen AS cierres_teclado_teclado_lateral_touch_screen, h.hooper AS cierres_hooper, h.monitor_pantalla AS cierres_monitor_pantalla, h.fascia AS cierres_fascia, h.se_realizo_planchado_sw AS cierres_se_realizo_planchado_sw, h.version_instalada AS cierres_version_instalada, h.checker_visible AS cierres_checker_visible, h.activacion_checker AS cierres_activacion_checker, h.nombre_activa_checker AS cierres_nombre_activa_checker, h.csds_visible AS cierres_csds_visible, h.cierre_inmuebles AS cierres_cierre_inmuebles, h.revision_correcion_de_voltajes AS cierres_revision_correcion_de_voltajes, h.mejoramiento_de_imagen_limpieza AS cierres_mejoramiento_de_imagen_limpieza, h.revision_correccion_instalacion_de_aa AS cierres_revision_correccion_instalacion_de_aa, h.requiere_ups AS cierres_requiere_ups, h.cierre_cableado AS cierres_cierre_cableado, h.correcciones_red_interna AS cierres_correcciones_red_interna, h.revision_de_cableado_y_status_de_equipos AS cierres_revision_de_cableado_y_status_de_equipos, h.retiro_y_o_limpieza_de_equipos AS cierres_retiro_y_o_limpieza_de_equipos, h.cierre_comunicaciones AS cierres_cierre_comunicaciones, h.revision_enlace_equipos AS cierres_revision_enlace_equipos, h.prueba_de_calidad AS cierres_prueba_de_calidad, h.cierre_interventor AS cierres_cierre_interventor, h.cuenta_con_reporte_fotografico AS cierres_cuenta_con_reporte_fotografico, h.requiere_calcomanias AS cierres_requiere_calcomanias, h.requiere_mejoramiento_de_imagen AS cierres_requiere_mejoramiento_de_imagen, h. STATUS AS cierres_status, h.user_name AS cierres_user_name, h.created_at AS cierres_created_at, h.updated_at AS cierres_updated_at, h.cierre_idc AS cierres_cierre_idc, h.vandalismo AS cierres_vandalismo,
            case 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' then 0.663 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' then 0.663
                when a.que_se_mide ='RECEPTOR'  then 0.754
                when a.que_se_mide ='RECICLADOR'  then 1.5
            end as meta,
            case 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 0.663  then 'META'
              when a.que_se_mide ='DISPENSADOR' and c.d_local='SUC' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 0.663  then 'FUERA META'
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 0.663  then 'META' 
                when a.que_se_mide ='DISPENSADOR' and c.d_local='REM' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 0.663  then 'FUERA META' 
                when a.que_se_mide ='RECEPTOR' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 0.754 then 'META'
                when a.que_se_mide ='RECEPTOR'  AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 0.754 then 'FUERA META'
                when a.que_se_mide ='RECICLADOR' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) < 1.5 then 'META'
                when a.que_se_mide ='RECICLADOR' AND CAST(a.indispo_de_seleccion AS DECIMAL(10,4)) > 1.5 then 'FUERA META'
            end as en_meta
            FROM alta_datos_odt as a 
            LEFT JOIN indisp_al_dia as b ON a.atm=b.atm and a.que_se_mide=b.FUNCIONALIDAD 
            LEFT JOIN siga c on a.atm=c.pk_autoservicios_id
            LEFT JOIN cat_divisiones d on c.d_division=d.nombre
                        LEFT JOIN planeacion e on a.planeacion_id=e.id
                        LEFT JOIN analisis f on a.analisis_id=f.id
                        LEFT JOIN gestion g on a.gestion_id=g.id
                        LEFT JOIN cierres h on a.cierre_id=h.id
            where d.id in ($division)
                        and a.fecha_seleccion > DATE_ADD(NOW(),INTERVAL -70 DAY)
                        and a.status in ( 'Cierres', 'Cierre Pendiente', 'Completado')
            ORDER BY a.created_at DESC");

        return JsonResponse::collectionResponse($data);
    }

    public function getInfoAtmByTablaSiga(Request $request){

        $atm               = array_get($request, 'atm');
        $que_se_mide       = array_get($request, 'que_Se_Mide');
        $indisp_al_dia_id  = array_get($request, 'indisp_al_dia_id');
        $alta_datos_odt_id = array_get($request, 'alta_datos_odt_id');

      if($indisp_al_dia_id == 'NoAplica'){
        $data = DB::table('siga')
                //->join('indisp_al_dia', 'indisp_al_dia.ATM', '=', 'siga.pk_autoservicios_id')
                ->where('siga.pk_autoservicios_id', '=', $atm)
                ->get();

        $planeacion_id = DB::table('alta_datos_odt')
                                    ->where('id', $alta_datos_odt_id)
                                    ->select('planeacion_id', 'analisis_id', 'gestion_id', 'cierre_id')
                                    ->get();

            $data[0]->planeacion = DB::table('planeacion')
                                    ->where('id', $planeacion_id[0]->planeacion_id)
                                    ->get();

            $data[0]->analisis = DB::table('analisis')
                                    ->where('id', $planeacion_id[0]->analisis_id)
                                    ->get();

            $data[0]->gestion = DB::table('gestion')
                                    ->where('id', $planeacion_id[0]->gestion_id)
                                    ->get();

            $data[0]->cierre = DB::table('cierres')
                                    ->where('id', $planeacion_id[0]->cierre_id)
                                    ->get();
      }

        if( $indisp_al_dia_id != 'NoAplica' || $indisp_al_dia_id != 'NoAplica' ){
            $data = DB::table('siga')
                
                ->join('indisp_al_dia', 'indisp_al_dia.ATM', '=', 'siga.pk_autoservicios_id')
                ->where('siga.pk_autoservicios_id', '=', $atm)
                ->where('indisp_al_dia.id', '=', $indisp_al_dia_id)
                ->where('indisp_al_dia.FUNCIONALIDAD', '=', $que_se_mide)
                ->select('siga.*', 'indisp_al_dia.MES_ANTERIOR_6 as mes_anterior_6', 'indisp_al_dia.MES_ANTERIOR_5 as mes_anterior_5', 'indisp_al_dia.MES_ANTERIOR_4 as mes_anterior_4', 'indisp_al_dia.MES_ANTERIOR_3 as mes_anterior_3', 'indisp_al_dia.MES_ANTERIOR_2 as mes_anterior_2', 'indisp_al_dia.MES_ANTERIOR_1 as mes_anterior_1', 'indisp_al_dia.IND_ACUM_ACTUAL as ind_acum_actual', 'indisp_al_dia.id as indisp_al_dia_id')
                ->get();

            $planeacion_id = DB::table('alta_datos_odt')
                                    ->where('id', $alta_datos_odt_id)
                                    ->select('planeacion_id', 'analisis_id', 'gestion_id', 'cierre_id')
                                    ->get();

            $data[0]->planeacion = DB::table('planeacion')
                                    ->where('id', $planeacion_id[0]->planeacion_id)
                                    ->get();

            $data[0]->analisis = DB::table('analisis')
                                    ->where('id', $planeacion_id[0]->analisis_id)
                                    ->get();

            $data[0]->gestion = DB::table('gestion')
                                    ->where('id', $planeacion_id[0]->gestion_id)
                                    ->get();

            $data[0]->cierre = DB::table('cierres')
                                    ->where('id', $planeacion_id[0]->cierre_id)
                                    ->get();

            $data[0]->alta_datos_odt = DB::table('alta_datos_odt')
                                    ->where('id', $alta_datos_odt_id)
                                    ->get();
        }

        return JsonResponse::singleResponse(["message" => "Info insertada" , 
          "Data" => $data, 
        ]);
    }

    public function subir_excel_alta_datos_odt_csv(Request $request)
    {

        SendEmailAltaDatosOdt::dispatch('marcoantonio.negrete.contractor@bbva.com', 'Archivo subido Correctamente');

        $now = Carbon::now('America/Mexico_City');

        $userId = Auth::id();
        $this->internal->create(array(
            'user_id'       => $userId,
            'evento'        => 'Se ha Importado CSV-Alta datos ODT o Excel ',
            'created_at'    => $now,
            'updated_at'    => $now
        ));

    }

    public function downloadReporteDatosOdt($fecha_inicio, $fecha_fin){

        $now = Carbon::now('America/Mexico_City');

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha descargado un excel de la tabla alta_datos_odt',
            'created_at'    => $now,
            'updated_at'    => $now
        ));

      //aqui empieza el ciclo

        $data = DB::table('alta_datos_odt')
                    ->whereBetween('alta_datos_odt.created_at', [$fecha_inicio, $fecha_fin])
                    ->join('indisp_al_dia', 'indisp_al_dia.ATM', '=', 'alta_datos_odt.atm')
                    ->select('alta_datos_odt.*', 'indisp_al_dia.MANTENIMIENTO_TECNICO as mantenimiento_tecnico', 'indisp_al_dia.RECUPERACION_MANUAL as recuperacion_manual')
                    ->get();

        foreach($data as $item){
            $item->suma_mant_tecnico_recup_manual = $item->mantenimiento_tecnico + $item->recuperacion_manual;
        }

        //var_dump($data);
        //return JsonResponse::collectionResponse($data);
        
        $tot_record_found = 0;
        if ($data != null || $data != '') {
            $tot_record_found = 1;

            $CsvData = array('ATM, QUE SE MIDE, FECHA DE SELECCION, INDISPONIBILIDAD DE SELECCION, MANTENIMIENTO TECNICO, RECUPERACION MANUAL, MANT TEC + REC MAN INICIAL, TOTAL TRANSACCIONES (Prom 6 meses), UNIVERSO MES ACTUAL, CASO INICIAL, FECHA ESCALADO DAR, FECHA ESCALADO BANCA, USUARIO, FECHA DE CREACION');
            foreach ($data as $value) {

                $atm                            = str_replace(',', '', $value->atm);
                $que_se_mide                    = str_replace(',', '', $value->que_se_mide);
                $fecha_seleccion                = str_replace(',', '', $value->fecha_seleccion);
                $indispo_de_seleccion           = str_replace(',', '', $value->indispo_de_seleccion);
                $mantenimiento_tecnico          = str_replace(',', '', $value->mantenimiento_tecnico);
                $recuperacion_manual            = str_replace(',', '', $value->recuperacion_manual);
                $suma_mant_tecnico_recup_manual = str_replace(',', '', $value->suma_mant_tecnico_recup_manual);
                $total_transacciones            = str_replace(',', '', $value->total_transacciones);
                $universo_mes_actual            = str_replace(',', '', $value->universo_mes_actual);
                $caso_inicial                   = str_replace(',', '', $value->caso_inicial);
                $fecha_escalado_dar             = str_replace(',', '', $value->fecha_escalado_dar);
                $fecha_escalado_banca           = str_replace(',', '', $value->fecha_escalado_banca);
                $user_name                      = str_replace(',', '', $value->user_name);
                $created_at                     = str_replace(',', '', $value->created_at);

                $CsvData[] = $atm . ',' . $que_se_mide . ',' . $fecha_seleccion . ',' . $indispo_de_seleccion . ',' . $mantenimiento_tecnico . ',' . $recuperacion_manual . ',' . $suma_mant_tecnico_recup_manual . ',' . $total_transacciones . ',' . $universo_mes_actual . ',' . $caso_inicial . ',' . $fecha_escalado_dar . ',' . $fecha_escalado_banca . ',' . $user_name . ',' . $created_at;
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

    public function createNewSeguimientoOdt(Request $request, $atm, $que_se_mide, $alta_datos_odt_id){

        $autoservicio_reincidente = array_get($request, 'autoservicio_reincidente');
        $num_dias_reincidencia    = array_get($request, 'num_dias_reincidencia');
        $motivo_de_reincidencia   = array_get($request, 'motivo_de_reincidencia');
        $falla_fin_de_semana      = array_get($request, 'falla_fin_de_semana');
        $causa_reincidencia       = array_get($request, 'causa_reincidencia');
        $contribuyente            = array_get($request, 'contribuyente');
        $comentarios              = array_get($request, 'comentarios');
        $now                      = Carbon::now('America/Mexico_City');
        $user_name                = Auth::user()->name;
        
        $data = DB::table('seguimiento_datos_odt')->insert(array(
            'alta_datos_odt_id'        => $alta_datos_odt_id,
            'autoservicio_reincidente' => $autoservicio_reincidente,
            'num_dias_reincidencia'    => $num_dias_reincidencia,
            'motivo_de_reincidencia'   => $motivo_de_reincidencia,
            'falla_fin_de_semana'      => $falla_fin_de_semana,
            'causa_reincidencia'       => $causa_reincidencia,
            'contribuyente'            => $contribuyente,
            'comentarios'              => $comentarios,
            'user_name'                => $user_name,
            'created_at'               => $now,
            'updated_at'               => $now,
        ));

        DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
            'gestionado' => 'SI',
            'updated_at' => $now,
        ));

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha creado un registro de la tabla seguimiento_datos_odt',
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info insertada" , 
          "Data" => $data, 
        ]);
    }

    public function updateSeguimientoOdt(Request $request, $seguimiento_id, $alta_datos_odt_id){

        $autoservicio_reincidente = array_get($request, 'autoservicio_reincidente');
        $num_dias_reincidencia    = array_get($request, 'num_dias_reincidencia');
        $motivo_de_reincidencia   = array_get($request, 'motivo_de_reincidencia');
        $falla_fin_de_semana      = array_get($request, 'falla_fin_de_semana');
        $causa_reincidencia       = array_get($request, 'causa_reincidencia');
        $contribuyente            = array_get($request, 'contribuyente');
        $comentarios              = array_get($request, 'comentarios');
        $now                      = Carbon::now('America/Mexico_City');
        
        $data = DB::table('seguimiento_datos_odt')->where('id', $seguimiento_id)->update(array(
            'autoservicio_reincidente' => $autoservicio_reincidente,
            'num_dias_reincidencia'    => $num_dias_reincidencia,
            'motivo_de_reincidencia'   => $motivo_de_reincidencia,
            'falla_fin_de_semana'      => $falla_fin_de_semana,
            'causa_reincidencia'       => $causa_reincidencia,
            'contribuyente'            => $contribuyente,
            'comentarios'              => $comentarios,
            'updated_at'               => $now,
        ));

        DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->update(array(
            'gestionado' => 'SI',
            'updated_at' => $now,
        ));

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha actualizado el id =' . $seguimiento_id . ' de la tabla seguimiento_datos_odt',
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info insertada" , 
          "Data" => $data, 
        ]);
    }

    public function getDataSeguimientoByAltaDatosOdtId(Request $request, $alta_datos_odt_id){

        $data = DB::table('seguimiento_datos_odt')
            ->where('seguimiento_datos_odt.alta_datos_odt_id', '=', $alta_datos_odt_id)
            ->get();

        $data[0]->num_dias_reincidencia = (int)$data[0]->num_dias_reincidencia;
        
        return JsonResponse::singleResponse(["message" => "Info insertada" , 
          "Data" => $data, 
        ]);
    }

    public function getInfoAtm($atm){

        $data = DB::table('siga')
            ->where('pk_autoservicios_id', '=', $atm)
            ->get();

        $existe_data = $data->count();


        $existe = DB::table('alta_datos_odt')->where('atm', $atm)->get();
        $existe = $existe->count();

        if($existe == 0 ){

            if($existe_data == 0){
                return JsonResponse::errorResponse("No existe en la base de datos el ATM " . $atm, 404);
            }

            return JsonResponse::singleResponse(["message" => "Info encontrada" , 
              "Data" => $data, 
            ]);
        }else{
           return JsonResponse::errorResponse("Ya existe un caso abierto para este atm " . $atm, 404); 
        }
        
        
    }
}