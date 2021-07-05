<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $fillable = [
        'placa','marca','modelo','nom',
        'tipo_id','propietario_id','tarifa_id','foto',
        'rfid','descripcion','fecha_reg','status'
    ];

    public function propietario()
    {
        return $this->belongsTo('torremall\Propietario','propietario_id','id');
    }

    public function tarifa()
    {
        return $this->belongsTo('torremall\Tarifa','tarifa_id','id');
    }

    public function tipo()
    {
        return $this->belongsTo('torremall\TipoVehiculo','tipo_id','id');
    }

    public function ingresoSalida()
    {
        return $this->hasMany('torremall\IngresoSalida','vehiculo_id','id');
    }

    public function mapeo()
    {
        return $this->hasOne('torremall\Mapeo','vehiculo_id','id');
    }
}
