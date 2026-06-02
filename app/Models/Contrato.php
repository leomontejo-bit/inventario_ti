<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contrato extends Model
{
    protected $table = 'contratos';

    const UPDATED_AT = null;

    protected $fillable = [
        'activo_id',
        'tipo',
        'proveedor',
        'num_contrato',
        'contacto_proveedor',
        'telefono_proveedor',
        'fecha_inicio',
        'fecha_fin',
        'monto',
        'moneda',
        'estado',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function activo(): BelongsTo
    {
        return $this->belongsTo(Activo::class);
    }
}
