<?php

use App\Http\Controllers\Web\ActivoWebController;
use App\Http\Controllers\Web\AuditoriaWebController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CatalogoWebController;
use App\Http\Controllers\Web\ColaboradorWebController;
use App\Http\Controllers\Web\ContratoWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DepartamentoWebController;
use App\Http\Controllers\Web\EscaneoWebController;
use App\Http\Controllers\Web\EtiquetaWebController;
use App\Http\Controllers\Web\HotelWebController;
use App\Http\Controllers\Web\TipoActivoWebController;
use App\Http\Controllers\Web\LicenciaWebController;
use Illuminate\Support\Facades\Route;

// ===== Autenticación (público) =====
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// ===== Rutas protegidas (requieren sesión iniciada) =====
Route::middleware('auth')->group(function () {

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Activos
Route::resource('activos', ActivoWebController::class)
    ->parameters(['activos' => 'activo']);
Route::post('activos/{activo}/asignar', [ActivoWebController::class, 'asignar'])->name('activos.asignar');
Route::post('activos/{activo}/devolver', [ActivoWebController::class, 'devolver'])->name('activos.devolver');
Route::post('activos/{activo}/baja', [ActivoWebController::class, 'darDeBaja'])->name('activos.baja');
Route::get('activos/{activo}/etiqueta', [EtiquetaWebController::class, 'imprimir'])->name('etiquetas.imprimir');

// Auditoría / trazabilidad
Route::get('auditoria', [AuditoriaWebController::class, 'index'])->name('auditoria.index');

// Escaneo con pistola lectora de código de barras
Route::get('escaneo', [EscaneoWebController::class, 'index'])->name('escaneo.index');
Route::post('escaneo/limpiar', [EscaneoWebController::class, 'limpiar'])->name('escaneo.limpiar');

// Colaboradores
Route::resource('colaboradores', ColaboradorWebController::class)
    ->parameters(['colaboradores' => 'colaborador'])
    ->except('show');

// Licencias
Route::resource('licencias', LicenciaWebController::class)
    ->parameters(['licencias' => 'licencia'])
    ->except('show');

// Contratos
Route::resource('contratos', ContratoWebController::class)
    ->parameters(['contratos' => 'contrato'])
    ->except('show');

// Catálogos: hub + secciones administrables (cada una con su tabla, buscador y formulario)
Route::get('catalogos', [CatalogoWebController::class, 'index'])->name('catalogos.index');
Route::resource('catalogos/hoteles', HotelWebController::class)
    ->parameters(['hoteles' => 'hotel'])->names('catalogos.hoteles')->except('show');
Route::resource('catalogos/departamentos', DepartamentoWebController::class)
    ->parameters(['departamentos' => 'departamento'])->names('catalogos.departamentos')->except('show');
Route::resource('catalogos/tipos', TipoActivoWebController::class)
    ->parameters(['tipos' => 'tipo'])->names('catalogos.tipos')->except('show');

}); // fin rutas protegidas
