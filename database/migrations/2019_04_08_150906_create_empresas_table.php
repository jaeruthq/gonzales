<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cod');
            $table->string('nit');
            $table->string('nro_aut');
            $table->string('nro_emp');
            $table->string('name');
            $table->string('alias');
            $table->string('pais');
            $table->string('dpto');
            $table->string('ciudad');
            $table->string('zona');
            $table->string('calle');
            $table->string('nro');
            $table->string('email')->nullable();
            $table->string('fono');
            $table->string('cel');
            $table->string('fax')->nullable();
            $table->string('casilla')->nullable();
            $table->string('web')->nullable();
            $table->string('logo',155);
            $table->string('actividad_eco');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresas');
    }
}
