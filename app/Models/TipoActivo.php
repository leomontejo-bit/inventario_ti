<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoActivo extends Model
{
    protected $table = 'tipos_activo';

    // La tabla no tiene columnas de timestamps
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'categoria',
        'prefijo_codigo',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function activos(): HasMany
    {
        return $this->hasMany(Activo::class, 'tipo_activo_id');
    }
}
