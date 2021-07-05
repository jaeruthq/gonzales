<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use torremall\Empresa;
use torremall\Ubicacion;
use torremall\Mapeo;
use torremall\Vehiculo;
use torremall\LogSeguimiento;

class MapeoController extends Controller
{
    public function index(Ubicacion $seccion, Request $request)
    {
        $empresa = Empresa::first();
        $mapeo = Mapeo::where('ubicacion_id', $seccion->id)
            ->get();

        if ($request->ajax()) {
            return response()->JSON(view('secciones.parcial.mapeo', compact('seccion', 'mapeo'))->render());
        }

        if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
            return view('secciones.mapeo', compact('empresa', 'seccion', 'mapeo'));
        }
        return view('errors.sin_permiso', compact('empresa'));
    }

    public function acciones(Request $request)
    {
        $ac = $request->accion;
        $seccion = Ubicacion::find($request->seccion);
        switch ($ac) {
            case 'guardar':
                $nom = $request->nom;
                $mapeo = new Mapeo([
                    'nom' =>  mb_strtoupper($nom),
                    'ubicacion_id' => $seccion->id
                ]);
                $seccion->capacidad = $seccion->capacidad + 1;
                $seccion->save();
                $seccion->mapeo()->save($mapeo);
                LogSeguimiento::create([
                    'user_id' => Auth::user()->id,
                    'accion' => 'CREACIÓN',
                    'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' REGISTRO UNA UBICACIÓN',
                    'modulo' => 'SECCIONES/MAPEOS',
                    'fecha' => date('Y-m-d'),
                    'hora' => date('H:i:s')
                ]);
                break;
            case 'modificar':
                $mapeo = Mapeo::find($request->id);
                $mapeo->nom = mb_strtoupper($request->nom);
                $mapeo->save();
                LogSeguimiento::create([
                    'user_id' => Auth::user()->id,
                    'accion' => 'MODIFICACIÓN',
                    'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' MODIFICÓ UNA UBICACIÓN',
                    'modulo' => 'SECCIONES/MAPEOS',
                    'fecha' => date('Y-m-d'),
                    'hora' => date('H:i:s')
                ]);
                break;
            case 'eliminar':
                $seccion->capacidad = $seccion->capacidad - 1;
                $seccion->save();
                $mapeo = Mapeo::find($request->id);
                $mapeo->delete();
                LogSeguimiento::create([
                    'user_id' => Auth::user()->id,
                    'accion' => 'ELIMINACIÓN',
                    'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' ELIMINÓ UNA UBICACIÓN',
                    'modulo' => 'SECCIONES/MAPEOS',
                    'fecha' => date('Y-m-d'),
                    'hora' => date('H:i:s')
                ]);
                break;
        }

        return response()->JSON(true);
    }

    public function obtener_mapeo(Request $request)
    {
        $mapeos = Mapeo::where('ubicacion_id', $request->seccion)->get();
        $mapeo = '';
        foreach ($mapeos as $value) {
            $ocupado = 'no';
            $txt_ocupado = 'Disponible';
            $seleccionado = '';
            if ($value->ocupado == 1) {
                $ocupado = 'si';
                $txt_ocupado = 'Ocupado';
            }
            if ($value->id == $request->sw) {
                $ocupado = 'seleccionado';
                $txt_ocupado = 'Posición actual';
            }
            $mapeo .= '  <div class="elemento ' . $ocupado . '" data-toggle="tooltip" title="' . $txt_ocupado . '">
                        <input type="text" class="id" value="' . $value->id . '" readonly hidden>
                        <input type="text" class="nom" value="' . $value->nom . '" readonly hidden>
                        <div class="nombre">
                            ' . $value->nom . '
                        </div>
                    </div>';
        }
        return response()->JSON($mapeo);
    }

    public function obtieneUbicacionVehiculo(Request $request)
    {
        $_rfid = $request->rfid;
        $vehiculo = Vehiculo::where('rfid', $_rfid)->get()->first();
        if ($vehiculo) {
            $mapeo = Mapeo::where('ocupado', 1)
                ->where('vehiculo_id', $vehiculo->id)
                ->get()->first();
            if ($mapeo) {
                return response()->JSON([
                    'sw' => true,
                    'mapeo' => $mapeo->nom,
                    'seccion' => $mapeo->ubicacion->nom,
                ]);
            }
        }
        return response()->JSON([
            'sw' => false,
            'seccion' => '',
            'mapeo' => '',
        ]);
    }
}
