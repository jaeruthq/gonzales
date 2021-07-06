<?php

use Illuminate\Database\Seeder;

use torremall\Permiso;
use torremall\UserPermiso;

class PermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permiso::create([
            'modulo' => 'TODOS'
        ]);

        // UserPermiso::create([
        //     'user_id' => 1,
        //     'permiso_id' => 1,
        // ]);

        // USUARIOS
        Permiso::create([
            'modulo' => 'USUARIOS',
            'nombre' => 'users.index',
            'descripcion' => 'VER LA LISTA DE USUARIOS'
        ]);
        Permiso::create([
            'modulo' => 'USUARIOS',
            'nombre' => 'users.create',
            'descripcion' => 'CREAR USUARIOS'
        ]);
        Permiso::create([
            'modulo' => 'USUARIOS',
            'nombre' => 'users.edit',
            'descripcion' => 'EDITAR USUARIOS'
        ]);
        Permiso::create([
            'modulo' => 'USUARIOS',
            'nombre' => 'users.destroy',
            'descripcion' => 'ELIMINAR USUARIOS'
        ]);

        // TARIFAS
        Permiso::create([
            'modulo' => 'TARIFAS',
            'nombre' => 'tarifas.index',
            'descripcion' => 'VER LA LISTA DE TARIFAS'
        ]);
        Permiso::create([
            'modulo' => 'TARIFAS',
            'nombre' => 'tarifas.create',
            'descripcion' => 'CREAR TARIFAS'
        ]);
        Permiso::create([
            'modulo' => 'TARIFAS',
            'nombre' => 'tarifas.edit',
            'descripcion' => 'EDITAR TARIFAS'
        ]);
        Permiso::create([
            'modulo' => 'TARIFAS',
            'nombre' => 'tarifas.destroy',
            'descripcion' => 'ELIMINAR TARIFAS'
        ]);

        // PROPIETARIOS
        Permiso::create([
            'modulo' => 'PROPIETARIOS',
            'nombre' => 'propietarios.index',
            'descripcion' => 'VER LA LISTA DE PROPIETARIOS'
        ]);
        Permiso::create([
            'modulo' => 'PROPIETARIOS',
            'nombre' => 'propietarios.create',
            'descripcion' => 'CREAR PROPIETARIOS'
        ]);
        Permiso::create([
            'modulo' => 'PROPIETARIOS',
            'nombre' => 'propietarios.edit',
            'descripcion' => 'EDITAR PROPIETARIOS'
        ]);
        Permiso::create([
            'modulo' => 'PROPIETARIOS',
            'nombre' => 'propietarios.destroy',
            'descripcion' => 'ELIMINAR PROPIETARIOS'
        ]);

        // VEHICULOS
        Permiso::create([
            'modulo' => 'VEHICULOS',
            'nombre' => 'vehiculos.index',
            'descripcion' => 'VER LA LISTA DE VEHICULOS'
        ]);
        Permiso::create([
            'modulo' => 'VEHICULOS',
            'nombre' => 'vehiculos.create',
            'descripcion' => 'CREAR VEHICULOS'
        ]);
        Permiso::create([
            'modulo' => 'VEHICULOS',
            'nombre' => 'vehiculos.edit',
            'descripcion' => 'EDITAR VEHICULOS'
        ]);
        Permiso::create([
            'modulo' => 'VEHICULOS',
            'nombre' => 'vehiculos.destroy',
            'descripcion' => 'ELIMINAR VEHICULOS'
        ]);

        // TIPOS DE VEHICULOS
        Permiso::create([
            'modulo' => 'TIPO DE VEHICULOS',
            'nombre' => 'tipos.index',
            'descripcion' => 'VER LA LISTA DE TIPO DE VEHICULOS'
        ]);
        Permiso::create([
            'modulo' => 'TIPO DE VEHICULOS',
            'nombre' => 'tipos.create',
            'descripcion' => 'CREAR TIPO DE VEHICULOS'
        ]);
        Permiso::create([
            'modulo' => 'TIPO DE VEHICULOS',
            'nombre' => 'tipos.edit',
            'descripcion' => 'EDITAR TIPO DE VEHICULOS'
        ]);
        Permiso::create([
            'modulo' => 'TIPO DE VEHICULOS',
            'nombre' => 'tipos.destroy',
            'descripcion' => 'ELIMINAR TIPO DE VEHICULOS'
        ]);

        // SECCIONES
        Permiso::create([
            'modulo' => 'SECCIONES',
            'nombre' => 'secciones.index',
            'descripcion' => 'VER LA LISTA DE SECCIONES'
        ]);
        Permiso::create([
            'modulo' => 'SECCIONES',
            'nombre' => 'secciones.create',
            'descripcion' => 'CREAR SECCIONES - UBICACIONES'
        ]);
        Permiso::create([
            'modulo' => 'SECCIONES',
            'nombre' => 'secciones.edit',
            'descripcion' => 'EDITAR SECCIONES - UBICACIONES'
        ]);
        Permiso::create([
            'modulo' => 'SECCIONES',
            'nombre' => 'secciones.destroy',
            'descripcion' => 'ELIMINAR SECCIONES - UBICACIONES'
        ]);

        // COBROS
        Permiso::create([
            'modulo' => 'COBROS',
            'nombre' => 'cobros.index',
            'descripcion' => 'VER LA LISTA DE COBROS'
        ]);
        Permiso::create([
            'modulo' => 'COBROS',
            'nombre' => 'cobros.create',
            'descripcion' => 'REGISTRAR COBROS'
        ]);
        Permiso::create([
            'modulo' => 'COBROS',
            'nombre' => 'cobros.edit',
            'descripcion' => 'EDITAR COBROS'
        ]);
        Permiso::create([
            'modulo' => 'COBROS',
            'nombre' => 'cobros.destroy',
            'descripcion' => 'ELIMINAR COBROS'
        ]);

        // INGRESOS Y SALIDAS
        Permiso::create([
            'modulo' => 'INGRESOS Y SALIDAS',
            'nombre' => 'ingresos_salidas.index',
            'descripcion' => 'VER LA LISTA DE INGRESOS Y SALIDAS'
        ]);
        Permiso::create([
            'modulo' => 'INGRESOS Y SALIDAS',
            'nombre' => 'ingresos_salidas.create',
            'descripcion' => 'REGISTRAR INGRESOS Y SALIDAS'
        ]);

        // REPORTES
        Permiso::create([
            'modulo' => 'REPORTES',
            'nombre' => 'reportes.index',
            'descripcion' => 'VER Y GENERAR REPORTES'
        ]);

        // EMPRESA
        Permiso::create([
            'modulo' => 'EMPRESA',
            'nombre' => 'empresa.index',
            'descripcion' => 'VER INFORMACIÓN DE LA EMPRESA'
        ]);

        Permiso::create([
            'modulo' => 'EMPRESA',
            'nombre' => 'empresa.edit',
            'descripcion' => 'EDITAR INFORMACIÓN DE LA EMPRESA'
        ]);

        // USUARIOS PERMISOS
        Permiso::create([
            'modulo' => 'USUARIOS PERMISOS',
            'nombre' => 'user_permisos.index',
            'descripcion' => 'VER LA LISTA DE PERMISOS DE UN USUARIO'
        ]);

        Permiso::create([
            'modulo' => 'USUARIOS PERMISOS',
            'nombre' => 'user_permisos.edit',
            'descripcion' => 'MODIFICAR LOS PERMISOS DE USUARIOS'
        ]);
    }
}
