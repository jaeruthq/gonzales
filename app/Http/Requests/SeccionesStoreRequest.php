<?php

namespace torremall\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeccionesStoreRequest extends FormRequest
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
            //
            'capacidad' => 'max:3:ubicacions,capacidad',
        ];
    }
    public function messages()
    {
        return [
           'capacidad.max' => 'La capacidad debe mayor a 3 digitos',
           
           
        ];
    }
}
