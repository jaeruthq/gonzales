<?php

use Illuminate\Database\Seeder;

use torremall\User;
use torremall\DatosUsuario;
use Illuminate\Support\Facades\Hash;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // USUARIO POR DEFECTO ADMIN
        User::create([
            'name' => 'admin',
            'password' => Hash::make('admin'),
            'tipo' => 'ADMINISTRADOR',
            'foto' => 'user_default.png',
            'status' => 1
        ]);

        // USUARIOS CON DATOS
        $user_auxiliar = User::create([
            'name' => 'JCARVAJAL',
            'password' => Hash::make('12345678'),
            'tipo' => 'AUXILIAR',
            'foto' => 'user_default.png',
            'status' => 1
        ]);


        $datos_almacen = new DatosUsuario([
            'nom' => 'JHONNY',
            'apep' => 'CARVAJAL',
            'apem' => 'MAMANI',
            'ci' => '12345678',
            'ci_exp' => 'LP',
            'dir' => 'ZONA LOS OLIVOS CALLE 4 #156',
            'fono' => '232367',
            'cel' => '78994612',
            'foto' => '155144470121002JHONNY.jpg',
        ]);

        // RELACIONANDO USUARIO CON DATOS
        $user_auxiliar->datosUsuario()->save($datos_almacen);
    }
}
