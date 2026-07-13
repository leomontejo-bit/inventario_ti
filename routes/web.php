<?php

use App\Http\Controllers\Web\ActivoWebController;
use App\Http\Controllers\Web\AuditoriaWebController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CatalogoWebController;
use App\Http\Controllers\Web\ColaboradorWebController;
use App\Http\Controllers\Web\ConfiguracionSistemaWebController;
use App\Http\Controllers\Web\ContratoWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DepartamentoWebController;
use App\Http\Controllers\Web\EscaneoWebController;
use App\Http\Controllers\Web\EtiquetaWebController;
use App\Http\Controllers\Web\HotelWebController;
use App\Http\Controllers\Web\LicenciaWebController;
use App\Http\Controllers\Web\PerfilWebController;
use App\Http\Controllers\Web\TipoActivoWebController;
use App\Http\Controllers\Web\UsuarioSistemaWebController;
use Illuminate\Support\Facades\Route;

// ===== Autenticación (público) =====
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
// El logo es visible en el acceso; modificarlo sigue requiriendo ser administrador.
Route::get('configuracion/logo', [ConfiguracionSistemaWebController::class, 'logo'])->name('configuracion.logo');

// ===== Rutas protegidas (requieren sesión iniciada) =====
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil personal
    Route::get('perfil', [PerfilWebController::class, 'edit'])->name('perfil.edit');
    Route::put('perfil', [PerfilWebController::class, 'update'])->name('perfil.update');
    Route::get('perfil/foto', [PerfilWebController::class, 'foto'])->name('perfil.foto');

    // Apariencia general del sistema (solo administradores)
    Route::get('configuracion', [ConfiguracionSistemaWebController::class, 'edit'])->name('configuracion.edit');
    Route::put('configuracion', [ConfiguracionSistemaWebController::class, 'update'])->name('configuracion.update');

    // Activos
    Route::resource('activos', ActivoWebController::class)
        ->parameters(['activos' => 'activo']);
    Route::post('activos/{activo}/asignar', [ActivoWebController::class, 'asignar'])->name('activos.asignar');
    Route::post('activos/{activo}/devolver', [ActivoWebController::class, 'devolver'])->name('activos.devolver');
    Route::post('activos/{activo}/baja', [ActivoWebController::class, 'darDeBaja'])->name('activos.baja');
    Route::get('activos/{activo}/etiqueta', [EtiquetaWebController::class, 'imprimir'])->name('etiquetas.imprimir');

    // Auditoría / trazabilidad
    Route::get('auditoria', [AuditoriaWebController::class, 'index'])->name('auditoria.index');

    // Usuarios del sistema
    Route::patch('usuarios/{usuario}/toggle', [UsuarioSistemaWebController::class, 'toggle'])->name('usuarios.toggle');
    Route::post('usuarios/{usuario}/reset-password', [UsuarioSistemaWebController::class, 'resetPassword'])->middleware('throttle:10,1')->name('usuarios.reset-password');
    Route::resource('usuarios', UsuarioSistemaWebController::class)
        ->parameters(['usuarios' => 'usuario'])
        ->except('show', 'destroy');

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
