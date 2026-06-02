<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activo extends Model
{
    protected $table = 'activos';

    protected $fillable = [
        'tipo_activo_id',
        'hotel_id',
        'departamento_id',
        'colaborador_id',
        'num_inventario',
        'codigo_interno_ti',
        'codigo_barras',
        'num_serie',
        'nombre_equipo',
        'marca',
        'modelo',
        'procesador',
        'ram_gb',
        'almacenamiento',
        'sistema_operativo',
        'direccion_ip',
        'direccion_mac',
        'estado',
        'fecha_adquisicion',
        'fecha_baja',
        'motivo_baja',
        'valor_adquisicion',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'ram_gb' => 'decimal:2',
            'valor_adquisicion' => 'decimal:2',
            'fecha_adquisicion' => 'date',
            'fecha_baja' => 'date',
        ];
    }

    public function tipoActivo(): BelongsTo
    {
        return $this->belongsTo(TipoActivo::class, 'tipo_activo_id');
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class);
    }

    public function licencias(): HasMany
    {
        return $this->hasMany(LicenciaSoftware::class);
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class);
    }

    public function etiquetas(): HasMany
    {
        return $this->hasMany(Etiqueta::class);
    }

    /**
     * Asignación vigente (sin fecha de devolución).
     */
    public function asignacionVigente()
    {
        return $this->hasOne(Asignacion::class)->whereNull('fecha_devolucion');
    }
}
