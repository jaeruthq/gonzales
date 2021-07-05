<?php

namespace torremall\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehiculoUpdateRequest extends FormRequest
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
            'nom' => 'min:5|max:20|regex:/^[\pL\s]+$/u|required:vehiculos,nom,'.$this->vehiculo->id,
            'placa' => 'min:7|max:8|alpha_num|required|unique:vehiculos,placa,'.$this->vehiculo->id,
            'marca' => 'nullable|max:20:vehiculos,marca,'.$this->vehiculo->id,
            'modelo' => 'nullable|max:20:vehiculos,modelo,'.$this->vehiculo->id,
            'rfid' => 'max:30|unique:vehiculos,rfid,'.$this->vehiculo->id,
        ];
    }

    public function messages()
    {
        return [
            'nom.regex'=>'El nombre solo debe contener letras, no numeros.',
             'nom.min'=>'El nombre debe contener al menos 5 letras',
            'nom.max'=>'El nombre debe contener mas 20 letras',
            'placa.min'=>'La placa debe debe contener al menos 7 numero y letras',
            'placa.max'=>'La placa debe contener mas 8 numero y letras',
            'placa.alpha_num'=>'El apellido materno solo debe contener numero y letras.',
            'placa.unique'=>'Esta placa ya esta siendo utilizado ',
            'marca.max'=>'La marca del vehiculo debe contener 20 letras',
            'modelo.max'=>'La modelo del vehiculo debe contener 20 letras',
            'rfid.unique' => 'Ese codigo RFID ya esta siendo utilizado.',
            'rfid.max'=>'El nombre debe contener maximo 30 digitos ',
        ];
    }
}
