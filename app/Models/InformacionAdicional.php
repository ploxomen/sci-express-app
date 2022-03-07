<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InformacionAdicional extends Model
{
    public static function obtenerComprobantes(){
        return DB::table('comprobantes')->where(['estado' => 1])->get();
    }
    public static function viaEnvio()
    {
        return DB::table('via_envio')->where(['estado' => 1])->get();
    }
    public static function situacionEstado()
    {
        return DB::table('situacion_estado')->where(['estado' => 1])->get();
    }
}
