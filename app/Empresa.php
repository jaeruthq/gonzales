<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = [
        'cod','nit','nro_aut','nro_emp',
        'name','alias','pais','dpto',
        'ciudad','zona','calle','nro',
        'email','fono','cel','fax',
        'casilla','web','logo','actividad_eco',
    ];
}
