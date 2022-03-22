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

    public static function agregar($guia,$detalles,$cli,$cli_bool)
    {
        DB::beginTransaction();
        try {
            $id_cli_cli = $guia['id_destinatario'];
            if(!empty($cli_bool)){
                $id_cli_cli = DB::table('clientes_de_clientes')->insertGetId([
                    'tipo_doc' => $guia['dest_tipo_docu'],
                    'nro_doc' => $guia['dest_nro_docu'],
                    'nombres' => $guia['id_destinatario'],
                    'direccion' => $guia['dest_dire'],
                    'departamento' => $guia['dest_depa'],
                    'provincia' => $guia['dest_prov'],
                    'distrito' => $guia['dest_dist']
                ]);
                if($cli_bool == 'true' && !empty($cli)){
                    DB::table('relacion_cliente_cliente')->insert(['id_cliente' => $cli,'id_cliente_de_cliente' => $id_cli_cli]);
                }
            }
            if(isset($guia['add_cli_bool'])){
                unset($guia['add_cli_bool']);
            }
            if (isset($guia['add_cli_cli'])) {
                unset($guia['add_cli_cli']);
            }
            $guia['id_destinatario'] = $id_cli_cli;
            $id_guia = DB::table('guias')->insertGetId($guia);
            $tot_cant = 0;
            $tot_kg = 0;
            $tot_vol = 0;
            for ($i=0; $i < count($detalles) ; $i++) {
                $detalles[$i]['id_guia'] = $id_guia;
                $tot_cant += floatval($detalles[$i]['cantidad']);
                $tot_kg += floatval($detalles[$i]['kg']);
                $tot_vol += floatval($detalles[$i]['volumen']);
            }
            DB::table('detalle_guias')->insert($detalles);
            DB::table('guias')->where(['id' => $id_guia])->update(['tot_cant' => $tot_cant, 'tot_kg' => $tot_kg, 'tot_vol' => $tot_vol]);
            DB::commit();
            return ['success' => 'La guía fue creada correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function obtenerGuias($filtros)
    {
        $guias = DB::select('CALL sp_obtener_guias(?,?)',[$filtros['prov_dest'], $filtros['prov_orig']]);
        for ($i=0; $i < count($guias) ; $i++) {
            $guias[$i]->dest_prov = (new Ubigeo)->getNameProvincia($guias[$i]->dest_prov);
            $guias[$i]->remi_prov = (new Ubigeo)->getNameProvincia($guias[$i]->remi_prov);
        }
        return $guias;
    }
}
