<?php

namespace App\Models;

use Exception;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    protected $table = 'usuarios';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'ty_document',
        'n_document',
        'name',
        'last_name',
        'email',
        'password',
        'phone',
        'direction',
        'nacimiento',
        'sexo',
        'status',
        'create_at',
        'update_at',
        'small_menu'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public static function addUser($usuario)
    {
        DB::beginTransaction();
        try {
            $roles = [];
            $clientesUsuarios = [];
            $idDocumentoCli = [];
            $centroCosto = [];
            $centroCuenta = [];

            if(isset($usuario['roles'])){
                $roles = $usuario['roles'];
                unset($usuario['roles']);
            }
            if (isset($usuario['id_documento_cli']) && isset($usuario['centro_costo']) && isset($usuario['centro_cuenta'])) {
                $idDocumentoCli = $usuario['id_documento_cli'];
                $centroCosto = $usuario['centro_costo'];
                $centroCuenta = $usuario['centro_cuenta'];
                unset($usuario['id_documento_cli']);
                unset($usuario['centro_costo']);
                unset($usuario['centro_cuenta']);
            }
            $id = User::insertGetId($usuario);
            for ($i = 0; $i < count($idDocumentoCli); $i++) {
                $clientesUsuarios[] = [
                    'cod_cliente' => $idDocumentoCli[$i],
                    'centro_costo' => $centroCosto[$i],
                    'cuenta' => $centroCuenta[$i],
                    'cod_usuario' => $id
                ];
            }
            $new_roles = [];
            foreach ($roles as $rol) {
                $new_roles[] = [
                    'id_usuario' => $id,
                    'id_rol' => $rol
                ];
            }
            DB::table('usuario_rol')->insert($new_roles);
            DB::table('usuario_cliente')->insert($clientesUsuarios);
            DB::commit();
            return ['success' => true];
        } catch (Exception $error) {
            DB::rollBack();
            return ['error' => $error->getMessage()];
        }
    }
    public static function getCentroCostoCliente($idCliente)
    {
        $centroCostos = DB::table('clientes')->select('centros_costo')->where(['estado' => 1,'id' => $idCliente])->first();
        return !empty($centroCostos->centros_costo) ? explode(';', $centroCostos->centros_costo) : null;
    }
    public static function misUsuarios($id_usuario)
    {
        return DB::table('usuarios AS u')->select('u.id','u.n_document', 'u.name', 'u.last_name', 'u.email', 'u.phone', 'u.status','a.area')
        ->leftJoin('area AS a','u.id_area','=','a.id')
        ->where('u.status', '!=', 0)->where("u.id","!=",1)->get();
    }
    public static function getModulos($id_user)
    {
        return DB::select('CALL sp_obtener_modulos(?)',[$id_user]);
    }
    public static function cambiarRol($id_user,$id_rol)
    {
        $usuario = DB::table('usuario_rol')->where(['id_usuario' => $id_user,'id_rol' => $id_rol])->first();
        if(empty($usuario)){
            return false;
        }
        DB::table('usuario_rol')->where(['id_usuario' => $id_user])->update(['active' => 0]);
        DB::table('usuario_rol')->where(['id_usuario' => $id_user, 'id_rol' => $id_rol])->update(['active' => 1]);
        return true;
    }
    public static function defaultSelectRol($id_usuario)
    {
        $usuario_rol = DB::table('usuario_rol')->where(['id_usuario' => $id_usuario]);
        if($usuario_rol->where('active','=',1)->count() === 0){
            DB::table('usuario_rol')->where(['id_usuario' => $id_usuario,'active' => 0])->limit(1)->update(['active' => 1]);
        }
    }
    public static function miArea($id_user)
    {
        return DB::table('usuarios AS u')->select('a.area')->join('area AS a','u.id_area','=','a.id')->where(['u.id' => $id_user])->first();
    }
    public static function getRoles($id_user)
    {
        return DB::table('usuario_rol AS ur')->select('r.id','r.rol','ur.active')->join('roles AS r','ur.id_rol','=','r.id')->where(['ur.id_usuario' => $id_user])->get();
    }
    public static function obtenerUsuarioRol($id_rol)
    {
        return DB::select('CALL sp_obtener_usuarios_rol(?)', [$id_rol]);
    }
    public static function obtenerRolActivo($id_user)
    {
        return DB::table('usuario_rol')->select('id_rol')->where(['id_usuario' => $id_user,'active' => 1])->first();
    }
    public static function obtenerDatosEditar($id_user)
    {
        $usuario = DB::table('usuarios AS u')->select('id', 'type_document', 'n_document', 'name', 'last_name', 'email', 'phone', 'sexo', 'nacimiento', 'direction', 'id_area')->where(['id' => $id_user])->first();
        $rolClienteInvitado = DB::table('usuario_rol')->where(['id_usuario' => $id_user, 'id_rol' => 10])->count();
        $response = [];
        if(!empty($usuario)){
            $response['datos'] = $usuario;
            if($rolClienteInvitado > 0){
                $response['ccInvitado'] = DB::table('usuario_cliente')->select('id','cod_cliente','centro_costo')->where(['cod_usuario' => $id_user, 'estado' => 1])->get();
                foreach ($response['ccInvitado'] as $key => $value) {
                    $response['ccInvitado'][$key]->adicional = Clientes::centrosCosto($usuario->id,$value->cod_cliente);
                }
            }
        }
        return $response;
    }
    public static function actualizarUsuario($idUsuario,$datos,$agregarCc,$actualizarCc)
    {
        DB::beginTransaction();
        try {
            foreach ($agregarCc as $add) {
                DB::table('usuario_cliente')->insert(['cod_usuario' => $idUsuario,'cod_cliente' => $add['cliente'],'centro_costo' => $add['centroCosto'],'cuenta' => $add['cuenta']]);
            }
            foreach ($actualizarCc as $update) {
                DB::table('usuario_cliente')->where(['cod_usuario' => $idUsuario,'id' => $update['idInvitado']])->update([
                    'cod_cliente' => $update['cliente'],'centro_costo' => $update['centroCosto'],'cuenta' => $update['cuenta']
                ]);
            }
            User::where(['id' => $idUsuario])->update($datos);
            DB::commit();
            return ['success' => 'usuario actualizado correctamente'];
        }catch (\Throwable $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }
    }
    public static function eliminarCcUsuarioInvitado($idUsuario,$idClienteInvitado)
    {
        return DB::table('usuario_cliente')->where(['cod_usuario' => $idUsuario, 'id' => $idClienteInvitado])->delete();
    }
    public static function obtenerUnUsuario($id_usuario)
    {
        return DB::table('usuarios')->select("id","type_document","n_document","name","last_name","email","phone","direction","nacimiento","sexo")->where(['id' => $id_usuario])->first();
    }
    public static function obtenerClientesTrabajadores($idUsuario)
    {
        return DB::table("usuario_cliente AS uc")->select('c.id','c.nom_cli','c.nro_doc')->join("clientes AS c",'c.id','=', 'uc.cod_cliente')->where(['uc.cod_usuario' => $idUsuario,'uc.estado' => 1])->get();
    }
    public static function obtenerDatosHome($id_user, $tipo)
    {
        $resultado = 1;
        switch ($tipo) {
            case 1:
                //Número de guias sin asignar
                $resultado = DB::table('guias')->where(['activo' => 1, 'estado' => 1, 'id_asignado' => null])->count();
                break;
            case 2:
                //Número de guias sin manifestar
                $resultado = DB::table('guias')->where(['activo' => 1, 'estado' => 1,'situ' => 1])->count();
            break;
            case 3:
                //Número de manifiestos sin embarcar
                $resultado = DB::table('manifiesto')->where(['estado' => 1, 'activo' => 1])->count();
            break;
            case 4:
                //Número de solicitudes sin asignar
                $resultado = DB::table('solicitud AS s')->leftJoin('asignar_solicitud AS as','s.id','=','as.id_solicitud')->where(['s.activo' => 1,'as.id_user' => null])->count();
            break;
            case 5:
                //RECOJOS PARA HOY CONDUCTOR 
                $query = "SELECT DISTINCT(COUNT(r.id)) AS total FROM recojo r INNER JOIN recojo_detalle rd ON r.id = rd.id_recojo WHERE rd.id_conductor = ? AND r.fecha_recojo = ? AND r.estado IN(1,2)";
                $resultado = DB::select($query,[$id_user,date('Y-m-d')]);
                $resultado = $resultado[0]->total;
                // $resultado = 5;
            break;
            case 6:
                //RECOJOS PARA HOY DEALER 
                $query = "SELECT DISTINCT(COUNT(r.id)) AS total FROM recojo r INNER JOIN recojo_detalle rd ON r.id = rd.id_recojo WHERE rd.id_dealer = ? AND r.fecha_recojo = ? AND r.estado IN(1,2)";
                $resultado = DB::select($query, [$id_user, date('Y-m-d')]);
                $resultado = $resultado[0]->total;
            break;
            case 7:
                //TOTAL RECOJOS DEALER 
                $resultado = DB::table('recojo_detalle AS rd')->leftJoin('recojo AS r', 'r.id', '=', 'rd.id_recojo')->where(['rd.id_dealer' => $id_user])->groupBy('rd.id_recojo')->count();
            break;
            case 8:
                //TOTAL RECOJOS CONDUCTOR 
                $resultado = DB::table('recojo_detalle AS rd')->leftJoin('recojo AS r', 'r.id', '=', 'rd.id_recojo')->where(['rd.id_conductor' => $id_user])->groupBy('rd.id_recojo')->count();
                break;
            case 9:
                //EMBARQUE PARA HOY CONDUCTOR 
                $resultado = DB::table('embarque')->where(['id_cond' => $id_user,'completado' => 0, 'fch_emba' => date('Y-m-d'), 'activo' => 1])->count();
            break;
            case 10:
                //EMBARQUE PARA HOY DEALER 
                $n = DB::select('CALL sp_obtener_embarques_cluster(?,?,?)',[$id_user,date('Y-m-d'),1]);
                $resultado = $n[0]->total;
            break;
            case 11:
                //EMBARQUEs ASIGNADOS CONDUCTOR 
                $resultado = DB::table('embarque')->where(['id_cond' => $id_user,'activo' => 1])->count();
            break;
            case 12:
                //EMBARQUEs asignados delaer 
                $n = DB::select('CALL sp_obtener_embarques_cluster(?,?,?)', [$id_user, date('Y-m-d'), 2]);
                $resultado = $n[0]->total;            
            break;
            case 13:
                //INFORMES PENDIENTES SOLO PARA DEALER
                $n = DB::select('CALL sp_obtener_informes_pendientes(?)',[$id_user]);
                $resultado = $n[0]->total;
            break;
            case 14:
                //GUIAS POR ARRIBAR PARA CONFIRMAR CLUSTER
                $query = "SELECT COUNT(*) AS total FROM guias g INNER JOIN cluster c ON g.id_cluster = c.id WHERE c.id_usuario = ? AND g.estado = 1 AND g.situ = 5 AND g.activo = 1";
                $resultado = DB::select($query, [$id_user]);
                $resultado = $resultado[0]->total;
            break;
            case 15:
                //GUIAS POR ENTREGAR CLUSTER
                $query = "SELECT COUNT(*) AS total FROM guias g LEFT JOIN cluster c ON g.id_cluster = c.id WHERE c.id_usuario = ? AND g.estado = 1 AND g.situ IN(6,7) AND g.activo = 1";
                $resultado = DB::select($query, [$id_user]);
                $resultado = $resultado[0]->total;
                break;
            case 16:
                //GUIAS POR RETORNAR CLUSTER
                $query = "SELECT COUNT(*) AS total FROM guias g LEFT JOIN cluster c ON g.id_cluster = c.id WHERE c.id_usuario = ? AND g.estado = 1 AND g.situ IN(8,9,14) AND g.activo = 1";
                $resultado = DB::select($query, [$id_user]);
                $resultado = $resultado[0]->total;
            break;
            case 17:
                //GUIAS EMBARQUE PENDIENTES DIGITADOR
                $query = "SELECT COUNT(DISTINCT rge.prefijo_guia_embarque,rge.nro_guia_embarque) AS total FROM relacion_guias_embarque rge LEFT JOIN guias g ON rge.nro_guia_embarque = g.nro_guia AND rge.prefijo_guia_embarque = g.prefijo WHERE g.id IS NULL";
                $resultado = DB::select($query);
                $resultado = $resultado[0]->total;
            break;
            case 18:
                //GUIAS ASIGNADAS POR MANIFESTAR DIGITADOR
                $resultado = DB::table('guias')->where(['situ' => 1,'id_asignado' => $id_user, 'activo' => 1 , 'estado' => 1])->count();
            break;
            case 19:
                //MANIFIESTOS PARA EMBARCAR DIGITADOR
                $resultado = DB::table('manifiesto')->where(['activo' => 1, 'id_responsable' => $id_user,'estado' => 1])->count();
            break;
            case 20:
                //SOLICITUD PENDIENTE PARA PROGRAMAR RECOJO SECTORISTA
                $query = "SELECT COUNT(*) AS total FROM asignar_solicitud asig INNER JOIN solicitud so ON asig.id_solicitud = so.id WHERE so.estado = 1 AND asig.id_user = ?";
                $resultado = DB::select($query,[$id_user]);
                $resultado = $resultado[0]->total;
            break;
            case 21:
                //TODAS LAS GUIAS POR CONFIRMAR ARRIBO SECTORISTA
                $query = "SELECT COUNT(*) AS total FROM relacion_guias_embarque rg LEFT JOIN guias g ON rg.id_guia = g.id LEFT JOIN embarque e ON rg.id_embarque = e.id WHERE g.estado = 1 AND e.activo = 1 AND g.situ = 5 AND g.activo = 1";
                $resultado = DB::select($query);
                $resultado = $resultado[0]->total;
            break;
            case 22:
                //TODAS LAS GUIAS EN REPARTO SECTORISTA
                $query = "SELECT COUNT(*) AS total FROM guias g WHERE g.estado = 1 AND g.situ IN(6,7) AND g.activo = 1";
                $resultado = DB::select($query);
                $resultado = $resultado[0]->total;
            break;
            case 23:
                //TODAS LAS GUIAS POR RETORNAR
                $query = "SELECT COUNT(*) AS total FROM guias g WHERE g.estado = 1 AND g.situ IN(8,9,14) AND g.activo = 1";
                $resultado = DB::select($query);
                $resultado = $resultado[0]->total;
            break;
            case 24:
                //RECOJOS DEL DIA PROGRAMADOS 
                $query = "SELECT COUNT(*) AS total FROM solicitud WHERE dia_recojo = ? AND activo = 1 AND estado >= 1";
                $resultado = DB::select($query, [date('Y-m-d')]);
                $resultado = $resultado[0]->total;
                break;
            case 25:
                //RECOJOS PROGRAMADOS 
                $query = "SELECT COUNT(*) AS total FROM solicitud WHERE activo = 1 AND estado >= 2";
                $resultado = DB::select($query);
                $resultado = $resultado[0]->total;
                break;
            case 26:
                //RECOJOS EFECTUADOS 
                $query = "SELECT COUNT(*) AS total FROM solicitud WHERE activo = 1 AND estado >= 4";
                $resultado = DB::select($query);
                $resultado = $resultado[0]->total;
            break;
            case 27:
                //GUIAS EMITIDAS HOY PENDIENTES PARA MANIFESTAR
                $resultado = DB::table('guias')->where(['situ' => 1,'fech_emis' => date('Y-m-d'),'activo' => 1, 'estado' => 1])->count();
            break;
            case 28:
                //EMBARQUES PENDIENTES
                $resultado = DB::table('embarque')->where(['activo' => 1, 'completado' => 0])->count();
            break;
            case 29:
                //EMBARQUES EFECTUADOS
                $query = "SELECT COUNT(*) AS total FROM embarque WHERE activo = 1 AND completado IN(1,2,3,4,5)";
                $resultado = DB::select($query);
                $resultado = $resultado[0]->total;
            break;
            case 30:
                //EMBARQUES POR INFORMAR
                $resultado = DB::table('embarque')->where(['activo' => 1, 'completado' => 1])->count();
            break;
            case 31:
                //TODAS LAS GUIAS ENTREGADAS EN DESTINO FINAL
                $query = "SELECT COUNT(*) AS total FROM guias g WHERE g.estado = 1 AND g.situ >= 8 AND g.situ != 14 AND g.activo = 1";
                $resultado = DB::select($query);
                $resultado = $resultado[0]->total;
            break;
            case 32:
                //TODAS LAS GUIAS ENTREGADAS EN DESTINO FINAL CLUSTER
                $query = "SELECT COUNT(*) AS total FROM guias g LEFT JOIN cluster c ON g.id_cluster = c.id WHERE g.estado = 1 AND c.id_usuario = ? AND g.situ >= 8 AND g.situ != 14 AND g.activo = 1";
                $resultado = DB::select($query,[$id_user]);
                $resultado = $resultado[0]->total;
            break;
            case 33:
                //TODOS LOS RECOJOS DE MES MANAGER
                $query = "SELECT DISTINCT(COUNT(r.id)) AS total FROM recojo r INNER JOIN recojo_detalle rd ON r.id = rd.id_recojo WHERE r.activo = 1 AND YEAR(r.fecha_recojo) = ? AND MONTH(r.fecha_recojo) = ?";
                $resultado = DB::select($query, [date('Y'),date('m')]);
                $resultado = $resultado[0]->total;
                break;
            case 34:
                //TODOS LOS RECOJOS CONCRETADOS DE HOY MANAGER
                $query = "SELECT DISTINCT(COUNT(r.id)) AS total FROM recojo r INNER JOIN recojo_detalle rd ON r.id = rd.id_recojo WHERE r.activo = 1 AND YEAR(r.fecha_recojo) = ? AND MONTH(r.fecha_recojo) = ? AND r.estado = 3";
                $resultado = DB::select($query, [date('Y'), date('m')]);
                $resultado = $resultado[0]->total;
                break;
            case 35:
                //TODOS LOS EMBARQUES DE HOY MANAGER
                $query = "SELECT COUNT(*) AS total FROM embarque WHERE activo = 1 AND completado >= 0 AND YEAR(fch_emba) = ? AND MONTH(fch_emba) = ?";
                $resultado = DB::select($query, [date('Y'), date('m')]);
                $resultado = $resultado[0]->total;
                break;
            case 36:
                //TODOS LOS EMBARQUES CONCRETADOS DE HOY MANAGER
                $query = "SELECT COUNT(*) AS total FROM embarque WHERE completado >= 3 AND activo = 1 AND YEAR(fch_emba) = ? AND MONTH(fch_emba) = ?";
                $resultado = DB::select($query, [date('Y'), date('m')]);
                $resultado = $resultado[0]->total;

                break;
            case 37:
                //TODAS LAS ENTREGAS DE HOY MANAGER
                $query = "SELECT COUNT(*) AS total FROM guias WHERE situ IN(7,8) AND activo = 1 AND estado = 1 AND YEAR(fecha_reparto) = ? AND MONTH(fecha_reparto) = ?";
                $resultado = DB::select($query, [date('Y'), date('m')]);
                $resultado = $resultado[0]->total;
                break;
            case 38:
                //TODAS LAS ENTREGAS CONCRETADAS DE HOY MANAGER
                $query = "SELECT COUNT(*) AS total FROM guias WHERE situ IN(8,14) AND activo = 1 AND estado = 1 AND YEAR(fecha_reparto) = ? AND MONTH(fecha_reparto) = ? AND fecha_hr_entregado IS NOT NULL";
                $resultado = DB::select($query, [date('Y'), date('m')]);
                $resultado = $resultado[0]->total;
                break;
            
        }
        return $resultado;
    }
    public static function filtrosManager($tipo,$fecha)
    {
        $resultado = 0;
        switch ($tipo) {
            case 33:
                //TODOS LOS RECOJOS DE HOY MANAGER
                $query = "SELECT DISTINCT(COUNT(r.id)) AS total FROM recojo r INNER JOIN recojo_detalle rd ON r.id = rd.id_recojo WHERE r.activo = 1 AND r.fecha_recojo = ?";
                $resultado = DB::select($query, [$fecha]);
                $resultado = $resultado[0]->total;
                break;
            case 34:
                //TODOS LOS RECOJOS CONCRETADOS DE HOY MANAGER
                $query = "SELECT DISTINCT(COUNT(r.id)) AS total FROM recojo r INNER JOIN recojo_detalle rd ON r.id = rd.id_recojo WHERE r.activo = 1 AND r.fecha_recojo = ? AND r.estado = 3";
                $resultado = DB::select($query, [$fecha]);
                $resultado = $resultado[0]->total;
                break;
            case 35:
                //TODOS LOS EMBARQUES DE HOY MANAGER
                $query = "SELECT COUNT(*) AS total FROM embarque WHERE activo = 1 AND completado >= 0 AND fch_emba = ?";
                $resultado = DB::select($query, [$fecha]);
                $resultado = $resultado[0]->total;
                break;
            case 36:
                //TODOS LOS EMBARQUES CONCRETADOS DE HOY MANAGER
                $query = "SELECT COUNT(*) AS total FROM embarque WHERE completado >= 3 AND activo = 1 AND fch_emba = ?";
                $resultado = DB::select($query, [$fecha]);
                $resultado = $resultado[0]->total;

                break;
            case 37:
                //TODAS LAS ENTREGAS DE HOY MANAGER
                $query = "SELECT COUNT(*) AS total FROM guias WHERE situ IN(7,8) AND activo = 1 AND estado = 1 AND fecha_reparto = ?";
                $resultado = DB::select($query, [$fecha]);
                $resultado = $resultado[0]->total;
                break;
            case 38:
                //TODAS LAS ENTREGAS CONCRETADAS DE HOY MANAGER
                $query = "SELECT COUNT(*) AS total FROM guias WHERE situ IN(8,14) AND activo = 1 AND estado = 1 AND fecha_reparto = ? AND fecha_hr_entregado IS NOT NULL";
                $resultado = DB::select($query, [$fecha]);
                $resultado = $resultado[0]->total;
                break;
        }
        return $resultado;
    }
}
