<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetornoCargoImgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retorno_cargo_img', function (Blueprint $table) {
            $table->id();
            $table->integer('retorno_cargo_id');
            $table->string('nombre');
            $table->string('url');
            $table->foreign('retorno_cargo_id')->on('retorno_cargo')->references('id');
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
        Schema::dropIfExists('retorno_cargo_img');
    }
}
