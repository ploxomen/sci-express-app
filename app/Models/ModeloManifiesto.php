<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class ModeloManifiesto extends Model
{
    protected $table = 'manifiesto';
    public $timestamps = false;

    public static function agregarManifiesto($manifiesto,$guias)
    {
        DB::beginTransaction();
        try {
            $id_manifiesto = DB::table('manifiesto')->insertGetId($manifiesto);
            for ($i=0; $i < count($guias) ; $i++) {
                $guias[$i]['id_manifiesto'] = $id_manifiesto;
            }
            DB::table('detalle_manifiesto')->insert($guias);
            foreach ($guias as $n_guia) {
                DB::table('guias')->where(['id' => $n_guia['id_guia']])->update(['situ' => 2]);
            }
            DB::commit();
            return ['success' => 'Manifiesto agregado correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function eliminarManifiesto($idManifiesto)
    {
        DB::beginTransaction();
        try {
            $embarque = DB::table('embarque_manifiesto')->where(['id_manifiesto' => $idManifiesto])->first();
            if(!empty($embarque)){
                return ['error' => 'El manifiesto no puede ser eliminado debido a que está asociado al embarque N° ' . str_pad($embarque->id,5,'0',STR_PAD_LEFT)];
            }
            DB::table('detalle_manifiesto AS dm')->join('guias AS g','dm.id_guia','=','g.id')
            ->where('dm.id_manifiesto','=',$idManifiesto)->update(['g.situ' => 1,'g.estado' => 1]);
            DB::table('manifiesto')->where(['id' => $idManifiesto])->delete();
            DB::commit();
            return ['success' => 'Manifiesto eliminado correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function manifiestoAgregarManual()
    {
        return DB::table('manifiesto')->select('id','via_envio')->where(['activo' => 1,'estado' => 1])->get();
    }
    public static function actualizarGuiasAtravezManifiesto(int $idManifiesto, int $estado)
    {
        $estadoGuia = $estado;
        if($estado === 1 || $estado === 2){
            $estadoGuia++;
        }else if($estado === 3 || $estado === 4){
            $estadoGuia = 4;
        }
        DB::table("detalle_manifiesto AS dm")->join("guias AS g","dm.id_guia","=","g.id")->where(['dm.id_manifiesto' => $idManifiesto,'g.estado' => 1,'g.activo' => 1])->whereBetween('g.situ',[2,6])->update(['g.situ' => $estadoGuia]);
    }
    public static function reportesManifiestos($fechaInicio, $fechaFin)
    {
        $manifiestos = DB::select('CALL sp_reportes_manifiestos(?,?)', [$fechaInicio, $fechaFin]);
        foreach ($manifiestos as $manifiesto) {
            if ($manifiesto->tipo_base == 'distrito') {
                $manifiesto->nombreBase = (new Ubigeo)->getNameDistrito($manifiesto->nombreBase);
            }
            $manifiesto->prov_orig = (new Ubigeo)->getNameProvincia($manifiesto->prov_orig);
            $manifiesto->prov_dest = (new Ubigeo)->getNameProvincia($manifiesto->prov_dest);
        }
        return $manifiestos;
    }
    public static function obtenerTodosManifiestos($fechaInicio,$fechaFin,$trayecto,$provOrigen,$provFinal,$cluster,$estado,$id)
    {
        $manifiestos = DB::select('CALL sp_manifiesto_obtener(?,?,?,?,?,?,?,?)',[$fechaInicio,$fechaFin,$trayecto,$provOrigen,$provFinal,$cluster,$estado,$id]);
        $usuarios = User::obtenerUsuarioRol(3);
        foreach ($manifiestos as $mani) {
            $mani->listUsu = $usuarios;
            $mani->nom_prov_orig = (new Ubigeo)->getNameProvincia($mani->prov_orig);
            $mani->nom_prov_dest = (new Ubigeo)->getNameProvincia($mani->prov_dest);
            $mani->guias_asociadas = json_decode($mani->guias_asociadas);
            $mani->listCluster = ModeloCluster::obtenerUnClusterProvincia($mani->prov_dest);
        }
        return $manifiestos;
    }
    public static function agregarGuiaManifiestoManual($idManifiesto, $idGuia)
    {
        DB::beginTransaction();
        try {
            if(DB::table('detalle_manifiesto')->where(['id_manifiesto' => $idManifiesto, 'id_guia' => $idGuia])->count() > 0){
                return ['error' => 'Esta guía ya se encuentra manifestada'];
            }
            DB::table('detalle_manifiesto')->insert(['id_manifiesto' => $idManifiesto, 'id_guia' => $idGuia]);
            DB::table('guias')->where(['id' => $idGuia])->update(['situ' => 2, 'text_disconforme' => null, 'fecha_reparto' => null, 'fecha_hr_confirmada' => null, 'fecha_hr_entregado' => null, 'incidencia_envio' => null, 'situacion_envio' => null, 'incidencia_durante_envio' => null, 'situacion_durante_envio' => null, 'text_durante_disconforme' => null, 'estado_entregado' => null, 'nro_visitas_reparto' => null, 'motivo_no_ent' => null]);
            DB::commit();
            return ['success' => 'Guía manifestada correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function eliminarGuiaManifiesto($idManifiesto,$idGuia)
    {
        DB::beginTransaction();
        try {
            if(DB::table('detalle_manifiesto')->where(['id_manifiesto' => $idManifiesto])->count() === 1){
                return ['error' => 'El manifiesto debe de tener al menos una guía asociada'];
            }
            DB::table('detalle_manifiesto')->where(['id_manifiesto' => $idManifiesto, 'id_guia' => $idGuia])->delete();
            DB::table('guias')->where(['id' => $idGuia])->update(['situ' => 1, 'text_disconforme' => null, 'fecha_reparto' => null, 'fecha_hr_confirmada' => null, 'fecha_hr_entregado' => null, 'incidencia_envio' => null, 'situacion_envio' => null, 'incidencia_durante_envio' => null, 'situacion_durante_envio' => null, 'text_durante_disconforme' => null, 'estado_entregado' => null, 'nro_visitas_reparto' => null, 'motivo_no_ent' => null]);
            DB::commit();
            return ['success' => 'Guía eliminada del manifiesto correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function manifiestoPendientes($idCluster)
    {
        return DB::table('guias')->select('id','prefijo','nro_guia')->where(['situ' => 1,'estado' => 1,'id_cluster' => $idCluster])->get();
    }
    public static function obtenerSituacionManifiesto()
    {
        return DB::table('estado_manifiesto_embarque')->where(['activo' => 1])->get();
    }
    public static function embarcarManifiesto($datos,$id_user)
    {
        DB::beginTransaction();
        try {
            if (isset($datos['nosotros'])) {
                $datos['nosotros'] = 1;
            } else {
                $datos['nosotros'] = 0;
            }
            $datos['id_deal2'] = $datos['id_deal2'] == 'NINGUNO' ? null : $datos['id_deal2'];
            $transportes = isset($datos['id_empr_tran']) ? $datos['id_empr_tran'] : null;
            $manifiestos = isset($datos['lista_manifiestos']) ? json_decode($datos['lista_manifiestos'], true) : null;
            $fletes_cluster = isset($datos['flestes_cluster']) ? json_decode($datos['flestes_cluster'], true) : null;
            if (isset($datos['id_empr_tran'])) {
                unset($datos['id_empr_tran']);
            }
            if (isset($datos['lista_manifiestos'])) {
                unset($datos['lista_manifiestos']);
            }
            if (isset($datos['flestes_cluster'])) {
                unset($datos['flestes_cluster']);
            }
            $filterManifiestos = [];
            foreach ($manifiestos as $v) {
                $filterManifiestos[] = $v['id_manifiesto'];
            }
            $datos['user_create'] = $id_user;
            $id_embarque = DB::table('embarque')->insertGetId($datos);
            for ($i=0; $i < count($fletes_cluster); $i++) {
                $nombreCiudad = $fletes_cluster[$i]['nombreCiudad'];
                $embarque_ciudad = [
                    'id_ciudad' => empty($fletes_cluster[$i]['id_ciudad']) || $fletes_cluster[$i]['id_ciudad'] == 'null' ? null : $fletes_cluster[$i]['id_ciudad'],
                    'ubigeo_distrito' => $fletes_cluster[$i]['ubigeo'],
                    'tipo_base' => $fletes_cluster[$i]['tipo_base'],
                    'tipo_trayecto' => $fletes_cluster[$i]['transfer'],
                    'id_embarque' => $id_embarque
                ];
                $idEmbarqueCiudad = DB::table('embarque_ciudad_llegada')->insertGetId($embarque_ciudad);
                foreach ($fletes_cluster[$i]['fletes'] as $flete) {
                    $embarqueFlete = array_merge(['id_embarque_ciudad' => $idEmbarqueCiudad],$flete);
                    DB::table('embarque_fletes')->insert($embarqueFlete);
                }
                if (!empty($filterManifiestos)) {
                    $existManifiestos = DB::table('manifiesto AS m')->select('m.id')->join('ciudades_base_cluster AS cbc','m.id_cluster','=','cbc.id_cluster')
                    ->where(['cbc.activo' => 1,'cbc.base' => 1,'cbc.ubigeo_distrito'=>$embarque_ciudad['ubigeo_distrito'],'cbc.tipo_base' => $embarque_ciudad['tipo_base'],'m.via_envio' => $embarque_ciudad['tipo_trayecto'], 
                    'cbc.id_ciudad' => $embarque_ciudad['id_ciudad']])->whereIn('m.id',$filterManifiestos)->get();
                    if($existManifiestos->isEmpty()){
                        DB::rollBack();
                        return ['error' => 'No se encontró manifiestos para la ciudad base ' . $nombreCiudad];
                    }
                    $nManifiesto = [];
                    foreach ($existManifiestos as $nm) {
                       $nManifiesto[] = [
                        'id_embarque_ciudad' => $idEmbarqueCiudad,
                        'id_manifiesto' => $nm->id
                       ];
                    }
                    DB::table('embarque_manifiesto')->insert($nManifiesto);
                }
            }
            if (!empty($transportes)) {
                $newTrasn = [];
                for ($i = 0; $i < count($transportes); $i++) {
                    $newTrasn[] = ['id_empr_tran' => $transportes[$i], 'id_list_emba' => $id_embarque];
                }
                DB::table('transporte_embarque')->insert($newTrasn);
            }
            DB::table('manifiesto AS m')->join('detalle_manifiesto AS dm','m.id','=','dm.id_manifiesto')
            ->join('guias AS g','g.id','=','dm.id_guia')->whereIn('m.id', $filterManifiestos)->where(['m.estado' => 1,'g.situ' => 2])->update(['m.estado' => 2,'g.situ' => 3]);
            DB::commit();
            return ['success' => 'Embarque programado con exito','ciudades' => InformacionAdicional::obtenerCiudadBaseEmbarcar($id_user)];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function obtenerEmbarcar($ciudad_base,$tipo,$id_usuario)
    {
        $manifiestos = DB::select('call sp_obtener_manifiesto_embarcar(?,?,?)',[$ciudad_base,$tipo, $id_usuario]);
        foreach ($manifiestos as $m) {
            $m->prov_orig = (new Ubigeo)->getNameProvincia($m->prov_orig);
            $m->prov_dest = (new Ubigeo)->getNameProvincia($m->prov_dest);
            if($m->tipo_base == 'distrito'){
                $m->nombre_ciudad_base = (new Ubigeo)->getNameDistrito($m->nombre_ciudad_base);
            }
        }
        return $manifiestos;
    }
}
