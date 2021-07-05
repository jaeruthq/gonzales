<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;

class Pendiente extends Model
{
    protected $fillable = [
        'rfid','registrado'
    ];
}
