<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMetricaRequest extends FormRequest
{
    public function authorize()
    {
        // Permitir que todos los usuarios realicen esta solicitud
        return true;
    }

    public function rules()
    {
        return [
            'IdMetrica' => 'required|integer', // Asegúrate de que exista en la tabla eventos
            'Metrica' => 'required|string|max:150', // Asegúrate de que sea único en la tabla jueces
        ];
    }

    public function messages()
    {
        return [
            'IdMetrica.required' => 'El campo IdMetrica es obligatorio.',
            'IdMetrica.integer' => 'El campo IdMetrica debe ser un número entero.',
            'Metrica.required' => 'El campo Metrica es obligatorio.',

        ];
    }
}
