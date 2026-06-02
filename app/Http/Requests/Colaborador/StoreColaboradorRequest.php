<?php

namespace App\Http\Requests\Colaborador;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreColaboradorRequest extends FormRequest
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
            'hotel_id' => ['required', 'integer', 'exists:hoteles,id'],
            'departamento_id' => ['required', 'integer', 'exists:departamentos,id'],
            'nombre' => ['required', 'string', 'max:200'],
            'num_empleado' => ['required', 'string', 'max:50', 'unique:colaboradores,num_empleado'],
            'email_corporativo' => ['nullable', 'email', 'max:150'],
            'usuario_ad' => ['nullable', 'string', 'max:100'],
            'puesto' => ['nullable', 'string', 'max:150'],
            'estado' => ['nullable', Rule::in(['activo', 'baja', 'vacaciones', 'licencia'])],
        ];
    }
}
