<?php

namespace torremall\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TarifaStoreRequest extends FormRequest
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
            'nom' => 'min:5|max:15|alpha_num:tarifas,nom',
            'horas' => 'min:1|max:6:tarifas,horas',
            //'precio' => 'min:1|max:6:tarifas,precio',
             'precio' => 'regex:/^\d{1,4}(\.\d{1,2})?$/|required:tarifas,precio',
             'descripcion' => 'nullable|max:50:tarifas,descripcion',

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
