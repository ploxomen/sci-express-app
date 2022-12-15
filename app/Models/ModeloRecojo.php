<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Exception;
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
    public static function getRecojosDriDea($id_trabajor)
    {
        $solicitudes = DB::select('CALL sp_obtener_solicitudes_trabajador(?,?,?)',[$id_trabajor,1,0]);
        foreach ($solicitudes as $soli) {
            
            $soli->detalleSolicitud = DB::select('CALL sp_obtener_solicitudes_trabajador(?,?,?)',[$id_trabajor,2,$soli->id]);
            foreach ($soli->detalleSolicitud as $detalle) {
                $detalle->depart_cli_recojo = (new Ubigeo)->getNameDepartamento($detalle->depart_cli_recojo);
                $detalle->ciudad_cli_recojo = (new Ubigeo)->getNameProvincia($detalle->ciudad_cli_recojo);
                $detalle->distrito_cli_recojo = (new Ubigeo)->getNameDistrito($detalle->distrito_cli_recojo);
            }
        }
        return $solicitudes;
    }
    public static function verRecojos($fechaIncio,$fechaFin,$estado)
    {
        return DB::select('CALL sp_ver_recojos(?,?,?)',[$fechaIncio,$fechaFin,$estado]);
    }
    public static function verRecojosActualizar($idRecojo)
    {
        return DB::table('recojo_detalle AS r')->select('r.id','r.id_solicitud', 'r.id_vehiculo', 'r.id_cluster', 'r.id_dealer', 'r.id_conductor', 'r.fecha_recojida', 'r.documento_recojo', 'r.nro_guias_consolidadas', 'r.bultos_totales_recojidos')
        ->selectRaw("IF(s.id_soli IS NULL,c.nom_cli,pv.nomb_prov) AS clienteFinal,IF(s.id_soli IS NULL,'CLIENTE','PROVEEDOR') AS tipoClienteFinal,LPAD(s.nro_solicitud,5,'0') AS nro_solicitud,DATE_FORMAT(s.dia_recojo,'%d/%m/%Y') AS dia_programado,TIME_FORMAT(hr_recojo_estimado,'%h:%i %p')AS hr_recojo_estimado,TIME_FORMAT(r.hr_recojida_llegada,'%H:%i') AS hr_recojida_llegada,TIME_FORMAT(r.hr_recojida_despacho,'%H:%i') AS hr_recojida_despacho,TIME_FORMAT(r.hr_recojida_salida,'%H:%i') AS hr_recojida_salida")
        ->join('solicitud AS s','r.id_solicitud','=','s.id')
        ->leftJoin('proveedor_clientes AS pv','pv.id','=','s.id_soli')
        ->leftJoin('clientes AS c', 'c.id', '=', 's.id_cliente')
        ->where(['r.id_recojo' => $idRecojo])->get();
    }
    public static function eliminarDetalleRecojo($idDetalleRecojo,$idRecojo,$estado,$msgSuccess,$userId)
    {
        $numDetalleRecojo = DB::table('recojo_detalle')->where('id_recojo', $idRecojo)->count();
        if($numDetalleRecojo === 1){
            return ['error' => 'el recojo debe tener como minimo una solicitud'];
        }
        DB::beginTransaction();
        try {
            $solicitud = DB::table('recojo_detalle')->select('id_solicitud')->where(['id' => $idDetalleRecojo])->first();
            DB::table('recojo_detalle')->where(['id' => $idDetalleRecojo])->delete();
            DB::table('recojo')->where('id', $idRecojo)->update(['cantidad_solicitud' => $numDetalleRecojo - 1,'usuario_modificado' => $userId,'fecha_modificada' => now()]);
            DB::table('solicitud')->where('id',$solicitud->id_solicitud)->update(['estado' => $estado,'last_modificacion' => now(),'last_user' => $userId]);
            DB::commit();
            return ['success' => $msgSuccess];
        } catch (Exception $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }
    }
    public static function eliminarRecojo($idRecojo,$estado,$msgSuccess,$userId)
    {
        DB::beginTransaction();
        try {
            DB::table('solicitud AS s')
            ->join('recojo_detalle AS rd','rd.id_solicitud','=','s.id')
            ->where(['rd.id_recojo' => $idRecojo])->update(['s.estado' => $estado, 's.last_modificacion' => now(), 's.last_user' => $userId]);
            DB::table('recojo_detalle')->where(['id_recojo' => $idRecojo])->delete();
            DB::table('recojo')->where(['id' => $idRecojo])->delete();
            DB::commit();
            return ['success' => $msgSuccess];
        } catch (Exception $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }
    }
    public static function agregarRecojo($fechaRecojo,$responsableRecojo,$vehiculo,$cluster,$dealer,$conductor,$userId,$solicitudes)
    {
        DB::beginTransaction();
        try {
            $datosRecojo = [
                'fecha_recojo' => $fechaRecojo,
                'responsable_recojo' => $responsableRecojo,
                'usuario_creado' => $userId,
                'cantidad_solicitud' => count($solicitudes)
            ];
            $idRecojo = DB::table('recojo')->insertGetId($datosRecojo);
            foreach ($solicitudes as $soli) {
                $datosDetalleRecojo = [
                    'id_recojo' => $idRecojo,
                    'id_solicitud' => $soli,
                    'id_vehiculo' => $vehiculo,
                    'id_cluster' => $cluster,
                    'id_dealer' => $dealer,
                    'id_conductor' => $conductor,
                    'usuario_creador' => $userId
                ];
                DB::table('recojo_detalle')->insert($datosDetalleRecojo);
                DB::table('solicitud')->where(['id' => $soli, 'usuario_responsable' => $userId])->update(['estado' => 2]);
            }
            DB::commit();
            return ['success' => 'datos registrados correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function updateRecoTraba($id,$bulto,$doc,$nro_guias,$id_user)
    {
        $hora = date('H:i');
        return DB::select('CALL sp_recojo_update_trabajador(?,?,?,?,?,?)',[$id,$bulto,$doc,$nro_guias,$id_user,$hora]);
    }

}
