<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenciaSoftware extends Model
{
    protected $table = 'licencias_software';

    const UPDATED_AT = null;

    protected $fillable = [
        'activo_id',
        'nombre_software',
        'version',
        'fabricante',
        'tipo_licencia',
        'clave_producto',
        'num_licencias',
        'fecha_adquisicion',
        'fecha_vencimiento',
        'proveedor',
        'costo',
        'estado',
        'notas',
    ];

    protected $hidden = [
        'clave_producto',
    ];

    protected function casts(): array
    {
        return [
            'num_licencias' => 'integer',
            'costo' => 'decimal:2',
            'fecha_adquisicion' => 'date',
            'fecha_vencimiento' => 'date',
        ];
    }

    public function activo(): BelongsTo
    {
        return $this->belongsTo(Activo::class);
    }
}
