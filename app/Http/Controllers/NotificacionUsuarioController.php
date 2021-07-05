<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionUsuarioController extends Controller
{
    public function update(Request $request)
    {
        Auth::user()->notificaciones()->update([
            'visto' => 1
        ]);
        return response()->JSON(true);
    }
}