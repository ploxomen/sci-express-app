<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModeloSolicitud extends Model
{
    protected $table = 'solicitud';
    public $timestamps = false;
    

    public static function getLastId()
    {
        return ModeloSolicitud::select('id')->latest('id')->first();
    }
    public static function obtenerTotalPorProcesar()
    {
        return DB::select('call sp_obtener_solicitudes_por_procesar()');

    }
    public static function getSolicitud($estado,$id_user = 0)
    {
        $solicitudes = DB::select('CALL sp_obtener_solicitudes(?,?)',[$estado, $id_user]);
        if(!empty($solicitudes)){
            $estados_solicitud = EstadoSolicitud::where(['activado' => 1])->get();
            $usuarios = DB::select('CALL sp_obtener_usuarios_rol(?)',[3]);
            foreach ($solicitudes as $soli) {
                $soli->listSoli = $estados_solicitud;
                $soli->listUsu = $usuarios;
            }
        }
        return $solicitudes;
    }
    public static function obtenerDetalleSoli($id)
    {
        $result = DB::select('CALL sp_obtener_detalle_solicitud(?)',[$id]);
        if(isset($result[0])){
            $result[0]->format_depar_cli = (new Ubigeo)->getNameDepartamento($result[0]->depar_cli);
            $result[0]->format_ciudad_cli = (new Ubigeo)->getNameProvincia($result[0]->ciudad_cli);
            $result[0]->format_distrito_cli = (new Ubigeo)->getNameDistrito($result[0]->distrito_cli);
        }
        return isset($result[0]) ? ['success' => $result[0]] : ['info' => 'No se encontró los detalles de esta solicitud'];
    }
    public static function asignarPersonalSoli($datos)
    {
        $exist = DB::table('asignar_solicitud')->where(['id_solicitud' => $datos['id_solicitud']])->first();
        if(!empty($exist)){
            DB::table('asignar_solicitud')->where(['id_solicitud' => $datos['id_solicitud']])->update(['id_user' => $datos['id_user']]);
        }else{
            DB::table('asignar_solicitud')->insert($datos);
        }
        return 'se asignó correctamente';
    }
    public static function addSolicitud($datos,$user)
    {
        $datos['user_create'] = $user;
        $id_proveedor = 0;
        if($datos['nuevo_prov'] == 'true' && isset($datos['agregar_prov'])){
            if($datos['agregar_prov'] == 'true'){
                $proveedor = [
                    'id_cliente' => $datos['id_cliente'],
                    'tipo_docu_prov' => $datos['doc_iden_soli'],
                    'nro_docu_prov' => $datos['nro_doc_soli'],
                    'nomb_prov' => $datos['id_soli'],
                    'depa_prov' => $datos['depar_cli'],
                    'prov_prov' => $datos['ciudad_cli'],
                    'dist_prov' => $datos['distrito_cli'],
                    'dire_prov' => $datos['direccion_cli']
                ];
                $id_proveedor = DB::table('proveedor_clientes')->insertGetId($proveedor);
            }
            $datos['nom_soli'] = $datos['id_soli'];
        }
        if(!is_null($datos['id_soli']) && $datos['nuevo_prov'] == 'false'){
            $id_proveedor = intval($datos['id_soli']) === 0 ? 0 : $datos['id_soli'];
            $datos['nom_soli'] = (DB::table('proveedor_clientes')->select('nomb_prov')->where(['activo' => 1 , 'id' => $datos['id_soli']])->first())->nomb_prov;
        }
        $datos['id_soli'] = $id_proveedor;
        if (isset($datos['nuevo_prov'])) {
            unset($datos['nuevo_prov']);
        }
        if(isset($datos['agregar_prov'])) {
            unset($datos['agregar_prov']);
        }
        $id_soli = DB::table('solicitud')->insertGetId($datos);
        DB::table('recojo')->insert(['id_solicitud' => $id_soli]);
        return true;
        
    }
    public static function obtenerProveedor($id_proveedor)
    {
        $proveedor = DB::table('proveedor_clientes')->where(['activo' => 1, 'id' => $id_proveedor])->first();
        if(!empty($proveedor)){
            $proveedor->listProv = (new Ubigeo)->getProvinciasCliente($proveedor->depa_prov);
            $proveedor->listDist = (new Ubigeo)->getDistritosCliente($proveedor->prov_prov);
        }else{
            $proveedor = false;
        }
        
        return $proveedor;
    }
}
