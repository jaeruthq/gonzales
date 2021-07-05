<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;
use torremall\User;
use torremall\Permiso;

class UserPermiso extends Model
{
    protected $fillable = [
        'user_id', 'permiso_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'permiso_id');
    }

    public static function asignarPermisosUser($id)
    {
        $permisos_admin = [
            'USUARIOS',
            'TARIFAS',
            'PROPIETARIOS',
            'VEHICULOS',
            'TIPO DE VEHICULOS',
            'SECCIONES',
            'COBROS',
            'INGRESOS Y SALIDAS',
            'REPORTES',
            'EMPRESA',
            'USUARIOS PERMISOS',
        ];

        $permisos_aux = [
            'PROPIETARIOS',
            'VEHICULOS',
            'TIPO DE VEHICULOS',
            'SECCIONES',
            'COBROS',
            'INGRESOS Y SALIDAS',
            'REPORTES',
        ];


        $user = User::find($id);
        if ($user->tipo == 'ADMINISTRADOR') {
            for ($i = 0; $i < count($permisos_admin); $i++) {
                $permisos = Permiso::where('modulo', $permisos_admin[$i])->get();
                foreach ($permisos as $permiso) {
                    if ($permiso->nombre != 'cobros.create')
                        UserPermiso::create([
                            'user_id' => $user->id,
                            'permiso_id' => $permiso->id
                        ]);
                }
            }
        } else {
            for ($i = 0; $i < count($permisos_aux); $i++) {
                $permisos = Permiso::where('modulo', $permisos_aux[$i])->get();
                foreach ($permisos as $permiso) {
                    if ($permiso->nombre != 'cobros.create')
                        UserPermiso::create([
                            'user_id' => $user->id,
                            'permiso_id' => $permiso->id
                        ]);
                }
            }
        }

        return true;
    }
}
