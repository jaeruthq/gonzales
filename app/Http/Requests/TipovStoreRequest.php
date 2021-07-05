<?php

namespace torremall\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipovStoreRequest extends FormRequest
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
            'nom' => 'min:4|max:30|regex:/^[\pL\s]+$/u|required:tipo_vehiculos,nom',
            'descripcion' => 'nullable|max:50:tipo_vehiculos,nom',
        ];
    }

    public function messages()
    {
        return [
            'nom.regex'=>'El nombre del tipo de vehiculo solo debe contener letras, no numeros.',
            'nom.min'=>'El nombre del tipo de vehiculo debe contener al menos 5 letras',
            'nom.max'=>'El nombre del tipo de vehiculo debe contener  30 letras',
            'descripcion.max'=>'La descripcion de vehiculo debe contener  50 letras',
        ];
    }
}
