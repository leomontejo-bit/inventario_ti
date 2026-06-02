<?php

namespace App\Http\Requests\Licencia;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLicenciaRequest extends FormRequest
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
        return [
            'activo_id' => ['nullable', 'integer', 'exists:activos,id'],
            'nombre_software' => ['sometimes', 'string', 'max:200'],
            'version' => ['nullable', 'string', 'max:50'],
            'fabricante' => ['nullable', 'string', 'max:100'],
            'tipo_licencia' => ['sometimes', Rule::in(['oem', 'volumen', 'suscripcion', 'freeware', 'otro'])],
            'clave_producto' => ['nullable', 'string', 'max:255'],
            'num_licencias' => ['sometimes', 'integer', 'min:1'],
            'fecha_adquisicion' => ['nullable', 'date'],
            'fecha_vencimiento' => ['nullable', 'date'],
            'proveedor' => ['nullable', 'string', 'max:150'],
            'costo' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['sometimes', Rule::in(['activa', 'vencida', 'baja'])],
            'notas' => ['nullable', 'string'],
        ];
    }
}
