<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use torremall\Http\Requests\VehiculoStoreRequest;
use torremall\Http\Requests\VehiculoUpdateRequest;
use torremall\Empresa;
use torremall\Vehiculo;
use torremall\Propietario;
use torremall\Tarifa;
use torremall\TipoVehiculo;
use Barryvdh\DomPDF\Facade as PDF;
use torremall\IngresoSalida;
use torremall\Mapeo;
use torremall\NotificacionUsuario;
use torremall\Pendiente;
use torremall\User;
use torremall\LogSeguimiento;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        $empresa = Empresa::first();
        $vehiculos = Vehiculo::where('status', 1)->get();
        if ($request->ajax()) {
            return response()->JSON(view('vehiculos.parcial.lista', compact('vehiculos'))->render());
        } else {
            if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
                return view('vehiculos.index', compact('empresa', 'vehiculos'));
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
            $propietarios = Propietario::where('status', 1)->get();
            $tarifas = Tarifa::all();
            $tipos = TipoVehiculo::all();

            $array_propietarios[''] = '';
            $array_tarifas[''] = '';
            $array_tipos[''] = '';

            foreach ($propietarios as $value) {
                $array_propietarios[$value->id] = $value->nom . ' ' . $value->apep . ' ' . $value->apem;
            }

            foreach ($tarifas as $value) {
                $array_tarifas[$value->id] = $value->nom;
            }

            foreach ($tipos as $value) {
                $array_tipos[$value->id] = $value->nom;
            }

            return view('vehiculos.create', compact('empresa', 'array_propietarios', 'array_tarifas', 'array_tipos'));
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
    public function store(VehiculoStoreRequest $request)
    {
        // CREANDO LOS DATOS DEL VEHICULO
        $vehiculo = new Vehiculo(array_map('mb_strtoupper', $request->except('foto')));

        $nom_foto = 'vehiculo_default.jpg';
        if ($request->hasFile('foto')) {
            //obtener el archivo
            $file_foto = $request->file('foto');
            $extension = "." . $file_foto->getClientOriginalExtension();
            $nom_foto = str_replace(' ', '_', $vehiculo->nom) . $vehiculo->placa . time() . $extension;
            $file_foto->move(public_path() . "/imgs/vehiculos/", $nom_foto);
        }

        //completar los campos foto y fecha registro del vehiculo
        $vehiculo->foto = $nom_foto;
        $vehiculo->status = 1;
        $vehiculo->fecha_reg = date('Y-m-d');
        $vehiculo->save();

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'CREACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' REGISTRO UN VEHICULO',
            'modulo' => 'VEHICULOS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return redirect()->route('vehiculos.edit', $vehiculo->id)->with('registrado', 'success');
    }

    public function store_ingreso(VehiculoStoreRequest $request)
    {
        // CREANDO LOS DATOS DEL VEHICULO
        $vehiculo = new Vehiculo(array_map('mb_strtoupper', $request->except('foto')));

        $nom_foto = 'vehiculo_default.jpg';
        if ($request->hasFile('foto')) {
            //obtener el archivo
            $file_foto = $request->file('foto');
            $extension = "." . $file_foto->getClientOriginalExtension();
            $nom_foto = str_replace(' ', '_', $vehiculo->nom) . $vehiculo->placa . time() . $extension;
            $file_foto->move(public_path() . "/imgs/vehiculos/", $nom_foto);
        }

        //completar los campos foto y fecha registro del vehiculo
        $vehiculo->foto = $nom_foto;
        $vehiculo->status = 1;
        $vehiculo->fecha_reg = date('Y-m-d');
        $vehiculo->save();


        $pendiente = Pendiente::where('rfid', $vehiculo->rfid)->get();
        foreach ($pendiente as $value) {
            $value->delete();
        }

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'CREACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' REGISTRO UN VEHICULO',
            'modulo' => 'VEHICULOS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return response()->JSON([
            'sw' => true,
            'rfid' => $vehiculo->rfid
        ]);
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
    public function edit(Vehiculo $vehiculo)
    {
        $empresa = Empresa::first();
        if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
            $propietarios = Propietario::where('status', 1)->get();
            $tarifas = Tarifa::all();
            $tipos = TipoVehiculo::all();

            $array_propietarios[''] = '';
            $array_tarifas[''] = '';
            $array_tipos[''] = '';

            foreach ($propietarios as $value) {
                $array_propietarios[$value->id] = $value->nom . ' ' . $value->apep . ' ' . $value->apem;
            }

            foreach ($tarifas as $value) {
                $array_tarifas[$value->id] = $value->nom;
            }

            foreach ($tipos as $value) {
                $array_tipos[$value->id] = $value->nom;
            }
            return view('vehiculos.edit', compact('vehiculo', 'empresa', 'array_propietarios', 'array_tarifas', 'array_tipos'));
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
    public function update(VehiculoUpdateRequest $request, Vehiculo $vehiculo)
    {
        $vehiculo->update(array_map('mb_strtoupper', $request->except('foto')));
        if ($request->hasFile('foto')) {
            // ELIMINAR ANTIGUA FOTOS
            $antiguo = $vehiculo->foto;
            \File::delete(public_path() . '/imgs/vehiculos/' . $antiguo);

            //obtener el archivo
            $file_foto = $request->file('foto');
            $extension = "." . $file_foto->getClientOriginalExtension();
            $nom_foto = str_replace(' ', '_', $vehiculo->nom) . $vehiculo->placa . time() . $extension;
            $file_foto->move(public_path() . "/imgs/vehiculos/", $nom_foto);
            //completar los campos foto y fecha registro del vehiculo
            $vehiculo->foto = $nom_foto;
            $vehiculo->save();
        }

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'MODIFICACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' MODIFICO UN VEHICULO',
            'modulo' => 'VEHICULOS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);
        return redirect()->route('vehiculos.edit', $vehiculo->id)->with('editado', 'exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vehiculo $vehiculo)
    {
        $vehiculo->status = 0;
        $vehiculo->save();

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'ELIMINACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' ELIMINO UN VEHICULO',
            'modulo' => 'VEHICULOS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);
        return response()->JSON([
            'msg' => 'Eliminación éxitosa.',
        ]);
    }

    public function kardex(Vehiculo $vehiculo)
    {
        $empresa = Empresa::first();

        $pdf = PDF::loadView('vehiculos.kardex', compact('vehiculo', 'empresa'))->setPaper('letter', 'portrait');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('Kardex.pdf');
    }
}
