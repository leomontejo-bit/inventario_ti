<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Views\ActivoDetalle;
use App\Models\Views\ActivoSinAsignar;
use App\Models\Views\ContratoPorVencer;
use App\Models\Views\LicenciaPorVencer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    /**
     * Reporte general de activos con todos los datos unidos (vista v_activos_detalle).
     */
    public function activosDetalle(Request $request): JsonResponse
    {
        $query = ActivoDetalle::query()
            ->when($request->input('hotel_codigo'), fn ($q, $v) => $q->where('hotel_codigo', $v))
            ->when($request->input('estado'), fn ($q, $v) => $q->where('estado', $v));

        return response()->json($query->paginate($request->integer('por_pagina', 50)));
    }

    /**
     * Activos en stock sin asignar (vista v_activos_sin_asignar).
     */
    public function activosSinAsignar(): JsonResponse
    {
        return response()->json(ActivoSinAsignar::all());
    }

    /**
     * Licencias que vencen en los próximos 60 días (vista v_licencias_por_vencer).
     */
    public function licenciasPorVencer(): JsonResponse
    {
        return response()->json(LicenciaPorVencer::all());
    }

    /**
     * Contratos que vencen en los próximos 60 días (vista v_contratos_por_vencer).
     */
    public function contratosPorVencer(): JsonResponse
    {
        return response()->json(ContratoPorVencer::all());
    }
}
