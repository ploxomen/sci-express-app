<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModeloGuias extends Model
{
    protected $table = 'guias';
    public $timestamps = false;

    public static function agregar($guia,$detalles)
    {
        DB::beginTransaction();
        try {
            $id_guia = DB::table('guias')->insertGetId($guia);
            for ($i=0; $i < count($detalles) ; $i++) {
                $detalles[$i]['id_guia'] = $id_guia;
            }
            DB::table('detalle_guias')->insert($detalles);
            DB::commit();
            return ['success' => 'La guía fue creada correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function obtenerGuias($filtros)
    {
        $guias = DB::select('CALL sp_obtener_guias(?)',[$filtros['prov_dest']]);
        for ($i=0; $i < count($guias) ; $i++) {
            $guias[$i]->dest_prov = (new Ubigeo)->getNameProvincia($guias[$i]->dest_prov);
        }
        return $guias;
    }
}
