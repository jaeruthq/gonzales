<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    protected $fillable = [
        'salida_id','cobrado'
    ];

    public function salida()
    {
        return $this->belongsTo('torremall\IngresoSalida','salida_id','id');
    }
}
