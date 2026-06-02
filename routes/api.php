<?php

use App\Http\Controllers\Inventario\ActivoAccionController;
use App\Http\Controllers\Inventario\ActivoController;
use App\Http\Controllers\Inventario\ColaboradorController;
use App\Http\Controllers\Inventario\ContratoController;
use App\Http\Controllers\Inventario\DepartamentoController;
use App\Http\Controllers\Inventario\HotelController;
use App\Http\Controllers\Inventario\LicenciaController;
use App\Http\Controllers\Inventario\ReporteController;
use App\Http\Controllers\Inventario\TipoActivoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas API - Inventario TI
|--------------------------------------------------------------------------
| Prefijo automático: /api
*/

Route::prefix('inventario')->name('api.inventario.')->group(function () {
    // --- Activos (CRUD + acciones de ciclo de vida) ---
    Route::apiResource('activos', ActivoController::class);
    Route::post('activos/{activo}/asignar', [ActivoAccionController::class, 'asignar']);
    Route::post('activos/{activo}/devolver', [ActivoAccionController::class, 'devolver']);
    Route::post('activos/{activo}/baja', [ActivoAccionController::class, 'darDeBaja']);

    // --- Colaboradores ---
    Route::apiResource('colaboradores', ColaboradorController::class)
        ->parameters(['colaboradores' => 'colaborador']);

    // --- Licencias y contratos ---
    Route::apiResource('licencias', LicenciaController::class);
    Route::apiResource('contratos', ContratoController::class);

    // --- Catálogos ---
    Route::apiResource('hoteles', HotelController::class)
        ->parameters(['hoteles' => 'hotel']);
    Route::apiResource('departamentos', DepartamentoController::class);
    Route::apiResource('tipos-activo', TipoActivoController::class)->parameters(['tipos-activo' => 'tipoActivo']);

    // --- Reportes (vistas SQL) ---
    Route::prefix('reportes')->group(function () {
        Route::get('activos-detalle', [ReporteController::class, 'activosDetalle']);
        Route::get('activos-sin-asignar', [ReporteController::class, 'activosSinAsignar']);
        Route::get('licencias-por-vencer', [ReporteController::class, 'licenciasPorVencer']);
        Route::get('contratos-por-vencer', [ReporteController::class, 'contratosPorVencer']);
    });
});
