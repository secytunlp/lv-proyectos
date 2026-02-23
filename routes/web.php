<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\PersonalController;
use App\Http\Controllers\UniversidadController;
use App\Http\Controllers\TituloController;
use App\Http\Controllers\InvestigadorController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\IntegranteController;
use App\Http\Controllers\IntegranteEstadoController;
use App\Http\Controllers\SolicitudSicadiController;
use App\Http\Controllers\SolicitudSicadiEstadoController;
use App\Http\Controllers\JovenController;
use App\Http\Controllers\JovenEstadoController;
use App\Http\Controllers\JovenEvaluacionController;
use App\Http\Controllers\JovenEvaluacionEstadoController;
use App\Http\Controllers\ViajeController;
use App\Http\Controllers\ViajeEstadoController;
use App\Http\Controllers\ViajeEvaluacionController;
use App\Http\Controllers\ViajeEvaluacionEstadoController;

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
    return redirect(route('login'));
});

Route::get('/error-403', function () {
    return view('errors.403');
})->name('error-403');

Auth::routes();




Route::group(['middleware' => 'auth'], function() {
    Route::get('select-rol', [UserController::class, 'selectRol'])->name('select-rol');
    Route::post('save-selected-rol', [UserController::class, 'saveSelectedRol'])->name('save-selected-rol');
});
Route::get('/home', function () {
    return view('welcome'); // Cambia 'welcome' al nombre de tu vista
})->middleware(['auth', 'CheckSelectedRolePermissions'])->name('home');
Route::group(['middleware' => ['auth', 'CheckSelectedRolePermissions']], function() {
    //Route::get('/home', [JovenController::class, 'index'])->name('home');
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::get('perfil', [UserController::class, 'perfil'])->name('users.perfil');
    Route::post('updatePerfil', [UserController::class, 'updatePerfil'])->name('users.updatePerfil');
    Route::post('user-datatable', [UserController::class, 'dataTable'])->name('users.dataTable');



    Route::resource('personals', PersonalController::class);

    Route::resource('universidads', UniversidadController::class);

    Route::resource('titulos', TituloController::class);
    Route::post('titulo-datatable', [TituloController::class, 'dataTable'])->name('titulos.dataTable');

    Route::resource('investigadors', InvestigadorController::class);
    Route::post('investigador-datatable', [InvestigadorController::class, 'dataTable'])->name('investigadors.dataTable');
    /*Route::post('/investigadors/clear-filter', [InvestigadorController::class, 'clearFilter'])->name('investigadors.clearFilter')*/

    Route::resource('solicitud_sicadis', SolicitudSicadiController::class);
    Route::post('solicitud_sicadi-datatable', [SolicitudSicadiController::class, 'dataTable'])->name('solicitud_sicadis.dataTable');
    Route::get('solicitud_sicadi-pdf', [SolicitudSicadiController::class, 'generatePDF'])->name('solicitud_sicadis.solicitud-pdf');
    Route::get('solicitud_sicadi-archivos', [SolicitudSicadiController::class, 'archivos'])->name('solicitud_sicadis.archivos');
    Route::post('enviarSicadi/{id}', [SolicitudSicadiController::class, 'enviar'])->name('solicitud_sicadis.enviar');
    Route::post('admitirSicadi/{id}', [SolicitudSicadiController::class, 'admitir'])->name('solicitud_sicadis.admitir');
    Route::get('solicitud_sicadis/{solicitud_sicadi}/rechazar', [SolicitudSicadiController::class, 'rechazar'])->name('solicitud_sicadis.rechazar');
    Route::put('solicitud_sicadis/{solicitud_sicadi}/deny', [SolicitudSicadiController::class, 'saveDeny'])->name('solicitud_sicadis.saveDeny');
    Route::get('solicitud_sicadis/{solicitud_sicadi}/rectificar', [SolicitudSicadiController::class, 'rectificar'])->name('solicitud_sicadis.rectificar');
    Route::put('solicitud_sicadis/{solicitud_sicadi}/rect', [SolicitudSicadiController::class, 'saveRect'])->name('solicitud_sicadis.saveRect');
    Route::get('solicitud_sicadi-exportar', [SolicitudSicadiController::class, 'exportar'])->name('solicitud_sicadis.exportar');
    Route::get('/migrar-fotos-sicadi', [SolicitudSicadiController::class, 'migrarFotosSicadi'])->name('solicitud_sicadis.migrarFotos');


    Route::get('importar_sicadi', [SolicitudSicadiController::class, 'importar'])->name('solicitud_sicadis.importar');
    Route::post('solicitud-sicadis/importar', [SolicitudSicadiController::class, 'importprocess'])->name('solicitud_sicadis.importprocess');

    Route::resource('solicitud_sicadi_estados', SolicitudSicadiEstadoController::class);
    Route::post('solicitud_sicadi_estado-datatable', [SolicitudSicadiEstadoController::class, 'dataTable'])->name('solicitud_sicadi_estados.dataTable');

    Route::resource('proyectos', ProyectoController::class);
    Route::post('proyecto-datatable', [ProyectoController::class, 'dataTable'])->name('proyectos.dataTable');


    Route::get('integrantes/{integrante}/rechazar', [IntegranteController::class, 'rechazar'])->name('integrantes.rechazar');
    Route::put('integrantes/{integrante}/deny', [IntegranteController::class, 'saveDeny'])->name('integrantes.saveDeny');
    Route::get('integrantes/{integrante}/baja', [IntegranteController::class, 'baja'])->name('integrantes.baja');
    Route::put('integrantes/{integrante}/remove', [IntegranteController::class, 'remove'])->name('integrantes.remove');
    Route::get('integrantes/{integrante}/cambio', [IntegranteController::class, 'cambio'])->name('integrantes.cambio');
    Route::put('integrantes/{integrante}/cambiar', [IntegranteController::class, 'cambiar'])->name('integrantes.cambiar');
    Route::get('integrantes/{integrante}/rechazarBaja', [IntegranteController::class, 'rechazarBaja'])->name('integrantes.rechazarBaja');
    Route::put('integrantes/{integrante}/denyBaja', [IntegranteController::class, 'saveDenyBaja'])->name('integrantes.saveDenyBaja');
    Route::get('integrantes/{integrante}/rechazarCambio', [IntegranteController::class, 'rechazarCambio'])->name('integrantes.rechazarCambio');
    Route::put('integrantes/{integrante}/denyCambio', [IntegranteController::class, 'saveDenyCambio'])->name('integrantes.saveDenyCambio');
    Route::get('integrantes/{integrante}/cambioHS', [IntegranteController::class, 'cambioHS'])->name('integrantes.cambioHS');
    Route::put('integrantes/{integrante}/cambiarHS', [IntegranteController::class, 'cambiarHS'])->name('integrantes.cambiarHS');
    Route::get('integrantes/{integrante}/rechazarCambioHS', [IntegranteController::class, 'rechazarCambioHS'])->name('integrantes.rechazarCambioHS');
    Route::put('integrantes/{integrante}/denyCambioHS', [IntegranteController::class, 'saveDenyCambioHS'])->name('integrantes.saveDenyCambioHS');
    Route::get('integrantes/{integrante}/cambioTipo', [IntegranteController::class, 'cambioTipo'])->name('integrantes.cambioTipo');
    Route::put('integrantes/{integrante}/cambiarTipo', [IntegranteController::class, 'cambiarTipo'])->name('integrantes.cambiarTipo');
    Route::get('integrantes/{integrante}/rechazarCambioTipo', [IntegranteController::class, 'rechazarCambioTipo'])->name('integrantes.rechazarCambioTipo');
    Route::put('integrantes/{integrante}/denyCambioTipo', [IntegranteController::class, 'saveDenyCambioTipo'])->name('integrantes.saveDenyCambioTipo');
    Route::post('anular/{id}', [IntegranteController::class, 'anular'])->name('integrantes.anular');
    Route::post('anularCambio/{id}', [IntegranteController::class, 'anularCambio'])->name('integrantes.anularCambio');
    Route::post('anularHS/{id}', [IntegranteController::class, 'anularHS'])->name('integrantes.anularHS');
    Route::post('anularTipo/{id}', [IntegranteController::class, 'anularTipo'])->name('integrantes.anularTipo');
    Route::resource('integrantes', IntegranteController::class);
    Route::post('integrante-datatable', [IntegranteController::class, 'dataTable'])->name('integrantes.dataTable');

    Route::get('buscar_investigador', [IntegranteController::class, 'buscarInvestigador'])->name('integrantes.buscarInvestigador');
    Route::get('alta-pdf', [IntegranteController::class, 'generateAltaPDF'])->name('integrantes.alta-pdf');
    Route::get('baja-pdf', [IntegranteController::class, 'generateBajaPDF'])->name('integrantes.baja-pdf');
    Route::get('cambio-pdf', [IntegranteController::class, 'generateCambioPDF'])->name('integrantes.cambio-pdf');
    Route::get('cambioHS-pdf', [IntegranteController::class, 'generateCambioHSPDF'])->name('integrantes.cambioHS-pdf');
    Route::get('cambioTipo-pdf', [IntegranteController::class, 'generateCambioTipoPDF'])->name('integrantes.cambioTipo-pdf');
    Route::get('archivos', [IntegranteController::class, 'archivos'])->name('integrantes.archivos');
    Route::post('enviar/{id}', [IntegranteController::class, 'enviar'])->name('integrantes.enviar');
    Route::post('admitir/{id}', [IntegranteController::class, 'admitir'])->name('integrantes.admitir');
    Route::post('enviarBaja/{id}', [IntegranteController::class, 'enviarBaja'])->name('integrantes.enviarBaja');
    Route::post('admitirBaja/{id}', [IntegranteController::class, 'admitirBaja'])->name('integrantes.admitirBaja');
    Route::post('enviarCambio/{id}', [IntegranteController::class, 'enviarCambio'])->name('integrantes.enviarCambio');
    Route::post('admitirCambio/{id}', [IntegranteController::class, 'admitirCambio'])->name('integrantes.admitirCambio');
    Route::post('enviarCambioHS/{id}', [IntegranteController::class, 'enviarCambioHS'])->name('integrantes.enviarCambioHS');
    Route::post('admitirCambioHS/{id}', [IntegranteController::class, 'admitirCambioHS'])->name('integrantes.admitirCambioHS');
    Route::post('enviarCambioTipo/{id}', [IntegranteController::class, 'enviarCambioTipo'])->name('integrantes.enviarCambioTipo');
    Route::post('admitirCambioTipo/{id}', [IntegranteController::class, 'admitirCambioTipo'])->name('integrantes.admitirCambioTipo');

    Route::resource('integrante_estados', IntegranteEstadoController::class);
    Route::post('integrante_estado-datatable', [IntegranteEstadoController::class, 'dataTable'])->name('integrante_estados.dataTable');

    Route::resource('jovens', JovenController::class);
    Route::post('joven-datatable', [JovenController::class, 'dataTable'])->name('jovens.dataTable');

    Route::get('joven-pdf', [JovenController::class, 'generatePDF'])->name('jovens.solicitud-pdf');
    Route::get('joven-archivos', [JovenController::class, 'archivos'])->name('jovens.archivos');
    Route::post('enviarJoven/{id}', [JovenController::class, 'enviar'])->name('jovens.enviar');
    Route::post('admitirJoven/{id}', [JovenController::class, 'admitir'])->name('jovens.admitir');
    Route::get('jovens/{joven}/rechazar', [JovenController::class, 'rechazar'])->name('jovens.rechazar');
    Route::put('jovens/{joven}/deny', [JovenController::class, 'saveDeny'])->name('jovens.saveDeny');
    Route::get('joven-exportar', [JovenController::class, 'exportar'])->name('jovens.exportar');

    Route::resource('joven_estados', JovenEstadoController::class);
    Route::post('joven_estado-datatable', [JovenEstadoController::class, 'dataTable'])->name('joven_estados.dataTable');

    Route::resource('joven_evaluacions', JovenEvaluacionController::class);
    Route::post('joven_evaluacion-datatable', [JovenEvaluacionController::class, 'dataTable'])->name('joven_evaluacions.dataTable');

    Route::get('/joven_evaluacions/{joven_id}/enviar', [JovenEvaluacionController::class, 'enviar'])->name('joven_evaluacions.enviar');
    Route::post('aceptar/{id}', [JovenEvaluacionController::class, 'aceptar'])->name('joven_evaluacions.aceptar');
    Route::get('joven_evaluacions/{joven}/rechazar', [JovenEvaluacionController::class, 'rechazar'])->name('joven_evaluacions.rechazar');
    Route::put('joven_evaluacions/{joven}/deny', [JovenEvaluacionController::class, 'saveDeny'])->name('joven_evaluacions.saveDeny');
    Route::get('joven_evaluacions/{joven}/evaluar', [JovenEvaluacionController::class, 'evaluar'])->name('joven_evaluacions.evaluar');
    Route::put('joven_evaluacions/{joven}/saveEvaluar', [JovenEvaluacionController::class, 'saveEvaluar'])->name('joven_evaluacions.saveEvaluar');
    Route::get('joven_evaluacions-pdf', [JovenEvaluacionController::class, 'generatePDF'])->name('joven_evaluacions.evaluacion-pdf');
    Route::post('send/{id}', [JovenEvaluacionController::class, 'send'])->name('joven_evaluacions.send');
    Route::get('/joven_evaluacions/{joven_id}/actualizar', [JovenEvaluacionController::class, 'actualizar'])->name('joven_evaluacions.actualizar');


    Route::resource('joven_evaluacion_estados', JovenEvaluacionEstadoController::class);
    Route::post('joven_evaluacion_estado-datatable', [JovenEvaluacionEstadoController::class, 'dataTable'])->name('joven_evaluacion_estados.dataTable');

    Route::resource('viajes', ViajeController::class);
    Route::post('viaje-datatable', [ViajeController::class, 'dataTable'])->name('viajes.dataTable');

    Route::get('viaje-pdf', [ViajeController::class, 'generatePDF'])->name('viajes.solicitud-pdf');
    Route::get('viaje-archivos', [ViajeController::class, 'archivos'])->name('viajes.archivos');
    Route::post('enviarViaje/{id}', [ViajeController::class, 'enviar'])->name('viajes.enviar');
    Route::post('admitirViaje/{id}', [ViajeController::class, 'admitir'])->name('viajes.admitir');
    Route::get('viajes/{viaje}/rechazar', [ViajeController::class, 'rechazar'])->name('viajes.rechazar');
    Route::put('viajes/{viaje}/deny', [ViajeController::class, 'saveDeny'])->name('viajes.saveDeny');
    Route::get('viaje-exportar', [ViajeController::class, 'exportar'])->name('viajes.exportar');

    Route::resource('viaje_estados', ViajeEstadoController::class);
    Route::post('viaje_estado-datatable', [ViajeEstadoController::class, 'dataTable'])->name('viaje_estados.dataTable');

    Route::resource('viaje_evaluacions', ViajeEvaluacionController::class);
    Route::post('viaje_evaluacion-datatable', [ViajeEvaluacionController::class, 'dataTable'])->name('viaje_evaluacions.dataTable');

    Route::get('/viaje_evaluacions/{viaje_id}/enviar', [ViajeEvaluacionController::class, 'enviar'])->name('viaje_evaluacions.enviar');
    Route::post('aceptarViaje/{id}', [ViajeEvaluacionController::class, 'aceptar'])->name('viaje_evaluacions.aceptar');
    Route::get('viaje_evaluacions/{viaje}/rechazar', [ViajeEvaluacionController::class, 'rechazar'])->name('viaje_evaluacions.rechazar');
    Route::put('viaje_evaluacions/{viaje}/deny', [ViajeEvaluacionController::class, 'saveDeny'])->name('viaje_evaluacions.saveDeny');
    Route::get('viaje_evaluacions/{viaje}/evaluar', [ViajeEvaluacionController::class, 'evaluar'])->name('viaje_evaluacions.evaluar');
    Route::put('viaje_evaluacions/{viaje}/saveEvaluar', [ViajeEvaluacionController::class, 'saveEvaluar'])->name('viaje_evaluacions.saveEvaluar');
    Route::get('viaje_evaluacions-pdf', [ViajeEvaluacionController::class, 'generatePDF'])->name('viaje_evaluacions.evaluacion-pdf');
    Route::post('sendViaje/{id}', [ViajeEvaluacionController::class, 'send'])->name('viaje_evaluacions.send');
    Route::get('/viaje_evaluacions/{viaje_id}/actualizar', [ViajeEvaluacionController::class, 'actualizar'])->name('viaje_evaluacions.actualizar');

    Route::resource('viaje_evaluacion_estados', ViajeEvaluacionEstadoController::class);
    Route::post('viaje_evaluacion_estado-datatable', [ViajeEvaluacionEstadoController::class, 'dataTable'])->name('viaje_evaluacion_estados.dataTable');

});
