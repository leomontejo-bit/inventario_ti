<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    protected $table = 'auditoria';

    // Usa la columna fecha en lugar de timestamps estándar
    public $timestamps = false;

    protected $fillable = [
        'activo_id',
        'usuario_sistema_id',
        'tabla_afectada',
        'registro_id',
        'accion',
        'valores_anteriores',
        'valores_nuevos',
        'ip_cliente',
        'fecha',
    ];

    protected function casts(): array
    {
        return [
            'valores_anteriores' => 'array',
            'valores_nuevos' => 'array',
            'fecha' => 'datetime',
        ];
    }

    public function activo(): BelongsTo
    {
        return $this->belongsTo(Activo::class);
    }

    public function usuarioSistema(): BelongsTo
    {
        return $this->belongsTo(UsuarioSistema::class, 'usuario_sistema_id');
    }
}
