<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmbarqueCiudadBaseImgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('embarque_ciudad_base_imgs', function (Blueprint $table) {
            $table->id();
            $table->integer('id_ciudad_llegada');
            $table->string('nombre_original');
            $table->string('nombre_unico');
            $table->text('ruta');
            $table->string('tipo_archivo');
            $table->foreign('id_ciudad_llegada')->on('embarque_ciudad_llegada')->references('id');
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
        Schema::dropIfExists('embarque_ciudad_base_imgs');
    }
}
