<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEstablecimientoRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambia esto según tu lógica de autorización
    }

    public function rules()
    {
        return [
            'IdEstablecimiento' => 'required|integer',
            'Establecimiento' => 'required|string|max:60',
            'Ubicacion' => 'required|string',
            'Capacidad' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'IdEstablecimiento.required' => 'El ID del establecimiento es obligatorio.',
            'Establecimiento.required' => 'El nombre del establecimiento es obligatorio.',
            'Ubicacion.required' => 'La ubicación es obligatoria.',
            'Capacidad.required' => 'La capacidad es obligatoria.',
        ];
    }
}
