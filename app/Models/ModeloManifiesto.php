<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModeloManifiesto extends Model
{
    protected $table = 'manifiesto';
    public $timestamps = false;

    public static function agregarManifiesto($manifiesto,$guias)
    {
        DB::beginTransaction();
        try {
            $id_manifiesto = DB::table('manifiesto')->insertGetId($manifiesto);
            for ($i=0; $i < count($guias) ; $i++) {
                $guias[$i]['id_manifiesto'] = $id_manifiesto;
            }
            DB::table('detalle_manifiesto')->insert($guias);
            foreach ($guias as $n_guia) {
                DB::table('guias')->where(['id' => $n_guia['id_guia']])->update(['situ' => 2]);
            }
            DB::commit();
            return ['success' => 'Manifiesto agregado correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function embarcarManifiesto($datos,$id_user)
    {
        DB::beginTransaction();
        try {
            if (isset($datos['nosotros'])) {
                $datos['nosotros'] = 1;
            } else {
                $datos['nosotros'] = 0;
            }
            $transportes = isset($datos['id_empr_tran']) ? $datos['id_empr_tran'] : null;
            $manifiestos = isset($datos['lista_manifiestos']) ? json_decode($datos['lista_manifiestos'], true) : null;
            if (isset($datos['id_empr_tran'])) {
                unset($datos['id_empr_tran']);
            }
            if (isset($datos['lista_manifiestos'])) {
                unset($datos['lista_manifiestos']);
            }
            $datos['user_create'] = $id_user;
            $fletes_cluster = json_decode($datos['flestes_cluster'],true);
            unset($datos['flestes_cluster']);

            $id_embarque = DB::table('embarque')->insertGetId($datos);
            for ($i=0; $i < count($fletes_cluster); $i++) {
                $fletes_cluster[$i]['id_embarque'] = $id_embarque;
            }
            DB::table('fletes_embarque')->insert($fletes_cluster);
            if (!empty($transportes)) {
                $newTrasn = [];
                for ($i = 0; $i < count($transportes); $i++) {
                    $newTrasn[] = ['id_empr_tran' => $transportes[$i], 'id_list_emba' => $id_embarque];
                }
                DB::table('transporte_embarque')->insert($newTrasn);
            }
            if (!empty($manifiestos)) {
                for ($i = 0; $i < count($manifiestos); $i++) {
                    $manifiestos[$i]['id_embarque'] = $id_embarque;
                }
                DB::table('embarque_manifiesto')->insert($manifiestos);
            }
            DB::select('CALL sp_embarque_actualizar_estado(?)',[$id_embarque]);
            DB::commit();
            return ['success' => 'Embarque programado con exito'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function obtenerEmbarcar($ciudad_base,$tipo,$id_usuario)
    {
        $manifiestos = DB::select('call sp_obtener_manifiesto_embarcar(?,?,?)',[$ciudad_base,$tipo, $id_usuario]);
        foreach ($manifiestos as $m) {
            $m->prov_orig = (new Ubigeo)->getNameProvincia($m->prov_orig);
            $m->prov_dest = (new Ubigeo)->getNameProvincia($m->prov_dest);
            if($m->tipo_base == 'distrito'){
                $m->nombre_ciudad_base = (new Ubigeo)->getNameDistrito($m->nombre_ciudad_base);
            }
        }
        return $manifiestos;
    }
}
