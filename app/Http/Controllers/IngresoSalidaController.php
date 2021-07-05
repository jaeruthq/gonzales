<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use torremall\Empresa;
use torremall\IngresoSalida;
use torremall\NotificacionUsuario;
use torremall\User;
use torremall\Vehiculo;
use torremall\Cobro;
use torremall\Factura;
use torremall\Mapeo;
use torremall\Salida;
use torremall\Ubicacion;
use torremall\LogSeguimiento;

class IngresoSalidaController extends Controller
{
    public function index(Request $request)
    {
        $empresa = Empresa::first();
        $ingresos_salidas = IngresoSalida::where('status', 1)
            ->where('tipo', '!=', 'SALIDA MENSUAL')->get();
        if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
            return view('ingresos_salidas.index', compact('empresa', 'ingresos_salidas'));
        }
        return view('errors.sin_permiso', compact('empresa'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $empresa = Empresa::first();

        $vehiculos = Vehiculo::where('status', 1)->get();
        $array_vehiculos = [];

        $array_vehiculos[''] = '';
        foreach ($vehiculos as $value) {
            $array_vehiculos[$value->id] = $value->nom . ' | ' . $value->propietario->nom . ' ' . $value->propietario->apep . ' ' . $value->propietario->apem;
        }

        $secciones = Ubicacion::where('status', 1)->get();
        $array_secciones = [];

        $array_secciones[''] = '';
        foreach ($secciones as $value) {
            $array_secciones[$value->id] = $value->nom;
        }

        if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
            return view('ingresos_salidas.create', compact('empresa', 'array_vehiculos', 'array_secciones'));
        }
        return view('errors.sin_permiso', compact('empresa'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vehiculo = Vehiculo::find($request->vehiculo_id);

        $empresa = Empresa::first();
        $ingreso_salida = new IngresoSalida(array_map('mb_strtoupper', $request->all()));
        $ingreso_salida->status = 1;
        $ingreso_salida->tipo = 'NORMAL';
        if ($vehiculo->tarifa->horas >= 672) {
            $ingreso_salida->tipo = 'INGRESO MENSUAL';
        }
        $ingreso_salida->save();

        // CREAR LAS NOTIFICACIONES PARA LOS USUARIOS
        $usuarios = User::where('status', 1)
            ->where('tipo', '!=', 'CONTROL')->get();
        foreach ($usuarios as $value) {
            $notificacion = new NotificacionUsuario([
                'ingresoSalida_id' => $ingreso_salida->id,
                'hora' => $ingreso_salida->hora,
                'fecha' => $ingreso_salida->fecha_reg,
                'user_id' => $value->id,
                'visto' => 0
            ]);

            $notificacion->save();
        }

        if ($request->accion == 'INGRESO') {
            // Ingreso para vehiculos con tarifa mayor o igual a 672 horas(mensual)
            // si es ingreso marcar ocupado el lugar e indicar el vehiculo que lo ocupo
            $mapeo = Mapeo::find($request->mapeo_id);
            $mapeo->ocupado = 1;
            $mapeo->vehiculo_id = $request->vehiculo_id;
            $mapeo->save();

            if ($request->ajax()) {
                // Despues de reservar el mapeo registrar su salida con la fecha + 672+ hrs
                $horas_tarifa = $vehiculo->tarifa->horas;
                $nueva_salida = new IngresoSalida([
                    'vehiculo_id' => $request->vehiculo_id,
                    'accion' => 'SALIDA',
                    'hora' => date('H:i', strtotime(date('H:i') . '+' . $horas_tarifa . ' hours')),
                    'fecha_reg' => date('Y-m-d', strtotime(date('Y-m-d') . '+' . $horas_tarifa . ' hours')),
                    'tipo' => 'SALIDA MENSUAL',
                    'status' => 1,
                ]);
                $nueva_salida->save();

                // Ademas de su cobro y factura
                $cobro = new Cobro([
                    'salida_id' => $nueva_salida->id,
                    'vehiculo_id' => $nueva_salida->vehiculo_id,
                    'tarifa_id' => $nueva_salida->vehiculo->tarifa_id,
                    'tiempo_cobrado' => $horas_tarifa,
                    'fecha_reg' => date('Y-m-d'),
                    'hora' => date('H:i'),
                    'hora_ingreso' => $ingreso_salida->hora,
                    'fecha_ingreso' => $ingreso_salida->fecha_reg,
                    'total' => $vehiculo->tarifa->precio,
                    'status' => 1
                ]);
                $cobro->save();

                // factura
                $nro_fac = 0;
                $ultima_factura = Factura::get()->last();
                if ($ultima_factura) {
                    $nro_fac = $ultima_factura->nro_factura + 1;
                } else {
                    $nro_fac = 10001;
                }

                // CREAR UN CÓDIGO DE CONTROL
                // crear un array
                $array_codigo = [];
                for ($i = 1; $i <= 9; $i++) {
                    $array_codigo[] = $i; //agregar los números del 1 al 9
                }
                array_push($array_codigo, 'A', 'B', 'C', 'D', 'E', 'F'); //agregar las letras para poder generar un # hexadecimal
                //generar el código
                $codigo_control = '';
                for ($i = 1; $i <= 10; $i++) {
                    $indice = mt_rand(0, 14);
                    $codigo_control .= $array_codigo[$indice];
                    if ($i % 2 == 0) {
                        $codigo_control .= '-';
                    }
                }
                $codigo_control = substr($codigo_control, 0, strlen($codigo_control) - 1); //quitar el ultimo guión

                $qr = $empresa->name . '<br>' . $nro_fac . '<br>' . $codigo_control;
                $qr_nom = $nro_fac . '_' . time() . '.png';
                $factura = new Factura([
                    'cobro_id' => $cobro->id,
                    'a_nombre' => mb_strtoupper($vehiculo->propietario->apep),
                    'nit' => mb_strtoupper($vehiculo->propietario->ci),
                    'nro_factura' => $nro_fac,
                    'codigo_control' => $codigo_control,
                    'qr' => $qr_nom,
                    'cobro_id' => $cobro->id,
                    'total' => $cobro->total,
                    'fecha' => $cobro->fecha_reg,
                    'hora' => $cobro->hora,
                    'fecha_emision' => date('Y-m-d', strtotime($cobro->fecha_reg . '+6 months')),
                    'estado' => 1,
                    'observacion' => 'COBRO ANTICIPADO POR PARQUEO DE 672 HORAS O MAS'
                ]);
                $factura->save();

                /* GENERAR CÓDIGO QR */
                $base_64 = base64_encode(\QrCode::format('png')->size(400)->generate($qr));
                $imagen_codigo_qr = base64_decode($base_64);
                file_put_contents(public_path() . '/imgs/qr/' . $qr_nom, $imagen_codigo_qr);

                $cobro_salida = new Salida();
                $cobro_salida->salida_id = $nueva_salida->id;
                $cobro_salida->cobrado = 0;
                $cobro_salida->save();
                return response()->json([
                    'msg' => 'ingreso'
                ]);
            }
        }

        if ($request->accion == 'SALIDA') {
            $mapeo = Mapeo::where('vehiculo_id', $ingreso_salida->vehiculo->id)->get()->first();
            if ($mapeo) {
                $mapeo->ocupado = 0;
                $mapeo->vehiculo_id = 0;
                $mapeo->save();
            } else {
                return redirect()->route('ingresos_salidas.index')->with('NoMapeo', 'error');
            }

            // COMPROBAR QUE EL VEHICULO QUE SALDRA NO TIENE UN COBRO
            $cobro_vehiculo = Salida::where('salida_id', $ingreso_salida->id)->get()->first();
            // En caso de que ya este cobrado solo mandar la respuesta de salida
            if (!$cobro_vehiculo) {
                // SI NO SE COBRO CREAR EL COBRO Y SU FACTURA
                $cobro = new Cobro([
                    'salida_id' => $ingreso_salida->id,
                    'vehiculo_id' => $ingreso_salida->vehiculo_id,
                    'tarifa_id' => $ingreso_salida->vehiculo->tarifa_id,
                    'tiempo_cobrado' => $request->txtTiempo,
                    'fecha_reg' => $ingreso_salida->fecha_reg,
                    'hora' => $ingreso_salida->hora,
                    'hora_ingreso' => $request->txtHoraIngreso,
                    'fecha_ingreso' => $request->txtFechaIngreso,
                    'total' => $request->txtTotal,
                    'status' => 1
                ]);
                $cobro->save();

                // factura
                $nro_fac = 0;
                $ultima_factura = Factura::get()->last();
                if ($ultima_factura) {
                    $nro_fac = $ultima_factura->nro_factura + 1;
                } else {
                    $nro_fac = 10001;
                }

                // CREAR UN CÓDIGO DE CONTROL
                // crear un array
                $array_codigo = [];
                for ($i = 1; $i <= 9; $i++) {
                    $array_codigo[] = $i; //agregar los números del 1 al 9
                }
                array_push($array_codigo, 'A', 'B', 'C', 'D', 'E', 'F'); //agregar las letras para poder generar un # hexadecimal
                //generar el código
                $codigo_control = '';
                for ($i = 1; $i <= 10; $i++) {
                    $indice = mt_rand(0, 14);
                    $codigo_control .= $array_codigo[$indice];
                    if ($i % 2 == 0) {
                        $codigo_control .= '-';
                    }
                }
                $codigo_control = substr($codigo_control, 0, strlen($codigo_control) - 1); //quitar el ultimo guión

                $qr = $empresa->name . '<br>' . $nro_fac . '<br>' . $codigo_control;
                $qr_nom = $nro_fac . '_' . time() . '.png';
                $factura = new Factura([
                    'cobro_id' => $cobro->id,
                    'a_nombre' => mb_strtoupper($request->a_nombre),
                    'nit' => mb_strtoupper($request->nit),
                    'nro_factura' => $nro_fac,
                    'codigo_control' => $codigo_control,
                    'qr' => $qr_nom,
                    'cobro_id' => $cobro->id,
                    'total' => $cobro->total,
                    'fecha' => $cobro->fecha_reg,
                    'hora' => $cobro->hora,
                    'fecha_emision' => date('Y-m-d', strtotime($cobro->fecha_reg . '+6 months')),
                    'estado' => 1,
                    'observacion' => ''
                ]);
                $factura->save();

                /* GENERAR CÓDIGO QR--------------------------------------------------------------------- */
                $base_64 = base64_encode(\QrCode::format('png')->size(400)->generate($qr));
                $imagen_codigo_qr = base64_decode($base_64);
                file_put_contents(public_path() . '/imgs/qr/' . $qr_nom, $imagen_codigo_qr);

                $nueva_salida = new Salida();
                $nueva_salida->salida_id = $ingreso_salida->id;
                $nueva_salida->cobrado = 0;
                $nueva_salida->save();
            }

            if ($request->ajax()) {
                return response()->json([
                    'msg' => 'salida'
                ]);
            }

            return redirect()->route('cobros.show', $cobro->id)->with('registrado', 'exito');
        }

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'CREACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' REGISTRO UN INGRESO/SALIDA',
            'modulo' => 'INGRESOS Y SALIDAS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return redirect()->route('ingresos_salidas.index')->with('registrado', 'exito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(IngresoSalida $ingreso_salida)
    {
        $empresa = Empresa::first();
        if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
            return view('ingresos_salidas.show', compact('ingreso_salida', 'empresa'));
        }
        return view('errors.sin_permiso', compact('empresa'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(IngresoSalida $ingreso_salida)
    {
        $empresa = Empresa::first();
        $vehiculos = Vehiculo::where('status', 1)->get();
        $array_vehiculos = [];

        $array_vehiculos[''] = '';
        foreach ($vehiculos as $value) {
            $array_vehiculos[$value->id] = $value->nom . '. PROPIETARIO: ' . $value->propietario->nom . ' ' . $value->propietario->apep . ' ' . $value->propietario->apem;
        }

        $secciones = Ubicacion::where('status', 1)->get();
        $array_secciones = [];

        $array_secciones[''] = '';
        foreach ($secciones as $value) {
            $array_secciones[$value->id] = $value->nom;
        }

        if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
            return view('ingresos_salidas.edit', compact('ingreso_salida', 'empresa', 'array_vehiculos', 'array_secciones'));
        }
        return view('errors.sin_permiso', compact('empresa'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IngresoSalida $ingreso_salida)
    {
        $ingreso_salida->update(array_map('mb_strtoupper', $request->all()));

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'MODIFICACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' MODIFICÓ UN REGISTRO DE INGRESO/SALIDA',
            'modulo' => 'INGRESOS Y SALIDAS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return redirect()->route('ingresos_salidas.edit', $ingreso_salida->id)->with('editado', 'exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(IngresoSalida $ingreso_salida)
    {
        $ingreso_salida->status = 0;
        $ingreso_salida->save();

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'ELIMINACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' ELIMINÓ UN REGISTRO DE INGRESO/SALIDA',
            'modulo' => 'INGRESOS Y SALIDAS',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return response()->JSON([
            'msg' => 'Eliminación éxitosa.',
        ]);
    }

    public function carga_accion(Request $request)
    {
        $vehiculo = Vehiculo::where('rfid', $request->rfid)->get()->first();

        if ($vehiculo) {
            $ingreso_salida = IngresoSalida::where('vehiculo_id', $vehiculo->id)->get()->last();
            $accion = 'INGRESO';

            if ($ingreso_salida) {
                if ($ingreso_salida->accion == 'INGRESO') {
                    $accion = 'SALIDA';
                }
            }

            $nom_vehiculo = $vehiculo->nom . ' | ' . $vehiculo->propietario->nom . ' ' . $vehiculo->propietario->apep . ' ' . $vehiculo->propietario->apem;

            $ingreso_salida_historico = IngresoSalida::where('vehiculo_id', $vehiculo->id)
                ->where('tipo', 'HISTORICO')
                ->get()->last();
            if ($ingreso_salida_historico) {
                $accion = 'INGRESO';
            }

            if ($accion == 'INGRESO') {
                $horas_tarifa = $vehiculo->tarifa->horas;

                $modal = '';
                if ($horas_tarifa >= 672) {
                    // Si la tarifa es mensual 1ro comprobar si la fecha y hora ya estan vencidas
                    // en caso de que no, no desocupar su lugar, caso contrario desocuparlo.
                    // Por lo que 1ro se comprobara si tiene un lugar ocupado
                    // Si lo tiene obtener la fecha y hora de salida registrada
                    // Caso contrario mandar el modal que se debe mostrar en el control

                    // buscar en los mapeos
                    $mapeo = Mapeo::where('vehiculo_id', $vehiculo->id)
                        ->where('ocupado', 1)->get()->first();

                    if ($mapeo) {
                        // Si tiene mapeo comprobar si su fecha de mensualidad se vencio o no
                        // Si se vencio desocupar el lugar y marcar su registro
                        // Caso contrario solo registrar el ingreso o salida
                        $salida_vehiculo = IngresoSalida::where('vehiculo_id', $vehiculo->id)
                            ->where('accion', 'SALIDA')
                            ->where('tipo', 'SALIDA MENSUAL')
                            ->get()->last();
                        $fecha_salida = $salida_vehiculo->fecha_reg;
                        $hora_salida = $salida_vehiculo->hora;

                        $accion_historico = 'INGRESO';
                        // saber si este vehiculo ya tiene un registro historico.
                        // En caso de que no tenga la accion sera SALIDA
                        // Caso contrario dependera de la ultima accion
                        $ultimo_historico = IngresoSalida::where('vehiculo_id', $vehiculo->id)
                            ->where('tipo', 'HISTORICO')
                            ->get()->last();
                        if ($ultimo_historico) {
                            if ($ultimo_historico->accion == 'INGRESO') {
                                $accion_historico = 'SALIDA';
                            }
                        } else {
                            $accion_historico = 'SALIDA';
                        }

                        if (date('Y-m-d') > date('Y-m-d', strtotime($fecha_salida))) {
                            $mapeo->ocupado = 0;
                            $mapeo->vehiculo_id = 0;
                            $mapeo->save();
                        } elseif (date('Y-m-d') == date('Y-m-d', strtotime($fecha_salida))) {
                            if (date('H:i') >= date('H:i', strtotime($hora_salida))) {
                                $mapeo->ocupado = 0;
                                $mapeo->vehiculo_id = 0;
                                $mapeo->save();
                            }
                        }

                        // AQUI SE REGISTRARAN LOS REGISTROS HISTORICOS
                        $ingreso_salida = new IngresoSalida([
                            'vehiculo_id' => $vehiculo->id,
                            'accion' => $accion_historico,
                            'hora' => date('H:i'),
                            'fecha_reg' => date('Y-m-d'),
                            'tipo' => 'HISTORICO',
                            'status' => 1,
                        ]);

                        $ingreso_salida->save();

                        // CREAR LAS NOTIFICACIONES PARA LOS USUARIOS
                        $usuarios = User::where('status', 1)
                            ->where('tipo', '!=', 'CONTROL')->get();
                        foreach ($usuarios as $value) {
                            $notificacion = new NotificacionUsuario([
                                'ingresoSalida_id' => $ingreso_salida->id,
                                'hora' => date('H:i'),
                                'fecha' => date('Y-m-d'),
                                'user_id' => $value->id,
                                'visto' => 0
                            ]);

                            $notificacion->save();
                        }

                        if ($accion_historico == 'INGRESO' && $mapeo->ocupado == 1 && $mapeo->vehiculo_id == $vehiculo->id) {
                            return response()->JSON([
                                'msg' => 'bien',
                                'vehiculo_id' => $vehiculo->id,
                                'vehiculo' => $vehiculo->nom . '  ' . $vehiculo->placa ?: 'S/P',
                                'seccion' => $vehiculo->mapeo->ubicacion->nom,
                                'mapeo' => $vehiculo->mapeo->nom,
                                'accion' => 'INGRESO HISTORICO'
                            ]);
                        }
                        return response()->JSON([
                            'msg' => 'bien',
                            'vehiculo_id' => $vehiculo->id,
                            'vehiculo' => $nom_vehiculo,
                            'modal' => '',
                            'accion' => 'SALIDA MENSUAL'
                        ]);
                    } else {
                        $modal = 'modal2';
                        return response()->JSON([
                            'msg' => 'bien',
                            'vehiculo_id' => $vehiculo->id,
                            'vehiculo' => $nom_vehiculo,
                            'modal' => $modal,
                            'accion' => $accion
                        ]);
                    }
                } elseif ($horas_tarifa < 672) {
                    // Si la tarifa es menor a mensual mostrar el modal con la 1ra ubicación disponible
                    $disponible = Mapeo::select('ubicacions.nom as seccion', 'mapeos.nom as mapeo', 'mapeos.id')
                        ->join('ubicacions', 'ubicacions.id', '=', 'mapeos.ubicacion_id')
                        ->orderBy('mapeos.ubicacion_id', 'ASC')
                        ->orderBy('mapeos.id', 'ASC')
                        ->where('mapeos.vehiculo_id', 0)
                        ->where('mapeos.ocupado', 0)
                        ->where('ubicacions.status', 1)
                        ->get()->first();
                    // BUSCAR EL MAPEO PARA MARCARLO COMO OCUPADO
                    $mapeo = Mapeo::find($disponible->id);
                    $mapeo->ocupado = 1;
                    $mapeo->vehiculo_id = $vehiculo->id;
                    $mapeo->save();
                    // registro el ingreso
                    $ingreso_salida = new IngresoSalida([
                        'vehiculo_id' => $vehiculo->id,
                        'accion' => 'INGRESO',
                        'hora' => date('H:i'),
                        'fecha_reg' => date('Y-m-d'),
                        'tipo' => 'NORMAL',
                        'status' => 1,
                    ]);

                    $ingreso_salida->save();

                    // CREAR LAS NOTIFICACIONES PARA LOS USUARIOS
                    $usuarios = User::where('status', 1)
                        ->where('tipo', '!=', 'CONTROL')->get();
                    foreach ($usuarios as $value) {
                        $notificacion = new NotificacionUsuario([
                            'ingresoSalida_id' => $ingreso_salida->id,
                            'hora' => $ingreso_salida->hora,
                            'fecha' => $ingreso_salida->fecha_reg,
                            'user_id' => $value->id,
                            'visto' => 0
                        ]);

                        $notificacion->save();
                    }

                    $modal = 'modal1';
                    return response()->JSON([
                        'msg' => 'bien',
                        'vehiculo_id' => $vehiculo->id,
                        'vehiculo' => $vehiculo->nom . '  ' . $vehiculo->placa ?: 'S/P',
                        'modal' => $modal,
                        'accion' => $accion,
                        'disponible' => $disponible
                    ]);
                }
            }

            return response()->JSON([
                'msg' => 'bien',
                'vehiculo_id' => $vehiculo->id,
                'vehiculo' => $nom_vehiculo,
                'modal' => '',
                'accion' => $accion,
            ]);
        } else {
            return response()->JSON([
                'msg' => 'Mal',
                'vehiculo_id' => '',
                'vehiculo' => '',
                'modal' => '',
                'accion' => ''
            ]);
        }
    }

    public static function registraIngresoSalidaMensual(Vehiculo $vehiculo)
    {
    }
}
