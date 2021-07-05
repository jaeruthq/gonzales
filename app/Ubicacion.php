<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    protected $fillable = [
        'nom','capacidad','descripcion','status',
    ];

    public function mapeo()
    {
        return $this->hasMany('torremall\Mapeo','ubicacion_id','id');
    }
}
