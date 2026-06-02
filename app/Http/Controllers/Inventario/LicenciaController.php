<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Licencia\StoreLicenciaRequest;
use App\Http\Requests\Licencia\UpdateLicenciaRequest;
use App\Models\LicenciaSoftware;
use App\Services\LicenciaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenciaController extends Controller
{
    public function __construct(
        private readonly LicenciaService $licencias,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filtros = $request->only(['estado', 'tipo_licencia', 'buscar']);

        return response()->json(
            $this->licencias->listar($filtros, $request->integer('por_pagina', 20))
        );
    }

    public function store(StoreLicenciaRequest $request): JsonResponse
    {
        $licencia = $this->licencias->crear($request->validated());

        return response()->json($licencia, 201);
    }

    public function show(LicenciaSoftware $licencia): JsonResponse
    {
        return response()->json($licencia->load('activo'));
    }

    public function update(UpdateLicenciaRequest $request, LicenciaSoftware $licencia): JsonResponse
    {
        $licencia = $this->licencias->actualizar($licencia, $request->validated());

        return response()->json($licencia);
    }

    public function destroy(LicenciaSoftware $licencia): JsonResponse
    {
        $this->licencias->eliminar($licencia);

        return response()->json(null, 204);
    }
}
