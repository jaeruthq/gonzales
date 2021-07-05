<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use torremall\Empresa;
use torremall\IngresoSalida;
use torremall\Mapeo;
use torremall\NotificacionUsuario;
use torremall\Ubicacion;
use torremall\User;
use torremall\Vehiculo;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $empresa = Empresa::first();

        $usuarios = count(User::where('status',1)->get());

        $vehiculos_hoy = count(IngresoSalida::where('fecha_reg',date('Y-m-d'))
                                            ->where('accion','INGRESO')->get());
        
        $secciones = count(Ubicacion::where('status',1)->get());

        $disponibles = count(Mapeo::where('ocupado',0)->get());

        $vehiculos_total = count(Vehiculo::where('status',1)->get());

        $secciones_lista = Ubicacion::where('status',1)->get();

        if(Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR')
        {
            return view('home',compact('empresa','vehiculos_hoy','vehiculos_total','disponibles','secciones','usuarios','secciones_lista'));
        }
        else{
            $secciones = Ubicacion::where('status',1)->get();
            $array_secciones = [];

            foreach($secciones as $value)
            {
                $array_secciones[$value->id] = $value->nom;
            }
            return view('layouts.control',compact('empresa','array_secciones'));
        }
    }

    public function notificaciones(Request $request)
    {
        $num_noti = NotificacionUsuario::numNotificaciones(Auth::user()->id);
        $listaNotificacionesAdmin = NotificacionUsuario::listaNotificaciones(Auth::user()->id);

        return response()->JSON([
            'num_noti' => $num_noti,
            'listaNotificacionesAdmin' => $listaNotificacionesAdmin,
        ]);
    }
}
