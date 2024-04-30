<?php

namespace App\Imports;

use App\Entities\Siga;
use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SigaImport implements ToModel, WithHeadingRow
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

        if ($row['instalacionfecha']) {
            $n = $row['instalacionfecha'];

            $pattern = "#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#";
            $validate = preg_match($pattern, $n);
            
            if($validate == 0){
                $n = null;
            }else{
                $ano = substr($n, 6, 4);
                $mes = substr($n, 3, 2);
                $dia = substr($n, 0, 2);

                $n = $ano . '-' . $mes . '-' . $dia;

                $n = Carbon::parse($n)->format('Y-m-d');
            }
            
            $row['instalacionfecha'] = $n;
        }

        if ($row['seguridadfisicafechareanclaje']) {
            $n = $row['seguridadfisicafechareanclaje'];
            $pattern = "#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#";
            $validate = preg_match($pattern, $n);
            
            if($validate == 0){
                $n = null;
            }else{
                $ano = substr($n, 6, 4);
                $mes = substr($n, 3, 2);
                $dia = substr($n, 0, 2);

                $n = $ano . '-' . $mes . '-' . $dia;

                $n = Carbon::parse($n)->format('Y-m-d');
            }
            $row['seguridadfisicafechareanclaje'] = $n;
        }

        if ($row['d_fecha_1ra_instalacion']) {
            $n = $row['d_fecha_1ra_instalacion'];
            $pattern = "#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#";
            $validate = preg_match($pattern, $n);
            
            if($validate == 0){
                $n = null;
            }else{
                $ano = substr($n, 6, 4);
                $mes = substr($n, 3, 2);
                $dia = substr($n, 0, 2);

                $n = $ano . '-' . $mes . '-' . $dia;

                $n = Carbon::parse($n)->format('Y-m-d');
            }
            $row['d_fecha_1ra_instalacion'] = $n;
        }

        if ($row['suministrofechaups']) {
            $n = $row['suministrofechaups'];
            $pattern = "#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#";
            $validate = preg_match($pattern, $n);
            
            if($validate == 0){
                $n = null;
            }else{
                $ano = substr($n, 6, 4);
                $mes = substr($n, 3, 2);
                $dia = substr($n, 0, 2);

                $n = $ano . '-' . $mes . '-' . $dia;

                $n = Carbon::parse($n)->format('Y-m-d');
            }
            $row['suministrofechaups'] = $n;
        }

        if ($row['adquisicionexpiraciongarantia']) {
            $n = $row['adquisicionexpiraciongarantia'];
            $pattern = "#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#";
            $validate = preg_match($pattern, $n);
            
            if($validate == 0){
                $n = null;
            }else{
                $ano = substr($n, 6, 4);
                $mes = substr($n, 3, 2);
                $dia = substr($n, 0, 2);

                $n = $ano . '-' . $mes . '-' . $dia;

                $n = Carbon::parse($n)->format('Y-m-d');
            }
            $row['adquisicionexpiraciongarantia'] = $n;
        }

        if ($row['contratoexpiracion']) {
            $n = $row['contratoexpiracion'];
            $ano = substr($n, 6, 4);
            $pattern = "#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#";
            $validate = preg_match($pattern, $n);
            
            if($validate == 0){
                $n = null;
            }else{
                $ano = substr($n, 6, 4);
                $mes = substr($n, 3, 2);
                $dia = substr($n, 0, 2);

                $n = $ano . '-' . $mes . '-' . $dia;

                $n = Carbon::parse($n)->format('Y-m-d');
            }
            $row['contratoexpiracion'] = $n;
        }
        if ($row['sigafechaactualizacion']) {
            $n = $row['sigafechaactualizacion'];
            $pattern = "#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#";
            $validate = preg_match($pattern, $n);
            
            if($validate == 0){
                $n = null;
            }else{
                $ano = substr($n, 6, 4);
                $mes = substr($n, 3, 2);
                $dia = substr($n, 0, 2);

                $n = $ano . '-' . $mes . '-' . $dia;

                $n = Carbon::parse($n)->format('Y-m-d');
            }
            $row['sigafechaactualizacion'] = $n;
        }

        if ($row['sigaultimaconexion']) {
            $n = $row['sigaultimaconexion'];

            if($n == null || $n == '' || $n == '0000-00-00' ){
                $n = null;
            }else{
                $ano = substr($n, 6, 4);
                $mes = substr($n, 3, 2);
                $dia = substr($n, 0, 2);
                $hora = substr($n, 11, 5);

                $n = $ano . '-' . $mes . '-' . $dia . ' ' . $hora . ':00';
                $n = Carbon::parse($n)->format('Y-m-d H:i');
            }

            $row['sigaultimaconexion'] = $n;
        }
        if ($row['zg_informacionalatms']) {
            $n = $row['zg_informacionalatms'];
            $pattern = "#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#";
            $validate = preg_match($pattern, $n);
            
            if($validate == 0){
                $n = null;
            }else{
                $ano = substr($n, 6, 4);
                $mes = substr($n, 3, 2);
                $dia = substr($n, 0, 2);

                $n = $ano . '-' . $mes . '-' . $dia;

                $n = Carbon::parse($n)->format('Y-m-d');
            }
            $row['zg_informacionalatms'] = $n;
        }
        return \App\Entities\Siga::updateOrCreate([
            //Add unique field combo to match here
            //For example, perhaps you only want one entry per user:
            'pk_autoservicios_id'    => $row['pk_autoservicios_id'],
        ],[
            'd_sitio'                             => $row['d_sitio'] ?? null,
            'd_cr'                                => $row['d_cr'] ?? null,
            'd_division'                          => $row['d_division'] ?? null,
            'd_marca'                             => $row['d_marca'] ?? null,
            'd_modelo'                            => $row['d_modelo'] ?? null,
            'IdentificacionNumeroSerieId'         => $row['identificacionnumeroserieid'] ?? null,
            'IdentificacionSiaff'                 => $row['identificacionsiaff'] ?? null,
            'd_tipo'                              => $row['d_tipo'] ?? null,
            'd_carga'                             => $row['d_carga'] ?? null,
            'd_interfase'                         => $row['d_interfase'] ?? null,
            'InstalacionFecha'                    => $row['instalacionfecha'] ?? null,
            'Institucion'                         => $row['institucion'] ?? null,
            'InstitucionGiro'                     => $row['instituciongiro'] ?? null,
            'd_grupo'                             => $row['d_grupo'] ?? null,
            'd_banca'                             => $row['d_banca'] ?? null,
            'd_estatus_autoservicio'              => $row['d_estatus_autoservicio'] ?? null,
            'd_atm_en_sitio'                      => $row['d_atm_en_sitio'] ?? null,
            'IdentificacionProyecto'              => $row['identificacionproyecto'] ?? null,
            'AdquisicionGarantia'                 => $row['adquisiciongarantia'] ?? null,
            'd_calle'                             => $row['d_calle'] ?? null,
            'd_num_exterior'                      => $row['d_num_exterior'] ?? null,
            'd_estado'                            => $row['d_estado'] ?? null,
            'd_ciudad'                            => $row['d_ciudad'] ?? null,
            'd_cp'                                => $row['d_cp'] ?? null,
            'd_delegacion_o_municipio'            => $row['d_delegacion_o_municipio'] ?? null,
            'd_colonia'                           => $row['d_colonia'] ?? null,
            'd_latitud'                           => $row['d_latitud'] ?? null,
            'd_longitud'                          => $row['d_longitud'] ?? null,
            'UbicacionTipoSitio'                  => $row['ubicaciontipositio'] ?? null,
            'd_local'                             => $row['d_local'] ?? null,
            'UbicacionCelula'                     => $row['ubicacioncelula'] ?? null,
            'UbicacionClaveSitio'                 => $row['ubicacionclavesitio'] ?? null,
            'UbicacionModeloInstalacion'          => $row['ubicacionmodeloinstalacion'] ?? null,
            'd_idc'                               => $row['d_idc'] ?? null,
            'd_dias_idc'                          => $row['d_dias_idc'] ?? null,
            'd_horario_idc'                       => $row['d_horario_idc'] ?? null,
            'IdcDiasFinSemana'                    => $row['idcdiasfinsemana'] ?? null,
            'IdcHorarioFinSemana'                 => $row['idchorariofinsemana'] ?? null,
            'IdcNivelesServicio'                  => $row['idcnivelesservicio'] ?? null,
            'd_etv'                               => $row['d_etv'] ?? null,
            'd_dias_etv'                          => $row['d_dias_etv'] ?? null,
            'd_hrs_etv'                           => $row['d_hrs_etv'] ?? null,
            'EtvDiasFinSemana'                    => $row['etvdiasfinsemana'] ?? null,
            'EtvHorarioFinSemana'                 => $row['etvhorariofinsemana'] ?? null,
            'EtvNivelServicio'                    => $row['etvnivelservicio'] ?? null,
            'd_cond_esp_acceso_idc'               => $row['d_condiciones_especiales_acceso_idc'] ?? null,
            'd_cond_esp_acceso_etv'               => $row['d_condiciones_especiales_acceso_etv'] ?? null,
            'EtvPlazaOrigen'                      => $row['etvplazaorigen'] ?? null,
            'Contacto1Nombre'                     => $row['contacto1nombre'] ?? null,
            'Contacto1Telefono'                   => $row['contacto1telefono'] ?? null,
            'Contacto1Celular'                    => $row['contacto1celular'] ?? null,
            'Contacto1Mail'                       => $row['contacto1mail'] ?? null,
            'Contacto2Nombre'                     => $row['contacto2nombre'] ?? null,
            'Contacto2Telefono'                   => $row['contacto2telefono'] ?? null,
            'Contacto2Celular'                    => $row['contacto2celular'] ?? null,
            'Contacto2Mail'                       => $row['contacto2mail'] ?? null,
            'Contacto1erNivel'                    => $row['contacto1ernivel'] ?? null,
            'SeguridadFisicaBoveda'               => $row['seguridadfisicaboveda'] ?? null,
            'AccesoDias'                          => $row['accesodias'] ?? null,
            'AccesoHorario'                       => $row['accesohorario'] ?? null,
            'd_version_multivendor'               => $row['d_version_multivendor'] ?? null,
            'SwCsds'                              => $row['swcsds'] ?? null,
            'SwSistemaOperativo'                  => $row['swsistemaoperativo'] ?? null,
            'd_ups'                               => $row['d_ups'] ?? null,
            'ConfigDenonCass'                     => $row['configuraciondenominacioncasseteros'] ?? null,
            'd_checker'                           => $row['d_checker'] ?? null,
            'SeguridadLogicaRkl'                  => $row['seguridadlogicarkl'] ?? null,
            'EquipamientoLectoraCheques'          => $row['equipamientolectoracheques'] ?? null,
            'EquipamientoBiometrico'              => $row['equipamientobiometrico'] ?? null,
            'EquipamientoCamaraAxis'              => $row['equipamientocamaraaxis'] ?? null,
            'd_enlace'                            => $row['d_enlace'] ?? null,
            'd_ip'                                => $row['d_ip'] ?? null,
            'd_telecontrol'                       => $row['d_telecontrol'] ?? null,
            'd_equipo_comunicacion'               => $row['d_equipo_comunicacion'] ?? null,
            'ComunicacionesReferenciaTelmex'      => $row['comunicacionesreferenciatelmex'] ?? null,
            'SeguridadFisicaTipoAnclaje'          => $row['seguridadfisicatipoanclaje'] ?? null,
            'SeguridadFisicaFechaReanclaje'       => $row['seguridadfisicafechareanclaje'] ?? null,
            'AdquisicionMes'                      => $row['adquisicionmes'] ?? null,
            'd_fecha_adquisicion'                 => $row['d_fecha_adquisicion'] ?? null,
            'IdentificacionIdAnterior'            => $row['identificacionidanterior'] ?? null,
            'BancaVip'                            => $row['bancavip'] ?? null,
            'Contrato'                            => $row['contrato'] ?? null,
            'd_fecha_1ra_instalacion'             => $row['d_fecha_1ra_instalacion'] ?? null,
            'ContratoPagaRenta'                   => $row['contratopagarenta'] ?? null,
            'd_tipo_seguridad'                    => $row['d_tipo_seguridad'] ?? null,
            'ContratoNominas'                     => $row['contratonominas'] ?? null,
            'SuministroSupresorPicos'             => $row['suministrosupresorpicos'] ?? null,
            'ContratoComisionRetiro'              => $row['contratocomisionretiro'] ?? null,
            'ContratoComisionConsulta'            => $row['contratocomisionconsulta'] ?? null,
            'ContratoCriterioComision'            => $row['contratocriteriocomision'] ?? null,
            'd_ram'                               => $row['d_ram'] ?? null,
            'd_firmware_dispensador'              => $row['d_firmware_dispensador'] ?? null,
            'd_procesador'                        => $row['d_procesador'] ?? null,
            'd_host_name'                         => $row['d_host_name'] ?? null,
            'HwModeloProcesador'                  => $row['hwmodeloprocesador'] ?? null,
            'SeguridadFisicaRiesgo'               => $row['seguridadfisicariesgo'] ?? null,
            'HwConfiguracion'                     => $row['hwconfiguracion'] ?? null,
            'd_ampliacion_ab'                     => $row['d_ampliacion_ab'] ?? null,
            'd_respaldo_comm'                     => $row['d_respaldo_comm'] ?? null,
            'SuministroFechaUps'                  => $row['suministrofechaups'] ?? null,
            'AdquisicionOrdenCompra'              => $row['adquisicionordencompra'] ?? null,
            'IdcComunicacion'                     => $row['idccomunicacion'] ?? null,
            'ConfiguracionTipoTabla'              => $row['configuraciontipotabla'] ?? null,
            'AdquisicionExpiracionGarantia'       => $row['adquisicionexpiraciongarantia'] ?? null,
            'IdentificacionActivoFijo'            => $row['identificacionactivofijo'] ?? null,
            'SwEsq'                               => $row['swesq'] ?? null,
            'd_ubicacion'                         => $row['d_ubicacion'] ?? null,
            'UbicacionZonaHoraria'                => $row['ubicacionzonahoraria'] ?? null,
            'AccesoNumeroDiasIngreso'             => $row['accesonumerodiasingreso'] ?? null,
            'AccesoIngresoFinSemana'              => $row['accesoingresofinsemana'] ?? null,
            'AccesoAcuerdo'                       => $row['accesoacuerdo'] ?? null,
            'AccesoContacto1erNivel'              => $row['accesocontacto1ernivel'] ?? null,
            'SeguridadLogicaSep'                  => $row['seguridadlogicasep'] ?? null,
            'ComunicacionesCableado'              => $row['comunicacionescableado'] ?? null,
            'SigaVersion'                         => $row['sigaversion'] ?? null,
            'ContratoMontoRenta'                  => $row['contratomontorenta'] ?? null,
            'EtvKilometrosDotacion'               => $row['etvkilometrosdotacion'] ?? null,
            'EtviNvelesServicioDotacion'          => $row['etvinvelesserviciodotacion'] ?? null,
            'SuministroInmueble'                  => $row['suministroinmueble'] ?? null,
            'SuministroAdecuaciones'              => $row['suministroadecuaciones'] ?? null,
            'ContratoExpiracion'                  => $row['contratoexpiracion'] ?? null,
            'SigaFechaActualizacion'              => $row['sigafechaactualizacion'] ?? null,
            'InstitucionGiroEmpresarial'          => $row['instituciongiroempresarial'] ?? null,
            'BancaClasificacion'                  => $row['bancaclasificacion'] ?? null,
            'SigaUltimaConexion'                  => $row['sigaultimaconexion'] ?? null,
            'zg_InformacionAlATMs'                => $row['zg_informacionalatms'] ?? null,
            'user_name'                           => Auth::user()->name,
            'created_at'                          => $now,
            'updated_at'                          => $now,
        ]);  
    }
}
