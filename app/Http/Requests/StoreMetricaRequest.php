<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMetricaRequest extends FormRequest
{
    public function authorize()
    {
        // Permitir que todos los usuarios realicen esta solicitud
        return true;
    }

    public function rules()
    {
        return [
            'IdEvento' => 'required|integer', // Asegúrate de que exista en la tabla eventos
            'Metrica' => 'required|string|max:150', // Asegúrate de que sea único en la tabla jueces
        ];
    }

    public function messages()
    {
        return [
            'IdEvento.required' => 'El campo IdEvento es obligatorio.',
            'IdEvento.integer' => 'El campo IdEvento debe ser un número entero.',
            'Metrica.required' => 'El campo Metrica es obligatorio.',

        ];
    }
}
