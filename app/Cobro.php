<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;

class Cobro extends Model
{
    protected $fillable = [
        'salida_id','vehiculo_id','tarifa_id','tiempo_cobrado',
        'fecha_reg','hora','hora_ingreso','fecha_ingreso',
        'total','status'
    ];

    public function salida()
    {
        return $this->belongsTo('torremall\IngresoSalida','salida_id','id');
    }

    public function vehiculo()
    {
        return $this->belongsTo('torremall\Vehiculo','vehiculo_id','id');
    }

    public function tarifa()
    {
        return $this->belongsTo('torremall\Tarifa','tarifa_id','id');
    }

    public function factura()
    {
        return $this->hasOne('torremall\Factura','cobro_id','id');
    }
}
