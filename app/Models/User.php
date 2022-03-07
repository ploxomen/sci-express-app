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
            if(isset($usuario['roles'])){
                $roles = $usuario['roles'];
                unset($usuario['roles']);
            }
            $id = User::insertGetId($usuario);
            $new_roles = [];
            foreach ($roles as $rol) {
                $new_roles[] = [
                    'id_usuario' => $id,
                    'id_rol' => $rol
                ];
            }
            DB::table('usuario_rol')->insert($new_roles);
            DB::commit();
            return ['success' => true];
        } catch (Exception $error) {
            DB::rollBack();
            return ['error' => $error->getMessage()];
        }
    }
    public static function getModulos($id_user)
    {
        return DB::select('CALL sp_obtener_modulos(?)',[$id_user]);
    }
    public function getRoles($id_user)
    {
        return false;
    }
}
