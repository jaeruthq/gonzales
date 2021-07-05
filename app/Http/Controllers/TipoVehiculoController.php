<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use torremall\Http\Requests\TipovStoreRequest;
use torremall\Http\Requests\TipovUpdateRequest;

use torremall\Empresa;
use torremall\Propietario;
use torremall\TipoVehiculo;
use torremall\LogSeguimiento;

class TipoVehiculoController extends Controller
{
    public function index(Request $request)
    {
        $empresa = Empresa::first();
        $tipos = TipoVehiculo::all();
        if ($request->ajax()) {
            return response()->JSON(view('tipos.parcial.lista', compact('tipos'))->render());
        } else {
            if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
                return view('tipos.index', compact('empresa', 'tipos'));
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
            return view('tipos.create', compact('empresa'));
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
    public function store(TipovStoreRequest $request)
    {
        $tipo = new TipoVehiculo(array_map('mb_strtoupper', $request->all()));
        $tipo->save();

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'CREACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' REGISTRO UN NUEVO TIPO DE VEHICULO',
            'modulo' => 'TIPO DE VEHICULOS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return redirect()->route('tipos.edit', $tipo->id)->with('registrado', 'exito');
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
    public function edit(TipoVehiculo $tipo)
    {
        $empresa = Empresa::first();
        if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
            return view('tipos.edit', compact('tipo', 'empresa'));
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
    public function update(TipovUpdateRequest $request, TipoVehiculo $tipo)
    {
        $tipo->update(array_map('mb_strtoupper', $request->all()));
        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'MODIFICACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' MODIFICÓ UN REGISTRO TIPO DE VEHICULO',
            'modulo' => 'TIPO DE VEHICULOS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);
        return redirect()->route('tipos.edit', $tipo->id)->with('editado', 'exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TipoVehiculo $tipo)
    {
        $tipo->delete();
        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'ELIMINACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' ELIMINÓ UN REGISTRO TIPO DE VEHICULO',
            'modulo' => 'TIPO DE VEHICULOS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);
        return response()->JSON([
            'msg' => 'Eliminación éxitosa.',
        ]);
    }

    public function cargaTipos(Request $request)
    {
        $tipos = TipoVehiculo::all();
        $options = '<option value=""></option>';
        foreach ($tipos  as $value) {
            $options .= '<option value="' . $value->id . '">' . $value->nom . '</option>';
        }
        return response()->JSON($options);
    }
}
