<?php

namespace App\Http\Controllers\v1;
use App\helpers\JsonResponse;
use App\Repositories\Eloquent\UserRepository as User;
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

class CasosController extends Controller
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

    public function getCasos(){

        $userId = Auth::id();
        $divisiones = DB::table('users_divisiones')
                  ->join('cat_divisiones', 'cat_divisiones.id', '=', 'users_divisiones.division_id')
                  ->where('user_id', $userId)
                  ->select('cat_divisiones.*')
                  ->get();

        $division=collect($divisiones)->implode('nombre' , "','");

        $division = "'" . $division . "'";

        $roles = DB::table('users_rol')->where('user_id', $userId)->select('users_rol.*')->get();

        $rol=collect($roles)->implode('rol_id',',');

        $user = Auth::user();


        $casos = DB::select("SELECT a.*,b.id as alta_datos_odt_id, b.grupo,b.status,b.prioridad,b.que_se_mide,b.ano_del_caso,b.mes_del_caso,fecha_seleccion,b.indispo_de_seleccion,c.IND_ACUM_ACTUAL,d.d_local,d.d_fecha_1ra_instalacion,d.d_estatus_autoservicio, e.status_finalizacion_del_odt,
concat(truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0),' A. ',truncate((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12,0) ,' M. ',truncate(((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12 - truncate((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12,0))*30,0), ' D.') as fecha_query,case when concat(truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))>= 8 then 'rojo' else 'negro' end as color_fecha,
f.id as rol_id, g.odts_mes,case when i.id is null then 'NO' else 'SI' end as autoservicio_reincidente,case when i.num_dias_reincidencia is not null then num_dias_reincidencia end as num_dias_reincidencia
            FROM casos a
            LEFT JOIN (select a.* from alta_datos_odt a LEFT JOIN (select max(id) as id from alta_datos_odt GROUP BY caso ) b on a.id=b.id where not(b.id is null)) as b ON a.caso = b.caso
            LEFT JOIN indisp_al_dia as c ON a.atm=c.atm and b.que_se_mide=c.FUNCIONALIDAD
            LEFT JOIN siga as d ON a.atm=d.pk_autoservicios_id
            LEFT JOIN (select max(id) as id, atm, status_finalizacion_del_odt from gestion) as e ON a.atm = e.atm
            LEFT JOIN cat_rol_esatus f on b.`status`= f.nombre_estatus
            LEFT JOIN (select caso,COUNT(atm) odts_mes from alta_datos_odt where fecha_seleccion BETWEEN DATE_ADD(now(),INTERVAL -61 day) and now()  GROUP BY caso) g on a.caso=g.caso
            LEFT JOIN (
                        select a.id,a.atm,a.gestion_id,a.planeacion_id, b.hora_termino_de_la_visita,c.fecha_de_cita,d.fallatipo,min(d.fechafallainicio) as fechafallainicio, DATEDIFF(min(d.fechafallainicio),c.fecha_de_cita) as num_dias_reincidencia from  alta_datos_odt a 
                        LEFT JOIN (select id,status_finalizacion_del_odt,hora_termino_de_la_visita from  gestion where status_finalizacion_del_odt in ('SE FINALIZA OK LA INTERVENCION')) b on a.gestion_id=b.id
                        LEFT JOIN planeacion c on a.planeacion_id=c.id
                        LEFT JOIN (select __IDAtm,fallatipo,fechafallainicio from detalle_fallas where FallaImpacto > 15 and FechaFallaInicio BETWEEN DATE_ADD(now(),INTERVAL -61 day) and now() ORDER BY fechafallainicio desc) d on  a.atm=d.__IDAtm and d.fechafallainicio>c.fecha_de_cita
                        where b.id is not null and fecha_de_cita BETWEEN DATE_ADD(now(),INTERVAL -61 day) and now() and d.__IDAtm is not null  GROUP BY atm
                       ) i on i.atm=b.atm 
           WHERE f.id in ($rol) and d.d_division in ($division) and fecha_seleccion BETWEEN DATE_ADD(now(),INTERVAL -70 day) and now()
           GROUP BY a.caso ORDER BY b.id DESC");

        return JsonResponse::collectionResponse($casos);
    }

    public function getCasosPlaneacion(){

        $casos = DB::select("SELECT a.*,b.id as alta_datos_odt_id, b.grupo,b.status,b.prioridad,b.que_se_mide,b.ano_del_caso,b.mes_del_caso,fecha_seleccion,b.indispo_de_seleccion,c.IND_ACUM_ACTUAL,d.d_local,d.d_fecha_1ra_instalacion,d.d_estatus_autoservicio, e.status_finalizacion_del_odt,
concat(truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0),' A. ',truncate((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12,0) ,' M. ',truncate(((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12 - truncate((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12,0))*30,0), ' D.') as fecha_query,case when concat(truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))>= 8 then 'rojo' else 'negro' end as color_fecha
            FROM casos a
            LEFT JOIN (select a.* from alta_datos_odt a LEFT JOIN (select max(id) as id from alta_datos_odt GROUP BY caso ) b on a.id=b.id where not(b.id is null)) as b ON a.atm = b.atm 
            LEFT JOIN indisp_al_dia as c ON a.atm=c.atm and b.que_se_mide=c.FUNCIONALIDAD
            LEFT JOIN siga as d ON a.atm=d.pk_autoservicios_id
             LEFT JOIN (select max(id) as id, atm, status_finalizacion_del_odt from gestion) as e ON a.atm = e.atm
           WHERE b.`status` in ( 'Planeacion', 'Planeacion Pendiente', 'Planeacion Pendiente Fin', 'Completado')
            ORDER BY b.id DESC");

        return JsonResponse::collectionResponse($casos);
    }

    public function getCasosAnalisis(){

        $casos = DB::select("SELECT a.*,b.id as alta_datos_odt_id, b.grupo,b.status,b.prioridad,b.que_se_mide,b.ano_del_caso,b.mes_del_caso,fecha_seleccion,b.indispo_de_seleccion,c.IND_ACUM_ACTUAL,d.d_local,d.d_fecha_1ra_instalacion,d.d_estatus_autoservicio, e.status_finalizacion_del_odt,
concat(truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0),' A. ',truncate((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12,0) ,' M. ',truncate(((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12 - truncate((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12,0))*30,0), ' D.') as fecha_query,case when concat(truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))>= 8 then 'rojo' else 'negro' end as color_fecha
            FROM casos a
            LEFT JOIN (select a.* from alta_datos_odt a LEFT JOIN (select max(id) as id from alta_datos_odt GROUP BY caso ) b on a.id=b.id where not(b.id is null)) as b ON a.atm = b.atm 
            LEFT JOIN indisp_al_dia as c ON a.atm=c.atm and b.que_se_mide=c.FUNCIONALIDAD
            LEFT JOIN siga as d ON a.atm=d.pk_autoservicios_id
             LEFT JOIN (select max(id) as id, atm, status_finalizacion_del_odt from gestion) as e ON a.atm = e.atm
           WHERE b.`status` in ( 'Analisis', 'Analisis Pendiente', 'Completado')
            ORDER BY b.id DESC");

        return JsonResponse::collectionResponse($casos);
    }

    public function getCasosGestion(){

        $casos = DB::select("SELECT a.*,b.id as alta_datos_odt_id, b.grupo,b.status,b.prioridad,b.que_se_mide,b.ano_del_caso,b.mes_del_caso,fecha_seleccion,b.indispo_de_seleccion,c.IND_ACUM_ACTUAL,d.d_local,d.d_fecha_1ra_instalacion,d.d_estatus_autoservicio, e.status_finalizacion_del_odt,
concat(truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0),' A. ',truncate((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12,0) ,' M. ',truncate(((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12 - truncate((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12,0))*30,0), ' D.') as fecha_query,case when concat(truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))>= 8 then 'rojo' else 'negro' end as color_fecha
            FROM casos a
            LEFT JOIN (select a.* from alta_datos_odt a LEFT JOIN (select max(id) as id from alta_datos_odt GROUP BY caso ) b on a.id=b.id where not(b.id is null)) as b ON a.atm = b.atm 
            LEFT JOIN indisp_al_dia as c ON a.atm=c.atm and b.que_se_mide=c.FUNCIONALIDAD
            LEFT JOIN siga as d ON a.atm=d.pk_autoservicios_id
             LEFT JOIN (select max(id) as id, atm, status_finalizacion_del_odt from gestion) as e ON a.atm = e.atm
           WHERE b.`status` in ( 'Gestion', 'Gestion Pendiente', 'Completado')
            ORDER BY b.id DESC");

        return JsonResponse::collectionResponse($casos);
    }

    public function getCasosCierres(){

        $casos = DB::select("SELECT a.*,b.id as alta_datos_odt_id, b.grupo,b.status,b.prioridad,b.que_se_mide,b.ano_del_caso,b.mes_del_caso,fecha_seleccion,b.indispo_de_seleccion,c.IND_ACUM_ACTUAL,d.d_local,d.d_fecha_1ra_instalacion,d.d_estatus_autoservicio, e.status_finalizacion_del_odt,
concat(truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0),' A. ',truncate((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12,0) ,' M. ',truncate(((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12 - truncate((DATEDIFF(now(), d_fecha_1ra_instalacion)/365-truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))*12,0))*30,0), ' D.') as fecha_query,case when concat(truncate(DATEDIFF(now(), d_fecha_1ra_instalacion)/365,0))>= 8 then 'rojo' else 'negro' end as color_fecha
            FROM casos a
            LEFT JOIN (select a.* from alta_datos_odt a LEFT JOIN (select max(id) as id from alta_datos_odt GROUP BY caso ) b on a.id=b.id where not(b.id is null)) as b ON a.atm = b.atm 
            LEFT JOIN indisp_al_dia as c ON a.atm=c.atm and b.que_se_mide=c.FUNCIONALIDAD
            LEFT JOIN siga as d ON a.atm=d.pk_autoservicios_id
             LEFT JOIN (select max(id) as id, atm, status_finalizacion_del_odt from gestion) as e ON a.atm = e.atm
           WHERE b.`status` in ( 'Cierres', 'Cierre Pendiente', 'Completado')
            ORDER BY b.id DESC");

        return JsonResponse::collectionResponse($casos);
    }


    public function getInfoAtm($atm){

        $data = DB::table('siga')->where('pk_autoservicios_id', $atm)->get();
        $now  = Carbon::now('America/Mexico_City');

        $data[0]->fecha_prueba = Carbon::parse($data[0]->d_fecha_1ra_instalacion);
        $data[0]->fecha_query = date_diff($now, $data[0]->fecha_prueba)->format('%y A. %m M. %d D.');
        $data[0]->fecha_meses = $now->diff($data[0]->fecha_prueba);
        $data[0]->intervalo_meses = $data[0]->fecha_meses->format('%m')*1;

        $data[0]->intervalo_anios = ($data[0]->fecha_meses->format('%y'))*12;

        $data[0]->antiguedad_meses = $data[0]->intervalo_meses + $data[0]->intervalo_anios;

        return JsonResponse::singleResponse(["message" => "Info encontrada" , 
          "Data" => $data,
        ]);  
    }

    public function getInfoCasoByOdt($caso){

        $data = DB::table('alta_datos_odt')->where('caso', $caso)->get();

        $data2 = DB::table('siga')
            ->where('pk_autoservicios_id', '=', $data[0]->atm)
            ->get();

        return JsonResponse::singleResponse(["message" => "Info encontrada" , 
          "Data" => $data,
          "DataAtm" => $data2, 
        ]);  
    }

    public function uploadLastOdt($alta_datos_odt_id){

        $data                = DB::table('alta_datos_odt')->where('id', $alta_datos_odt_id)->get();
        $data_planeacion_ant = DB::table('planeacion')->where('id', $data[0]->planeacion_id)->get();
        $data_analisis_ant   = DB::table('analisis')->where('id', $data[0]->analisis_id)->get();
        $now                 = Carbon::now('America/Mexico_City');

        $planeacion_id_new =  DB::table('planeacion')->insertGetId(array(
            'responsable_planeacion'                => $data_planeacion_ant[0]->responsable_planeacion,
            'fecha_de_planeacion'                   => $data_planeacion_ant[0]->fecha_de_planeacion,
            'idc_fecha_solicitud_cita'              => $data_planeacion_ant[0]->idc_fecha_solicitud_cita,
            'idc_fecha_confirmacion_cita'           => $data_planeacion_ant[0]->idc_fecha_confirmacion_cita,
            'tipo_de_ingenieria'                    => $data_planeacion_ant[0]->tipo_de_ingenieria,
            'nombres_del_personal_a_asistir_de_idc' => $data_planeacion_ant[0]->nombres_del_personal_a_asistir_de_idc,
            'etv_fecha_solicitud_cita'              => $data_planeacion_ant[0]->etv_fecha_solicitud_cita,
            'etv_fecha_confirmacion_cita'           => $data_planeacion_ant[0]->etv_fecha_confirmacion_cita,
            'empresa_de_inmuebles'                  => $data_planeacion_ant[0]->empresa_de_inmuebles,
            'personal_de_inmuebles'                 => $data_planeacion_ant[0]->personal_de_inmuebles,
            'empresa_de_cableado'                   => $data_planeacion_ant[0]->empresa_de_cableado,
            'personal_de_cableado'                  => $data_planeacion_ant[0]->personal_de_cableado,
            'empresa_de_comunicaciones'             => $data_planeacion_ant[0]->empresa_de_comunicaciones,
            'personal_de_comunicaciones'            => $data_planeacion_ant[0]->personal_de_comunicaciones,
            'nombre_interventor'                    => $data_planeacion_ant[0]->nombre_interventor,
            'fecha_de_cita'                         => $data_planeacion_ant[0]->fecha_de_cita,
            'ticket_ob'                             => $data_planeacion_ant[0]->ticket_ob,
            'ticket_remedy'                         => $data_planeacion_ant[0]->ticket_remedy,
            'tas_idc'                               => $data_planeacion_ant[0]->tas_idc,
            'tas_etv'                               => $data_planeacion_ant[0]->tas_etv,
            'tas_seguimiento'                       => $data_planeacion_ant[0]->tas_seguimiento,
            'folio_inmuebles'                       => $data_planeacion_ant[0]->folio_inmuebles,
            'status'                                => $data_planeacion_ant[0]->status,
            'atm'                                   => $data_planeacion_ant[0]->atm,
            'no_odt'                                => $data_planeacion_ant[0]->no_odt,
            'user_name'                             => Auth::user()->name,
            'created_at'                            => $now,
            'updated_at'                            => $now
        ));

        $analisis_id_new =  DB::table('analisis')->insertGetId(array(
            'nombre_responsable'                       => $data_analisis_ant[0]->nombre_responsable,
            'nombre_analista'                          => $data_analisis_ant[0]->nombre_analista,
            'fecha_de_analisis'                        => $data_analisis_ant[0]->fecha_de_analisis,
            'aplican_acciones_de_idc'                  => $data_analisis_ant[0]->aplican_acciones_de_idc,
            'acciones_a_realizar_idc'                  => $data_analisis_ant[0]->acciones_a_realizar_idc,
            'mant_modulo_dispensador'                  => $data_analisis_ant[0]->mant_modulo_dispensador,
            'cambio_presentador_stacker'               => $data_analisis_ant[0]->cambio_presentador_stacker,
            'cambio_cosumibles'                        => $data_analisis_ant[0]->cambio_cosumibles,
            'cambio_pick_picker_estractor'             => $data_analisis_ant[0]->cambio_pick_picker_estractor,
            'cambio_caseteros'                         => $data_analisis_ant[0]->cambio_caseteros,
            'otro_1_dispensador'                       => $data_analisis_ant[0]->otro_1_dispensador,
            'nombre_pieza_otro_1_dispensador'          => $data_analisis_ant[0]->nombre_pieza_otro_1_dispensador,
            'otro_2_dispensador'                       => $data_analisis_ant[0]->otro_2_dispensador,
            'nombre_pieza_otro_2_dispensador'          => $data_analisis_ant[0]->nombre_pieza_otro_2_dispensador,
            'mant_modulo_aceptador'                    => $data_analisis_ant[0]->mant_modulo_aceptador,
            'cambio_escrow'                            => $data_analisis_ant[0]->cambio_escrow,
            'cambio_validador'                         => $data_analisis_ant[0]->cambio_validador,
            'cambio_shutter_cash_slot'                 => $data_analisis_ant[0]->cambio_shutter_cash_slot,
            'cambio_tarjeta_controladora'              => $data_analisis_ant[0]->cambio_tarjeta_controladora,
            'otro_1_aceptador'                         => $data_analisis_ant[0]->otro_1_aceptador,
            'nombre_pieza_otro_1_aceptador'            => $data_analisis_ant[0]->nombre_pieza_otro_1_aceptador,
            'otro_2_aceptador'                         => $data_analisis_ant[0]->otro_2_aceptador,
            'nombre_pieza_otro_2_aceptador'            => $data_analisis_ant[0]->nombre_pieza_otro_2_aceptador,
            'mant_modulo_idc_cpu'                      => $data_analisis_ant[0]->mant_modulo_idc_cpu,
            'cambio_dd'                                => $data_analisis_ant[0]->cambio_dd,
            'cambio_cpu'                               => $data_analisis_ant[0]->cambio_cpu,
            'mant_modulo_lectora'                      => $data_analisis_ant[0]->mant_modulo_lectora,
            'cambio_lectora'                           => $data_analisis_ant[0]->cambio_lectora,
            'mant_modulo_impresora'                    => $data_analisis_ant[0]->mant_modulo_impresora,
            'cambio_impresora'                         => $data_analisis_ant[0]->cambio_impresora,
            'fuente_de_poder'                          => $data_analisis_ant[0]->fuente_de_poder,
            'teclado_teclado_lateral_touch_screen'     => $data_analisis_ant[0]->teclado_teclado_lateral_touch_screen,
            'hopper'                                   => $data_analisis_ant[0]->hopper,
            'monitor_pantalla'                         => $data_analisis_ant[0]->monitor_pantalla,
            'fascia'                                   => $data_analisis_ant[0]->fascia,
            'se_requiere_planchado_de_sw'              => $data_analisis_ant[0]->se_requiere_planchado_de_sw,
            'ver_requerida'                            => $data_analisis_ant[0]->ver_requerida,
            'aplican_acciones_de_inmuebles'            => $data_analisis_ant[0]->aplican_acciones_de_inmuebles,
            'acciones_a_realizar_inmuebles'            => $data_analisis_ant[0]->acciones_a_realizar_inmuebles,
            'revision_correcion_de_voltajes'           => $data_analisis_ant[0]->revision_correcion_de_voltajes,
            'mejoramiento_de_imagen_limpieza'          => $data_analisis_ant[0]->mejoramiento_de_imagen_limpieza,
            'revision_correccion_instalacion_de_aa'    => $data_analisis_ant[0]->revision_correccion_instalacion_de_aa,
            'requiere_ups'                             => $data_analisis_ant[0]->requiere_ups,
            'aplican_acciones_de_cableado'             => $data_analisis_ant[0]->aplican_acciones_de_cableado,
            'acciones_a_realizar_cableado'             => $data_analisis_ant[0]->acciones_a_realizar_cableado,
            'correcciones_red_interna'                 => $data_analisis_ant[0]->correcciones_red_interna,
            'revision_de_cableado_y_status_de_equipos' => $data_analisis_ant[0]->revision_de_cableado_y_status_de_equipos,
            'retiro_y_o_limpieza_de_equipos'           => $data_analisis_ant[0]->retiro_y_o_limpieza_de_equipos,
            'aplican_acciones_de_comunicaciones'       => $data_analisis_ant[0]->aplican_acciones_de_comunicaciones,
            'acciones_a_realizar_comunicaciones'       => $data_analisis_ant[0]->acciones_a_realizar_comunicaciones,
            'revision_enlace_equipo'                   => $data_analisis_ant[0]->revision_enlace_equipo,
            'prueba_de_calidad'                        => $data_analisis_ant[0]->prueba_de_calidad,
            'aplica_la_asistencia_de_un_interventor'   => $data_analisis_ant[0]->aplica_la_asistencia_de_un_interventor,
            'tipo_de_visita'                           => $data_analisis_ant[0]->tipo_de_visita,
            'status'                                   => $data_analisis_ant[0]->status,
            'atm'                                      => $data_analisis_ant[0]->atm,
            'no_odt'                                   => $data_analisis_ant[0]->no_odt,
            'user_name'                                => Auth::user()->name,
            'created_at'                               => $now,
            'updated_at'                               => $now
        ));

        $alta_datos_odt_id_new =  DB::table('alta_datos_odt')->insertGetId(array(
            'prioridad'            => $data[0]->prioridad,
            'ano_del_caso'         => $data[0]->ano_del_caso,
            'mes_del_caso'         => $data[0]->mes_del_caso,
            'grupo'                => $data[0]->grupo,
            'caso'                 => $data[0]->caso,
            'atm'                  => $data[0]->atm,
            'que_se_mide'          => $data[0]->que_se_mide,
            'fecha_seleccion'      => $data[0]->fecha_seleccion,
            'indispo_de_seleccion' => $data[0]->indispo_de_seleccion,
            'universo_mes_actual'  => $data[0]->universo_mes_actual,
            'caso_inicial'         => $data[0]->caso_inicial,
            'fecha_escalado_dar'   => $data[0]->fecha_escalado_dar,
            'fecha_escalado_banca' => $data[0]->fecha_escalado_banca,
            'user_name'            => Auth::user()->name,
            'total_transacciones'  => $data[0]->total_transacciones,
            'gestionado'           => $data[0]->gestionado,
            'no_odt'               => $data[0]->no_odt,
            'planeacion_id'        => $planeacion_id_new,
            'analisis_id'          => $analisis_id_new,
            'status'               => 'Planeacion Pendiente Fin',
            'created_at'           => $now,
            'updated_at'           => $now
        ));

        return JsonResponse::singleResponse(["message" => "Info encontrada" , 
          "Data" => $alta_datos_odt_id_new,
        ]);  
    }

    public function createCaso(Request $request){

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
        $no_odt = '';

        $existe = DB::select("SELECT count(atm) as total from alta_datos_odt where atm='$atm' and status  not in ('Completado') and fecha_seleccion BETWEEN DATE_ADD(now(),INTERVAL -70 DAY) and   NOW()");

        if($existe[0]->total == 0 ){
            DB::select("CALL insertaCaso(         
                '" . $prioridad . "' ,
                '" . $ano_del_caso . "' ,
                '" . $mes_del_caso . "' ,
                '" . $grupo . "' ,
                '" . $atm . "' , 
                '" . $que_se_mide . "' ,
                '" . $fecha_seleccion . "' ,
                '" . $indispo_de_seleccion . "',
                '" . $universo_mes_actual . "' ,
                '" . $caso_inicial . "' ,
                '" . $fecha_escalado_dar . "' ,
                '" . $fecha_escalado_banca . "' ,
                '" . Auth::user()->name . "' ,
                '" . $total_transacciones . "'  ,
                '" . $no_odt . "'                                
            )");

            $this->internal->create(array(
                'user_id'       => Auth::user()->name,
                'evento'        => 'Se ha creado un nuevo item en la tabla alta_datos_odt y tabla casos',
                'created_at'    => $now,
                'updated_at'    => $now
            ));

            return JsonResponse::singleResponse(["message" => "Info insertada" , 
              //"Data" => $data, 
            ]);
        }else{
            return JsonResponse::errorResponse("No es posible crear un caso para este ATM, ya existe un caso abierto", 404);
        }

          
    }

    public function updateCaso(Request $request, $caso){
        $atm                  = array_get($request, 'atm');
        $caso                 = array_get($request, 'caso');
        $que_se_mide          = array_get($request, 'que_se_mide');
        $fecha_seleccion      = array_get($request, 'fecha_seleccion');
        $indispo_de_seleccion = array_get($request, 'indispo_de_seleccion');
        $total_transacciones  = array_get($request, 'total_transacciones');

        $grupo        = array_get($request, 'grupo');
        $mes_del_caso = array_get($request, 'mes_del_caso');
        $ano_del_caso = array_get($request, 'ano_del_caso');
        $prioridad    = array_get($request, 'prioridad');

        $universo_mes_actual  = array_get($request, 'universo_mes_actual');
        $caso_inicial         = array_get($request, 'caso_inicial');
        $fecha_escalado_dar   = array_get($request, 'fecha_escalado_dar');
        $fecha_escalado_banca = array_get($request, 'fecha_escalado_banca');
        $now                  = Carbon::now('America/Mexico_City');
        $user_name            = Auth::user()->name;
        
        $data = DB::table('alta_datos_odt')->where('caso', $caso)->update(array(
            'que_se_mide'          => $que_se_mide,
            'fecha_seleccion'      => $fecha_seleccion,
            'indispo_de_seleccion' => $indispo_de_seleccion,
            'total_transacciones'  => $total_transacciones,
            'grupo'                => $grupo,
            'mes_del_caso'         => $mes_del_caso,
            'ano_del_caso'         => $ano_del_caso,
            'prioridad'            => $prioridad,
            'universo_mes_actual'  => $universo_mes_actual,
            'caso_inicial'         => $caso_inicial,
            'fecha_escalado_dar'   => $fecha_escalado_dar,
            'fecha_escalado_banca' => $fecha_escalado_banca,
            'user_name'            => $user_name,
            'updated_at'           => $now,
        ));

        $this->internal->create(array(
            'user_id'       => Auth::user()->name,
            'evento'        => 'Se ha actualizado el caso =' . $caso . ' de la tabla alta_datos_odt',
            'created_at'    => $now,
            'updated_at'    => $now
        ));
        
        return JsonResponse::singleResponse(["message" => "Info actualizada" , 
        ]);
    }

    public function destroyCaso($caso)
    {
        try {

            $now = Carbon::now('America/Mexico_City');
            DB::table('casos')->where('caso', $caso)->delete();
            DB::table('alta_datos_odt')->where('caso', $caso)->delete();

            $this->internal->create(array(
                'user_id'       => Auth::user()->name,
                'evento'        => 'Se ha eliminado el caso =' . $caso . ' de la tabla alta_datos_odt y tabla casos',
                'created_at'    => $now,
                'updated_at'    => $now
            ));
 

            return JsonResponse::singleResponse([ "message" => "El caso ha sido eliminado." ]);
        } catch (ModelNotFoundException $exception) {
            \Log::error("Eliminando usuario...", [
                "model"   => $exception->getModel(),
                "message" => $exception->getMessage(),
                "code"    => $exception->getCode()
            ]);

            return JsonResponse::errorResponse("No es posible eliminar el usuario, informacion no encontrado.", 404);
        }

    }

    public function getOdts($atm, $caso){

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
            a.indispo_de_seleccion, 
            b.MANTENIMIENTO_TECNICO as mantenimiento_tecnico, 
            b.RECUPERACION_MANUAL as recuperacion_manual, 
            a.total_transacciones, 
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
            d.id as division_id
            FROM alta_datos_odt as a 

             LEFT JOIN (
                            select a.* from indisp_al_dia a
                            LEFT JOIN
                                (select  max(id) as maximo,atm from indisp_al_dia GROUP BY atm) b
                            on a.atm=b.atm and a.id=b.maximo
                            where not(b.atm is null)) as b ON a.atm=b.atm and a.que_se_mide=b.FUNCIONALIDAD 
            LEFT JOIN siga c on a.atm=c.pk_autoservicios_id
            LEFT JOIN cat_divisiones d on c.d_division=d.nombre
            where a.atm = '$atm' and a.caso='$caso' 
            and d.id in ($division)
            ORDER BY a.created_at DESC");

        foreach($data as $item){
            $item->indispo_de_seleccion = (float)($item->indispo_de_seleccion);
            $item->mantenimiento_tecnico = (float)($item->mantenimiento_tecnico);
            $item->recuperacion_manual = (float)($item->recuperacion_manual);
            $item->total_transacciones = (int)($item->total_transacciones);
            $item->suma_mant_tecnico_recup_manual = $item->mantenimiento_tecnico + $item->recuperacion_manual;
            $item->planeacion = DB::table('planeacion')->where('id', $item->planeacion_id)->get();
            $item->analisis = DB::table('analisis')->where('id', $item->analisis_id)->get();
            $item->gestion = DB::table('gestion')->where('id', $item->gestion_id)->get();
            $item->cierre = DB::table('cierres')->where('id', $item->cierre_id)->get();
        }

        return JsonResponse::collectionResponse($data);
    }

}
