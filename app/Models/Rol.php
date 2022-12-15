<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Rol extends Model
{
    protected $table = 'roles';
    public $timestamps = false;
    protected $fillable = ['rol','id_rol'];

    public static function getRolArea($id_area)
    {
        return DB::select('CALL sp_get_roles_area(?)',[$id_area]);
    }
    public static function obtenerRolesUsuario($id_usuario)
    {
       return DB::table('usuario_rol AS ur')->select('r.id','r.rol','ur.active')->join('roles AS r','ur.id_rol','=','r.id')->where(['ur.id_usuario' => $id_usuario])->get();
    }
    public static function modificarUsuarioRol($id_usuario,$roles,$deleteRol)
    {
        DB::beginTransaction();
        try {
            DB::table('usuario_rol')->where(['id_usuario' => $id_usuario])->delete();
            foreach ($roles as $key=>$rol) {
                $roles[$key]['id_usuario'] = $id_usuario;
                $roles[$key]['active'] = $key === 0 ? 1 : 0;
            }
            DB::table('usuario_rol')->insert($roles);
            if($deleteRol){
                DB::table('usuario_cliente')->where(['cod_usuario' => $id_usuario])->delete();
            }
            DB::commit();
            return ['success' => 'roles actualizados con exito'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
}
