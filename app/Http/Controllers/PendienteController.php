<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;

use torremall\Pendiente;
use torremall\Vehiculo;

class PendienteController extends Controller
{
    public function obtienePendiente()
    {   
        $pendiente = Pendiente::where('registrado','NO')->get()->first();
        if($pendiente)
        {
            return response()->JSON([
                'msg' => 'SI',
                'rfid' => $pendiente->rfid
            ]);
        }
        else{
            return response()->JSON([
                'msg' => 'NO',
                'rfid' => ''
            ]);
        }
    }

    public function store(Request $request)
    {
        Pendiente::create([
            'rfid' => $request->rfid,
            'registrado' => 'NO' 
        ]);

        return response()->JSON(true);
    }

    public function compruebaRegistro(Request $request)
    {
        $_rfid = $request->rfid;
        $vehiculo = Vehiculo::where('rfid',$_rfid)->get()->first();
        if($vehiculo)
        {
            return response()->JSON(true);
        }
        return response()->JSON(false);
    }   
}
