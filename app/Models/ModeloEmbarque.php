<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModeloEmbarque extends Model
{
    protected $table = "embarque";
    public $timestamps = false;

    public static function embarcarTrabajador($id_user){
        $embarques = DB::select('CALL sp_embarcar_trabajador(?)',[$id_user]);
        foreach ($embarques as $em) {
            $em->transportes = DB::table('transporte_embarque AS te')->select('et.id','et.empresa','et.aereo')
            ->join('empresa_transporte AS et','te.id_empr_tran','=','et.id')->where(['te.id_list_emba' => $em->id, 'et.activo' => 1])->get();
            $em->manfiestos = DB::select('CALL sp_embarque_embarcar_manifiesto(?)',[$em->id]);
            foreach ($em->manfiestos as $prov) {
                $prov->listaManifiestos = explode(',',$prov->listaManifiestos);
                if($prov->tipo_base == 'distrito'){
                    $prov->nombreciudad = mb_strtoupper((new Ubigeo)->getNameDistrito($prov->ubigeo_distrito),'UTF-8');
                }
            }
        }
        return $embarques;
    }
    public static function eliminarEmbarque($idEmbarque)
    {
        DB::beginTransaction();
        try {
            DB::table('embarque_manifiesto AS em')->join('manifiesto AS m','em.id_manifiesto','=','m.id')
            ->join('detalle_manifiesto AS dm', 'dm.id_manifiesto', '=', 'm.id')
            ->join('guias AS g', 'g.id', '=', 'dm.id_guia')
            ->where(['em.id_embarque' => $idEmbarque])->update(['m.estado' => 1 ,'g.situ' => 2]);
            DB::table('embarque')->where(['id' => $idEmbarque])->delete();
            DB::commit();
            return ['success' => 'Embarque eliminado correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function mostrarEditarFletes($idEmbarque)
    {
        $fletes = DB::table('fletes_embarque AS fe')->select('fe.id','fe.via_envio','fe.id_cluster',DB::raw('fn_obtener_nombres_clusters(fe.id_cluster) AS nom_cluster'),'cbc.tipo_base','cbc.ubigeo_distrito','c.nombre_ciudad','fe.cajas','fe.jabas','fe.cajones','fe.sobres','fe.paquetes','fe.otros','fe.detalle_otros','fe.sub_total_bultos','fe.subtotal_fletes')
        ->join('ciudades_base_cluster AS cbc','fe.id_cluster','=','cbc.id_cluster')
        ->leftJoin('ciudades AS c','cbc.id_ciudad','=','c.id')
        ->where(['fe.id_embarque' => $idEmbarque,'cbc.base' => 1,'cbc.activo' => 1])->get();
        foreach ($fletes as $fle) {
            $fle->nombreciudad = $fle->tipo_base == 'ciudad' ? $fle->nombre_ciudad : (new Ubigeo)->getNameDistrito($fle->ubigeo_distrito);
        }
        $total = DB::table('embarque')->select('total_bultos_paquete','flet_esti')->where(['id' => $idEmbarque])->first();
        return ['ciudades' => $fletes,'totales' => $total];
    }
    public static function eliminarManifiestoEmbarque($idEmbarque,$idManifiesto)
    {
        if(DB::table('embarque_manifiesto')->where(['id_embarque' => $idEmbarque])->count() === 1){
            return ['error' => 'El embarque debe tener al menos un manifiesto'];
        }
       DB::select('CALL sp_embarque_eliminar_manifiesto(?,?)',[$idEmbarque,$idManifiesto]);
       return ['success' => 'Manifiesto eliminado correctamente'];
    }
    public static function agregarManifiestoEmbarque($idEmbarque, $idManifiesto)
    {
        DB::select('CALL sp_embarque_agregar_manifiesto(?,?)', [$idEmbarque, $idManifiesto]);
        return ['success' => 'Manifiesto agregado correctamente'];
    }
    public static function misEmbarquesFiltros($fechaInicio,$fechaFin,$driver,$dealer,$vehiculo,$estado,$idEmbarque)
    {
        $embarques = DB::select('CALL sp_embarque_obtener(?,?,?,?,?,?,?)',[$fechaInicio, $fechaFin, $driver, $dealer, $vehiculo, $estado, $idEmbarque]);
        foreach ($embarques as $emb) {
            $emb->manifiestos = json_decode($emb->manifiestos,true);
        }  
        return $embarques;
    }

    public static function actualizarEmbarque($id_embarque,$id_user,$empresa)
    {
        return DB::select('CALL sp_actualizar_embarque_trabajador(?,?,?,?)',[$id_embarque,$id_user,date('H:i'),$empresa]);
    }
    public static function obtenerEmbarqueInforme($id_usuario)
    {
        return DB::select('CALL sp_obtener_embarques_informar(?)',[$id_usuario]);
    }
    public static function obtenerEmbarqueDetalle($id_embarque,$id_user)
    {
        return DB::select('CALL sp_obtener_embarque_detalle_informe(?,?)',[$id_embarque,$id_user]);
    }
    public static function obtenerCiudadesBaseFleteVerif($id_manifiestos)
    {
        $obtener_ciudades = DB::select('CALL sp_obtener_ciudades_base_flete(?)', [$id_manifiestos]);
        foreach ($obtener_ciudades as $guias) {
            if (!empty($guias)) {
                $guias->n_guias = json_decode($guias->n_guias);
            }
            if ($guias->tipo_base == 'distrito') {
                $guias->nombreciudad = (new Ubigeo)->getNameDistrito($guias->nombreciudad);
            }
        }
        return $obtener_ciudades;
    }
    public static function obtenerCiudadesBaseFlete($id_manifiestos,$usuario)
    {
        $obtener_ciudades = DB::select('CALL sp_obtener_ciudades_base_flete(?,?)', [$id_manifiestos, $usuario]);
        foreach ($obtener_ciudades as $guias) {
            if (!empty($guias)) {
                $guias->n_guias = json_decode($guias->n_guias);
            }
            if ($guias->tipo_base == 'distrito') {
                $guias->nombreciudad = (new Ubigeo)->getNameDistrito($guias->nombreciudad);
            }
        }
        return $obtener_ciudades;
    }
    public static function obtenerGuiasEmbarqueInsertar($id_embarque,$id_usuario)
    {
        $obtener_ciudades = DB::select('CALL sp_generar_guias_embarque(?,?)',[$id_embarque, $id_usuario]);
        foreach ($obtener_ciudades as $guias) {
            if(!empty($guias)){
                $guias->n_guias = json_decode($guias->n_guias);
            }
            if($guias->tipo_base == 'distrito'){
                $guias->nombreciudad = (new Ubigeo)->getNameDistrito($guias->nombreciudad);
            }
        }
        return $obtener_ciudades;
    }
    public static function agregarInformeEmbarque($datos,$usuario)
    {
        DB::beginTransaction();
        try {
            $fecha_reparto = $datos['fecha_llegada'];
            $limite_hora = date('N',strtotime($datos['fecha_llegada'])) == 6 ? strtotime('12:00') : strtotime("17:00");
            $hora_llegada = strtotime($datos['hr_llegada']);
            if($hora_llegada > $limite_hora){
                $fecha_reparto = date('Y-m-d',strtotime($fecha_reparto . '+ 1 day'));
            }
            if(date('N', strtotime($fecha_reparto)) == 7){
                $fecha_reparto = date('Y-m-d', strtotime($fecha_reparto . '+ 1 day'));
            }
            $id = $datos['id_embarque'];
            $guias_embarque = !empty($datos['guias_embarque']) ? json_decode($datos['guias_embarque'],true) : [];
            foreach ($guias_embarque as $key => $guia) {
                if (strlen(trim($guia['nro_guia_embarque'])) > 11 && strpos($guia['nro_guia_embarque'], '-') != 3) {
                    return ['error' => 'La guía N° ' . $guia['nro_guia_embarque'] . ' no está con el formato solicitado'];
                }
                $nro_guia = explode('-', trim($guia['nro_guia_embarque']));
                if (DB::table('guias')->where(['nro_guia' => $nro_guia[1], 'prefijo' => $nro_guia[0]])->count() > 0) {
                    return ['error' => 'La guía de embarque N° <b>' . $guia['nro_guia_embarque'] . '</b> ya se encuentra registrada, por favor intente con otro número de guía.'];
                }
                if (DB::table('relacion_guias_embarque')->where(['nro_guia_embarque' => $nro_guia[1], 'prefijo_guia_embarque' => $nro_guia[0]])->count() > 0) {
                    return ['error' => 'La guía de embarque N° <b>' . $guia['nro_guia_embarque'] . '</b> está pendiente por registrar, por favor intente con otro número de guía.'];
                }
                $guias_embarque[$key]['id_embarque'] = $id;
                $guias_embarque[$key]['prefijo_guia_embarque'] = $nro_guia[0];
                $guias_embarque[$key]['nro_guia_embarque'] = $nro_guia[1];
            }
            if(isset($datos['guias_embarque'])){
                unset($datos['guias_embarque']);
            }
            unset($datos['id_embarque']);
            if(isset($datos['comprobantes'])){
                $datos['comprobantes'] = implode(';',$datos['comprobantes']);
            }
            if (isset($datos['nro_comprobantes'])) {
                $datos['nro_comprobantes'] = implode(';', $datos['nro_comprobantes']);
            }
            $datos['estado'] = 4;
            $datos['user_update'] = $usuario;
            $datos['date_update'] = date('Y-m-d H:i:s');
            // dd($datos);
            DB::table('embarque')->where(['id' => $id, 'estado' => 3, 'activo' => 1])->update($datos);
            DB::table('relacion_guias_embarque')->insert($guias_embarque);
            DB::table('embarque_manifiesto AS em')->join('manifiesto AS m', 'em.id_manifiesto', '=', 'm.id')
                ->where(['m.activo' => 1, 'm.estado' => 3, 'em.id_embarque' => $id])
                ->update(['m.estado' => $datos['estado'], 'date_update' => $datos['date_update'], 'user_update' => $usuario]);
            DB::table('embarque_manifiesto AS em')->join('detalle_manifiesto AS dm','em.id_manifiesto','=', 'dm.id_manifiesto')
                ->join('guias AS g','dm.id_guia','=','g.id')->where(['em.id_embarque' => $id])
                ->update(['g.fecha_reparto' => $fecha_reparto]);
            DB::commit();
            return ['success' => 'Informe agregado con éxito'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function obtenerEmbarqueArribado($id_embarque,$id_guia,$id_usuario,$tipo)
    {
        $result = DB::select('CALL sp_obtener_embarques_arribar(?,?,?,?)',[$id_embarque,$id_guia,$id_usuario,$tipo]);
        switch ($tipo) {
            case 'manifiestos':
                foreach ($result as $mani) {
                    $mani->prov_dest = (new Ubigeo)->getNameProvincia($mani->prov_dest);
                    $mani->prov_orig = (new Ubigeo)->getNameProvincia($mani->prov_orig);
                    $mani->guias = DB::select('CALL sp_obtener_embarques_arribar(?,?,?,?)',[$mani->id,0, $id_usuario, 'obtener-guias']);
                    if(!empty($mani->guias)){
                        for ($i=0; $i < count($mani->guias); $i++) {
                            $mani->guias[$i]->fecha_reparto = date('d/m/Y',strtotime($mani->guias[$i]->fecha_reparto));
                        }
                    }
                }
                break;
            case 'detalle-guia':
                foreach ($result as $mani) {
                    $mani->remi_depa = (new Ubigeo)->getNameDepartamento($mani->remi_depa);
                    $mani->remi_prov = (new Ubigeo)->getNameProvincia($mani->remi_prov);
                    $mani->remi_dist = (new Ubigeo)->getNameDistrito($mani->remi_dist);
                    $mani->dest_depa = (new Ubigeo)->getNameDepartamento($mani->dest_depa);
                    $mani->dest_prov = (new Ubigeo)->getNameProvincia($mani->dest_prov);
                    $mani->dest_dist = (new Ubigeo)->getNameDistrito($mani->dest_dist);
                    $mani->detalles_guias = DB::table('detalle_guias')->where(['id_guia' => $mani->id])->get();
                }
            break;
            
        }
        return $result;
    }
    public static function devolucionCargo($guia,$tipo,$usuario)
    {
        $result = DB::select('CALL sp_retorno_cargo(?,?,?)',[$guia,$usuario,$tipo]);
        if($tipo == 'ver-cargos-pendientes'){
            foreach ($result as $guia) {
                $guia->remi_prov = (new Ubigeo)->getNameProvincia($guia->remi_prov);
                $guia->dest_prov = (new Ubigeo)->getNameProvincia($guia->dest_prov);
            }
        }
        return $result;
    }
    public static function guardarDevolucion($guias,$usuario)
    {
        for ($i=0; $i < count($guias); $i++) {
            $guias[$i]['id_usuario'] = $usuario;
        }
        DB::table('detalle_retorno_cargo')->insert($guias);
        return ['success' => 'Guias guardadas correctamente'];
    }
    public static function obtenerEmbarquesGuardados($idusuario)
    {
        return ['guias' => DB::select('CALL sp_obtener_embarques_guardados(?)', [$idusuario])];
    }
    public static function actualizarDevolucionCargo($nuevo_retorno,$guias_nuevas,$guias_guardadas,$usuario)
    {
        DB::beginTransaction();
        try {
            $id_retorno = DB::table('retorno_cargo')->insertGetId($nuevo_retorno);
            $guias = [];
            if(count($guias_nuevas) > 0){
                for ($i=0; $i < count($guias_nuevas); $i++) {
                    $guias_nuevas[$i]['id_retorno_cargo'] = $id_retorno;
                    $guias_nuevas[$i]['id_usuario'] = $usuario;
                    $guias[] = ['id_guia' => $guias_nuevas[$i]['id_guia']];
                }
                DB::table('detalle_retorno_cargo')->insert($guias_nuevas);
            }
            if (count($guias_guardadas) > 0) {
                foreach ($guias_guardadas as $gguardado) {
                    $guias[] = ['id_guia' => $gguardado['id_guia']];
                    DB::table('detalle_retorno_cargo')->where(['id_guia' => $gguardado['id_guia']])->update(['id_retorno_cargo' => $id_retorno,'id_usuario' => $usuario]);
                }
            }
            for ($i=0; $i < count($guias) ; $i++) {
                DB::select('CALL sp_retorno_cargo(?,?,?)', [$guias[$i]['id_guia'], $usuario, 'actualizar-guia']);
            }
            DB::commit();
            return ['success' => 'Información agregada correctamente'];
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
            DB::rollBack();
        }
    }
    public static function actualizarGuiaLlegadas($id_embarque,$id_manifiesto,$id_guia,$estado, $texto,$situacion,$incidencia,$usuario)
    {
        return DB::select('CALL sp_actualizar_llegadas_guias(?,?,?,?,?,?,?,?)',[$id_embarque, $id_manifiesto,$id_guia,$estado,$texto, $situacion, $incidencia, $usuario]);
    }
    public static function obtenerGuiasRepartoConfirmado($fecha_confirmado,$fecha_reparto,$esto_guia,$cluster, $ciudad_final, $tipo_ciudad)
    {
        return DB::select('CALL sp_obtener_guias_reparto_entrega(?,?,?,?,?,?,?)',[$fecha_confirmado, $fecha_reparto, $esto_guia, $cluster,'consulta',$ciudad_final,$tipo_ciudad]);
    }
    public static function obtenerDetalleGuiaEditarEmbarque($id_guia,$id_cluster)
    {
        $consulta = DB::select('CALL sp_obtener_guias_reparto_entrega(?,?,?,?,?,?,?)', ["", "", $id_guia, $id_cluster, 'detalle-guia',"",""]);
        if(isset($consulta[0])){
            $detalleEntregas = DB::table('numero_visitas_guias')->select("visita","motivo")->where(['id_guia' => $id_guia])->get();
            return ['success' => $consulta[0] , 'detalle' => $detalleEntregas];
        }else{
            return ['error' => 'No se encontró la guía seleccionada'];
        }
    }
    public static function calculoKpi($id_cluster)
    {
        $confirmadasTotal = DB::select('SELECT COUNT(*) AS total FROM guias WHERE situ IN(5,7,6) AND activo = 1 AND estado = 1 AND id_cluster = ?',[$id_cluster]);
        $confirmadas = DB::select('SELECT COUNT(*) AS total FROM guias WHERE situ IN(6,7) AND activo = 1 AND estado = 1 AND id_cluster = ?', [$id_cluster]);
        $entregadoTotal = DB::select('SELECT COUNT(*) AS total FROM guias WHERE situ IN(7,8,14) AND activo = 1 AND estado = 1 AND id_cluster = ?',[$id_cluster]);
        $entregado = DB::select('SELECT COUNT(*) AS total FROM guias WHERE situ IN (8,14) AND activo = 1 AND estado = 1 AND id_cluster = ?', [$id_cluster]);
        $atiempo = DB::select("SELECT COUNT(*) AS total FROM guias WHERE situ = 8 AND activo = 1 AND estado = 1 AND DATE_FORMAT(fecha_hr_entregado,'%Y-%m-%d') <= fecha_reparto AND id_cluster = ?",[$id_cluster]);
        $entregadoSioSi = DB::select("SELECT COUNT(*) AS total FROM guias WHERE situ = 8 AND activo = 1 AND estado = 1 AND id_cluster = ?",[$id_cluster]);
        $porEntregar = DB::select("SELECT COUNT(*) AS total FROM guias WHERE situ = 7 AND activo = 1 AND estado = 1 AND id_cluster = ?", [$id_cluster]);
        $incidencias = DB::select("SELECT COUNT(*) AS total FROM guias WHERE activo = 1 AND estado = 1 AND (situacion_durante_envio = 'INCIDENCIA' OR incidencia_durante_envio = 'PERDIDA TOTAL' OR incidencia_durante_envio = 'DAÑO TOTAL' OR estado_entregado = 'NO ENTREGADO') AND situ = 14 AND id_cluster = ?",[$id_cluster]);
        return [
            'Confirmadas' => [
                'porefectuar' => $porEntregar[0]->total,
                'completadas' => $confirmadas[0]->total,
                'total' => $confirmadasTotal[0]->total,
                'porcentaje' => $confirmadasTotal[0]->total !== 0 ? round(($confirmadas[0]->total * 100)/ $confirmadasTotal[0]->total) : 0
            ],
            'Incidencias' => [
                'completadas' => $incidencias[0]->total,
                'total' => $entregadoTotal[0]->total,
                'porcentaje' => $entregadoTotal[0]->total !== 0 ? round(($incidencias[0]->total * 100) / $entregadoTotal[0]->total) : 0            
            ],
            'Efectuadas' => [
                'completadas' => $entregado[0]->total,
                'total' => $entregadoTotal[0]->total,
                'porcentaje' => $entregadoTotal[0]->total !== 0 ? round(($entregado[0]->total * 100) / $entregadoTotal[0]->total) : 0
            ],
            'A tiempo' => [
                'completadas' => $atiempo[0]->total,
                'total' => $entregadoSioSi[0]->total,
                'porcentaje' => $entregado[0]->total !== 0 ? round(($atiempo[0]->total * 100) / $entregado[0]->total) : 0
            ]
        ];
    }
    public static function entregaGuias($id_usuario)
    {
        $result = DB::select('CALL sp_obtener_guias_entregar(?)',[$id_usuario]);
        foreach ($result as $mani) {
            $mani->remi_depa = (new Ubigeo)->getNameDepartamento($mani->remi_depa);
            $mani->remi_prov = (new Ubigeo)->getNameProvincia($mani->remi_prov);
            $mani->remi_dist = (new Ubigeo)->getNameDistrito($mani->remi_dist);
            $mani->dest_depa = (new Ubigeo)->getNameDepartamento($mani->dest_depa);
            $mani->dest_prov = (new Ubigeo)->getNameProvincia($mani->dest_prov);
            $mani->dest_dist = (new Ubigeo)->getNameDistrito($mani->dest_dist);
        }
        return $result;
    }
    public static function confirmarEntregaGuias($id_guia,$estados,$usuario,$archivos,$visitas)
    {
        DB::beginTransaction();
        try {
            $situacion = 8;
            if(!empty($visitas)){
                $situacion = 7;
                if($estados['nro_visitas_reparto'] == 'RETORNO'){
                    $estados['nro_visitas_reparto'] = null;
                    $situacion = 14;
                }else{
                    $reprogramado = date('Y-m-d',strtotime(date('Y-m-d') . "+1 days"));
                    $estados['estado_entregado'] = null;
                    $estados['motivo_no_ent'] = null;
                    if(date('w',strtotime($reprogramado)) === 0){
                        $reprogramado = date('Y-m-d', strtotime($reprogramado . "+1 days"));
                    }
                    DB::table('guias AS g')->join('cluster AS c', 'g.id_cluster', '=', 'c.id')->where(['c.id_usuario' => $usuario, 'g.id' => $id_guia, 'g.activo' => 1])->update(['fecha_reparto' => $reprogramado]);
                }
                $visitas['id_guia'] = $id_guia;
                $existHistoriaVisita = DB::table('numero_visitas_guias')->where(['id_guia' => $id_guia, 'visita' => $visitas['visita']])->first();
                if(!empty($existHistoriaVisita)){
                    DB::table('numero_visitas_guias')->where(['id' => $existHistoriaVisita->id])->update(['motivo' => $visitas['motivo']]);
                }else{
                    DB::table('numero_visitas_guias')->insert($visitas);
                }
            }
            if($estados['situacion_durante_envio'] == 'INCIDENCIA' && in_array($estados['incidencia_durante_envio'],['DAÑO TOTAL','PERDIDA TOTAL'])){
                $situacion = 14;
                $estados['estado_entregado'] = 'NO ENTREGADO';
                $estados['nro_visitas_reparto'] = null;
            }
            DB::select('CALL sp_actualizar_entrega_guia(?,?,?)',[$usuario, $id_guia, $situacion]);
            DB::table('imagenes_guia')->insert($archivos);
            if(empty($visitas) && $situacion == 8){
                $estados['fecha_hr_entregado'] = date('Y-m-d H:i:s');
            }
            DB::table('guias AS g')->join('cluster AS c','g.id_cluster','=','c.id')->where(['c.id_usuario' => $usuario, 'g.id' => $id_guia , 'g.situ' => $situacion, 'g.activo' => 1])->update($estados);
            if (empty($visitas) && ($situacion == 8 || $situacion == 14)) {
                DB::table('procesos_guia')->insert(['id_guia' => $id_guia, 'situacion' => $situacion, 'id_user' => $usuario]);
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
        }
    }
}
