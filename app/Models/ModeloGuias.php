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
    public static function obtenerDetalleMisGuias($guia)
    {
        $guia = DB::select('CALL sp_obtener_detalle_guia(?)',[$guia]);
        if (isset($guia[0])) {
            $guia[0]->remi_depa = (new Ubigeo)->getNameDepartamento($guia[0]->remi_depa);
            $guia[0]->remi_prov = (new Ubigeo)->getNameProvincia($guia[0]->remi_prov);
            $guia[0]->remi_dist = (new Ubigeo)->getNameDistrito($guia[0]->remi_dist);
            $guia[0]->dest_depa = (new Ubigeo)->getNameDepartamento($guia[0]->dest_depa);
            $guia[0]->dest_prov = (new Ubigeo)->getNameProvincia($guia[0]->dest_prov);
            $guia[0]->dest_dist = (new Ubigeo)->getNameDistrito($guia[0]->dest_dist);
            $guia[0]->detalles_guias = DB::table('detalle_guias')->where(['id_guia' => $guia[0]->id])->get();

        }
        return isset($guia[0]) ? $guia[0] : null;
    }
    public static function actualizarEstadoGuiasCluster($id_guia,$id_usuario,$estado)
    {
        return DB::select('CALL sp_actualizar_estado_guia_cluster(?,?,?)',[$id_guia,$id_usuario,$estado]);
    }
    public static function obtenerGuiasCluster($guia, $usuario,$estado,$tipo)
    {
        $resultado = DB::select('CALL sp_obtener_guias_cluster(?,?,?,?)',[$guia,$usuario,$estado,$tipo]);
        $estados = InformacionAdicional::situacionGuiaCluster();
        foreach ($resultado as $re) {
            $re->prov_orig = (new Ubigeo)->getNameProvincia($re->prov_orig);
            $re->prov_dest = (new Ubigeo)->getNameProvincia($re->prov_dest);
            $re->lista_estados = $estados;
        }
        return $resultado;
    }
    public static function obtenerGuiasClusterDetalle($guia, $usuario, $estado, $tipo)
    {
        $guia = DB::select('CALL sp_obtener_guias_cluster(?,?,?,?)', [$guia, $usuario, $estado, $tipo]);
        if (isset($guia[0])) {
            $guia[0]->remi_depa = (new Ubigeo)->getNameDepartamento($guia[0]->remi_depa);
            $guia[0]->remi_prov = (new Ubigeo)->getNameProvincia($guia[0]->remi_prov);
            $guia[0]->remi_dist = (new Ubigeo)->getNameDistrito($guia[0]->remi_dist);
            $guia[0]->dest_depa = (new Ubigeo)->getNameDepartamento($guia[0]->dest_depa);
            $guia[0]->dest_prov = (new Ubigeo)->getNameProvincia($guia[0]->dest_prov);
            $guia[0]->dest_dist = (new Ubigeo)->getNameDistrito($guia[0]->dest_dist);
            $guia[0]->detalles_guias = DB::table('detalle_guias')->where(['id_guia' => $guia[0]->id])->get();
        }
        return $guia;
    }
    public static function obtenerGuias($filtros,$id_usuario)
    {
        $guias = DB::select('CALL sp_obtener_guias(?,?,?)',[$filtros['prov_dest'], $filtros['prov_orig'],$id_usuario]);
        for ($i=0; $i < count($guias) ; $i++) {
            $guias[$i]->dest_prov = (new Ubigeo)->getNameProvincia($guias[$i]->dest_prov);
            $guias[$i]->remi_prov = (new Ubigeo)->getNameProvincia($guias[$i]->remi_prov);
        }
        return $guias;
    }
    public static function anularGuias($guia)
    {
        return DB::table('guias')->where(['id' => $guia])->update(['estado' => 2]);
    }
    public static function cambioEstadoGuia($guia,$estado)
    {
        return DB::table('guias')->where(['estado' => 1, 'id' => $guia])->update(['situ' => $estado]);
    }
    public static function guiaPorAsignar()
    {
        return DB::select('SELECT fn_obtener_guias_para_asignar() AS total');
    }
    public static function asignarPersona($usuario,$guia)
    {
        DB::table('guias')->where(['estado' => 1, 'id' => $guia])->update(['id_asignado' => $usuario]);
        return self::guiaPorAsignar();
    }
    public static function mostrarTodasGuias($estado,$tipo)
    {
        $resultado = DB::select('CALL sp_obtener_guias_todos(?,?)',[$estado,$tipo]);
        $estados = InformacionAdicional::situacionEstado();
        $usuarios = User::obtenerUsuarioRol(3);
        foreach ($resultado as $guia) {
            $guia->dest_prov = (new Ubigeo)->getNameProvincia($guia->dest_prov);
            $guia->remi_prov = (new Ubigeo)->getNameProvincia($guia->remi_prov);
            $guia->listSoli = $estados;
            $guia->listUsu = $usuarios;
        }
        return $resultado;
    }
}
