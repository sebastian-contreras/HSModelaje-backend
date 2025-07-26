<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContrasenaRequest extends FormRequest
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
            //
            'Contrasena' => 'required|string|min:6',
            'ContrasenaActual' => 'required|string',
            'ContrasenaConfirmacion' => 'required|string|same:Contrasena',
        ];
    }
    public function messages(): array
    {
        return [
            'Contrasena.required' => 'La contraseña es obligatoria.',
            'Contrasena.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'ContrasenaActual.required' => 'La contraseña actual es obligatoria.',
            'ContrasenaConfirmacion.required' => 'La confirmación de la contraseña es obligatoria.',
            'ContrasenaConfirmacion.same' => 'Las contraseñas no coinciden.',
        ];
    }
}
