<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules()
    {
        return [
            'Evento' => 'required|string|max:150',
            'FechaProbableInicio' => 'required|date',
            'FechaProbableFinal' => 'required|date',
            'Votacion ' => 'required|char',
            'IdEstablecimiento' => 'required|Number',
        ];
    }

    public function messages()
    {
        return [
            'Evento.required' => 'El nombre del evento es obligatorio.',
            'FechaProbableInicio.required' => 'La fecha probable de inicio es obligatoria.',
            'FechaProbableFinal.required' => 'La fecha probable de final es obligatoria.',
            'Votacion.required' => 'La votacion es obligatoria.',
            'IdEstablecimiento.required' => 'Es establecimiento es obligatoria.',
        ];
    }
}
