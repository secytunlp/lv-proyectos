<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolicitudSicadiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('custom.auth')->group(function () {
    Route::get('solicitud_sicadis/{id}', [SolicitudSicadiController::class, 'getInvestigadorById']);
    Route::post('solicitud_sicadis/filter', [SolicitudSicadiController::class, 'filterInvestigadores']);
    Route::get('solicitud_sicadis/unidades_academicas', [SolicitudSicadiController::class, 'getUnidadesAcademicas']);
    Route::get('solicitud_sicadis/categorias', [SolicitudSicadiController::class, 'getCategorias']);
    Route::get('solicitud_sicadis/subareas', [SolicitudSicadiController::class, 'getSubareas']);
});

