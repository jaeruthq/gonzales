<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use torremall\Http\Requests\PropietarioStoreRequest;
use torremall\Http\Requests\PropietarioUpdateRequest;

use torremall\Empresa;
use torremall\Propietario;
use torremall\LogSeguimiento;

class PropietarioController extends Controller
{
    public function index(Request $request)
    {
        $empresa = Empresa::first();
        $propietarios = Propietario::where('status', 1)->get();
        if ($request->ajax()) {
            return response()->JSON(view('propietarios.parcial.lista', compact('propietarios'))->render());
        } else {
            if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
                return view('propietarios.index', compact('empresa', 'propietarios'));
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
            return view('propietarios.create', compact('empresa'));
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
    public function store(PropietarioStoreRequest $request)
    {
        // CREANDO LOS DATOS DEL PROPIETARIO
        $propietario = new Propietario(array_map('mb_strtoupper', $request->except('foto')));

        $nom_foto = 'user_default.png';
        if ($request->hasFile('foto')) {
            //obtener el archivo
            $file_foto = $request->file('foto');
            $extension = "." . $file_foto->getClientOriginalExtension();
            $nom_foto = $propietario->apep . str_replace(' ', '_', $propietario->nom) . time() . $extension;
            $file_foto->move(public_path() . "/imgs/propietarios/", $nom_foto);
        }

        //completar los campos foto y fecha registro del propietario
        $propietario->foto = $nom_foto;
        $propietario->status = 1;
        $propietario->fecha_reg = date('Y-m-d');
        $propietario->save();

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'CREACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' REGISTRÓ UN NUEVO PROPIETARIO',
            'modulo' => 'PROPIETARIOS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        if ($request->ajax()) {
            return response()->JSON(true);
        }

        return redirect()->route('propietarios.edit', $propietario->id)->with('registrado', 'success');
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
    public function edit(Propietario $propietario)
    {
        $empresa = Empresa::first();
        if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
            return view('propietarios.edit', compact('propietario', 'empresa'));
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
    public function update(PropietarioUpdateRequest $request, Propietario $propietario)
    {
        $propietario->update(array_map('mb_strtoupper', $request->except('foto')));
        if ($request->hasFile('foto')) {
            // ELIMINAR ANTIGUA FOTOS
            $antiguo = $propietario->foto;
            \File::delete(public_path() . '/imgs/propietarios/' . $antiguo);

            //obtener el archivo
            $file_foto = $request->file('foto');
            $extension = "." . $file_foto->getClientOriginalExtension();
            $nom_foto = $propietario->apep . str_replace(' ', '_', $propietario->nom) . time() . $extension;
            $file_foto->move(public_path() . "/imgs/propietarios/", $nom_foto);
            //completar los campos foto y fecha registro del propietario
            $propietario->foto = $nom_foto;
            $propietario->save();
        }

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'MODIFICACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' MODIFICÓ UN REGISTRO PROPIETARIO',
            'modulo' => 'PROPIETARIOS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return redirect()->route('propietarios.edit', $propietario->id)->with('editado', 'exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Propietario $propietario)
    {
        $propietario->status = 0;
        $propietario->save();

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'ELIMINACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' ELIMINÓ UN REGISTRO PROPIETARIO',
            'modulo' => 'PROPIETARIOS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return response()->JSON([
            'msg' => 'Eliminación éxitosa.',
        ]);
    }

    public function cargaPropietarios(Request $request)
    {
        $propietarios = Propietario::where('status', 1)
            ->orderBy('nom', 'asc')->get();
        $options = '<option value=""></option>';
        foreach ($propietarios  as $value) {
            $options .= '<option value="' . $value->id . '">' . $value->nom . ' ' . $value->apep . ' ' . $value->apem . '</option>';
        }

        return response()->JSON($options);
    }
}
