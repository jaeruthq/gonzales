<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificacionUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificacion_usuarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ingresoSalida_id')->unsigned();
            $table->time('hora');
            $table->date('fecha');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('visto')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action')->onCascade('update');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notificacion_usuarios');
    }
}
