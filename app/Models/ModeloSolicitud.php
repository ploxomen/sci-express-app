<?php

namespace App\Models;

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

    public static function getSolicitud($estado,$cliente)
    {
        return DB::select('CALL sp_obtener_solicitudes(?,?)',[$estado,$cliente]);
    }
}
