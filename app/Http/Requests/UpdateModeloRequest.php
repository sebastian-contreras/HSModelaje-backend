<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModeloRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambia esto según tus necesidades de autorización
    }

    public function rules()
    {
        return [
            'IdModelo' => 'required|integer',
            'DNI' => 'required|string|max:11',
            'ApelName' => 'required|string|max:80',
            'FechaNacimiento' => 'required|date',
            'Sexo' => 'required|string|size:1|in:M,F,O', // Asumiendo que M = Masculino, F = Femenino
            'Telefono' => 'required|string|max:15',
            'Correo' => 'required|email|max:60',
        ];
    }

    public function messages()
    {
        return [
            'IdModelo.required' => 'El ID del modelo es obligatorio.',
            'DNI.required' => 'El DNI es obligatorio.',
            'ApelName.required' => 'El nombre y apellido es obligatorio.',
            'ApelName.max' => 'El nombre y apellido no puede exceder los 80 caracteres.',
            'FechaNacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'Sexo.required' => 'El sexo es obligatorio.',
            'Sexo.in' => 'El sexo debe ser M (Masculino) o F (Femenino).',
            'Telefono.max' => 'El teléfono no puede exceder los 15 caracteres.',
            'Correo.required' => 'El correo es obligatorio.',
            'Correo.email' => 'El correo debe ser una dirección de correo válida.',
        ];
    }
}
