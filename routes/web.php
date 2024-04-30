<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('login', 'APILoginController@login');

Route::group(['middleware' => ['jwt.auth', 'cors'],'prefix' => 'v1'], function () {

    Route::group(['namespace' => 'v1'], function () {
        
        //Usuarios
        Route::get('getDivisiones', 'UserController@getDivisions');
        Route::get('getRoles', 'UserController@getRoles');

        Route::get('usuarios', 'UserController@index');
        Route::get('usuarios/{user_id}', 'UserController@show');
        Route::post('usuarios', 'UserController@store');
        Route::post('usuarios/{user_id}', 'UserController@update');
        Route::delete('usuarios/{user_id}', 'UserController@destroy');
        Route::put('usuarios/{user_id}/resetPassword', 'UserController@updatePassword');

        //INSUMOS
        Route::get('infoInsumos', 'InsumosController@getInfoInsumos');
        Route::post('subirExcel_siga','InsumosController@subir_excel_siga_csv');
        Route::post('subirExcel_indispo_al_dia','InsumosController@subir_excel_indip_al_dia_csv');
        Route::post('subirExcel_fallas_mt_dia','InsumosController@subir_excel_fallas_mt_dia_csv');
        Route::post('subirExcel_indispo_dispensador_mas_receptor','InsumosController@subir_excel_indispo_dispensador_mas_receptor_csv');
        Route::post('subirExcel_indispo_sucursal_cr','InsumosController@subir_excel_indispo_sucursal_cr_csv');
        Route::post('subirExcel_detalleFallas','InsumosController@subir_excel_detalle_fallas_csv');
        Route::post('subirExcel_indispo_inicial','InsumosController@subir_excel_indispo_inicial_csv');

        //ALTA DATOS ODT
        Route::post('subirExcel_alta_datos_odt','AltaDatosOdtController@subir_excel_alta_datos_odt_csv');
        Route::get('descargaExcelDatosOdt/{fecha_inicio}/{fecha_fin}', 'AltaDatosOdtController@downloadReporteDatosOdt');
        
        Route::get('universoMesActual', 'AltaDatosOdtController@getUniversoMesActual');
        Route::post('universoMesActual', 'AltaDatosOdtController@createUniversoMesActual');
        Route::get('universoMesActual/{item_id}', 'AltaDatosOdtController@getItemUniversoMesActual');
        Route::put('universoMesActual/{item_id}', 'AltaDatosOdtController@updateUniversoMesActual');
        Route::delete('universoMesActual/{item_id}', 'AltaDatosOdtController@deleteItemUniversoMesActual');

        Route::get('alta_datos_odt', 'AltaDatosOdtController@getAltaDatosOdt');
        Route::get('alta_datos_odt_planeacion', 'AltaDatosOdtController@getAltaDatosOdtPlaneacion');
        Route::get('alta_datos_odt_analisis', 'AltaDatosOdtController@getAltaDatosOdtAnalisis');
        Route::get('alta_datos_odt_gestion', 'AltaDatosOdtController@getAltaDatosOdtGestion');
        Route::get('alta_datos_odt_cierres', 'AltaDatosOdtController@getAltaDatosOdtCierres');
        Route::post('alta_datos_odt', 'AltaDatosOdtController@createNewDataOdt');
        Route::put('alta_datos_odt/{seguimiento_id}/{alta_datos_odt_id}', 'AltaDatosOdtController@updateSeguimientoOdt');
        Route::delete('borrarItemAltaDatosOdt/{item_id}', 'AltaDatosOdtController@deleteItemAltaDatosItem');

        Route::post('seguimientoOdt/{atm}/{que_se_mide}/{alta_datos_odt_id}', 'AltaDatosOdtController@createNewSeguimientoOdt');

        Route::post('obtenerInfoAtmBySiga', 'AltaDatosOdtController@getInfoAtmByTablaSiga');
        Route::get('viewDatosSeguimiento/{alta_datos_odt_id}', 'AltaDatosOdtController@getDataSeguimientoByAltaDatosOdtId');

        Route::get('infoAtm/{atm}', 'AltaDatosOdtController@getInfoAtm');

        Route::post('envioreporteOdt', 'AltaDatosOdtController@envioReporteOdt');
        Route::post('envioreportePlantillaDrive', 'AltaDatosOdtController@envioReportePlantillaDrive');

        Route::put('habilitarCierre/{altadatosodt_id}', 'AltaDatosOdtController@habilitaCierre');

        //PLANEACION
        Route::post('crearPlaneacion', 'PlaneacionController@createPlaneacion');
        Route::get('planeacion/{planeacion_id}', 'PlaneacionController@getPlaneacionById');
        Route::put('planeacion/{planeacion_id}', 'PlaneacionController@updatePlaneacion');
        Route::put('planeacionPendiente/{planeacion_id}', 'PlaneacionController@updatePlaneacionPendiente');
        Route::get('nombres/{idc}/{division}/{tipo_de_ingenieria}', 'PlaneacionController@getNombreByIdcAndDivision');
        Route::get('empresas/{division}', 'PlaneacionController@getEmpresaByDivision');
        Route::get('interventores/{division}', 'PlaneacionController@getInterventorByDivision');
        Route::get('tipoIngenierias', 'PlaneacionController@getTipoIngenieria');
        Route::get('descargaExcelPlaneacion/{fecha_inicio}/{fecha_fin}', 'PlaneacionController@downloadReportePlaneacion');
        Route::put('updateFechaCita/{planeacion_id}', 'PlaneacionController@updateFechaCita');
        Route::put('updateticketseguimiento/{planeacion_id}', 'PlaneacionController@updateTicketSeguimiento');

        //ANALISIS
        Route::post('crearAnalisis', 'AnalisisController@createAnalisis');
        Route::get('analisis/{analisis_id}', 'AnalisisController@getAnalisisById');
        Route::put('analisis/{analisis_id}', 'AnalisisController@updateAnalisis');
        Route::get('descargaExcelAnalisis/{fecha_inicio}/{fecha_fin}', 'AnalisisController@downloadReporteAnalisis');

        Route::get('descargaExcelPrueba', 'AnalisisController@exportExcel');

        //GESTION
        Route::get('statusVisitas', 'GestionController@getStatusVisita');
        Route::get('piezasPendientes', 'GestionController@getPiezaPendiente');
        Route::get('gestion/{id}', 'GestionController@getGestionById');
        Route::put('gestion/{id}', 'GestionController@updateGestion');
        Route::post('crearGestion', 'GestionController@createGestion');
        Route::get('descargaExcelGestion/{fecha_inicio}/{fecha_fin}', 'GestionController@downloadReporteGestion');
        Route::put('gestionUpdateEstatus/{id}', 'GestionController@updateEstatusGestion');
        Route::put('gestionUpdateHorarios/{id}', 'GestionController@updateHorariosGestion');
        Route::get('getTipoVisita/{id}', 'GestionController@getTipoVisitas');

        //CIERRES
        Route::post('crearCierre', 'CierresController@createCierres');
        Route::get('cierre/{id}', 'CierresController@getCierreById');
        Route::put('cierre/{id}', 'CierresController@updateCierres');
        Route::put('cierre_idc/{id}', 'CierresController@updateCierresCompletado');
        Route::get('descargaExcelCierres/{fecha_inicio}/{fecha_fin}', 'CierresController@downloadReporteCierres');

        //CASOS
        Route::get('casos', 'CasosController@getCasos');
        Route::get('casosPlaneacion', 'CasosController@getCasosPlaneacion');
        Route::get('casosCierres', 'CasosController@getCasosCierres');
        Route::get('casosAnalisis', 'CasosController@getCasosAnalisis');
        Route::get('casosGestion', 'CasosController@getCasosGestion');
        Route::get('casosByOdts/{atm}/{caso}', 'CasosController@getOdts');
        Route::post('createCaso', 'CasosController@createCaso');
        Route::get('casos/{caso}', 'CasosController@getInfoCasoByOdt');
        Route::put('casos/{caso}', 'CasosController@updateCaso');
        Route::delete('casos/{caso}', 'CasosController@destroyCaso');
        Route::get('infoAtm/{atm}', 'CasosController@getInfoAtm');
        Route::get('cargarUltimaOdt/{alta_datos_odt_id}', 'CasosController@uploadLastOdt');

        //ATMS
        Route::get('atms', 'AtmsController@getAtms');
        Route::get('atms/indispo_al_dia/{atm}', 'AtmsController@getInfoAtmByIndispoAlDia');
        Route::get('alta_datos_odt_by_atm/{atm}', 'AtmsController@getAltaDatosOdtByAtm');


        Route::group(['prefix' => 'archivos'], function (\Illuminate\Routing\Router $router) {
            $router->post('storage/create', 'StorageController@save');
        });



        
    });

});



