<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    protected $table = 'hoteles';

    // La tabla solo tiene created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'nombre',
        'codigo',
        'direccion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function colaboradores(): HasMany
    {
        return $this->hasMany(Colaborador::class);
    }

    public function activos(): HasMany
    {
        return $this->hasMany(Activo::class);
    }
}
