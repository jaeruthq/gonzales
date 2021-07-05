<?php

namespace torremall\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TarifaUpdateRequest extends FormRequest
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
            'nom' => 'min:5|max:15|alpha_num:tarifas,nom,'.$this->tarifa->id,
            'horas' => 'min:1|max:4:tarifas,horas,'.$this->tarifa->id,
            'precio' => 'regex:/^\d{1,4}(\.\d{1,2})?$/|required:tarifas,precio,'.$this->tarifa->id,
          // 'precio' => 'regex:/^\d{1,4}(?:\.\d\d\d)*,\d\d$/|required:tarifas,precio,'.$this->tarifa->id,
            'descripcion' => 'nullable|max:50:tarifas,descripcion,'.$this->tarifa->id,

        ];
    }

    public function messages()
    {
        return [
           // 'name.unique' => 'Ese nombre de usuario no esta disponible.',
           'nom.alpha_num' => 'El nombre de la tarifa solo debe contener letra y numero',
           'nom.min' => 'El nombre de la tarifa debe ser minino de 5 letra y numero',
           'nom.max' => 'El nombre de la tarifa debe ser maximo de 15 letra y numero',
           'horas.min' => 'La hora debe ser minimo de 1 digito',
           'horas.max' => 'La hora debe ser maximo de 4 digitos',
           //'horas.numbers' => 'La hora debe ser de solo numeros enteros',

           'precio.regex' => 'el precio puede ser de 1 a 4 digitos y puede tener 2 decimales separado con punto(.)',         
          // 'precio.min' => 'El precio debe ser minimo de 1 digito',
           //'precio.max' => 'El precio debe ser maximo de 6 digitos',
            'descripcion.max'=>'La descripcion de vehiculo debe contener 50 letras',
           
        ];
    }
}
