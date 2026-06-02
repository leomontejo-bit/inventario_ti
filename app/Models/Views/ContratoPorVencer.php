<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

/**
 * Vista SQL v_contratos_por_vencer: contratos vigentes que vencen en <= 60 días.
 */
class ContratoPorVencer extends Model
{
    protected $table = 'v_contratos_por_vencer';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'fecha_fin' => 'date',
            'dias_restantes' => 'integer',
        ];
    }

    public function save(array $options = []): bool
    {
        return false;
    }
}
