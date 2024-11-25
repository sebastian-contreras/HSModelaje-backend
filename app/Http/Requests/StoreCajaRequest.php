<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCajaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'NumeroCaja' => 'nullable|integer|min:1', // Debe ser un número entero positivo
            'Tamaño' => 'nullable|string|max:4', // Tamaño con un máximo de 4 caracteres
            'Ubicacion' => 'nullable|string|max:250', // Ubicación con un máximo de 250 caracteres
            'Fila' => 'nullable|integer|min:0', // Debe ser un número entero no negativo
            'Columna' => 'nullable|integer|min:0', // Debe ser un número entero no negativo
            'Observaciones' => 'nullable|string', // Sin límite de caracteres
            'EstadoCaja' => 'nullable|in:A,I,N,B', // Debe ser uno de los valores permitidos
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     */
    public function messages()
    {
        return [
            'NumeroCaja.integer' => 'El número de caja debe ser un número entero.',
            'NumeroCaja.min' => 'El número de caja debe ser al menos 1.',
            'Tamaño.max' => 'El tamaño no puede exceder los 4 caracteres.',
            'Ubicacion.max' => 'La ubicación no puede exceder los 250 caracteres.',
            'Fila.integer' => 'La fila debe ser un número entero.',
            'Fila.min' => 'La fila no puede ser negativa.',
            'Columna.integer' => 'La columna debe ser un número entero.',
            'Columna.min' => 'La columna no puede ser negativa.',
            'EstadoCaja.in' => 'El estado de la caja debe ser uno de los siguientes: A, I, N, B.',
        ];
    }
}
