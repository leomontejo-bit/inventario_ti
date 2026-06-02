<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de solo lectura sobre la vista SQL v_activos_detalle.
 * Une activo + tipo + hotel + departamento + colaborador.
 */
class ActivoDetalle extends Model
{
    protected $table = 'v_activos_detalle';

    public $timestamps = false;

    // Las vistas no se escriben desde la app
    protected $guarded = [];

    public function save(array $options = []): bool
    {
        return false;
    }
}
