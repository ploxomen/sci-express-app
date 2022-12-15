<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Clientes extends Model
{   
    protected $table = 'clientes';
    public $timestamps = false;

    public static function getClientesFiles()
    {
        return Clientes::select('id','nro_doc','nom_cli')->where(['estado' => 1])->get();
    }
    public static function deleteProveedor($id_proveedor, $id_cliente)
    {
        return DB::table('proveedor_clientes')->where(['id_cliente' => $id_cliente, 'id' => $id_proveedor])->update(['activo' => 0]);
    }
    public static function getFileCliente($nro_doc)
    {
        $result = DB::select('CALL sp_get_files_estado_cliente(?)',[$nro_doc]);
        return isset($result[0]) ? $result[0] : [];
    }
    public static function obtenerInvitados($idCliente)
    {
        return DB::table('usuario_cliente AS uc')->select('u.last_name','u.name','u.type_document','u.n_document','u.id')->join('usuarios AS u','uc.cod_usuario','=','u.id')->where(['uc.cod_cliente' => $idCliente,'uc.estado' => 1])->get();
    }
    public static function centrosCosto($idInvitado,$idCliente)
    {
        $centroCosto = DB::table('usuario_cliente')->select('centro_costo', 'cuenta')->where(['cod_usuario' => $idInvitado,'cod_cliente' => $idCliente,'estado' => 1])->first();
        $centroCostoArray = [
            'cc' => null,
            'cuenta' => null
        ];
        // dd($idInvitado,$idCliente,$centroCosto);
        switch ($centroCosto->centro_costo) {
            case 'CC':
                $cc = DB::table('clientes')->select('centros_costo')->where(['id' => $idCliente])->first();
                if(empty($cc->centros_costo)){
                    $centroCostoArray['cc'] = ['Ninguno'];
                }else{
                    $centroCostoArray['cc'] = explode(';', $cc->centros_costo);
                }
            break;
            case 'Ninguno':
                $centroCostoArray['cc'] = ['Ninguno'];
            break;
            default:
                $centroCostoArray['cc'] = [empty($centroCosto->centro_costo) ? 'Ninguno' : $centroCosto->centro_costo];
            break;
        }
        $centroCostoArray['cuenta'] = !empty($centroCosto->cuenta) ? explode(';',$centroCosto->cuenta) : ['Ninguno'];
        return $centroCostoArray;
    }
    public static function comprobarNroCuenta($idCliente,$idUsuario,$cuenta)
    {
        if(mb_strtolower($cuenta,'UTF-8') === 'ninguno'){
            return null;
        }
        $centroCosto = DB::table('usuario_cliente')->select('centro_costo', 'cuenta')->where(['cod_usuario' => $idUsuario, 'cod_cliente' => $idCliente, 'estado' => 1])->first();
        $existCuenta = !empty($centroCosto->cuenta) && strpos($centroCosto->cuenta, $cuenta) !== false ? true : false;
        $cadena = null;
        if(!$existCuenta){
            $cadena = empty($centroCosto->cuenta) ? $cuenta : $centroCosto->cuenta . ';'. $cuenta;
        }
        return $cadena;
    }
    public static function addCliente($datos)
    {
        return DB::table('clientes')->insertGetId($datos);
    }
    public static function obtenerCliConProvee()
    {
        $clientes = DB::table('clientes')->select('id', 'nom_cli')->where(['estado' => 1])->get();
        foreach ($clientes as $cli) {
            $proveedores = DB::table('proveedor_clientes AS pc')->select('pc.id', 'pc.nomb_prov')->join('relacion_cliente_proveedor AS rc', 'pc.id', '=', 'rc.id_proveedor')->where(['pc.activo' => 1, 'rc.id_cliente' => $cli->id])->get();
            $cli->proveedores = !$proveedores->isEmpty() ? $proveedores : null;
        }
        return $clientes;
    }
    public static function obtenerProveedoresSoli($id_cliente)
    {
        return DB::table('proveedor_clientes AS pc')->select('pc.*')->join('relacion_cliente_proveedor AS rc','pc.id','=','rc.id_proveedor')->where(['pc.activo' => 1, 'rc.id_cliente' => $id_cliente])->get();
    }
    public static function obtenerProveedores($id_cliente)
    {
        $proveedores = self::obtenerProveedoresSoli($id_cliente);
        foreach ($proveedores as $prove) {
            $prove->listDepa = (new Ubigeo)->getDepartamentosClientes();
            $prove->listProv = (new Ubigeo)->getProvinciasCliente($prove->depa_prov);
            $prove->listDist = (new Ubigeo)->getDistritosCliente($prove->prov_prov);
        }
        return $proveedores;
    }
    public static function addOrUpdateProveedores($user_id,$add,$update,$id)
    {
        DB::beginTransaction();
        try {
            if (!empty($add)) {
                for ($i = 0; $i < count($add); $i++) {
                    $add[$i]['id_user_create'] = $user_id;
                }
                $id_prov = DB::table('proveedor_clientes')->insertGetId($add[0]);
                DB::table('relacion_cliente_proveedor')->insert(['id_cliente' => $id,'id_proveedor' => $id_prov]);

            }
            if (!empty($update)) {
                foreach ($update as $up) {
                    $date = date('Y-m-d H:i:s');
                    DB::table('proveedor_clientes AS pc')->join('relacion_cliente_proveedor AS rc','pc.id', '=','rc.id_proveedor')->where(['pc.id' => $up['id_prove'],'rc.id_cliente' => $id])->update([
                        'pc.tipo_docu_prov' => $up['tipo_docu_prov'],
                        'pc.nro_docu_prov' => $up['nro_docu_prov'],
                        'pc.nomb_prov' => $up['nomb_prov'],
                        'pc.depa_prov' => $up['depa_prov'],
                        'pc.dist_prov' => $up['dist_prov'],
                        'pc.tele_prov' => $up['tele_prov'],
                        'pc.prov_prov' => $up['prov_prov'],
                        'pc.dire_prov' => $up['dire_prov'],
                        'pc.email_prov' => $up['email_prov'],
                        'pc.fecha_last_update' => $date,
                        'pc.id_user_last_modifi' => $user_id,
                    ]);
                }
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
        }
    }
    public static function updateEstusFile($datos,$nro_doc)
    {
        Clientes::where(['nro_doc' => $nro_doc])->update($datos);
        $estado_file = 0;
        if(Clientes::where(['nro_doc' => $nro_doc, 'aprob_file' => 1, 'aprob_cred' => 1, 'alta_clien' => 1])->first()){
            $estado_file = 1;
        }
        Clientes::where(['nro_doc' => $nro_doc])->update(['estado_doc' => $estado_file]);
        return true;
    }
    public static function getDetalleSoliCli($id)
    {
        return Clientes::select('doc_ident','nro_doc','dir_sede_adr','dpto_sa','prov_sa','dist_sa','dir_sede_so','dpto_so','prov_so','dist_so')->where(['id' => $id])->first();
    }
    public static function obtenerCli($datos)
    {
        return DB::select('CALL sp_obtener_clientes_filtros(?)', [$datos['estadoDoc']]);

    }
    public static function obtenerProveedor($id_proveedor)
    {
        $proveedor = DB::table('proveedor_clientes')->where(['activo' => 1, 'id' => $id_proveedor])->first();
        if (!empty($proveedor)) {
            $proveedor->listProv = (new Ubigeo)->getProvinciasCliente($proveedor->depa_prov);
            $proveedor->listDist = (new Ubigeo)->getDistritosCliente($proveedor->prov_prov);
        } else {
            $proveedor = false;
        }

        return $proveedor;
    }
    public static function obtenerCliDeCli()
    {
        return DB::table('clientes_de_clientes')->where(['activo' => 1])->get();
    }
    public static function detalleCliDeCli($id_cli)
    {
        return DB::table('clientes_de_clientes')->where(['id' => $id_cli, 'activo' => 1])->first();
    }
    public static function obtenerNombreCliDest($id_ciudad)
    {
        $ciudad = DB::table('ciudades')->select('id')->where(['id' => $id_ciudad])->first();
        if(empty($ciudad)){
            return '';
        }else{
            return $ciudad->id;
        }
    }
    public static function obtenerComplejoDistante($tipo,$distrito,$ciudad)
    {
        $where = [
            'activo' => 1,
            'tipo_base' => $tipo,
            'ubigeo_distrito' => $distrito
        ];
        if(!empty($ciudad)){
            $where = [
                'id_ciudad' => $ciudad
            ];
        }
        return DB::table('ciudades_base_cluster')->select('ciudad_alejada','costo_ciudad_alejada')->where($where)->first();
    }

}
