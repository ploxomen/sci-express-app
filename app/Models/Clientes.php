<?php

namespace App\Models;

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
    public static function getFileCliente($nro_doc)
    {
        $result = DB::select('CALL sp_get_files_estado_cliente(?)',[$nro_doc]);
        return isset($result[0]) ? $result[0] : [];
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
    public static function getDetalleSoliCli($nro_doc)
    {
        return Clientes::select('doc_ident','nro_doc','dir_sede_adr','dpto_sa','prov_sa','dist_sa','dir_sede_so','dpto_so','prov_so','dist_so')->where(['nro_doc' => $nro_doc])->first();
    }
    public static function obtenerCli($datos,$tipo = "total",$inicio,$fin)
    {
        $result = DB::select('CALL sp_obtener_clientes_filtros(?,?,?,?,?,?)', [$datos['nom_cli'], $datos['estado_cli'], $datos['estado_doc_cli'], $tipo, $inicio, $fin]);
        switch ($tipo) {
            case 'total':
                return ['total' => $result[0]->total];
            break;
            case 'info':
                return ['clientes' => $result];
            break;
        }
    }

}
