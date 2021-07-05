<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use torremall\Empresa;
use torremall\Cobro;
use torremall\Tarifa;
use torremall\Vehiculo;
use torremall\Propietario;
use torremall\IngresoSalida;
use torremall\library\numero_a_letras\src\NumeroALetras;
use Barryvdh\DomPDF\Facade as PDF;
use torremall\Salida;
use torremall\LogSeguimiento;
use DateTime;

class CobroController extends Controller
{
    public function index(Request $request)
    {
        $empresa = Empresa::first();
        $cobros = Cobro::where('status', 1)->get();
        if ($request->ajax()) {
            return response()->JSON(view('cobros.parcial.lista', compact('cobros'))->render());
        } else {
            if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
                return view('cobros.index', compact('empresa', 'cobros'));
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
            return view('cobros.create', compact('empresa'));
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
    public function store(Request $request)
    {
        $cobro = new Cobro(array_map('mb_strtoupper', $request->all()));
        $cobro->save();

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'CREACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' REGISTRO UN NUEVO COBRO',
            'modulo' => 'COBROS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return redirect()->route('cobros.edit', $cobro->id)->with('registrado', 'exito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Cobro $cobro)
    {
        $empresa = Empresa::first();
        if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
            $convertir = new NumeroALetras();
            $array_monto = explode('.', $cobro->total);
            $literal = $convertir->convertir($array_monto[0]);
            $literal .= " " . $array_monto[1] . "/100." . " BOLIVIANOS";
            return view('cobros.show', compact('cobro', 'literal', 'empresa'));
        }
        return view('errors.sin_permiso', compact('empresa'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Cobro $cobro)
    {
        $empresa = Empresa::first();
        if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
            return view('cobros.edit', compact('cobro', 'empresa'));
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
    public function update(Request $request, Cobro $cobro)
    {
        $cobro->update(array_map('mb_strtoupper', $request->all()));
        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'MODIFICACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' MODIFICÓ UN REGISTRO DE COBRO',
            'modulo' => 'COBROS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);
        return redirect()->route('cobros.edit', $cobro->id)->with('editado', 'exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cobro $cobro)
    {
        $cobro->delete();
        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'ELIMINACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' ELIMINÓ UN REGISTRO DE COBRO',
            'modulo' => 'COBROS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return response()->JSON([
            'msg' => 'Eliminación éxitosa.',
        ]);
    }

    public function obtener_tarifa(Request $request)
    {
        $fecha = $request->fecha;
        $vehiculo = Vehiculo::where('rfid', $request->rfid)->get()->first();
        $tarifa = $vehiculo->tarifa;
        $ingreso = IngresoSalida::where('vehiculo_id', $vehiculo->id)
            ->where('status', 1)->get()->last();
        if ($ingreso) {
            if ($ingreso->accion == 'INGRESO') {
                $fecha_ingreso = $ingreso->fecha_reg;
                $fecha_ingreso_operacion = $ingreso->fecha_reg . ' ' . $ingreso->hora;
                $fecha_actual_operacion = date('Y-m-d H:i:s', strtotime($fecha . ' ' . $request->hora));

                $array_fecha_ingreso = explode('-', date('Y-m-d', strtotime($fecha_ingreso)));
                $array_fecha_actual = explode('-', date('Y-m-d', strtotime($fecha)));

                // CREAR FECHA1 Y FECHA2 DE TIPO DATETIME Y SACAR SU DIFERENCIA CON diff()
                // PARA ESO CARGAR LA CASLE DateTime -> use DateTime;
                $fecha1 = new DateTime($fecha_ingreso_operacion);
                $fecha2 = new DateTime($fecha_actual_operacion);
                $intervalo = $fecha1->diff($fecha2);
                if ($intervalo->y > 0) {
                    $transcurido = $intervalo->days * 24;
                    $transcurido = $transcurido + $intervalo->h;
                    if ($intervalo->i > 0) {
                        $transcurido++;
                    }
                } elseif ($intervalo->m > 0) {
                    $transcurido = $intervalo->days * 24;
                    $transcurido = $transcurido + $intervalo->h;
                    if ($intervalo->i > 0) {
                        $transcurido++;
                    }
                } elseif ($intervalo->d > 0) {
                    $transcurido = $intervalo->d * 24;
                    $transcurido = $transcurido + $intervalo->h;
                    if ($intervalo->i > 0) {
                        $transcurido++;
                    }
                } else {
                    $transcurido = $intervalo->h;
                    if ($intervalo->i > 0) {
                        $transcurido++;
                    } else {
                        $transcurido++;
                    }
                }

                $total = 0;
                $restante = 0;
                $horas_tarifa = $tarifa->horas;

                if ($transcurido > 0) {
                    if ($transcurido <= $horas_tarifa) {
                        $total = $tarifa->precio;
                    } else {
                        $restante = $transcurido;
                        $total = $total + $tarifa->precio;
                        do {
                            $total = $total + $tarifa->precio;
                            $restante = $restante - $horas_tarifa;
                        } while ($restante > $horas_tarifa);
                    }

                    // OBTENER EL TIEMPO TOTAL EN BASE AL PRECIO Y EL TIEMPO
                    $tiempo_final = ($total / $tarifa->precio) * $horas_tarifa;

                    return response()->JSON([
                        'msg' => 'SI',
                        'tarifa' => $tarifa->nom,
                        'tiempo_tarifa' => $horas_tarifa,
                        'precio' => $tarifa->precio,
                        'tiempo' => $tiempo_final,
                        'total' => $total,
                        'f_ingreso' => date('d/m/Y', strtotime($ingreso->fecha_reg)),
                        'f_salida' => date('d/m/Y', strtotime($fecha)),
                        'h_ingreso' => date('H:i', strtotime($ingreso->hora)),
                        'h_salida' => $request->hora,
                        'a_nombre' => $vehiculo->propietario->apep,
                        'nit' => $vehiculo->propietario->ci,
                        'vehiculo' => $vehiculo->nom,
                        'propietario' => $vehiculo->propietario->nom . ' ' . $vehiculo->propietario->apep . ' ' . $vehiculo->propietario->apem,
                        'horaIngreso' => $ingreso->hora,
                        'fechaIngreso' => $ingreso->fecha_reg,
                    ]);
                } else {
                    return response()->JSON([
                        'msg' => 'NO COBRAR',
                        'tarifa' => $tarifa->nom,
                        'tiempo_tarifa' => $tarifa->horas,
                        'precio' => $tarifa->precio,
                        'tiempo' => '0',
                        'total' => 0,
                        'f_ingreso' => date('d/m/Y', strtotime($ingreso->fecha_reg)),
                        'f_salida' => date('d/m/Y', strtotime($fecha)),
                        'h_ingreso' => date('H:i', strtotime($ingreso->hora)),
                        'h_salida' => $request->hora,
                        'a_nombre' => $vehiculo->propietario->apep,
                        'nit' => $vehiculo->propietario->ci,
                        'vehiculo' => $vehiculo->nom,
                        'propietario' => $vehiculo->propietario->nom . ' ' . $vehiculo->propietario->apep . ' ' . $vehiculo->propietario->apem,
                        'horaIngreso' => $ingreso->hora,
                        'fechaIngreso' => $ingreso->fecha_reg,
                    ]);
                }
            } else {
                return response()->JSON([
                    'msg' => 'NO',
                    'tarifa' => $tarifa->nom,
                    'tiempo_tarifa' => $tarifa->horas,
                    'precio' => $tarifa->precio,
                    'tiempo' => '0',
                    'total' => 0,
                    'f_ingreso' => '-',
                    'f_salida' => '-',
                    'h_ingreso' => '-',
                    'h_salida' => '-',
                    'a_nombre' => $vehiculo->propietario->apep,
                    'nit' => $vehiculo->propietario->ci,
                    'vehiculo' => $vehiculo->nom,
                    'propietario' => $vehiculo->propietario->nom . ' ' . $vehiculo->propietario->apep . ' ' . $vehiculo->propietario->apem,
                    'horaIngreso' => $ingreso->hora,
                    'fechaIngreso' => $ingreso->fecha_reg,
                ]);
            }
        } else {
            return response()->JSON([
                'msg' => 'NO',
                'tarifa' => $tarifa->nom,
                'tiempo_tarifa' => $tarifa->horas,
                'precio' => $tarifa->precio,
                'tiempo' => '0',
                'total' => 0,
                'f_ingreso' => '-',
                'f_salida' => '-',
                'h_ingreso' => '-',
                'h_salida' => '-',
                'a_nombre' => $vehiculo->propietario->apep,
                'nit' => $vehiculo->propietario->ci,
                'vehiculo' => $vehiculo->nom,
                'propietario' => $vehiculo->propietario->nom . ' ' . $vehiculo->propietario->apep . ' ' . $vehiculo->propietario->apem,
                'horaIngreso' => $ingreso->hora,
                'fechaIngreso' => $ingreso->fecha_reg,
            ]);
        }
    }

    public function factura(Cobro $cobro)
    {
        $empresa = Empresa::first();
        $convertir = new NumeroALetras();
        $array_monto = explode('.', $cobro->total);
        $literal = $convertir->convertir($array_monto[0]);
        $literal .= " " . $array_monto[1] . "/100." . " BOLIVIANOS";
        $pdf = PDF::loadView('cobros.factura', compact('empresa', 'cobro', 'literal'));
        return $pdf->stream('Factura.pdf');
    }

    public function control(Request $request)
    {
        // IR COBRANDO A PARTIR DE LA 1RA SALIDA QUE NO TIENE COBRO
        $ultimo_registro = Salida::where('cobrado', 0)->get()->first();

        if ($ultimo_registro) {
            // OBTENER LA SALIDA Y SU INGRESO
            $ultima_salida = IngresoSalida::where('accion', 'SALIDA')
                ->where('id', $ultimo_registro->salida_id)
                ->get()->first();

            $ingreso = IngresoSalida::where('accion', 'INGRESO')
                ->where('vehiculo_id', $ultima_salida->vehiculo_id)
                ->get()->last();

            if ($ultimo_registro && $ultima_salida && $ingreso) {
                $datos = [
                    'msg' => 'nuevo',
                    'vehiculo' => $ultima_salida->vehiculo->nom,
                    'propietario' => $ultima_salida->vehiculo->propietario->nom . ' ' . $ultima_salida->vehiculo->propietario->apep . ' ' . $ultima_salida->vehiculo->propietario->apem,
                    'fecha_ingreso' => $ingreso->fecha_reg,
                    'hora_ingreso' => $ingreso->hora,
                    'fecha_salida' => $ultima_salida->fecha_reg,
                    'hora_salida' => $ultima_salida->hora,
                    'tiempo_cobrado' => $ultima_salida->cobro->tiempo_cobrado,
                    'total' => $ultima_salida->cobro->total,
                    'a_nombre' => $ultima_salida->cobro->factura->a_nombre,
                    'nit' => $ultima_salida->cobro->factura->nit,
                    'cobro_id' => $ultima_salida->cobro->id,
                ];

                return response()->JSON($datos);
            }
        }
    }

    function realizar_cobro(Request $request)
    {
        $cobro = Cobro::find($request->cobro_id);

        $cobro->tiempo_cobrado = $request->tiempo_cobrado;
        $cobro->total = $request->total;
        $cobro->save();
        $cobro->factura->a_nombre = $request->a_nombre;
        $cobro->factura->nit = $request->nit;
        $cobro->factura->save();

        $cobro->salida->cobro_salida->cobrado = 1;
        $cobro->salida->cobro_salida->save();

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'CREACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' REGISTRO UN COBRO',
            'modulo' => 'COBROS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        $empresa = Empresa::first();
        $convertir = new NumeroALetras();
        $array_monto = explode('.', $cobro->total);
        $literal = $convertir->convertir($array_monto[0]);
        $literal .= " " . $array_monto[1] . "/100." . " BOLIVIANOS";
        $pdf = PDF::loadView('cobros.factura', compact('empresa', 'cobro', 'literal'));
        return $pdf->stream('Factura.pdf');
    }
}
