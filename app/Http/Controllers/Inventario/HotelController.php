<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Services\EliminacionCatalogoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HotelController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Hotel::orderBy('nombre')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'codigo' => ['required', 'string', 'max:20', 'unique:hoteles,codigo'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'activo' => ['boolean'],
        ]);

        return response()->json(Hotel::create($datos), 201);
    }

    public function show(Hotel $hotel): JsonResponse
    {
        return response()->json($hotel);
    }

    public function update(Request $request, Hotel $hotel): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => ['sometimes', 'string', 'max:100'],
            'codigo' => ['sometimes', 'string', 'max:20', Rule::unique('hoteles', 'codigo')->ignore($hotel->id)],
            'direccion' => ['nullable', 'string', 'max:200'],
            'activo' => ['boolean'],
        ]);

        $hotel->update($datos);

        return response()->json($hotel);
    }

    public function destroy(Hotel $hotel, EliminacionCatalogoService $eliminacion): JsonResponse
    {
        $eliminacion->hotel($hotel);

        return response()->json(null, 204);
    }
}
