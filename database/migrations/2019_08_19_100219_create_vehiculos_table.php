<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('placa')->nullable();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('nom');
            $table->bigInteger('tipo_id')->unsigned();
            $table->bigInteger('propietario_id')->unsigned();
            $table->bigInteger('tarifa_id')->unsigned();
            $table->string('foto',155);
            $table->string('rfid')->unique();
            $table->string('fecha_reg');
            $table->timestamps();

            $table->foreign('tipo_id')->references('id')->on('tipo_vehiculos')->onDelete('no action')->onCascade('update');
            $table->foreign('propietario_id')->references('id')->on('propietarios')->onDelete('no action')->onCascade('update');
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
        Schema::dropIfExists('vehiculos');
    }
}
