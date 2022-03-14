<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    public static function addCliente($datos)
    {
        return DB::table('clientes')->insertGetId($datos);
    }
    public static function obtenerProveedoresSoli($id_cliente)
    {
        return DB::table('proveedor_clientes')->where(['activo' => 1, 'id_cliente' => $id_cliente])->get();
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
                    $add[$i]['id_cliente'] = $id;
                    $add[$i]['id_user_create'] = $user_id;
                }
                DB::table('proveedor_clientes')->insert($add);
            }
            if (!empty($update)) {
                foreach ($update as $up) {
                    $date = date('Y-m-d H:i:s');
                    DB::table('proveedor_clientes')->where(['id' => $up['id_prove'], 'id_cliente' => $id])->update([
                        'tipo_docu_prov' => $up['tipo_docu_prov'],
                        'nro_docu_prov' => $up['nro_docu_prov'],
                        'nomb_prov' => $up['nomb_prov'],
                        'depa_prov' => $up['depa_prov'],
                        'dist_prov' => $up['dist_prov'],
                        'tele_prov' => $up['tele_prov'],
                        'prov_prov' => $up['prov_prov'],
                        'dire_prov' => $up['dire_prov'],
                        'email_prov' => $up['email_prov'],
                        'fecha_last_update' => $date,
                        'id_user_last_modifi' => $user_id,
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

}
