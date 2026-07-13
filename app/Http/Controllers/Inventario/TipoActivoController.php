<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\TipoActivo;
use App\Services\EliminacionCatalogoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TipoActivoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TipoActivo::orderBy('nombre')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'categoria' => ['required', Rule::in(['equipo_computo', 'periferico', 'red', 'licencia', 'contrato', 'otro'])],
            'prefijo_codigo' => ['nullable', 'string', 'max:10'],
            'activo' => ['boolean'],
        ]);

        return response()->json(TipoActivo::create($datos), 201);
    }

    public function show(TipoActivo $tipoActivo): JsonResponse
    {
        return response()->json($tipoActivo);
    }

    public function update(Request $request, TipoActivo $tipoActivo): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => ['sometimes', 'string', 'max:100'],
            'categoria' => ['sometimes', Rule::in(['equipo_computo', 'periferico', 'red', 'licencia', 'contrato', 'otro'])],
            'prefijo_codigo' => ['nullable', 'string', 'max:10'],
            'activo' => ['boolean'],
        ]);

        $tipoActivo->update($datos);

        return response()->json($tipoActivo);
    }

    public function destroy(TipoActivo $tipoActivo, EliminacionCatalogoService $eliminacion): JsonResponse
    {
        $eliminacion->tipoActivo($tipoActivo);

        return response()->json(null, 204);
    }
}
