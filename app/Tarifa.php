<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model
{
    protected $fillable = [
        'nom','horas','precio','fecha_reg',
        'descripcion'
    ];

    public function vehiculos()
    {
        return $this->hasMany('torremall\Vehiculo','tarifa_id','id');
    }
}
