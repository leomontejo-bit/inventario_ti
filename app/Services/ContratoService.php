<?php

namespace App\Services;

use App\Models\Contrato;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ContratoService
{
    /**
     * @param  array<string, mixed>  $filtros
     */
    public function listar(array $filtros = [], int $porPagina = 20): LengthAwarePaginator
    {
        return Contrato::query()
            ->with('activo')
            ->when($filtros['estado'] ?? null, fn (Builder $q, $v) => $q->where('estado', $v))
            ->when($filtros['tipo'] ?? null, fn (Builder $q, $v) => $q->where('tipo', $v))
            ->when($filtros['buscar'] ?? null, function (Builder $q, $v) {
                $q->where('proveedor', 'like', "%{$v}%")
                    ->orWhere('num_contrato', 'like', "%{$v}%");
            })
            ->orderByDesc('fecha_inicio')
            ->paginate($porPagina);
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function crear(array $datos): Contrato
    {
        return Contrato::create($datos);
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function actualizar(Contrato $contrato, array $datos): Contrato
    {
        $contrato->update($datos);

        return $contrato;
    }

    public function eliminar(Contrato $contrato): void
    {
        $contrato->delete();
    }
}
