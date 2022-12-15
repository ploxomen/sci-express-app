<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModelFacturacion extends Model
{
    public static function obtenerGuiasFacturacion($cliente,$traslado,$fechaInicio,$fechaFin)
    {
        $guias = DB::select('CALL sp_facturacion_ver_guias(?,?,?,?)',[$cliente, $fechaInicio,$fechaFin,$traslado]);
        foreach ($guias as $guia) {
            if($guia->tipo_base == 'distrito'){
                $guia->baseCluster = (new Ubigeo)->getNameDistrito($guia->baseCluster);
            }
            if (is_null($guia->ciudadFinal)) {
                $guia->ciudadFinal = (new Ubigeo)->getNameDistrito($guia->dest_dist);
            }
            $guia->descripcionGuia = json_decode($guia->descripcionGuia,true);
        }
        return $guias;
    }
}
