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
    public static function obtenerCiudadesFinales($id_cluster)
    {
        $clusters = DB::select('CALL sp_obtener_cluster_destinos_finales(?)',[$id_cluster]);
        foreach ($clusters as $cluster) {
            if($cluster->tipo_base == 'distrito'){
                $cluster->nombre_ciudad = (new Ubigeo)->getNameDistrito($cluster->nombre_ciudad);
            }
        }
        return $clusters;
    }
    public static function obtenerUnClusterProvincia($provincia)
    {
        return self::select('usuarios.name', 'usuarios.last_name', 'cluster.id')->join('usuarios', 'cluster.id_usuario', '=', 'usuarios.id')->where(['cluster.activo' => 1, 'cluster.provincia_clu' => $provincia])->get();
    }
    public static function getCodigoCluster($id_usuario)
    {
        return DB::table('cluster')->select('id')->where(['id_usuario' => $id_usuario])->first();
    }
    public static function agregarNuevoCluster($usuario,$cluster,$ciudades_base)
    {
        DB::beginTransaction();
        try {
            $id_usuario = DB::table('usuarios')->insertGetId($usuario);
            $cluster['id_usuario'] = $id_usuario;
            $id_cluster = DB::table('cluster')->insertGetId($cluster);
            $ciudades_destino = [];
            foreach ($ciudades_base as $valor) {
                $destinos = [
                    'id_cluster' => $id_cluster, 
                    'tipo_base' => !empty($valor->ciudad) ? 'ciudad' : 'distrito', 
                    'ubigeo_distrito' => $valor->distrito, 
                    'id_ciudad' => null,
                    'mov1' => $valor->mov1,
                    'ciudad_alejada' => $valor->ciudad_alejada,
                    'costo_ciudad_alejada' => floatval($valor->costo_ciudad_alejada),
                    'base' => $valor->ciudad_base
                ];
                if(!empty($valor->ciudad)){
                    $exist_ciudad = DB::table('ciudades')->select('id')->where(['ubigeo_distrito' => $valor->distrito, 'id' => $valor->ciudad])->first();
                    if (empty($exist_ciudad)) {
                        $new_base_ciudad = DB::table('ciudades')->insertGetId(['ubigeo_distrito' => $valor->distrito, 'nombre_ciudad' => $valor->ciudad]);
                        $destinos['id_ciudad'] = $new_base_ciudad;
                    }else{
                        $destinos['id_ciudad'] = $exist_ciudad->id;
                    }        
                }
                $ciudades_destino[] = $destinos;
            }
            DB::table('usuario_rol')->insert(['id_usuario' => $id_usuario,'id_rol' => 8, 'active' => 1]);
            DB::table('ciudades_base_cluster')->insert($ciudades_destino);
            DB::commit();
            return ['success' => 'Cluster agregado correctamente'];
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
            $datos[0]->ciudades = DB::table('ciudades_base_cluster')->select('id','tipo_base','ubigeo_distrito','mov1','id_ciudad','base', 'ciudad_alejada', 'costo_ciudad_alejada')->where(['id_cluster' => $datos[0]->cluster_id,'activo' => 1])->get();
            foreach ($datos[0]->ciudades as $city) {
                $city->listCity = DB::table('ciudades')->select('id','nombre_ciudad')->where(['ubigeo_distrito' => $city->ubigeo_distrito])->get();
            }
            $datos[0]->distritos = (new Ubigeo)->getDistritosCliente($datos[0]->provincia_clu);
            return $datos[0];
        }
        if($id == 0){
            foreach ($datos as $val) {
                if($val->tipo_base_cluster == 'distrito'){
                    $val->ciudad_base = (new Ubigeo)->getNameDistrito($val->ciudad_base);
                }
                $val->departamento_clu = (new Ubigeo)->getNameDepartamento($val->departamento_clu);
                $val->provincia_clu = (new Ubigeo)->getNameProvincia($val->provincia_clu);
            }
        }
        return $datos;
    }
    public static function updateCluster($id,$datos,$actualizar,$eliminar,$agregar)
    {
        DB::beginTransaction();
        try {
            DB::table('ciudades_base_cluster')->where(['id_cluster' => $id])->update(['base' => 0]);
            foreach ($eliminar as $valor_eliminar) {
                DB::table('ciudades_base_cluster')->where(['id_cluster' => $id,'id' => $valor_eliminar])->update(['activo' => 0, 'base' => 0]);
            }
            foreach ($agregar as $valor_agregar) {
                $destinos = [
                    'id_cluster' => $id,
                    'tipo_base' => !empty($valor_agregar->ciudad) ? 'ciudad' : 'distrito',
                    'ubigeo_distrito' => $valor_agregar->distrito,
                    'id_ciudad' => null,
                    'base' => $valor_agregar->ciudad_base,
                    'mov1' => $valor_agregar->mov1,
                    'ciudad_alejada' => $valor_agregar->ciudad_alejada,
                    'costo_ciudad_alejada' => !empty($valor_agregar->costo_ciudad_alejada) ? $valor_agregar->costo_ciudad_alejada : 0
                ];
                if (!empty($valor_agregar->ciudad)) {
                    $exist_ciudad = DB::table('ciudades')->select('id')->where(['ubigeo_distrito' => $valor_agregar->distrito, 'id' => $valor_agregar->ciudad])->first();
                    if (empty($exist_ciudad)) {
                        $new_base_ciudad = DB::table('ciudades')->insertGetId(['ubigeo_distrito' => $valor_agregar->distrito, 'nombre_ciudad' => $valor_agregar->ciudad]);
                        $destinos['id_ciudad'] = $new_base_ciudad;
                    } else {
                        $destinos['id_ciudad'] = $exist_ciudad->id;
                    }
                }
                DB::table('ciudades_base_cluster')->insert($destinos);
            }
            foreach ($actualizar as $valor_actualizar) {
                $ciudad_actua = [
                    'tipo_base' => !empty($valor_actualizar->ciudad) ? 'ciudad' : 'distrito',
                    'ubigeo_distrito' => $valor_actualizar->distrito,
                    'id_ciudad' => null,
                    'base' => $valor_actualizar->ciudad_base,
                    'mov1' => $valor_actualizar->mov1,
                    'ciudad_alejada' => $valor_actualizar->ciudad_alejada,
                    'costo_ciudad_alejada' => !empty($valor_actualizar->costo_ciudad_alejada) ? $valor_actualizar->costo_ciudad_alejada : 0
                ];
                if (!empty($valor_actualizar->ciudad)) {
                    $exist_ciudad = DB::table('ciudades')->select('id')->where(['ubigeo_distrito' => $valor_actualizar->distrito, 'id' => $valor_actualizar->ciudad])->first();
                    if (empty($exist_ciudad)) {
                        $new_base_ciudad = DB::table('ciudades')->insertGetId(['ubigeo_distrito' => $valor_actualizar->distrito, 'nombre_ciudad' => $valor_actualizar->ciudad]);
                        $ciudad_actua['id_ciudad'] = $new_base_ciudad;
                    } else {
                        $ciudad_actua['id_ciudad'] = $exist_ciudad->id;
                    }
                }
                DB::table('ciudades_base_cluster')->where(['id_cluster' => $id, 'id' => intval($valor_actualizar->id_ciudad)])->update($ciudad_actua);
            }
            DB::table('cluster AS c')->leftJoin('usuarios AS usu', 'c.id_usuario', '=', 'usu.id')->where(['c.id' => $id])->update($datos);
            DB::commit();
            return ['success' => 'Cluster actualizado correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function eliminarCluster($id)
    {
        return DB::table('cluster AS c')->leftJoin('usuarios AS usu', 'c.id_usuario', '=', 'usu.id')->where(['c.id' => $id])->update(['usu.status' => 0]);
    }
    public static function obtenerTodosClusters()
    {
        $clusters = DB::table('cluster AS c')->select('u.id','c.provincia_clu','c.id AS id_cluster','u.name','u.last_name','u.status')->join('usuarios AS u','c.id_usuario','=','u.id')->where(['c.activo' => 1])->whereIn("u.status",[1,2])->get();
        foreach ($clusters as $cluster) {
            $cluster->nombreProvincia = mb_strtoupper((new Ubigeo)->getNameProvincia($cluster->provincia_clu),"UTF-8");
        }
        return $clusters;
    }
    public static function obtenerTodosClustersFacturacion()
    {
        $clusters = DB::table('cluster AS c')->select('c.provincia_clu','c.id AS id_cluster','u.name','u.last_name','u.status')->join('usuarios AS u','c.id_usuario','=','u.id')->where("u.status","!=",0)->groupBy("u.type_document","u.n_document","u.name","u.last_name")->get();
        foreach ($clusters as $cluster) {
            $cluster->nomProv = mb_strtoupper((new Ubigeo())->getNameProvincia($cluster->provincia_clu),'UTF-8');
        }
        return $clusters;
    }
    public static function obtenerCiudadesBase(int|null $idCluster = null, bool $agruparDistrito = false){
        $ciudades = DB::table("ciudades_base_cluster AS cl")->select("cl.tipo_base","cl.ubigeo_distrito","c.nombre_ciudad","cl.ciudad_alejada", "cl.id AS id_ciudad_base")->leftJoin("ciudades AS c","cl.id_ciudad","=","c.id")->where(['cl.activo' => 1]);
        if($agruparDistrito){
            $ciudades->groupBy('cl.ubigeo_distrito');
        }
        if(!empty($idCluster)){
            $ciudades = $ciudades->where('cl.id_cluster',$idCluster);
        }
        $ciudades = $ciudades->get();
        foreach ($ciudades as $ciudad) {
            $ciudad->nombre_ciudad = mb_strtoupper($ciudad->tipo_base == "distrito" ? (new Ubigeo)->getNameDistrito($ciudad->ubigeo_distrito) : $ciudad->nombre_ciudad,"UTF-8");
        }
        return $ciudades;
    }
    public static function ciudadesClusters($idsClusters)
    {
        $ciudades = DB::table("ciudades_base_cluster AS cl")->select("cl.id","cl.id_ciudad","cl.tipo_base","cl.ubigeo_distrito","c.nombre_ciudad","cl.ciudad_alejada")->leftJoin("ciudades AS c","cl.id_ciudad","=","c.id")->where(['cl.activo' => 1])->whereIn("cl.id_cluster",$idsClusters)->get();
        foreach ($ciudades as $ciudad) {
            $ciudad->nombre_ciudad = mb_strtoupper($ciudad->tipo_base == "distrito" ? (new Ubigeo)->getNameDistrito($ciudad->ubigeo_distrito) : $ciudad->nombre_ciudad,"UTF-8");
        }
        return $ciudades;
    }
    
}
