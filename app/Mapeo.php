<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;

class Mapeo extends Model
{
    protected $fillable = [
        'nom','ubicacion_id','ocupado','vehiculo_id'
    ];

    public function ubicacion()
    {
        return $this->belongsTo('torremall\Ubicacion','ubicacion_id','id');
    }

    public function vehiculo()
    {
        return $this->belongsTo('torremall\Vehiculo','vehiculo_id','id');
    }
}
