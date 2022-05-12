<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModeloRecojo extends Model
{
    protected $table = 'recojo';
    public $timestamps = false;

    public static function getRecojos($id_solicitud)
    {
        $datos = DB::select('CALL sp_obtener_recojos(?)',[$id_solicitud]);
        return isset($datos[0]) ? $datos[0] : null;
    }
    public static function getRecojosDriDea($id_trabajor)
    {
        $solicitudes = DB::select('CALL sp_obtener_solicitudes_trabajador(?)',[$id_trabajor]);
        foreach ($solicitudes as $soli) {
            $soli->depar_cli = (new Ubigeo)->getNameDepartamento($soli->depar_cli);
            $soli->ciudad_cli = (new Ubigeo)->getNameProvincia($soli->ciudad_cli);
            $soli->distrito_cli = (new Ubigeo)->getNameDistrito($soli->distrito_cli);
        }
        return $solicitudes;
    }
    public static function updateRecoTraba($id,$bulto,$doc,$nro_guias,$id_user)
    {
        $hora = date('H:i');
        return DB::select('CALL sp_recojo_update_trabajador(?,?,?,?,?,?)',[$id,$bulto,$doc,$nro_guias,$id_user,$hora]);
    }

}
