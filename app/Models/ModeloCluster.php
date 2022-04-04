<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModeloCluster extends Model
{
    protected $table = 'cluster';
    public $timestamps = false;

    public static function obtenerClusterProvincia($provincia)
    {
        return DB::select('CALL sp_obtener_cluster_provincias(?)',[$provincia]);
    }
    public static function agregarNuevoCluster($usuario,$cluster)
    {
        DB::beginTransaction();
        try {
            $id_usuario = DB::table('usuarios')->insertGetId($usuario);
            $cluster['id_usuario'] = $id_usuario;
            DB::table('cluster')->insert($cluster);
            DB::commit();
            return ['success' => 'Cluster agregado con correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function obtenerCluster($id)
    {
        $datos = DB::select('CALL sp_mis_clusters(?)',[$id]);
        if($id != 0 && isset($datos[0])){
            $datos[0]->lista_provincia_clu = (new Ubigeo)->getProvinciasCliente($datos[0]->departamento_clu);
            $datos[0]->lista_distrito_clu = (new Ubigeo)->getDistritosCliente($datos[0]->provincia_clu);
            return $datos[0];
        }
        if($id == 0){
            foreach ($datos as $val) {
                $val->departamento_clu = (new Ubigeo)->getNameDepartamento($val->departamento_clu);
                $val->provincia_clu = (new Ubigeo)->getNameProvincia($val->provincia_clu);
                $val->distrito_clu = (new Ubigeo)->getNameDistrito($val->distrito_clu);
            }
        }
        return $datos;
    }
    public static function updateCluster($id,$datos)
    {
        return DB::table('cluster AS c')->leftJoin('usuarios AS usu', 'c.id_usuario','=', 'usu.id')->where(['c.id' => $id])->update($datos);
    }
    public static function eliminarCluster($id)
    {
        return DB::table('cluster AS c')->leftJoin('usuarios AS usu', 'c.id_usuario', '=', 'usu.id')->where(['c.id' => $id])->update(['usu.status' => 0]);
    }
    
}
