<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asignacion extends Model
{
    protected $table = 'asignaciones';

    const UPDATED_AT = null;

    protected $fillable = [
        'activo_id',
        'colaborador_id',
        'usuario_sistema_id',
        'fecha_asignacion',
        'fecha_devolucion',
        'motivo_devolucion',
        'condicion_entrega',
        'condicion_retorno',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'date',
            'fecha_devolucion' => 'date',
        ];
    }

    public function activo(): BelongsTo
    {
        return $this->belongsTo(Activo::class);
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function usuarioSistema(): BelongsTo
    {
        return $this->belongsTo(UsuarioSistema::class, 'usuario_sistema_id');
    }
}
