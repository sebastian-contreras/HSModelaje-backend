<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGastoRequest extends FormRequest
{
    public function authorize()
    {
        // Permitir que todos los usuarios realicen esta solicitud
        return true;
    }

    public function rules()
    {
        return [
            'IdEvento' => 'required|integer',
            'Gasto' => 'required|string|max:100',
            'Personal' => 'required|string|max:100',
            'Monto' => 'required|numeric|between:0,999999999999999.99',
            'Comprobante' => 'nullable|string|max:400',
        ];
    }

    public function messages()
    {
        return [
            'IdEvento.required' => 'El campo IdEvento es obligatorio.',
            'IdEvento.integer' => 'El campo IdEvento debe ser un número entero.',
            'Gasto.required' => 'El campo Gasto es obligatorio.',
            'Gasto.string' => 'El campo Gasto debe ser una cadena de texto.',
            'Gasto.max' => 'El campo Gasto no puede tener más de 100 caracteres.',
            'Personal.required' => 'El campo Personal es obligatorio.',
            'Personal.string' => 'El campo Personal debe ser una cadena de texto.',
            'Personal.max' => 'El campo Personal no puede tener más de 100 caracteres.',
            'Monto.required' => 'El campo Monto es obligatorio.',
            'Monto.numeric' => 'El campo Monto debe ser un número.',
            'Monto.between' => 'El campo Monto debe estar entre 0 y 999999999999999.99.',
            'Comprobante.string' => 'El campo Comprobante debe ser una cadena de texto.',
            'Comprobante.max' => 'El campo Comprobante no puede tener más de 400 caracteres.',
        ];
    }
}
