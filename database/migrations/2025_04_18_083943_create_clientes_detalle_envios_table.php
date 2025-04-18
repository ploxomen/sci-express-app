<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesDetalleEnviosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes_detalle_envios', function (Blueprint $table) {
            $table->id();
            $table->integer('cliente_id');
            $table->string('envase_embalaje');
            $table->decimal('largo',11,2);
            $table->decimal('ancho',11,2);
            $table->decimal('altura',11,2);
            $table->foreign('cliente_id')->references('id')->on('clientes');
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
        Schema::dropIfExists('clientes_detalle_envios');
    }
}
