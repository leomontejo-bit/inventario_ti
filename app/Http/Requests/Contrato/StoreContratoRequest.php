<?php

namespace App\Http\Requests\Contrato;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContratoRequest extends FormRequest
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
            'activo_id' => ['required', 'integer', 'exists:activos,id'],
            'tipo' => ['required', Rule::in(['leasing', 'mantenimiento', 'garantia', 'soporte', 'otro'])],
            'proveedor' => ['required', 'string', 'max:150'],
            'num_contrato' => ['nullable', 'string', 'max:100'],
            'contacto_proveedor' => ['nullable', 'string', 'max:150'],
            'telefono_proveedor' => ['nullable', 'string', 'max:50'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'monto' => ['nullable', 'numeric', 'min:0'],
            'moneda' => ['nullable', 'string', 'size:3'],
            'estado' => ['nullable', Rule::in(['vigente', 'vencido', 'cancelado'])],
            'notas' => ['nullable', 'string'],
        ];
    }
}
