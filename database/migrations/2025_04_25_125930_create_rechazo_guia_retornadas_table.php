<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRechazoGuiaRetornadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rechazo_guia_retornadas', function (Blueprint $table) {
            $table->id();
            $table->integer('guia_id');
            $table->integer('usuario_id');
            $table->text('opciones_denegado');
            $table->text('detalles')->nullable();
            $table->foreign('guia_id')->on('guias')->references('id');
            $table->foreign('usuario_id')->on('usuarios')->references('id');
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
        Schema::dropIfExists('rechazo_guia_retornadas');
    }
}
