<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIngresoSalidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingreso_salidas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('vehiculo_id')->unsigned();
            $table->enum('accion',['INGRESO','SALIDA']);
            $table->time('hora');
            $table->string('observacion',155)->nullable();
            $table->date('fecha_reg');
            $table->enum('tipo',['NORMAL','INGRESO MENSUAL','SALIDA MENSUAL','HISTORICO']);
            $table->integer('status');
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
        Schema::dropIfExists('ingreso_salidas');
    }
}
