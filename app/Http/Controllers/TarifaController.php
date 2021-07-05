<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use torremall\Http\Requests\TarifaStoreRequest;
use torremall\Http\Requests\TarifaUpdateRequest;


use torremall\Empresa;
use torremall\Tarifa;
use torremall\LogSeguimiento;

class TarifaController extends Controller
{
    public function index(Request $request)
    {
        $empresa = Empresa::first();
        $tarifas = Tarifa::all();
        if ($request->ajax()) {
            return response()->JSON(view('tarifas.parcial.lista', compact('tarifas'))->render());
        } else {
            if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
                return view('tarifas.index', compact('empresa', 'tarifas'));
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
        if (Auth::user()->tipo == 'ADMINISTRADOR') {
            return view('tarifas.create', compact('empresa'));
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
    public function store(TarifaStoreRequest $request)
    {
        $tarifa = new Tarifa(array_map('mb_strtoupper', $request->all()));
        $tarifa->fecha_reg = date('Y-m-d');
        $tarifa->save();

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'CREACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' REGISTRO UNA NUEVA TARIFA',
            'modulo' => 'TARIFAS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return redirect()->route('tarifas.edit', $tarifa->id)->with('registrado', 'exito');
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
    public function edit(Tarifa $tarifa)
    {
        $empresa = Empresa::first();
        if (Auth::user()->tipo == 'ADMINISTRADOR') {
            return view('tarifas.edit', compact('tarifa', 'empresa'));
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
    public function update(TarifaUpdateRequest $request, Tarifa $tarifa)
    {
        $tarifa->update(array_map('mb_strtoupper', $request->all()));
        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'MODIFICACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' MODIFICÓ UN REGISTRO TARIFA',
            'modulo' => 'TARIFAS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);
        return redirect()->route('tarifas.edit', $tarifa->id)->with('editado', 'exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tarifa $tarifa)
    {
        $tarifa->delete();
        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'ELIMINACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' ELIMINÓ UN REGISTRO TARIFA',
            'modulo' => 'TARIFAS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);
        return response()->JSON([
            'msg' => 'Eliminación éxitosa.',
        ]);
    }

    public function cargaTarifas(Request $request)
    {
        $tarifas = Tarifa::all();
        $options = '<option value=""></option>';
        foreach ($tarifas  as $value) {
            $options .= '<option value="' . $value->id . '">' . $value->nom . '</option>';
        }

        return response()->JSON($options);
    }
}
