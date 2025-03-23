<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEntradaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'IdEntrada' => 'required|integer',
            'IdZona' => 'required|integer',
            'ApelName' => 'required|string|max:100',
            'DNI' => 'required|string|max:11',
            'Correo' => 'required|email|max:100',
            'Telefono' => 'required|string|max:15',
            'Comprobante' => 'required|string|max:400',
        ];
    }

    public function messages(): array
    {
        return [
            'IdEntrada.required' => 'El campo ID de Entrada es obligatorio.',
            'IdEntrada.integer' => 'El campo ID de Entrada debe ser un número entero.',
            'IdZona.required' => 'El campo ID de Zona es obligatorio.',
            'IdZona.integer' => 'El campo ID de Zona debe ser un número entero.',
            'ApelName.required' => 'El campo Apellido y Nombre es obligatorio.',
            'ApelName.string' => 'El campo Apellido y Nombre debe ser una cadena de texto.',
            'ApelName.max' => 'El campo Apellido y Nombre no debe superar los 100 caracteres.',
            'DNI.required' => 'El campo DNI es obligatorio.',
            'DNI.string' => 'El campo DNI debe ser una cadena de texto.',
            'DNI.max' => 'El campo DNI no debe superar los 11 caracteres.',
            'Correo.required' => 'El campo Correo es obligatorio.',
            'Correo.email' => 'El campo Correo debe ser una dirección de correo válida.',
            'Correo.max' => 'El campo Correo no debe superar los 100 caracteres.',
            'Telefono.required' => 'El campo Teléfono es obligatorio.',
            'Telefono.string' => 'El campo Teléfono debe ser una cadena de texto.',
            'Telefono.max' => 'El campo Teléfono no debe superar los 15 caracteres.',
            'Comprobante.required' => 'El campo Comprobante es obligatorio.',
            'Comprobante.string' => 'El campo Comprobante debe ser una cadena de texto.',
            'Comprobante.max' => 'El campo Comprobante no debe superar los 400 caracteres.',
        ];
    }
}
