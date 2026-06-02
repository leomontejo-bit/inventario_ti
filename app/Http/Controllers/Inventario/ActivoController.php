<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Activo\StoreActivoRequest;
use App\Http\Requests\Activo\UpdateActivoRequest;
use App\Models\Activo;
use App\Services\ActivoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivoController extends Controller
{
    public function __construct(
        private readonly ActivoService $activos,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filtros = $request->only([
            'hotel_id', 'departamento_id', 'tipo_activo_id', 'estado', 'buscar',
        ]);

        return response()->json(
            $this->activos->listar($filtros, $request->integer('por_pagina', 20))
        );
    }

    public function store(StoreActivoRequest $request): JsonResponse
    {
        $activo = $this->activos->crear($request->validated());

        return response()->json($activo->load(['tipoActivo', 'hotel', 'departamento']), 201);
    }

    public function show(Activo $activo): JsonResponse
    {
        return response()->json(
            $activo->load(['tipoActivo', 'hotel', 'departamento', 'colaborador', 'licencias', 'contratos'])
        );
    }

    public function update(UpdateActivoRequest $request, Activo $activo): JsonResponse
    {
        $activo = $this->activos->actualizar($activo, $request->validated());

        return response()->json($activo->load(['tipoActivo', 'hotel', 'departamento']));
    }

    public function destroy(Activo $activo): JsonResponse
    {
        $this->activos->eliminar($activo);

        return response()->json(null, 204);
    }
}
