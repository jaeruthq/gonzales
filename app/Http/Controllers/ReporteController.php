<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use torremall\Empresa;
use torremall\Reporte;

use Barryvdh\DomPDF\Facade as PDF;
use torremall\DatosUsuario;
use torremall\IngresoSalida;
use torremall\Tarifa;
use torremall\Vehiculo;
use torremall\TipoVehiculo;
use torremall\User;
use torremall\Cobro;
use torremall\Mapeo;
use torremall\Ubicacion;
use torremall\LogSeguimiento;

use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        $empresa = Empresa::first();
        $tarifas = Tarifa::all();
        $tipos = TipoVehiculo::all();
        $secciones = Ubicacion::where('status', 1)->get();
        $vehiculos = Vehiculo::all();

        if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
            return view('reportes.index', compact('empresa', 'tarifas', 'tipos', 'secciones', 'vehiculos'));
        }
        return view('errors.sin_permiso', compact('empresa'));
    }

    public function usuarios(Request $request)
    {
        $empresa = Empresa::first();
        $usuarios = DatosUsuario::join('users', 'users.id', '=', 'datos_usuarios.user_id')
            ->where('status', 1)->get();

        $pdf = PDF::loadView('reportes.r_usuarios', compact('usuarios', 'empresa'))->setPaper('letter', 'landscape');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('Usuarios.pdf');
    }

    public function logs(Request $request)
    {
        $empresa = Empresa::first();

        $logs = LogSeguimiento::orderBy('fecha', 'DESC')->get();

        $pdf = PDF::loadView('reportes.r_logs', compact('logs', 'empresa'))->setPaper('letter', 'portrait');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('LogsUsuarios.pdf');
    }

    public function vehiculos(Request $request)
    {
        $empresa = Empresa::first();

        $vehiculos = Vehiculo::where('status', 1)->get();
        $filtro = $request->filtro;
        switch ($filtro) {
            case 'FECHA':
                $fecha_ini = $request->fecha_ini;
                $fecha_fin = $request->fecha_fin;
                $valida_ini = explode('-', $fecha_ini);
                $valida_fin = explode('-', $fecha_fin);
                if (count($valida_ini) == 3 && checkdate($valida_ini[1], $valida_ini[2], $valida_ini[0]) && count($valida_fin) == 3 && checkdate($valida_fin[1], $valida_fin[2], $valida_fin[0])) {
                    $vehiculos = Vehiculo::where('status', 1)
                        ->whereBetween('fecha_reg', [$fecha_ini, $fecha_fin])->get();
                }
                break;
            case 'TIPO':
                $tipo = $request->tipo;
                if ($tipo != 'TODOS') {
                    $vehiculos = Vehiculo::where('status', 1)
                        ->where('tipo_id', $tipo)->get();
                }
                break;
            case 'TARIFA':
                $tarifa = $request->tarifa;
                if ($tarifa != 'TODOS') {
                    $vehiculos = Vehiculo::where('status', 1)
                        ->where('tarifa_id', $tarifa)->get();
                }
                break;
        }

        $pdf = PDF::loadView('reportes.r_vehiculos', compact('vehiculos', 'empresa'))->setPaper('letter', 'landscape');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('Vehiculos.pdf');
    }

    public function ingresos(Request $request)
    {
        $empresa = Empresa::first();

        $ingresos = IngresoSalida::select('ingreso_salidas.*', 'vehiculos.nom as vehiculo', 'vehiculos.foto', 'propietarios.nom', 'propietarios.apep', 'propietarios.apem')
            ->join('vehiculos', 'vehiculos.id', '=', 'ingreso_salidas.vehiculo_id')
            ->join('propietarios', 'propietarios.id', '=', 'vehiculos.propietario_id')
            ->where('ingreso_salidas.status', 1)
            ->where('accion', 'INGRESO')->get();
        $filtro = $request->filtro;
        switch ($filtro) {
            case 'FECHA':
                $fecha_ini = $request->fecha_ini;
                $fecha_fin = $request->fecha_fin;
                $valida_ini = explode('-', $fecha_ini);
                $valida_fin = explode('-', $fecha_fin);
                if (count($valida_ini) == 3 && checkdate($valida_ini[1], $valida_ini[2], $valida_ini[0]) && count($valida_fin) == 3 && checkdate($valida_fin[1], $valida_fin[2], $valida_fin[0])) {
                    $ingresos = IngresoSalida::select('ingreso_salidas.*', 'vehiculos.nom as vehiculo', 'vehiculos.foto', 'propietarios.nom', 'propietarios.apep', 'propietarios.apem')
                        ->join('vehiculos', 'vehiculos.id', '=', 'ingreso_salidas.vehiculo_id')
                        ->join('propietarios', 'propietarios.id', '=', 'vehiculos.propietario_id')
                        ->where('ingreso_salidas.status', 1)
                        ->where('accion', 'INGRESO')
                        ->whereBetween('ingreso_salidas.fecha_reg', [$fecha_ini, $fecha_fin])->get();
                }
                break;
            case 'HORA':
                $hora_ini = $request->hora_ini;
                $hora_fin = $request->hora_fin;
                if ($hora_ini != '' && $hora_fin != '' && $hora_fin != null && $hora_fin != null) {
                    $ingresos = IngresoSalida::select('ingreso_salidas.*', 'vehiculos.nom as vehiculo', 'vehiculos.foto', 'propietarios.nom', 'propietarios.apep', 'propietarios.apem')
                        ->join('vehiculos', 'vehiculos.id', '=', 'ingreso_salidas.vehiculo_id')
                        ->join('propietarios', 'propietarios.id', '=', 'vehiculos.propietario_id')
                        ->where('ingreso_salidas.status', 1)
                        ->where('accion', 'INGRESO')
                        ->whereBetween('hora', [$hora_ini, $hora_fin])->get();
                }
                break;
            case 'TIPO':
                $tipo = $request->tipo;
                if ($tipo != 'TODOS') {
                    $ingresos = IngresoSalida::select('ingreso_salidas.*', 'vehiculos.nom as vehiculo', 'vehiculos.foto', 'propietarios.nom', 'propietarios.apep', 'propietarios.apem')
                        ->join('vehiculos', 'vehiculos.id', '=', 'ingreso_salidas.vehiculo_id')
                        ->join('propietarios', 'propietarios.id', '=', 'vehiculos.propietario_id')
                        ->where('ingreso_salidas.status', 1)
                        ->where('ingreso_salidas.accion', 'INGRESO')
                        ->where('vehiculos.tipo_id', $tipo)->get();
                }
                break;
            case 'TARIFA':
                $tarifa = $request->tarifa;
                if ($tarifa != 'TODOS') {
                    $ingresos = IngresoSalida::select('ingreso_salidas.*', 'vehiculos.nom as vehiculo', 'vehiculos.foto', 'propietarios.nom', 'propietarios.apep', 'propietarios.apem')
                        ->join('vehiculos', 'vehiculos.id', '=', 'ingreso_salidas.vehiculo_id')
                        ->join('propietarios', 'propietarios.id', '=', 'vehiculos.propietario_id')
                        ->where('ingreso_salidas.status', 1)
                        ->where('ingreso_salidas.accion', 'INGRESO')
                        ->where('tarifa_id', $tarifa)->get();
                }
                break;
        }

        $pdf = PDF::loadView('reportes.r_ingresos', compact('ingresos', 'empresa'))->setPaper('letter', 'landscape');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('Ingresos.pdf');
    }

    public function salidas(Request $request)
    {
        $empresa = Empresa::first();

        $salidas = IngresoSalida::select('ingreso_salidas.*', 'vehiculos.nom as vehiculo', 'vehiculos.foto', 'propietarios.nom', 'propietarios.apep', 'propietarios.apem')
            ->join('vehiculos', 'vehiculos.id', '=', 'ingreso_salidas.vehiculo_id')
            ->join('propietarios', 'propietarios.id', '=', 'vehiculos.propietario_id')
            ->where('ingreso_salidas.status', 1)
            ->where('ingreso_salidas.tipo', '!=', 'SALIDA MENSUAL')
            ->where('accion', 'SALIDA')->get();
        $filtro = $request->filtro;
        switch ($filtro) {
            case 'FECHA':
                $fecha_ini = $request->fecha_ini;
                $fecha_fin = $request->fecha_fin;
                $valida_ini = explode('-', $fecha_ini);
                $valida_fin = explode('-', $fecha_fin);
                if (count($valida_ini) == 3 && checkdate($valida_ini[1], $valida_ini[2], $valida_ini[0]) && count($valida_fin) == 3 && checkdate($valida_fin[1], $valida_fin[2], $valida_fin[0])) {
                    $salidas = IngresoSalida::select('ingreso_salidas.*', 'vehiculos.nom as vehiculo', 'vehiculos.foto', 'propietarios.nom', 'propietarios.apep', 'propietarios.apem')
                        ->join('vehiculos', 'vehiculos.id', '=', 'ingreso_salidas.vehiculo_id')
                        ->join('propietarios', 'propietarios.id', '=', 'vehiculos.propietario_id')
                        ->where('ingreso_salidas.status', 1)
                        ->where('ingreso_salidas.tipo', '!=', 'SALIDA MENSUAL')
                        ->where('accion', 'SALIDA')
                        ->whereBetween('ingreso_salidas.fecha_reg', [$fecha_ini, $fecha_fin])->get();
                }
                break;
            case 'HORA':
                $hora_ini = $request->hora_ini;
                $hora_fin = $request->hora_fin;
                if ($hora_ini != '' && $hora_fin != '' && $hora_fin != null && $hora_fin != null) {
                    $salidas = IngresoSalida::select('ingreso_salidas.*', 'vehiculos.nom as vehiculo', 'vehiculos.foto', 'propietarios.nom', 'propietarios.apep', 'propietarios.apem')
                        ->join('vehiculos', 'vehiculos.id', '=', 'ingreso_salidas.vehiculo_id')
                        ->join('propietarios', 'propietarios.id', '=', 'vehiculos.propietario_id')
                        ->where('ingreso_salidas.status', 1)
                        ->where('ingreso_salidas.tipo', '!=', 'SALIDA MENSUAL')
                        ->where('accion', 'SALIDA')
                        ->whereBetween('hora', [$hora_ini, $hora_fin])->get();
                }
                break;
            case 'TIPO':
                $tipo = $request->tipo;
                if ($tipo != 'TODOS') {
                    $salidas = IngresoSalida::select('ingreso_salidas.*', 'vehiculos.nom as vehiculo', 'vehiculos.foto', 'propietarios.nom', 'propietarios.apep', 'propietarios.apem')
                        ->join('vehiculos', 'vehiculos.id', '=', 'ingreso_salidas.vehiculo_id')
                        ->join('propietarios', 'propietarios.id', '=', 'vehiculos.propietario_id')
                        ->where('ingreso_salidas.status', 1)
                        ->where('ingreso_salidas.tipo', '!=', 'SALIDA MENSUAL')
                        ->where('ingreso_salidas.accion', 'SALIDA')
                        ->where('vehiculos.tipo_id', $tipo)->get();
                }
                break;
            case 'TARIFA':
                $tarifa = $request->tarifa;
                if ($tarifa != 'TODOS') {
                    $salidas = IngresoSalida::select('ingreso_salidas.*', 'vehiculos.nom as vehiculo', 'vehiculos.foto', 'propietarios.nom', 'propietarios.apep', 'propietarios.apem')
                        ->join('vehiculos', 'vehiculos.id', '=', 'ingreso_salidas.vehiculo_id')
                        ->join('propietarios', 'propietarios.id', '=', 'vehiculos.propietario_id')
                        ->where('ingreso_salidas.status', 1)
                        ->where('ingreso_salidas.tipo', '!=', 'SALIDA MENSUAL')
                        ->where('ingreso_salidas.accion', 'SALIDA')
                        ->where('tarifa_id', $tarifa)->get();
                }
                break;
        }

        $pdf = PDF::loadView('reportes.r_salidas', compact('salidas', 'empresa'))->setPaper('letter', 'landscape');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('Salidas.pdf');
    }

    public function tarifas(Request $request)
    {
        $empresa = Empresa::first();
        $tarifas = Tarifa::all();
        $filtro = $request->filtro;
        switch ($filtro) {
            case 'FECHA':
                $fecha_ini = $request->fecha_ini;
                $fecha_fin = $request->fecha_fin;
                $valida_ini = explode('-', $fecha_ini);
                $valida_fin = explode('-', $fecha_fin);
                if (count($valida_ini) == 3 && checkdate($valida_ini[1], $valida_ini[2], $valida_ini[0]) && count($valida_fin) == 3 && checkdate($valida_fin[1], $valida_fin[2], $valida_fin[0])) {
                    $tarifas = Tarifa::whereBetween('fecha_reg', [$fecha_ini, $fecha_fin])->get();
                }
                break;
            case 'TARIFA':
                $tarifa = $request->tarifa;
                if ($tarifa != 'TODOS') {
                    $tarifas = Tarifa::where('id', $tarifa)->get();
                }
                break;
        }

        $pdf = PDF::loadView('reportes.r_tarifas', compact('tarifas', 'empresa'))->setPaper('letter', 'landscape');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('Tarifas.pdf');
    }

    public function cobros(Request $request)
    {
        $empresa = Empresa::first();
        $cobros = Cobro::where('status', 1)
            ->orderBy('created_at', 'asc')->get();
        $filtro = $request->filtro;
        switch ($filtro) {
            case 'FECHA':
                $fecha_ini = $request->fecha_ini;
                $fecha_fin = $request->fecha_fin;
                $valida_ini = explode('-', $fecha_ini);
                $valida_fin = explode('-', $fecha_fin);
                if (count($valida_ini) == 3 && checkdate($valida_ini[1], $valida_ini[2], $valida_ini[0]) && count($valida_fin) == 3 && checkdate($valida_fin[1], $valida_fin[2], $valida_fin[0])) {
                    $cobros = Cobro::where('status', 1)
                        ->whereBetween('fecha_reg', [$fecha_ini, $fecha_fin])
                        ->orderBy('created_at', 'asc')->get();
                }
                break;
            case 'TARIFA':
                $tarifa = $request->tarifa;
                if ($tarifa != 'TODOS') {
                    $cobros = Cobro::where('status', 1)
                        ->where('tarifa_id', $tarifa)
                        ->orderBy('created_at', 'asc')->get();
                }
                break;
        }

        $pdf = PDF::loadView('reportes.r_cobros', compact('cobros', 'empresa'))->setPaper('letter', 'landscape');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('Cobros.pdf');
    }

    public function ubicaciones(Request $request)
    {
        $empresa = Empresa::first();

        $secciones = Ubicacion::where('status', 1)->get();

        $seccion = $request->seccion;
        if ($seccion != 'TODOS') {
            $secciones = Ubicacion::where('status', 1)
                ->where('id', $seccion)->get();
        }

        $cont_disponibles = [];
        $cont_ocupados = [];
        $disponibles = [];
        $ocupados = [];
        foreach ($secciones as $value) {
            $disponibles[$value->id] = [];
            $ocupados[$value->id] = [];
            $cont_disponibles[$value->id] = 0;
            $cont_ocupados[$value->id] = 0;
            $mapeos = Mapeo::where('ubicacion_id', $value->id)->get();
            foreach ($mapeos as $mapeo) {
                if ($mapeo->ocupado == 0) {
                    $cont_disponibles[$value->id]++;
                    $disponibles[$value->id][] = $mapeo;
                } else {
                    $cont_ocupados[$value->id]++;
                    $ocupados[$value->id][] = $mapeo;
                }
            }
        }

        // return view('reportes.r_ubicaciones',compact('secciones','disponibles','ocupados','empresa'))->rendeR();

        $pdf = PDF::loadView('reportes.r_ubicaciones', compact('secciones', 'disponibles', 'ocupados', 'cont_disponibles', 'cont_ocupados', 'empresa'))->setPaper('letter', 'landscape');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('Ubicaciones.pdf');
    }

    public function cobros_grafico()
    {
        $empresa = Empresa::first();
        $tarifas = Tarifa::all();
        return view('reportes.r_cobros_grafico', compact('empresa', 'tarifas'));
    }
    public function ubicaciones_grafico()
    {
        $empresa = Empresa::first();
        $secciones = Ubicacion::where('status', 1)->get();
        return view('reportes.r_ubicaciones_grafico', compact('empresa', 'secciones'));
    }

    public function cargaCobros(Request $request)
    {
        $filtro = $request->filtro;
        $tarifas = Tarifa::all();
        $categorias = [];
        $datos = [];
        $total = 0.00;
        switch ($filtro) {
            case 'todos':
                foreach ($tarifas as $value) {
                    $categorias[] = $value->nom;
                    $suma = DB::select("SELECT SUM(total) as suma_cobro FROM cobros 
                                        WHERE tarifa_id = $value->id
                                        GROUP BY tarifa_id");
                    if (count($suma) > 0) {
                        $datos[] = [$value->nom, floatval($suma[0]->suma_cobro)];
                        $total = $total + $suma[0]->suma_cobro;
                    } else {
                        $datos[] = [$value->nom, 0];
                    }
                }
                break;
            case 'fecha':
                $fecha_ini = $request->fecha_ini;
                $fecha_fin = $request->fecha_fin;
                $valida_ini = explode('-', $fecha_ini);
                $valida_fin = explode('-', $fecha_fin);
                if (count($valida_ini) == 3 && checkdate($valida_ini[1], $valida_ini[2], $valida_ini[0]) && count($valida_fin) == 3 && checkdate($valida_fin[1], $valida_fin[2], $valida_fin[0])) {
                    foreach ($tarifas as $value) {
                        $categorias[] = $value->nom;

                        $suma = DB::select("SELECT SUM(total) as suma_cobro FROM cobros 
                                        WHERE tarifa_id = $value->id
                                        AND fecha_reg BETWEEN '" . $fecha_ini . "' AND '" . $fecha_fin . "'
                                        GROUP BY tarifa_id");
                        if (count($suma) > 0) {
                            $datos[] = [$value->nom, floatval($suma[0]->suma_cobro)];
                            $total = $total + $suma[0]->suma_cobro;
                        } else {
                            $datos[] = [$value->nom, 0];
                        }
                    }
                }
                break;
            case 'tarifa':
                $tarifa = $request->tarifa;
                if ($tarifa != 'TODOS') {
                    $tarifas = Tarifa::where('id', $tarifa)->get();
                }
                foreach ($tarifas as $value) {
                    $categorias[] = $value->nom;
                    $suma = DB::select("SELECT SUM(total) as suma_cobro FROM cobros 
                                        WHERE tarifa_id = $value->id
                                        GROUP BY tarifa_id");
                    if (count($suma) > 0) {
                        $datos[] = [$value->nom, floatval($suma[0]->suma_cobro)];
                        $total = $total + $suma[0]->suma_cobro;
                    } else {
                        $datos[] = [$value->nom, 0];
                    }
                }
                break;
        }



        return response()->json([
            'datos' => $datos,
            'categorias' => $categorias,
            'total' => $total
        ]);
    }

    public function cargaUbicaciones(Request $request)
    {
        $seccion = $request->seccion;
        $secciones = Ubicacion::where('status', 1)->get();
        if ($seccion != 'TODOS') {
            $secciones = Ubicacion::where('status', 1)
                ->where('id', $seccion)->get();
        }
        // return response()->JSON($seccion);

        $categorias = [];
        $datos = [];

        $disponibles = 0;
        $ocupados = 0;

        $array_disponibles = [];
        $array_ocupados = [];

        foreach ($secciones as $value) {
            $categorias[] = $value->nom;
            $disponibles = count(Mapeo::where('ubicacion_id', $value->id)
                ->where('ocupado', 0)->get());
            $ocupados = count(Mapeo::where('ubicacion_id', $value->id)
                ->where('ocupado', 1)->get());
            $array_disponibles[] = $disponibles;
            $array_ocupados[] = $ocupados;
        }

        $datos[] = [
            'name' => 'DISPONIBLES',
            'data' => $array_disponibles,
            'dataLabels' => [
                'enabled' => true,
                'rotation' => 0,
                'color' => '#000000',
                'align' => 'center',
                'format' => '{point.y:.0f}'
            ]
        ];

        $datos[] = [
            'name' => 'OCUPADOS',
            'data' => $array_ocupados,
            'dataLabels' => [
                'enabled' => true,
                'rotation' => 0,
                'color' => '#000000',
                'align' => 'center',
                'format' => '{point.y:.0f}'
            ]
        ];

        return response()->json([
            'datos' => $datos,
            'categorias' => $categorias,
        ]);
    }

    public function ingresos_salidas(Request $request)
    {
        $empresa = Empresa::first();
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $ingresos = IngresoSalida::where('status', 1)
            ->whereBetween('fecha_reg', [$fecha_ini, $fecha_fin])
            ->where('tipo', '!=', 'SALIDA MENSUAL')
            ->where('accion', 'INGRESO')
            ->orderBy('fecha_reg', 'desc')
            ->get();

        // JUNTAR CON SUS SALIDAS
        $array_salidas = [];
        foreach ($ingresos as $ingreso) {
            $array_salidas[$ingreso->id] = '';
            // buscar su salida
            $salida = IngresoSalida::where('status', 1)
                ->where('id', '>', $ingreso->id)
                ->where('vehiculo_id', $ingreso->vehiculo_id)
                ->where('tipo', '!=', 'SALIDA MENSUAL')
                ->orderBy('id', 'asc')
                ->get()->first();
            if ($salida) {
                $array_salidas[$ingreso->id] = $salida->hora;
            }
        }

        $pdf = PDF::loadView('reportes.r_ingresos_salidas', compact('ingresos', 'array_salidas', 'empresa'))->setPaper('letter', 'landscape');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('Ubicaciones.pdf');
    }


    public function ingresos_salidas_vehiculos(Request $request)
    {
        $empresa = Empresa::first();
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $vehi = $request->vehi;
        $ingresos = IngresoSalida::where('status', 1)
            ->whereBetween('fecha_reg', [$fecha_ini, $fecha_fin])
            ->where('vehiculo_id', $vehi)
            ->where('tipo', '!=', 'SALIDA MENSUAL')
            ->where('accion', 'INGRESO')
            ->orderBy('fecha_reg', 'desc')
            ->get();

        // JUNTAR CON SUS SALIDAS
        $array_salidas = [];
        foreach ($ingresos as $ingreso) {
            $array_salidas[$ingreso->id] = '';
            // buscar su salida
            $salida = IngresoSalida::where('status', 1)
                ->where('id', '>', $ingreso->id)
                ->where('vehiculo_id', $ingreso->vehiculo_id)
                ->where('tipo', '!=', 'SALIDA MENSUAL')
                ->orderBy('id', 'asc')
                ->get()->first();
            if ($salida) {
                $array_salidas[$ingreso->id] = $salida->hora;
            }
        }

        $pdf = PDF::loadView('reportes.r_ingresos_salidas_vehiculos', compact('ingresos', 'array_salidas', 'empresa'))->setPaper('letter', 'landscape');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('Ubicaciones.pdf');
    }
}
