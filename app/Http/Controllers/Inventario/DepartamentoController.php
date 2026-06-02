<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Departamento::orderBy('nombre')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'activo' => ['boolean'],
        ]);

        return response()->json(Departamento::create($datos), 201);
    }

    public function show(Departamento $departamento): JsonResponse
    {
        return response()->json($departamento);
    }

    public function update(Request $request, Departamento $departamento): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => ['sometimes', 'string', 'max:100'],
            'activo' => ['boolean'],
        ]);

        $departamento->update($datos);

        return response()->json($departamento);
    }

    public function destroy(Departamento $departamento): JsonResponse
    {
        $departamento->delete();

        return response()->json(null, 204);
    }
}
