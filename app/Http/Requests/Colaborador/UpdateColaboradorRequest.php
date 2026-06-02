<?php

namespace App\Http\Requests\Colaborador;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateColaboradorRequest extends FormRequest
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
        $colaboradorId = $this->route('colaborador')?->id;

        return [
            'hotel_id' => ['sometimes', 'integer', 'exists:hoteles,id'],
            'departamento_id' => ['sometimes', 'integer', 'exists:departamentos,id'],
            'nombre' => ['sometimes', 'string', 'max:200'],
            'num_empleado' => ['sometimes', 'string', 'max:50', Rule::unique('colaboradores', 'num_empleado')->ignore($colaboradorId)],
            'email_corporativo' => ['nullable', 'email', 'max:150'],
            'usuario_ad' => ['nullable', 'string', 'max:100'],
            'puesto' => ['nullable', 'string', 'max:150'],
            'estado' => ['sometimes', Rule::in(['activo', 'baja', 'vacaciones', 'licencia'])],
        ];
    }
}
