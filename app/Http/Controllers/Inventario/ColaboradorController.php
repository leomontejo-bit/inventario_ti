<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Colaborador\StoreColaboradorRequest;
use App\Http\Requests\Colaborador\UpdateColaboradorRequest;
use App\Models\Colaborador;
use App\Services\ColaboradorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ColaboradorController extends Controller
{
    public function __construct(
        private readonly ColaboradorService $colaboradores,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filtros = $request->only(['hotel_id', 'departamento_id', 'estado', 'buscar']);

        return response()->json(
            $this->colaboradores->listar($filtros, $request->integer('por_pagina', 20))
        );
    }

    public function store(StoreColaboradorRequest $request): JsonResponse
    {
        $colaborador = $this->colaboradores->crear($request->validated());

        return response()->json($colaborador->load(['hotel', 'departamento']), 201);
    }

    public function show(Colaborador $colaborador): JsonResponse
    {
        return response()->json(
            $colaborador->load(['hotel', 'departamento', 'activos'])
        );
    }

    public function update(UpdateColaboradorRequest $request, Colaborador $colaborador): JsonResponse
    {
        $colaborador = $this->colaboradores->actualizar($colaborador, $request->validated());

        return response()->json($colaborador->load(['hotel', 'departamento']));
    }

    public function destroy(Colaborador $colaborador): JsonResponse
    {
        $this->colaboradores->eliminar($colaborador);

        return response()->json(null, 204);
    }
}
