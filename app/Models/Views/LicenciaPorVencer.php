<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

/**
 * Vista SQL v_licencias_por_vencer: licencias activas que vencen en <= 60 días.
 */
class LicenciaPorVencer extends Model
{
    protected $table = 'v_licencias_por_vencer';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'fecha_vencimiento' => 'date',
            'dias_restantes' => 'integer',
        ];
    }

    public function save(array $options = []): bool
    {
        return false;
    }
}
