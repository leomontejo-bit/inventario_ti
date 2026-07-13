<?php

namespace App\Services;

use App\Exceptions\EliminacionBloqueadaException;
use App\Models\Departamento;
use App\Models\Hotel;
use App\Models\TipoActivo;

class EliminacionCatalogoService
{
    public function hotel(Hotel $hotel): void
    {
        $activos = $hotel->activos()->count();
        $colaboradores = $hotel->colaboradores()->count();

        if ($activos || $colaboradores) {
            throw new EliminacionBloqueadaException(
                "tiene {$activos} activo(s) y {$colaboradores} colaborador(es) asociados.",
                'reasigna esos registros a otro hotel o desactiva el hotel para conservar su historial.',
            );
        }

        $hotel->delete();
    }

    public function departamento(Departamento $departamento): void
    {
        $activos = $departamento->activos()->count();
        $colaboradores = $departamento->colaboradores()->count();

        if ($activos || $colaboradores) {
            throw new EliminacionBloqueadaException(
                "tiene {$activos} activo(s) y {$colaboradores} colaborador(es) asociados.",
                'reasigna esos registros a otro departamento o desactiva el departamento.',
            );
        }

        $departamento->delete();
    }

    public function tipoActivo(TipoActivo $tipo): void
    {
        $activos = $tipo->activos()->count();

        if ($activos) {
            throw new EliminacionBloqueadaException(
                "tiene {$activos} activo(s) asociado(s).",
                'cambia el tipo de esos activos o desactiva este tipo para que ya no se use en nuevos registros.',
            );
        }

        $tipo->delete();
    }
}
