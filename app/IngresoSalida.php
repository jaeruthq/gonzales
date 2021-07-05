<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;

class IngresoSalida extends Model
{
    protected $fillable = [
        'vehiculo_id','accion','hora','observacion','fecha_reg'
        ,'tipo','status'
    ];

    public function vehiculo()
    {
        return $this->belongsTo('torremall\Vehiculo','vehiculo_id','id');
    }

    public function cobro()
    {
        return $this->hasOne('torremall\Cobro','salida_id','id');
    }

    public function cobro_salida()
    {
        return $this->hasOne('torremall\Salida','salida_id','id');
    }
}
