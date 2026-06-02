<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contrato\StoreContratoRequest;
use App\Http\Requests\Contrato\UpdateContratoRequest;
use App\Models\Contrato;
use App\Services\ContratoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContratoController extends Controller
{
    public function __construct(
        private readonly ContratoService $contratos,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filtros = $request->only(['estado', 'tipo', 'buscar']);

        return response()->json(
            $this->contratos->listar($filtros, $request->integer('por_pagina', 20))
        );
    }

    public function store(StoreContratoRequest $request): JsonResponse
    {
        $contrato = $this->contratos->crear($request->validated());

        return response()->json($contrato, 201);
    }

    public function show(Contrato $contrato): JsonResponse
    {
        return response()->json($contrato->load('activo'));
    }

    public function update(UpdateContratoRequest $request, Contrato $contrato): JsonResponse
    {
        $contrato = $this->contratos->actualizar($contrato, $request->validated());

        return response()->json($contrato);
    }

    public function destroy(Contrato $contrato): JsonResponse
    {
        $this->contratos->eliminar($contrato);

        return response()->json(null, 204);
    }
}
