<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use torremall\Http\Requests\SeccionesStoreRequest;

use torremall\Empresa;
use torremall\Ubicacion;
use torremall\Mapeo;
use torremall\LogSeguimiento;

class UbicacionController extends Controller
{
    public function index(Request $request)
    {
        $empresa = Empresa::first();
        $secciones = Ubicacion::where('status', 1)->get();
        if ($request->ajax()) {
            return response()->JSON(view('secciones.parcial.lista', compact('secciones'))->render());
        } else {
            if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
                return view('secciones.index', compact('empresa', 'secciones'));
            } else {
                return view('errors.sin_permiso', compact('empresa'));
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $empresa = Empresa::first();
        if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
            return view('secciones.create', compact('empresa'));
        } else {
            return view('errors.sin_permiso', compact('empresa'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SeccionesStoreRequest$request)
    {
        $seccion = new Ubicacion(array_map('mb_strtoupper', $request->all()));
        $seccion->status = 1;
        $seccion->save();

        $capacidad = $request->capacidad;
        // DESPUES DE CREAR LA SECCION - UBICACION CREAR EL MAPEO
        // SE USARA UN ARRAY DEL ABCEDEARIO PARA ASIGNAR LA POSICION
        $array_az = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $cont = 0;
        $letra = 0;
        $numero = 1;
        $sw = 0;
        while ($cont < $capacidad) {
            if ($letra == 26) {
                $sw = 1;
                $letra = 0;
            }

            if ($sw == 0) {
                $mapeo  = new Mapeo([
                    'nom' => $array_az[$letra],
                    'ubicacion_id' => $seccion->id,
                    'ocupado' => 0,
                    'vehiculo_id' => 0
                ]);
            } else {
                $mapeo  = new Mapeo([
                    'nom' => $array_az[$letra] . '-' . $numero,
                    'ubicacion_id' => $seccion->id,
                    'ocupado' => 0,
                    'vehiculo_id' => 0
                ]);
                $numero++;
            }
            $letra++;
            $mapeo->save();
            $cont++;
        }

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'CREACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' REGISTRO UNA NUEVA SECCIÓN',
            'modulo' => 'SECCIONES',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return redirect()->route('secciones.edit', $seccion->id)->with('registrado', 'exito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Ubicacion $seccion)
    {
        $empresa = Empresa::first();
        if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
            return view('secciones.edit', compact('seccion', 'empresa'));
        } else {
            return view('errors.sin_permiso', compact('empresa'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ubicacion $seccion)
    {
        $valor = $seccion->update(array_map('mb_strtoupper', $request->all()));
        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'MODIFICACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' MODIFICÓ UNA SECCIÓN',
            'modulo' => 'SECCIONES',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);
        return redirect()->route('secciones.edit', $seccion->id)->with('editado', 'exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ubicacion $seccion)
    {
        $seccion->status = 0;
        $seccion->save();
        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'ELIMINACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' ELIMINÓ UNA SECCIÓN',
            'modulo' => 'SECCIONES',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);
        return response()->JSON([
            'msg' => 'Eliminación éxitosa.',
        ]);
    }

    public function obtener_seccion(Request $request)
    {
        $seccion = Ubicacion::find($request->id);
        return response()->JSON($seccion->nom);
    }
}
