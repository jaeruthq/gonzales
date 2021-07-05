<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $fillable = [
        'modulo', 'nombre', 'descripcion'
    ];

    public function user_permisos()
    {
        return $this->hasMany(UserPermiso::class, 'permiso_id');
    }
}
