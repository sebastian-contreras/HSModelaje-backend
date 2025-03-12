<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatrocinadorRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambia esto según tu lógica de autorización
    }

    public function rules()
    {
        return [
            'IdPatrocinador' => 'required|integer', // Si es autoincremental, puede ser nulo
            'Patrocinador' => 'required|string|max:100',
            'Correo' => 'required|email|max:100',
            'Telefono' => 'required|string|max:10', // Asegúrate de que tenga exactamente 10 caracteres
            'DomicilioRef' => 'nullable|string|max:150', // Asegúrate de que tenga exactamente 10 caracteres
            'Descripcion' => 'nullable|string', // Puede ser nulo
        ];
    }

    public function messages()
    {
        return [
            'IdPatrocinador.required' => 'El campo IdPatrocinador es obligatorio.',
            'Patrocinador.required' => 'El campo Patrocinador es obligatorio.',
            'Correo.required' => 'El campo Correo es obligatorio.',
            'Telefono.required' => 'El campo Telefono es obligatorio.',
            'Telefono.size' => 'El campo Telefono debe tener exactamente 10 caracteres.',
        ];
    }
}
