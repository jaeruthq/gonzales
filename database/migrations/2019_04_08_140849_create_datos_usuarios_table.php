<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatosUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datos_usuarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nom');
            $table->string('apep');
            $table->string('apem')->nullable();
            $table->string('ci');
            $table->string('ci_exp');
            $table->string('dir');
            $table->string('email')->nullable();
            $table->string('fono')->nullable();
            $table->string('cel');
            $table->string('foto');
            $table->bigInteger('user_id')->unsigned();
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
        Schema::dropIfExists('datos_usuarios');
    }
}
