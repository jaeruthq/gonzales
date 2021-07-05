<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use torremall\Http\Requests\DatosUsuarioStoreRequest;
use torremall\Http\Requests\DatosUsuarioUpdateRequest;

use torremall\DatosUsuario;
use torremall\Empresa;
use torremall\User;
use torremall\LogSeguimiento;
use torremall\UserPermiso;

class DatosUsuarioController extends Controller
{
    public function index(Request $request)
    {
        $empresa = Empresa::first();
        if (Auth::user()->tipo == 'ADMINISTRADOR') {
            $usuarios = DatosUsuario::lista();
            if ($request->ajax()) {
                return response()->JSON(view('usuarios.parcial.lista', compact('usuarios'))->render());
            }
            return view('usuarios.index', compact('empresa', 'usuarios'));
        }
        return view('errors.sin_permiso', compact('empresa'));
    }

    public function create()
    {
        $empresa = Empresa::first();
        if (Auth::user()->tipo == 'ADMINISTRADOR') {
            return view('usuarios.create', compact('empresa'));
        }
        return view('errors.sin_permiso', compact('empresa'));
    }

    public function store(DatosUsuarioStoreRequest $request)
    {
        $d_u = new DatosUsuarioController();

        $cont = 0;
        do {
            $nombre_usuario = $d_u->nombreUsuario($request->nom, $request->apep);
            if ($cont > 0) {
                $nombre_usuario = $nombre_usuario . $cont;
            }
            $comprueba = User::where('name', $nombre_usuario)->get()->first();
            $cont++;
        } while ($comprueba);

        // CREANDO EL USUARIO
        $nuevo_usuario = new User();
        $nuevo_usuario->name = $nombre_usuario;
        $nuevo_usuario->password = Hash::make($request->ci);
        $nuevo_usuario->tipo = $request->tipo;
        $nuevo_usuario->foto = "user_default.png";
        $nuevo_usuario->status = 1;
        $nuevo_usuario->save();
        // CREANDO LOS DATOS DEL USUARIO
        $datosUsuario = new DatosUsuario(array_map('mb_strtoupper', $request->except('foto')));

        $nom_foto = 'user_default.png';
        if ($request->hasFile('foto')) {
            //obtener el archivo
            $file_foto = $request->file('foto');
            $extension = "." . $file_foto->getClientOriginalExtension();
            $nom_foto = $nombre_usuario . str_replace(' ', '_', $datosUsuario->nom) . time() . $extension;
            $file_foto->move(public_path() . "/imgs/personal/", $nom_foto);
        }

        //completar los campos foto y fecha registro del personal
        $datosUsuario->foto = $nom_foto;
        $nuevo_usuario->datosUsuario()->save($datosUsuario);

        UserPermiso::asignarPermisosUser($nuevo_usuario->id);

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'CREACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' REGISTRO UN NUEVO USUARIO',
            'modulo' => 'USUARIOS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return redirect()->route('users.edit', $datosUsuario->id)->with('success', 'success');
    }

    public function edit(DatosUsuario $datosUsuario)
    {
        $empresa = Empresa::first();
        if (Auth::user()->tipo == 'ADMINISTRADOR') {
            return view('usuarios.edit', compact('empresa', 'datosUsuario'));
        }
        return view('errors.sin_permiso', compact('empresa'));
    }

    public function update(DatosUsuarioUpdateRequest $request, DatosUsuario $datosUsuario)
    {
        $datosUsuario->update(array_map('mb_strtoupper', $request->except('foto')));
        if ($request->hasFile('foto')) {
            // ELIMINAR FOTO ANTIGUA
            $foto_antigua = $datosUsuario->foto;
            \File::delete(public_path() . "/imgs/personal/" . $foto_antigua);
            // SUBIR NUEVA FOTO
            $file_foto = $request->file('foto');
            $extension = "." . $file_foto->getClientOriginalExtension();
            $nom_foto = $datosUsuario->user->name . str_replace(' ', '_', $datosUsuario->nom) . time() . $extension;
            $file_foto->move(public_path() . "/imgs/personal/", $nom_foto);
            $datosUsuario->foto = $nom_foto;
            $datosUsuario->save();
        }

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'MODIFICACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' MODIFICÓ UN REGISTRO DE USUARIO',
            'modulo' => 'USUARIOS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return redirect()->route('users.edit', $datosUsuario->id)->with('success', 'success');
    }

    public function show(DatosUsuario $datosUsuario)
    {
    }

    public function destroy(User $user)
    {
        $user->status = 0;
        $user->save();

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'ELIMINACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' ELIMINÓ UN REGISTRO USUARIO',
            'modulo' => 'USUARIOS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return response()->JSON([
            'msg' => 'success',
        ]);
    }

    // FUNCIONES PARA CONFIGURAR LA CUENTA DEL USUARIO
    public function config_cuenta(User $user)
    {
        $empresa = Empresa::first();
        return view('usuarios.config', compact('empresa', 'user'));
    }

    public function cuenta_update(Request $request, User $user)
    {
        if ($request->oldPassword) {
            if (Hash::check($request->oldPassword, $user->password)) {
                if ($request->newPassword == $request->password_confirm) {
                    $user->password = Hash::make($request->newPassword);
                    $user->save();
                    return redirect()->route('users.config', $user->id)->with('password', 'exito');
                } else {
                    return redirect()->route('users.config', $user->id)->with('contra_error', 'comfirm');
                }
            } else {
                return redirect()->route('users.config', $user->id)->with('contra_error', 'old_password');
            }
        }
    }

    public function cuenta_update_foto(Request $request, User $user)
    {
        if ($request->ajax()) {
            if ($request->hasFile('foto')) {
                $archivo_img = $request->file('foto');
                $extension = '.' . $archivo_img->getClientOriginalExtension();
                $codigo = $user->name;
                $path = public_path() . '/imgs/users/' . $user->foto;
                if ($user->foto != 'user_default.png') {
                    \File::delete($path);
                }
                // SUBIENDO FOTO AL SERVIDOR
                if ($user->datosUsuario) {
                    $name_foto = $codigo . $user->datosUsuario->nom_u . time() . $extension; //determinar el nombre de la imagen y su extesion
                } else {
                    $name_foto = $codigo . time() . $extension; //determinar el nombre de la imagen y su extesion
                }
                $name_foto = str_replace(' ', '_', $name_foto);
                $archivo_img->move(public_path() . '/imgs/users/', $name_foto); //mover el archivo a la carpeta de destino

                $user->foto = $name_foto;
                $user->save();

                return response()->JSON([
                    'msg' => 'actualizado'
                ]);
            }
        }
    }

    private function nombreUsuario($nom, $apep)
    {
        //determinando el nombre de usuario inicial del 1er_nombre+apep+tipoUser
        $nombre_user = substr(mb_strtoupper($nom), 0, 1); //inicial 1er_nombre
        $nombre_user .= mb_strtoupper($apep);

        return $nombre_user;
    }
}
