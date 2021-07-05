<?php

namespace torremall\Http\Controllers;

use torremall\Http\Requests\ControlStoreRequest;
use torremall\Http\Requests\ControlUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use torremall\Empresa;
use torremall\User;
class UserController extends Controller
{
    public function index(Request $request)
    {
        $empresa = Empresa::first();
        $controles = User::where('status',1)
                        ->where('tipo','CONTROL')->get();
        if($request->ajax())
        {
            return response()->JSON(view('usuarios.parcial.lista',compact('controles'))->render()); 
        }
        else{
            if(Auth::user()->tipo == 'ADMINISTRADOR')
            {
                return view('usuarios.controles',compact('empresa','controles'));
            }
            else{
                return view('errors.sin_permiso',compact('empresa'));
            }
        }
    }

    public function control_create()
    {
        $empresa = Empresa::first();
        if(Auth::user()->tipo == 'ADMINISTRADOR')
        {                                    
            return view('usuarios.control_create',compact('empresa'));
        }
        else{
            return view('errors.sin_permiso',compact('empresa'));
        }
    }

    public function control_store(ControlStoreRequest $request)
    {
        $empresa = Empresa::first();
        if($request->password == $request->password_confirm)
        {
            $user = new User();
            $user->name = mb_strtoupper($request->name);
            $user->foto = 'user_default.png';
            $user->status = 1;
            $user->password = Hash::make($request->password);
            $user->tipo = 'CONTROL';
            $user->save();
    
            return redirect()->route('controles.edit',$user->id)->with('registrado','exito');
        }
        else{
            return view('usuarios.control_create',compact('empresa','request'))->with('error','Las contraseñas no coinciden.');
        }
    }

    public function edit(User $user)
    {
        $empresa = Empresa::first();
        if(Auth::user()->tipo == 'ADMINISTRADOR')
        {
            return view('usuarios.control_edit',compact('user','empresa'));
        }
        else{
            return view('errors.sin_permiso',compact('empresa'));
        }
    }

    public function control_update(User $user, ControlUpdateRequest $request)
    {
        if($request->password == '' && $request->password_confirm == '')
        {
            $user->name = mb_strtoupper($request->name);
            $user->save();
            return redirect()->route('controles.edit',$user->id)->with('editado','exito');
        }
        elseif($request->password != '' && $request->password_confirm != ''){
            if($request->password == $request->password_confirm)
            {
                $user->password = Hash::make($request->password);
                $user->save();
                return redirect()->route('controles.edit',$user->id)->with('editado','exito');
            }       
            else{
                return redirect()->route('controles.edit',$user->id)->with('error','Las contraseñas no coinciden.');
            }     
        }
        else{
            return redirect()->route('controles.edit',$user->id)->with('error','Debes completar todos los campos.');
        }
    }
}
