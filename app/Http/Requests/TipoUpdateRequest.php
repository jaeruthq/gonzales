<?php

namespace torremall\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipoUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nom' => 'unique:tipos,nom,'.$this->tipo->id,
        ];
    }

    public function messages()
    {
        return [
            'nom.unique' => 'Este tipo de producto ya existe.',
        ];
    }
}
