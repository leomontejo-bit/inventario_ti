<?php

namespace App\Services;

use App\Models\LicenciaSoftware;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class LicenciaService
{
    /**
     * @param  array<string, mixed>  $filtros
     */
    public function listar(array $filtros = [], int $porPagina = 20): LengthAwarePaginator
    {
        return LicenciaSoftware::query()
            ->with('activo')
            ->when($filtros['estado'] ?? null, fn (Builder $q, $v) => $q->where('estado', $v))
            ->when($filtros['tipo_licencia'] ?? null, fn (Builder $q, $v) => $q->where('tipo_licencia', $v))
            ->when($filtros['buscar'] ?? null, function (Builder $q, $v) {
                $q->where('nombre_software', 'like', "%{$v}%")
                    ->orWhere('fabricante', 'like', "%{$v}%");
            })
            ->orderBy('nombre_software')
            ->paginate($porPagina);
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function crear(array $datos): LicenciaSoftware
    {
        return LicenciaSoftware::create($datos);
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function actualizar(LicenciaSoftware $licencia, array $datos): LicenciaSoftware
    {
        $licencia->update($datos);

        return $licencia;
    }

    public function eliminar(LicenciaSoftware $licencia): void
    {
        $licencia->delete();
    }
}
