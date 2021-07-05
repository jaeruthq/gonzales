<?php

namespace torremall\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatosUsuarioUpdateRequest extends FormRequest
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
            
            'nom' => 'min:3|max:20|regex:/^[\pL\s]+$/u|required:datos_usuarios,nom,'.$this->datosUsuario->id,
          //  'nom' => 'min:3|max:20|alpha:datos_usuarios,nom,'.$this->datosUsuario->id,
            'apep' => 'min:3|max:20|alpha:datos_usuarios,apep,'.$this->datosUsuario->id,
            'apem' => 'nullable|max:20|alpha:datos_usuarios,apem,'.$this->datosUsuario->id,
            'ci' => 'min:7|max:10|unique:datos_usuarios,ci,'.$this->datosUsuario->id,
            'fono' => 'nullable|max:8:datos_usuarios,fono,'.$this->datosUsuario->id,
            'cel' => 'min:8|max:8:datos_usuarios,cel,'.$this->datosUsuario->id,
        ];
    }
     public function messages()
    {
        return [
           // 'name.unique' => 'Ese nombre de usuario no esta disponible.',
            'nom.regex' => 'El nombre del usuario solo debe contener letras',
           // 'nom.alpha' => 'El nombre del usuario solo debe contener letras, no numeros',
           'nom.min' => 'El nombre de usuario debe ser mas de 3 letras',
           'nom.max' => 'El nombre de usuario debe ser maximo de 20 letras',
           'apep.alpha' => 'El nombre del usuario solo debe contener letras, no numeros',
           'apep.min' => 'El nombre de usuario debe ser mas de 3 letras',
           'apep.max' => 'El nombre de usuario debe ser maximo de 20 letras',
           'apem.alpha' => 'El nombre del usuario solo debe contener letras, no numeros',
           'apem.min' => 'El nombre de usuario debe ser mas de 3 letras',
           'apem.max' => 'El nombre de usuario debe ser maximo de 20 letras',
           'ci.min' => 'El ci debe ser de 7 digitos',
           'ci.max' => 'El ci debe ser como maximo de 10 digitos',
           'fono.min' => 'El telefono debe ser de 0 digitos',
           'fono.max' => 'El telefono  debe ser como maximo de 8 digitos',
           'cel.min' => 'El numero de celular debe ser de 8 digitos',
           'cel.max' => 'El numero de celular debe ser como maximo de  8digitos',
        ];
    }
}
