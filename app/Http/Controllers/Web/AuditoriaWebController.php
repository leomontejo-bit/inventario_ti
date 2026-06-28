<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditoriaWebController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->only(['accion', 'tabla', 'buscar']);

        $registros = Auditoria::query()
            ->with(['activo', 'usuarioSistema'])
            ->when($filtros['accion'] ?? null, fn ($q, $v) => $q->where('accion', $v))
            ->when($filtros['tabla'] ?? null, fn ($q, $v) => $q->where('tabla_afectada', $v))
            ->when($filtros['buscar'] ?? null, function ($q, $v) {
                $q->where(function ($subquery) use ($v) {
                    $subquery
                        ->whereHas('activo', fn ($activo) => $activo->where('num_inventario', 'like', "%{$v}%"))
                        ->orWhere('ip_cliente', 'like', "%{$v}%");
                });
            })
            ->latest('fecha')
            ->paginate(25)
            ->withQueryString();

        return view('auditoria.index', [
            'registros' => $registros,
            'filtros' => $filtros,
            'acciones' => ['insertar', 'actualizar', 'eliminar', 'asignar', 'devolver', 'baja', 'importar_excel', 'imprimir_etiqueta', 'escaneo'],
        ]);
    }
}
