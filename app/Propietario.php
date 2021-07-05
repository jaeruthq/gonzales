<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;

class Propietario extends Model
{
    protected $fillable = [
        'nom','apep','apem','ci',
        'ci_exp','dir','correo','fono',
        'cel','foto'
    ];

    public function vehiculos()
    {
        return $this->hasMany('torremall\Vehiculo','propietario_id','id');
    }
}
