<?php

namespace torremall\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropietarioStoreRequest extends FormRequest
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
            'nom' => 'min:3|max:20|regex:/^[\pL\s]+$/u|required:propietarios,nom',
            'apep' => 'min:3|max:20|alpha:propietarios,apep',
            'apem' => 'nullable|max:20|alpha:propietarios,apem',
            'ci' => 'min:7|max:10|unique:propietarios,ci',
           // 'correo' => 'email:propietarios,correo',
           'fono' => 'nullable|max:8:propietarios,fono',
            'cel' => 'min:8|max:8:propietarios,cel',

        ];
    }
     public function messages()
    {
        return [
            
            'nom.regex'=>'El nombre solo debe contener letras, no numeros.',
            'nom.min'=>'El nombre debe contener al menos 3 letras',
            'nom.max'=>'El nombre debe contener mas 20 letras',
            'apep.alpha'=>'El apellido paterno solo debe contener letras, no numeros.',
            'apep.min'=>'El apellido paterno debe contener al menos 3 letras',
            'apep.max'=>'El nombre debe contener mas 20 letras',
            'apem.alpha'=>'El apellido materno solo debe contener letras, no numeros.',
            'apem.max'=>'El nombre debe contener mas 20 letras',
            'ci.min'=>'los digitos de ci debe der como 7 digitos ',
            'ci.unique'=>'Este CI  ya esta siendo utilizado ',
            'ci.min' => 'El ci debe ser de 7 digitos',
           'ci.max' => 'El ci debe ser como maximo de 10 digitos',
           'ci.unique' => 'El ci ya esta siendo utilizado.',
           //'fono.min' => 'El telefono debe ser de 0 digitos',
           'fono.max' => 'El telefono debe ser como maximo de 8 digitos',
           'cel.min' => 'El numero debe ser de 8 digitos',
           'cel.max' => 'El numero debe ser como maximo de  8digitos',
        ];
    }

    public function attributes()
    {
        return [
            'nom' => 'El nombre',
        ];
    }
}
