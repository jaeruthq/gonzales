<?php

namespace torremall\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use torremall\Empresa;
use torremall\LogSeguimiento;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $empresa = Empresa::first();
        if (Auth::user()->tipo == 'ADMINISTRADOR') {
            return view('empresa.index', compact('empresa'));
        }
        return view('errors.sin_permiso', compact('empresa'));
    }

    public function edit(Empresa $empresa)
    {
        $empresa = Empresa::first();
        if (Auth::user()->tipo == 'ADMINISTRADOR' || Auth::user()->tipo == 'AUXILIAR') {
            return view('empresa.edit', compact('tipo', 'empresa'));
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
    public function update(Request $request, Empresa $empresa)
    {
        $empresa->update(array_map('mb_strtoupper', $request->except('logo')));
        if ($request->hasFile('logo')) {
            $antiguo = $empresa->logo;
            \File::delete(public_path() . '/imgs/empresa/' . $antiguo);

            $file = $request->file('logo');
            $extension = '.' . $file->getClientOriginalExtension();
            $nom_file = $empresa->name . '_' . time() . $extension;
            $file->move(public_path() . '/imgs/empresa/', $nom_file);
            $empresa->logo = $nom_file;
            $empresa->save();
        }

        LogSeguimiento::create([
            'user_id' => Auth::user()->id,
            'accion' => 'MODIFICACIÓN',
            'descripcion' => 'EL USUARIO ' . Auth::user()->name . ' MODIFICÓ INFORMACIÓN DE LA EMPRESA',
            'modulo' => 'EMPRESA',
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);

        return redirect()->route('empresa.edit', $empresa->id)->with('editado', 'exito');
    }
}
