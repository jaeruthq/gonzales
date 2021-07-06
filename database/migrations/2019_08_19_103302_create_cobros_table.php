<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCobrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cobros', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('salida_id')->unsigned();
            $table->bigInteger('vehiculo_id')->unsigned();
            $table->bigInteger('tarifa_id')->unsigned();
            $table->integer('tiempo_cobrado');
            $table->date('fecha_reg');
            $table->time('hora');
            $table->time('hora_ingreso');
            $table->date('fecha_ingreso');
            $table->decimal('total',24,2);
            $table->integer('status');
            $table->timestamps();

            $table->foreign('salida_id')->references('id')->on('ingreso_salidas')->onDelete('no action')->onCascade('update');
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onDelete('no action')->onCascade('update');
            $table->foreign('tarifa_id')->references('id')->on('tarifas')->onDelete('no action')->onCascade('update');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cobros');
    }
}
