<?php

namespace App\Http\Requests\Activo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActivoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $activoId = $this->route('activo')?->id;

        return [
            'tipo_activo_id' => ['sometimes', 'integer', 'exists:tipos_activo,id'],
            'hotel_id' => ['sometimes', 'integer', 'exists:hoteles,id'],
            'departamento_id' => ['sometimes', 'integer', 'exists:departamentos,id'],
            'colaborador_id' => ['nullable', 'integer', 'exists:colaboradores,id'],

            'num_inventario' => ['sometimes', 'string', 'max:50', Rule::unique('activos', 'num_inventario')->ignore($activoId)],
            'codigo_interno_ti' => ['nullable', 'string', 'max:50'],
            'codigo_barras' => ['nullable', 'string', 'max:100', Rule::unique('activos', 'codigo_barras')->ignore($activoId)],
            'num_serie' => ['nullable', 'string', 'max:100'],

            'nombre_equipo' => ['nullable', 'string', 'max:150'],
            'marca' => ['nullable', 'string', 'max:100'],
            'modelo' => ['nullable', 'string', 'max:150'],
            'procesador' => ['nullable', 'string', 'max:150'],
            'ram_gb' => ['nullable', 'numeric', 'min:0'],
            'almacenamiento' => ['nullable', 'string', 'max:100'],
            'sistema_operativo' => ['nullable', 'string', 'max:150'],

            'direccion_ip' => ['nullable', 'string', 'max:45'],
            'direccion_mac' => ['nullable', 'string', 'max:17'],

            'estado' => ['sometimes', Rule::in(['activo', 'baja', 'mantenimiento', 'extraviado', 'stock', 'prestamo'])],
            'fecha_adquisicion' => ['nullable', 'date'],
            'valor_adquisicion' => ['nullable', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
        ];
    }
}
