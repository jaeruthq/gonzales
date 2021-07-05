<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;

class TipoVehiculo extends Model
{
    protected $fillable = [
        'nom','descripcion',
    ];

    public function vehiculo()
    {
        return $this->hasMany('torremall\Vehiculo','tipo_id','id');
    }
}
