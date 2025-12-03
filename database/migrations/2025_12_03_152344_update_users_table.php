<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuarios',function(Blueprint $table){
            $table->string('driver_license')->nullable()->after('direction');
        });
        Schema::table('vehiculos',function(Blueprint $table){
            $table->string('TUSE')->nullable()->after('year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usuarios',function(Blueprint $table){
            $table->dropColumn('driver_license');
        });
        Schema::table('vehiculos',function(Blueprint $table){
            $table->dropColumn('TUSE');
        });
    }
}
