<?php

namespace torremall;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use torremall\Permiso;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'password', 'tipo', 'foto', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /* =================================================== 
                            RELACIONES
    ====================================================== */
    public function datosUsuario()
    {
        return $this->hasOne('torremall\DatosUsuario', 'user_id', 'id');
    }

    public function proveedores()
    {
        return $this->hasMany('torremall\Proveedor', 'user_id', 'id');
    }

    public function productos()
    {
        return $this->hasMany('torremall\Producto', 'user_id', 'id');
    }

    public function clientes()
    {
        return $this->hasMany('torremall\Cliente', 'user_id', 'id');
    }

    public function ventas()
    {
        return $this->hasMany('torremall\Venta', 'users_id', 'id');
    }

    public function ingresos()
    {
        return $this->hasMany('torremall\Ingreso', 'user_id', 'id');
    }

    public function salidas()
    {
        return $this->hasMany('torremall\Salida', 'user_id', 'id');
    }

    public function notificaciones()
    {
        return $this->hasMany('torremall\NotificacionUsuario', 'user_id', 'id');
    }

    public function logs()
    {
        return $this->hasMany(LogSeguimiento::class, 'user_id');
    }

    public function permisos()
    {
        return $this->hasMany(UserPermiso::class, 'user_id');
    }

    public function hasPermiso($permiso)
    {
        $existe_todo = Permiso::select('permisos.id')
            ->join('user_permisos', 'user_permisos.permiso_id', '=', 'permisos.id')
            ->where('user_permisos.user_id', Auth::user()->id)
            ->where('permisos.modulo', 'TODOS')
            ->get()->first();
        if ($existe_todo) {
            return true;
        }

        $existe = Permiso::select('permisos.id')
            ->join('user_permisos', 'user_permisos.permiso_id', '=', 'permisos.id')
            ->where('user_permisos.user_id', Auth::user()->id)
            ->where('permisos.nombre', $permiso)
            ->get()->first();

        if ($existe) {
            return true;
        }

        return false;
    }
}
