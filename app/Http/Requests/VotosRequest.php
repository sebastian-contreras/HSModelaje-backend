<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VotosRequest extends FormRequest
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
            'IdParticipante' => 'required|integer',
            'IdJuez' => 'required|integer',
            'votos' => 'required|array',
            'votos.*.IdMetrica' => 'required|integer',
            'votos.*.Nota' => 'required|integer|min:0|max:10',
        ];
    }
}
