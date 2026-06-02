<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Activo;
use App\Services\ActivoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ActivoAccionController extends Controller
{
    public function __construct(
        private readonly ActivoService $activos,
    ) {
    }

    public function asignar(Request $request, Activo $activo): JsonResponse
    {
        $datos = $request->validate([
            'colaborador_id' => ['required', 'integer', 'exists:colaboradores,id'],
            'usuario_sistema_id' => ['required', 'integer', 'exists:usuarios_sistema,id'],
            'fecha_asignacion' => ['required', 'date'],
            'condicion_entrega' => ['nullable', Rule::in(['bueno', 'regular', 'dañado'])],
            'notas' => ['nullable', 'string'],
        ]);

        $asignacion = $this->activos->asignar(
            activo: $activo,
            colaboradorId: $datos['colaborador_id'],
            usuarioSistemaId: $datos['usuario_sistema_id'],
            fechaAsignacion: $datos['fecha_asignacion'],
            condicionEntrega: $datos['condicion_entrega'] ?? 'bueno',
            notas: $datos['notas'] ?? null,
        );

        return response()->json($asignacion, 201);
    }

    public function devolver(Request $request, Activo $activo): JsonResponse
    {
        $datos = $request->validate([
            'fecha_devolucion' => ['required', 'date'],
            'condicion_retorno' => ['nullable', Rule::in(['bueno', 'regular', 'dañado'])],
            'motivo_devolucion' => ['nullable', 'string', 'max:255'],
        ]);

        $this->activos->devolver(
            activo: $activo,
            fechaDevolucion: $datos['fecha_devolucion'],
            condicionRetorno: $datos['condicion_retorno'] ?? 'bueno',
            motivoDevolucion: $datos['motivo_devolucion'] ?? null,
        );

        return response()->json($activo->fresh());
    }

    public function darDeBaja(Request $request, Activo $activo): JsonResponse
    {
        $datos = $request->validate([
            'fecha_baja' => ['required', 'date'],
            'motivo_baja' => ['required', 'string', 'max:255'],
        ]);

        $activo = $this->activos->darDeBaja($activo, $datos['fecha_baja'], $datos['motivo_baja']);

        return response()->json($activo);
    }
}
