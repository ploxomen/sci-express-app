<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDetalleGuiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detalle_guias', function (Blueprint $table){
            $table->string('embase_embalaje_SUNAT')->after('embase_embalaje')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detalle_guias', function (Blueprint $table){
            $table->dropColumn('embase_embalaje_SUNAT');
        });
    }
}
