<?php

namespace App\Http\Controllers\v1;

use App\Repositories\Eloquent\UserRepository as User;
use App\Http\Controllers\Controller;
use App\helpers\JsonResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Traits\JWTTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Eloquent\InternalEventRepository as Internal;
use Carbon\Carbon;

class AtmsController extends Controller
{
    use JWTTrait;

    protected $user;
    protected $hidden = ['password', 'remember_token'];
    protected $internal;

    public function __construct(User $user, Internal $internal)
    {
        $this->user = $user;
        $this->internal = $internal;
    }

    public function getAtms()
    {

        $data = DB::table('alta_datos_odt')
                ->join('siga', 'siga.pk_autoservicios_id', '=', 'alta_datos_odt.atm')
                ->join('indisp_al_dia', 'indisp_al_dia.ATM', '=', 'alta_datos_odt.atm')
                ->select('alta_datos_odt.*', 'siga.*', 'indisp_al_dia.*')
                ->get();

        $now = Carbon::now('America/Mexico_City');

        foreach($data as $item){

            $item->fecha_prueba = Carbon::parse($item->d_fecha_1ra_instalacion);
            $item->fecha_query = date_diff($now, $item->fecha_prueba)->format('%y A. %m M. %d D.' );
            if((date_diff($now, $item->fecha_prueba)->format('%y'))*1 >= 8){
                $item->color_fecha = 'rojo';
            }else{
                $item->color_fecha = 'negro';
            }

        }
        
        return JsonResponse::collectionResponse($data);
    }

    public function getInfoAtmByIndispoAlDia($atm)
    {
        $now = Carbon::now('America/Mexico_City');
        $data = DB::table('indisp_al_dia')
                ->where('ATM', $atm)
                ->join('siga', 'siga.pk_autoservicios_id', '=', 'indisp_al_dia.ATM')
                ->get();

        $data[0]->fecha_prueba = Carbon::parse($data[0]->d_fecha_1ra_instalacion);
        $data[0]->fecha_query = date_diff($now, $data[0]->fecha_prueba)->format('%y A. %m M. %d D.' );
        if((date_diff($now, $data[0]->fecha_prueba)->format('%y'))*1 >= 8){
            $data[0]->color_fecha = 'rojo';
        }else{
            $data[0]->color_fecha = 'negro';
        }

        $data[0]->detalle_fallas = DB::table('detalle_fallas')->where('__IDAtm', $atm)->get();

        
        return JsonResponse::collectionResponse($data);
    }

    public function getAltaDatosOdtByAtm(Request $request, $atm){

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
            h.id AS cierres_id_cierres, h.atm AS cierres_atm, h.que_se_mide AS cierres_que_se_mide, h.modulo_vandalizado AS cierres_modulo_vandalizado, h.otro_especificar AS cierres_otro_especificar, h.fecha_escalamiento_dar AS cierres_fecha_escalamiento_dar, h.mant_modulo_dispensador AS cierres_mant_modulo_dispensador, h.cambio_presentador_stacker AS cierres_cambio_presentador_stacker, h.cambio_consumibles AS cierres_cambio_consumibles, h.cambio_pick_picker_estractor AS cierres_cambio_pick_picker_estractor, h.cambio_caseteros AS cierres_cambio_caseteros, h.otro_1_dispensador AS cierres_otro_1_dispensador, h.nombre_pieza_otro_1_dispensador AS cierres_nombre_pieza_otro_1_dispensador, h.otro_2_dispensador AS cierres_otro_2_dispensador, h.nombre_pieza_otro_2_dispensador AS cierres_nombre_pieza_otro_2_dispensador, h.mant_modulo_aceptador AS cierres_mant_modulo_aceptador, h.cambio_escrow AS cierres_cambio_escrow, h.cambio_validador AS cierres_cambio_validador, h.cambio_shutter_cash_slot AS cierres_cambio_shutter_cash_slot, h.cambio_tarjeta_cotroladora AS cierres_cambio_tarjeta_cotroladora, h.otro_1_aceptador AS cierres_otro_1_aceptador, h.nombre_pieza_otro_1_aceptador AS cierres_nombre_pieza_otro_1_aceptador, h.otro_2_aceptador AS cierres_otro_2_aceptador, h.nombre_pieza_otro_2_aceptador AS cierres_nombre_pieza_otro_2_aceptador, h.mant_modulo_cpu AS cierres_mant_modulo_cpu, h.cambio_dd AS cierres_cambio_dd, h.cambio_cpu AS cierres_cambio_cpu, h.mant_modulo_lectora AS cierres_mant_modulo_lectora, h.cambio_lectora AS cierres_cambio_lectora, h.mant_modulo_impresora AS cierres_mant_modulo_impresora, h.cambio_impresora AS cierres_cambio_impresora, h.fuente_de_poder AS cierres_fuente_de_poder, h.teclado_teclado_lateral_touch_screen AS cierres_teclado_teclado_lateral_touch_screen, h.hooper AS cierres_hooper, h.monitor_pantalla AS cierres_monitor_pantalla, h.fascia AS cierres_fascia, h.se_realizo_planchado_sw AS cierres_se_realizo_planchado_sw, h.version_instalada AS cierres_version_instalada, h.checker_visible AS cierres_checker_visible, h.activacion_checker AS cierres_activacion_checker, h.nombre_activa_checker AS cierres_nombre_activa_checker, h.csds_visible AS cierres_csds_visible, h.cierre_inmuebles AS cierres_cierre_inmuebles, h.revision_correcion_de_voltajes AS cierres_revision_correcion_de_voltajes, h.mejoramiento_de_imagen_limpieza AS cierres_mejoramiento_de_imagen_limpieza, h.revision_correccion_instalacion_de_aa AS cierres_revision_correccion_instalacion_de_aa, h.requiere_ups AS cierres_requiere_ups, h.cierre_cableado AS cierres_cierre_cableado, h.correcciones_red_interna AS cierres_correcciones_red_interna, h.revision_de_cableado_y_status_de_equipos AS cierres_revision_de_cableado_y_status_de_equipos, h.retiro_y_o_limpieza_de_equipos AS cierres_retiro_y_o_limpieza_de_equipos, h.cierre_comunicaciones AS cierres_cierre_comunicaciones, h.revision_enlace_equipos AS cierres_revision_enlace_equipos, h.prueba_de_calidad AS cierres_prueba_de_calidad, h.cierre_interventor AS cierres_cierre_interventor, h.cuenta_con_reporte_fotografico AS cierres_cuenta_con_reporte_fotografico, h.requiere_calcomanias AS cierres_requiere_calcomanias, h.requiere_mejoramiento_de_imagen AS cierres_requiere_mejoramiento_de_imagen, h. STATUS AS cierres_status, h.user_name AS cierres_user_name, h.created_at AS cierres_created_at, h.updated_at AS cierres_updated_at, h.cierre_idc AS cierres_cierre_idc, h.vandalismo AS cierres_vandalismo
            FROM alta_datos_odt as a 
            LEFT JOIN indisp_al_dia as b ON a.atm=b.atm and a.que_se_mide=b.FUNCIONALIDAD 
            LEFT JOIN siga c on a.atm=c.pk_autoservicios_id
            LEFT JOIN cat_divisiones d on c.d_division=d.nombre
                        LEFT JOIN planeacion e on a.planeacion_id=e.id
                        LEFT JOIN analisis f on a.analisis_id=f.id
                        LEFT JOIN gestion g on a.gestion_id=g.id
                        LEFT JOIN cierres h on a.cierre_id=h.id
            where d.id in ($division)
                        and a.fecha_seleccion > DATE_ADD(NOW(),INTERVAL -190 DAY)
                        and a.atm = '$atm'
            ORDER BY a.created_at DESC");

        return JsonResponse::collectionResponse($data);
    }
}