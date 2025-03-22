<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntradaPasarelaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'IdZona' => 'required|integer',
            'Apelname' => 'required|string|max:100',
            'DNI' => 'required|string|max:11',
            'Correo' => 'required|email|max:100',
            'Cantidad' => 'required|integer|gt:0',
            'Telefono' => 'required|string|max:15',
            'Archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'IdZona.required' => 'El campo ID de Zona es obligatorio.',
            'IdZona.integer' => 'El campo ID de Zona debe ser un número entero.',
            'Apelname.required' => 'El campo Apellido y Nombre es obligatorio.',
            'Apelname.string' => 'El campo Apellido y Nombre debe ser una cadena de texto.',
            'Apelname.max' => 'El campo Apellido y Nombre no debe superar los 100 caracteres.',
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
            'Cantidad.required' => 'El campo Cantidad es obligatorio.',
            'Cantidad.integer' => 'El campo Cantidad debe ser un número entero.',
            'Cantidad.gt' => 'El campo Cantidad debe ser mayor que 0.',
        ];
    }
}
