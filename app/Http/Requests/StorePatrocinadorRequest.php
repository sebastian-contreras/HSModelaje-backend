<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatrocinadorRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambia esto según tu lógica de autorización
    }

    public function rules()
    {
        return [
            'IdEvento' => 'required|integer',
            'Patrocinador' => 'required|string|max:100',
            'Correo' => 'required|email|max:100',
            'Telefono' => 'required|string|max:10', // Asegúrate de que tenga exactamente 10 caracteres
            'Descripcion' => 'nullable|string', // Puede ser nulo
        ];
    }

    public function messages()
    {
        return [
            'IdEvento.required' => 'El campo IdEvento es obligatorio.',
            'Patrocinador.required' => 'El campo Patrocinador es obligatorio.',
            'Correo.required' => 'El campo Correo es obligatorio.',
            'Telefono.required' => 'El campo Telefono es obligatorio.',
        ];
    }
}
