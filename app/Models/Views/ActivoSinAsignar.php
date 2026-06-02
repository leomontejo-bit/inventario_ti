<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

/**
 * Vista SQL v_activos_sin_asignar: stock disponible sin colaborador.
 */
class ActivoSinAsignar extends Model
{
    protected $table = 'v_activos_sin_asignar';

    public $timestamps = false;

    protected $guarded = [];

    public function save(array $options = []): bool
    {
        return false;
    }
}
