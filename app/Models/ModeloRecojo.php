<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModeloRecojo extends Model
{
    protected $table = 'recojo';
    public $timestamps = false;

    public static function getRecojos($id_solicitud)
    {
        $datos = DB::select('CALL sp_obtener_recojos(?)',[$id_solicitud]);
        return isset($datos[0]) ? $datos[0] : null;
    }

}
