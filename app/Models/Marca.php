<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marca';
    public $timestamps = false;
    protected $fillable = ['marca','estado'];

    public static function obtenerMarcas(){
        return Marca::where(['estado' => 1])->orderBy('marca','asc')->get();
    }
    public static function agregarMarca($valor)
    {
        return Marca::insert($valor);
    }
    public static function modificarMarca($id,$valor)
    {
        return Marca::where(['id' => $id])->update($valor);
    }
}
