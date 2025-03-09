<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJuezRequest extends FormRequest
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
            'DNI' => 'required|string|max:11', // Asegúrate de que sea único en la tabla jueces
            'ApelName' => 'required|string|max:80',
            'Correo' => 'required|email|max:60',
            'Telefono' => 'required|string|max:15',
        ];
    }

    public function messages()
    {
        return [
            'IdEvento.required' => 'El campo IdEvento es obligatorio.',
            'IdEvento.integer' => 'El campo IdEvento debe ser un número entero.',
            'DNI.required' => 'El campo DNI es obligatorio.',
            'DNI.unique' => 'El DNI ya está en uso.',
            'ApelName.required' => 'El campo ApelName es obligatorio.',
            'ApelName.max' => 'El campo ApelName no puede tener más de 80 caracteres.',
            'Correo.required' => 'El campo Correo es obligatorio.',
            'Correo.email' => 'El campo Correo debe ser una dirección de correo electrónico válida.',
            'Correo.max' => 'El campo Correo no puede tener más de 60 caracteres.',
            'Telefono.required' => 'El campo Telefono es obligatorio.',
            'Telefono.max' => 'El campo Telefono no puede tener más de 15 caracteres.',
            'EstadoJuez.required' => 'El campo EstadoJuez es obligatorio.',
            'EstadoJuez.size' => 'El campo EstadoJuez debe tener exactamente 1 carácter.',
            'EstadoJuez.in' => 'El campo EstadoJuez debe ser A (activo) o B (Dado de baja).',
        ];
    }
}
