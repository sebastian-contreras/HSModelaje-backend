<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreParticipanteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'IdEvento' => 'required|integer', // Asegúrate de que exista en la tabla eventos
            'IdModelo' => 'required|integer', // Asegúrate de que exista en la tabla modelos
            'Promotor' => 'nullable|string|max:100', // Asegúrate de que sea único en la tabla jueces
        ];
    }
    public function messages()
    {
        return [
            'IdEvento.required' => 'El campo Evento es obligatorio.',
            'IdEvento.integer' => 'El campo Evento debe ser un número entero.',
            'IdModelo.required' => 'El campo Modelo es obligatorio.',
            'IdModelo.integer' => 'El campo Modelo debe ser un número entero.',
        ];
    }
}
