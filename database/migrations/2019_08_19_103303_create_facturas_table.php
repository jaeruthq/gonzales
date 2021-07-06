<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cobro_id')->unsigned();
            $table->bigInteger('nro_factura');
            $table->string('codigo_control',20);
            $table->string('qr',50);
            $table->decimal('total',24,2);
            $table->date('fecha');
            $table->time('hora');
            $table->date('fecha_emision');
            $table->integer('estado');
            $table->string('observacion',100)->nullable();
            $table->timestamps();

            $table->foreign('cobro_id')->references('id')->on('cobros')->onDelete('no action')->onCascade('update');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facturas');
    }
}
