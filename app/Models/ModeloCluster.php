<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModeloCluster extends Model
{
    protected $table = 'cluster';
    public $timestamps = false;

    public static function obtenerClusterProvincia($provincia)
    {
        return DB::select('CALL sp_obtener_cluster_provincias(?)',[$provincia]);
    }
    
}
