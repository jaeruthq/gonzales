<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;
use torremall\User;
use torremall\UserPermiso;
use torremall\Permiso;
use Illuminate\Support\Facades\DB;

class UserPermisoController extends Controller
{
    public function index(User $user)
    {
        $modulos = [
            'USUARIOS',
            'TARIFÁS',
            'PROPIETARIOS',
            'VEHÍCULOS',
            'TIPO DE VEHÍCULOS',
            'SECCIONES',
            'COBROS',
            'INGRESOS Y SALIDAS',
            'REPORTES',
            'EMPRESA',
            'USUARIOS PERMISOS',
        ];

        $cont = 0;
        $html = '<div class="row">';
        for ($i = 0; $i < count($modulos); $i++) {
            $permisos = Permiso::where('modulo', $modulos[$i])->get();
            $html .= '<div class="col-md-3"><div class="elemento">
                        <div class="titulo">' . $modulos[$i] . '</div>';
            foreach ($permisos as $permiso) {
                $clase = 'sin_asignar';
                $existe = UserPermiso::where('user_id', $user->id)
                    ->where('permiso_id', $permiso->id)
                    ->get()
                    ->first();
                if ($existe) {
                    $clase = 'asignado';
                    $html .= '<div class="permiso ' . $clase . '" data-up="' . $existe->id . '" data-url="' . route('user_permisos.destroy', $existe->id) . '">' . $permiso->descripcion . '</div>';
                } else {
                    $html .= '<div class="permiso ' . $clase . '" data-permiso="' . $permiso->id . '">' . $permiso->descripcion . '</div>';
                }
            }
            $html .= '</div></div>';
            $cont++;
            if ($cont == 4) {
                $html .= '</div>';
                $html .= '<div class="row">';
                $cont = 0;
            }
        }
        $html .= '</div>';

        return view('usuarios.user_permisos', compact('user', 'html'));
    }

    public function store(User $user, Request $request)
    {
        $nuevo_permiso = UserPermiso::create([
            'user_id' => $user->id,
            'permiso_id' => $request->permiso_id
        ]);

        return response()->JSON([
            'sw' => true,
            'id' => $nuevo_permiso->id
        ]);
    }

    public function destroy(UserPermiso $user_permiso)
    {
        $id = $user_permiso->permiso_id;
        $user_permiso->delete();
        return response()->JSON([
            'sw' => true,
            'id' => $id
        ]);
    }

    public function permisos_usuarios_faltantes()
    {
        $usuarios = DB::select("SELECT u.id FROM datos_usuarios du JOIN users u ON du.user_id = u.id
                    WHERE NOT EXISTS (SELECT * FROM user_permisos WHERE user_permisos.user_id = u.id)");

        foreach ($usuarios as $user) {
            UserPermiso::asignarPermisosUser($user->id);
        }

        return redirect()->route('users.index')->with('registrado', 'exito');
    }
}
