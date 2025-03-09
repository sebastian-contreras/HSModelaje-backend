<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateZonaRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambia esto si necesitas autorización
    }

    public function rules()
    {
        return [
            'IdZona' => 'required|integer',
            'Zona' => 'required|string|max:100',
            'Capacidad' => 'required|integer',
            'AccesoDisc' => 'required|string|size:1',
            'Precio' => 'required|numeric|between:0,999999999999999.99',
            'Detalle' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'IdZona.required' => 'El campo Id Zona es obligatorio.',
            'Zona.required' => 'El campo Zona es obligatorio.',
            'Capacidad.required' => 'El campo Capacidad es obligatorio.',
            'AccesoDisc.required' => 'El campo Acceso Discapacitados es obligatorio.',
            'Precio.required' => 'El campo Precio es obligatorio.',
            'Detalle.string' => 'El campo Detalle debe ser un texto.',
        ];
    }
}
