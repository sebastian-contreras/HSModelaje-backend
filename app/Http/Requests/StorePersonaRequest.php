<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonaRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'CUIT' => 'required|string|unique:personas',
            'Apellido' => 'string',
            'Nombre' => 'string',
            'Nacionalidad' => 'string',
            'Actividad' => 'string',
            'Domicilio' => 'string',
            'Email' => 'email',
            'Telefono' => 'string',
            'Movil' => 'string',
            'SituacionFiscal' => 'string',
            'FNacimiento' => 'date',
            'DNI' => 'string',
            'Alias' => 'string',
            'CodPostal' => 'string',
            'PEP' => 'string',
            'EstadoPersona' => 'in:A,I', // Validación para EstadoPersona
        ];

    }
}
