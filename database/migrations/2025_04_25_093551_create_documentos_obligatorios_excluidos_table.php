<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosObligatoriosExcluidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos_obligatorios_excluidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doc_oblig_id');
            $table->integer('cliente_id');
            $table->foreign('doc_oblig_id')->on('documentos_obligatorios')->references('id')->onDelete('cascade');
            $table->foreign('cliente_id')->on('clientes')->references('id')->onDelete('cascade');
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
        Schema::dropIfExists('documentos_obligatorios_excluidos');
    }
}
