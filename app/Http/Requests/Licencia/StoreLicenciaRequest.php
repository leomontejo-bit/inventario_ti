<?php

namespace App\Http\Requests\Licencia;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLicenciaRequest extends FormRequest
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
            'nombre_software' => ['required', 'string', 'max:200'],
            'version' => ['nullable', 'string', 'max:50'],
            'fabricante' => ['nullable', 'string', 'max:100'],
            'tipo_licencia' => ['nullable', Rule::in(['oem', 'volumen', 'suscripcion', 'freeware', 'otro'])],
            'clave_producto' => ['nullable', 'string', 'max:255'],
            'num_licencias' => ['nullable', 'integer', 'min:1'],
            'fecha_adquisicion' => ['nullable', 'date'],
            'fecha_vencimiento' => ['nullable', 'date'],
            'proveedor' => ['nullable', 'string', 'max:150'],
            'costo' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['nullable', Rule::in(['activa', 'vencida', 'baja'])],
            'notas' => ['nullable', 'string'],
        ];
    }
}
