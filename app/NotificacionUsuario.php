<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotificacionUsuario extends Model
{
    protected $fillable = [
        'ingresoSalida_id','hora','fecha','user_id',
        'visto',
    ];

    public function user()
    {
        return $this->belongsTo('torremall\User','user_id','id');
    }

    // LISTA DE NOTIFICACIONES
    public static function numNotificaciones($user_id){
        return DB::select("SELECT COUNT(*) AS num_notis FROM notificacion_usuarios
                        WHERE user_id = $user_id
                        AND visto = 0")[0];
    }

    public static function listaNotificaciones($user_id){
        return DB::select("SELECT n.id as noti_id, n.fecha, n.hora, n.visto, n.created_at, i.accion, i.id, v.nom as vehiculo FROM notificacion_usuarios n
                        JOIN ingreso_salidas i on i.id = n.ingresoSalida_id
                        JOIN vehiculos v on v.id = i.vehiculo_id
                        WHERE n.user_id = $user_id
                        ORDER BY n.created_at DESC");
    }
}
