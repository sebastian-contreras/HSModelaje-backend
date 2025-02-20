<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'Username' => 'required|string|max:20',
            'Apellidos' => 'required|string|max:30',
            'Nombres' => 'required|string|max:30',
            'FechaNacimiento' => 'required|date',
            'Telefono' => 'required|string|max:15',
            'Email' => 'required|email|max:60',
            'Contrasena' => 'required|string',
            'Rol' => 'required|string|size:1',
        ];
    }
}
