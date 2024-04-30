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
use App\Jobs\SendEmailCargaSiga;
use App\Jobs\SendEmailCargaDetalleFallas;
use App\Jobs\SendEmailIndispoAlDia;
use App\Jobs\SendEmailFallasMtPorDia;
use App\Jobs\SendEmailIndispoDispensadorReceptor;
use App\Jobs\SendEmailIndispoSucursalCr;
use App\Jobs\SendEmailIndispoInicial;
use PDO;

class InsumosController extends Controller
{
    use JWTTrait;

    protected $internal;

    public function __construct(Internal $internal)
    {
        $this->internal = $internal;
    }

    public function getInfoInsumos(){

        $siga                             = DB::table('siga')->orderby('updated_at','DESC')->take(1)->select('updated_at')->get();
        $indispo_al_dia                   = DB::table('indisp_al_dia')->orderby('updated_at','DESC')->take(1)->select('updated_at')->get();
        $fallas_mt_por_dia                = DB::table('fallas_mt_por_dia')->orderby('updated_at','DESC')->take(1)->select('updated_at')->get();
        $indispo_dispensador_mas_receptor = DB::table('indispo_dispensador_mas_receptor')->orderby('updated_at','DESC')->take(1)->select('updated_at')->get();
        $indispo_sucursal_cr              = DB::table('indispo_sucursal_cr')->orderby('updated_at','DESC')->take(1)->select('updated_at')->get();
        $detalle_fallas              = DB::table('detalle_fallas')->orderby('updated_at','DESC')->take(1)->select('updated_at')->get();
        $indispo_inicial              = DB::table('indispo_inicial')->orderby('updated_at','DESC')->take(1)->select('updated_at')->get();

        return JsonResponse::singleResponse(["message" => "Info" , 
          "DataSiga"                  => $siga,
          "DataIndispoAlDia"          => $indispo_al_dia,
          "DataFallasMtPorDia"        => $fallas_mt_por_dia,
          "DataIndispoDisMasReceptor" => $indispo_dispensador_mas_receptor,
          "DataIndispoSucursalCr"     => $indispo_sucursal_cr, 
          "DataDetalleFallas"         => $detalle_fallas, 
          "DataIndispoInicial"        => $indispo_inicial, 
        ]);
    }

    public function subir_excel_detalle_fallas_csv(Request $request)
    {
        //DB::table('detalle_fallas')->delete();

        SendEmailCargaDetalleFallas::dispatch('marcoantonio.negrete.contractor@bbva.com', 'Archivo subido Correctamente');

        $now = Carbon::now('America/Mexico_City');

        $userId = Auth::id();
            $this->internal->create(array(
                'user_id'       => $userId,
                'evento'        => 'Se ha Importado CSV-Detalle Fallas o Excel ',
                'created_at'    => $now,
                'updated_at'    => $now
            ));
    }

    public function subir_excel_siga_csv(Request $request)
    {
        $userId = Auth::id();

        //DB::table('siga')->delete();

        //SendEmailCargaSiga::dispatch('marcoantonio.negrete.contractor@bbva.com', 'Archivo subido Correctamente');

        DB::select("DELETE from siga_test");

        //DB::connection()->getPdo()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);


        DB::unprepared("LOAD DATA INFILE 'F:/xampp/htdocs/estructurales_back/storage/app/SIGA/siga.csv' 
                    INTO TABLE siga_test FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES");


        $now = Carbon::now('America/Mexico_City');

        DB::select("DELETE from siga");

        DB::select("INSERT into siga
 select pk_autoservicios_id,d_sitio,d_cr,d_division,d_marca,d_modelo,IdentificacionNumeroSerieId,IdentificacionSiaff,d_tipo,d_carga,d_interfase,STR_TO_DATE(InstalacionFecha,'%d/%m/%Y'),Institucion,InstitucionGiro,d_grupo,d_banca,d_estatus_autoservicio,d_atm_en_sitio,IdentificacionProyecto,AdquisicionGarantia,d_calle,d_num_exterior,d_estado,d_ciudad,d_cp,d_delegacion_o_municipio,d_colonia,d_latitud,d_longitud,UbicacionTipoSitio,d_local,UbicacionCelula,UbicacionClaveSitio,UbicacionModeloInstalacion,d_idc,d_dias_idc,d_horario_idc,IdcDiasFinSemana,IdcHorarioFinSemana,IdcNivelesServicio,d_etv,d_dias_etv,d_hrs_etv,EtvDiasFinSemana,EtvHorarioFinSemana,EtvNivelServicio,d_cond_esp_acceso_idc,d_cond_esp_acceso_etv,EtvPlazaOrigen,Contacto1Nombre,Contacto1Telefono,Contacto1Celular,Contacto1Mail,Contacto2Nombre,Contacto2Telefono,Contacto2Celular,Contacto2Mail,Contacto1erNivel,STR_TO_DATE(SeguridadFisicaBoveda,'%d/%m/%Y'),AccesoDias,AccesoHorario,d_version_multivendor,SwCsds,SwSistemaOperativo,d_ups,ConfigDenonCass,d_checker,SeguridadLogicaRkl,EquipamientoLectoraCheques,EquipamientoBiometrico,EquipamientoCamaraAxis,d_enlace,d_ip,d_telecontrol,d_equipo_comunicacion,ComunicacionesReferenciaTelmex,SeguridadFisicaTipoAnclaje,SeguridadFisicaFechaReanclaje,AdquisicionMes,d_fecha_adquisicion,IdentificacionIdAnterior,BancaVip,Contrato,STR_TO_DATE(d_fecha_1ra_instalacion,'%d/%m/%Y'),ContratoPagaRenta,d_tipo_seguridad,ContratoNominas,SuministroSupresorPicos,ContratoComisionRetiro,ContratoComisionConsulta,ContratoCriterioComision,d_ram,d_firmware_dispensador,d_procesador,d_host_name,HwModeloProcesador,SeguridadFisicaRiesgo,HwConfiguracion,d_ampliacion_ab,d_respaldo_comm,STR_TO_DATE(SuministroFechaUps,'%d/%m/%Y'),AdquisicionOrdenCompra,IdcComunicacion,ConfiguracionTipoTabla,STR_TO_DATE(AdquisicionExpiracionGarantia,'%d/%m/%Y'),IdentificacionActivoFijo,SwEsq,d_ubicacion,UbicacionZonaHoraria,AccesoNumeroDiasIngreso,AccesoIngresoFinSemana,AccesoAcuerdo,AccesoContacto1erNivel,SeguridadLogicaSep,ComunicacionesCableado,SigaVersion,ContratoMontoRenta,EtvKilometrosDotacion,EtviNvelesServicioDotacion,SuministroInmueble,SuministroAdecuaciones,STR_TO_DATE(ContratoExpiracion,'%d/%m/%Y'),STR_TO_DATE(SigaFechaActualizacion,'%d/%m/%Y'),InstitucionGiroEmpresarial,BancaClasificacion,STR_TO_DATE(SigaUltimaConexion,'%d/%m/%Y %h:%i'),STR_TO_DATE(zg_InformacionAlATMs,'%d/%m/%Y'),now(),now(),'pedro'
 from siga_test");

        DB::select("UPDATE siga  set d_division = 'METRO SUR' where d_division = 'METROPOLITANA II';");
        DB::select("UPDATE siga  set d_division = 'METRO SUR' where d_division = 'METROPOLITANA SUR';");

        DB::select("UPDATE siga  set d_division = 'METRO NORTE' where d_division = 'METROPOLITANA I';");
        DB::select("UPDATE siga  set d_division = 'METRO NORTE' where d_division = 'METROPOLITANA NORTE';");

        DB::select("DELETE from  siga where LENGTH(pk_autoservicios_id) >4;");
        DB::select("UPDATE siga set pk_autoservicios_id= LPAD(pk_autoservicios_id,4,'0');");

        $userId = Auth::id();
        $this->internal->create(array(
            'user_id'       => $userId,
            'evento'        => 'Se ha Importado CSV-Siga o Excel ',
            'created_at'    => $now,
            'updated_at'    => $now
        ));

        $data_for_email = [
            'id_seguimiento2'   => 'hola'
        ];

        $hola = 'Hello';

        $user_destinatario = Auth::user();

        Mail::send('emails.cargaExitosaSiga', $data_for_email, function ($m) use ($hola, $user_destinatario) {
            $m->from('marcoantonio.negrete.contractor@bbva.com', 'ESTRUCTURALES');
            $m->to($user_destinatario['email'])->subject("Se ha cargado el archivo de manera exitosa");
          });

    }

    public function subir_excel_indip_al_dia_csv(Request $request)
    {
        //DB::table('indisp_al_dia')->delete();
        
        SendEmailIndispoAlDia::dispatch('marcoantonio.negrete.contractor@bbva.com', 'Archivo subido Correctamente');

        $now = Carbon::now('America/Mexico_City');

        $userId = Auth::id();
        $this->internal->create(array(
            'user_id'       => $userId,
            'evento'        => 'Se ha Importado CSV-Indispo al dia o Excel ',
            'created_at'    => $now,
            'updated_at'    => $now
        ));

    }

    public function subir_excel_fallas_mt_dia_csv(Request $request)
    {

        //DB::table('fallas_mt_por_dia')->delete();

        SendEmailFallasMtPorDia::dispatch('marcoantonio.negrete.contractor@bbva.com', 'Archivo subido Correctamente');

        $now = Carbon::now('America/Mexico_City');

        $userId = Auth::id();
        $this->internal->create(array(
            'user_id'       => $userId,
            'evento'        => 'Se ha Importado CSV-Fallas MT al dia o Excel ',
            'created_at'    => $now,
            'updated_at'    => $now
        ));

    }

    public function subir_excel_indispo_dispensador_mas_receptor_csv(Request $request)
    {
        SendEmailIndispoDispensadorReceptor::dispatch('marcoantonio.negrete.contractor@bbva.com', 'Archivo subido Correctamente');

        $now = Carbon::now('America/Mexico_City');

        $userId = Auth::id();
        $this->internal->create(array(
            'user_id'       => $userId,
            'evento'        => 'Se ha Importado CSV-Indispo dispensador mas receptor o Excel ',
            'created_at'    => $now,
            'updated_at'    => $now
        ));

    }

    public function subir_excel_indispo_sucursal_cr_csv(Request $request)
    {
        SendEmailIndispoSucursalCr::dispatch('marcoantonio.negrete.contractor@bbva.com', 'Archivo subido Correctamente');

        $now = Carbon::now('America/Mexico_City');

        $userId = Auth::id();
        $this->internal->create(array(
            'user_id'       => $userId,
            'evento'        => 'Se ha Importado CSV-Indispo sucursal CR o Excel ',
            'created_at'    => $now,
            'updated_at'    => $now
        ));

    }

    public function subir_excel_indispo_inicial_csv(Request $request)
    {
        SendEmailIndispoInicial::dispatch('marcoantonio.negrete.contractor@bbva.com', 'Archivo subido Correctamente');

        $now = Carbon::now('America/Mexico_City');

        $userId = Auth::id();
        $this->internal->create(array(
            'user_id'       => $userId,
            'evento'        => 'Se ha Importado CSV-Indispo Inicial o Excel ',
            'created_at'    => $now,
            'updated_at'    => $now
        ));

    }
}