<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaTransporte extends Model
{
    protected $table = "empresa_transporte";
    public $timestamps = false;

    public static function obtenerTransportes()
    {
        return EmpresaTransporte::where(['activo' => 1])->orderBy('empresa')->get();
    }
}
