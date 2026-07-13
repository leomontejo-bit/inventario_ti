<?php

namespace App\Services;

use App\Exceptions\EliminacionBloqueadaException;
use App\Models\Colaborador;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ColaboradorService
{
    /**
     * @param  array<string, mixed>  $filtros
     */
    public function listar(array $filtros = [], int $porPagina = 20): LengthAwarePaginator
    {
        return Colaborador::query()
            ->with(['hotel', 'departamento'])
            ->when($filtros['hotel_id'] ?? null, fn (Builder $q, $v) => $q->where('hotel_id', $v))
            ->when($filtros['departamento_id'] ?? null, fn (Builder $q, $v) => $q->where('departamento_id', $v))
            ->when($filtros['estado'] ?? null, fn (Builder $q, $v) => $q->where('estado', $v))
            ->when($filtros['buscar'] ?? null, function (Builder $q, $v) {
                $q->where(function (Builder $sub) use ($v) {
                    $sub->where('nombre', 'like', "%{$v}%")
                        ->orWhere('num_empleado', 'like', "%{$v}%")
                        ->orWhere('email_corporativo', 'like', "%{$v}%");
                });
            })
            ->orderBy('nombre')
            ->paginate($porPagina);
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function crear(array $datos): Colaborador
    {
        return Colaborador::create($datos);
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function actualizar(Colaborador $colaborador, array $datos): Colaborador
    {
        $colaborador->update($datos);

        return $colaborador;
    }

    public function eliminar(Colaborador $colaborador): void
    {
        $activos = $colaborador->activos()->count();
        $asignaciones = $colaborador->asignaciones()->count();

        if ($activos || $asignaciones) {
            throw new EliminacionBloqueadaException(
                "tiene {$activos} activo(s) asignado(s) y {$asignaciones} asignación(es) en su historial.",
                'devuelve o reasigna sus activos y cambia su estado a “inactivo” para conservar la trazabilidad.',
            );
        }

        $colaborador->delete();
    }
}
