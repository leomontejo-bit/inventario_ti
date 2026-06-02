<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Etiqueta extends Model
{
    protected $table = 'etiquetas';

    // Usa fecha_generacion en lugar de created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'activo_id',
        'usuario_sistema_id',
        'tipo_impresion',
        'fecha_generacion',
        'datos_etiqueta',
    ];

    protected function casts(): array
    {
        return [
            'fecha_generacion' => 'datetime',
            'datos_etiqueta' => 'array',
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
