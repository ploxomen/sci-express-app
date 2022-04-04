<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InformacionAdicional extends Model
{

    public static function viaEnvio()
    {
        return DB::table('via_envio')->where(['estado' => 1])->get();
    }
    public static function situacionEstado()
    {
        return DB::table('situacion_estado')->where(['estado' => 1])->get();
    }
    public static function situacionGuiaCluster()
    {
        return DB::table('situacion_estado')->where(['estado' => 1])->whereIn('id',[5,6,7,8,9,10])->get();
    }
    public static function obtenerEmpresaTrasnporte($id)
    {
        return DB::table('empresa_transporte')->where(['id' => $id, 'activo' => 1])->first();
    }
    public static function obtenerEmpresasTrasnportes()
    {
        return DB::table('empresa_transporte')->where(['activo' => 1])->get();
    }
    public static function agregarEmpresaTransporte($datos)
    {
        return DB::table('empresa_transporte')->insert($datos);
    }
    public static function actualizarEmpresaTransporte($datos,$id)
    {
        return DB::table('empresa_transporte')->where(['activo' => 1, 'id' => $id])->update($datos);
    }
    public static function eliminarEmpresaTransporte($id)
    {
        return DB::table('empresa_transporte')->where(['activo' => 1, 'id' => $id])->update(['activo' => 0]);
    }
}
