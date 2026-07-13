<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UsuarioSistema extends Authenticatable
{
    protected $table = 'usuarios_sistema';

    const UPDATED_AT = null;

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'foto_perfil',
        'password_hash',
        'rol',
        'activo',
        'ultimo_acceso',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'ultimo_acceso' => 'datetime',
        ];
    }

    /**
     * La contraseña vive en la columna password_hash (no en "password").
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'usuario_sistema_id');
    }

    public function etiquetas(): HasMany
    {
        return $this->hasMany(Etiqueta::class, 'usuario_sistema_id');
    }

    public function auditorias(): HasMany
    {
        return $this->hasMany(Auditoria::class, 'usuario_sistema_id');
    }
}
