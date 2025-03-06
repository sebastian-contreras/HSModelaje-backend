<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'IdEvento' => 'required',
            'Evento' => 'required|string|max:150',
            'FechaProbableInicio' => 'required|date',
            'FechaProbableFinal' => 'required|date',
            'Votacion ' => 'nullable',
            'IdEstablecimiento' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'IdEvento.required' => 'El evento es obligatorio.',
            'Evento.required' => 'El nombre del evento es obligatorio.',
            'FechaProbableInicio.required' => 'La fecha probable de inicio es obligatoria.',
            'FechaProbableFinal.required' => 'La fecha probable de final es obligatoria.',
            'Votacion.required' => 'La votacion es obligatoria.',
            'IdEstablecimiento.required' => 'Es establecimiento es obligatoria.',
        ];
    }
}
