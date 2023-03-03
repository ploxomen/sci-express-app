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

    protected $fillable = ['fch_emba','id_cond','id_deal','id_vehi','id_deal2','resp_emba','hr_fin_esti_emba', 'hr_ini_emba','fin_emba','flete_esti','total_bultos_paquete','importe_total','nosotros'];

    public static function embarcarTrabajador($idEmbarque,$id_user,$condicion){
        $embarques = DB::select('CALL sp_embarcar_trabajador(?,?,?)',[$idEmbarque,$id_user, $condicion]);
        foreach ($embarques as $em) {
            if($condicion == 'embarque'){
                $em->transportes = DB::table('transporte_embarque AS te')->select('et.id', 'et.empresa', 'et.aereo')
                ->join('empresa_transporte AS et', 'te.id_empr_tran', '=', 'et.id')->where(['te.id_list_emba' => $em->id, 'et.activo' => 1])->get();
            }
            $em->manfiestos = DB::select('CALL sp_embarque_embarcar_manifiesto(?)',[$em->id]);
            foreach ($em->manfiestos as $prov) {
                if($condicion == 'informe' && $prov->tipo_trayecto == 'TERRESTRE'){
                    $prov->guiasEmbarque = DB::table('embarque_manifiesto AS em')->select('g.prefijo','g.nro_guia')
                    ->join('detalle_manifiesto AS dm','em.id_manifiesto','=','dm.id_manifiesto')
                    ->join('guias AS g','dm.id_guia','=','g.id')
                    ->where(['em.id_embarque_ciudad' => $prov->id])->get();
                }
                $prov->listaManifiestos = explode(',',$prov->listaManifiestos);
                if($prov->tipo_base == 'distrito'){
                    $prov->nombreciudad = mb_strtoupper((new Ubigeo)->getNameDistrito($prov->ubigeo_distrito),'UTF-8');
                }
            }
        }
        return $embarques;
    }
    public static function actualizarInforme($datos,$idEmbarque,$usuario)
    { 
        DB::beginTransaction();
        try {
            $datosInformes = json_decode($datos['ciudades_base'], true);
            foreach ($datosInformes as $ciudad) {
                $fecha_reparto = null;
                if (!empty($ciudad['fechaLlegada']) && !empty($ciudad['fechaLlegada'])) {
                    $fecha_reparto = $ciudad['fechaLlegada'];
                    $limite_hora = date('N', strtotime($ciudad['fechaLlegada'])) == 6 ? strtotime('12:00') : strtotime("17:00");
                    $hora_llegada = strtotime($ciudad['hrLlegada']);
                    if ($hora_llegada > $limite_hora) {
                        $fecha_reparto = date('Y-m-d', strtotime($fecha_reparto . '+ 1 day'));
                    }
                    if (date('N', strtotime($fecha_reparto)) == 7) {
                        $fecha_reparto = date('Y-m-d', strtotime($fecha_reparto . '+ 1 day'));
                    }
                }
                $idEmbarqueCiudad = $ciudad['ciudadBase'];
                $datosEmbarqueCiudad = [
                    'ecl.fecha_llegada' => empty($ciudad['fechaLlegada']) ? null : $ciudad['fechaLlegada'],
                    'ecl.hr_llegada' =>  empty($ciudad['hrLlegada']) ? null : $ciudad['hrLlegada'],
                    'ecl.comprobantes' => null,
                    'ecl.nro_comprobantes' => null,
                    'ecl.id_empr_tran_emba' => empty($ciudad['empresaTransporte']) ? null : $ciudad['empresaTransporte'],
                    'ecl.importe_factura' => empty($ciudad['importeFactura']) ? null : $ciudad['importeFactura']
                ];
                if (!empty($ciudad['guiasEmbarque']) && $ciudad['trayecto'] == 'TERRESTRE') {
                    if (strlen(trim($ciudad['guiasEmbarque'])) > 11 && strpos($ciudad['guiasEmbarque'], '-') != 3) {
                        return ['error' => 'La guía de embarque N° ' . $ciudad['guiasEmbarque'] . ' no está con el formato solicitado'];
                    }
                    $nro_guia = explode('-', trim($ciudad['guiasEmbarque']));
                    if (DB::table('guias')->where(['nro_guia' => $nro_guia[1], 'prefijo' => $nro_guia[0]])->count() > 0) {
                        return ['error' => 'La guía de embarque N° <b>' . $ciudad['guiasEmbarque'] . '</b> ya se encuentra registrada, por favor intente con otro número de guía.'];
                    }
                    if (DB::table('embarque_ciudad_llegada')->where(['nro_guia_embarque' => $nro_guia[1], 'prefijo_guia_embarque' => $nro_guia[0]])->where('id', '!=', $idEmbarqueCiudad)->count() > 0) {
                        return ['error' => 'La guía de embarque N° <b>' . $ciudad['guiasEmbarque'] . '</b> está pendiente por registrar, por favor intente con otro número de guía.'];
                    }
                    $datosEmbarqueCiudad['ecl.nro_guia_embarque'] = $nro_guia[1];
                    $datosEmbarqueCiudad['ecl.prefijo_guia_embarque'] = $nro_guia[0];
                }
                if (!empty($ciudad['comprobantes'])) {
                    $datosEmbarqueCiudad['ecl.comprobantes'] = implode(';', $ciudad['comprobantes']);
                }
                if (!empty($ciudad['nroComprobantes'])) {
                    $datosEmbarqueCiudad['ecl.nro_comprobantes'] = implode(';', $ciudad['nroComprobantes']);
                }
                DB::table('embarque AS e')
                ->join('embarque_ciudad_llegada AS ecl', 'e.id', '=', 'ecl.id_embarque')
                ->join('embarque_fletes AS ef', 'ecl.id', '=', 'ef.id_embarque_ciudad')
                ->where(['e.activo' => 1, 'e.id' => $idEmbarque, 'ecl.id' => $idEmbarqueCiudad])
                ->update($datosEmbarqueCiudad);
                    //ACTUALIZAR GUIAS
                DB::table('embarque_manifiesto AS em')
                ->join('detalle_manifiesto AS dm', 'em.id_manifiesto', '=', 'dm.id_manifiesto')
                ->join('guias AS g', 'dm.id_guia', '=', 'g.id')->where(['em.id_embarque_ciudad' => $idEmbarqueCiudad])
                ->update(['g.fecha_reparto' => $fecha_reparto]);
            }
            DB::table('embarque')->where(['id' => $idEmbarque, 'activo' => 1])->update(['date_update' => now(), 'user_update' => $usuario]);
            DB::commit();
            return ['success' => 'Informe actualizado con éxito'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public function scopeVerEmbarqueInforme($query,$idEmbarque)
    {
        $embarque = DB::table('embarque AS e')->select('e.total_bultos_paquete', 'e.fch_emba')->where('id',$idEmbarque)->first();
        $embarque->lista_empresas_transporte = DB::table('transporte_embarque AS te')->select('et.id','et.empresa')->join('empresa_transporte AS et','te.id_empr_tran','=','et.id')->where('id_list_emba',$idEmbarque)->get();
        $embarque->manfiestos = DB::table('embarque_ciudad_llegada AS ecl')
        ->select('ecl.*','c.nombre_ciudad','et.empresa AS empresa_trasporte')->selectRaw('SUM(ef.cajas) AS embarque_nro_cajas,SUM(ef.cajones) AS embarque_nro_cajones, SUM(ef.jabas) AS embarque_nro_jabas, SUM(ef.paquetes) AS embarque_nro_paquetes, SUM(ef.sobres) AS embarque_nro_sobres, SUM(ef.otros) AS embarque_num_otros')
        ->join('embarque_fletes AS ef','ecl.id','=','ef.id_embarque_ciudad')
        ->leftJoin('ciudades AS c','ecl.id_ciudad','=','c.id')
        ->leftJoin('empresa_transporte AS et','ecl.id_empr_tran_emba','=','et.id')
        ->where('ecl.id_embarque',$idEmbarque)->groupBy('ecl.id')->get();
        foreach ($embarque->manfiestos as $detalle) {
            if($detalle->tipo_trayecto == 'TERRESTRE'){
                $detalle->guiasEmbarque = DB::table('embarque_manifiesto AS em')->select('g.prefijo', 'g.nro_guia')
                ->join('detalle_manifiesto AS dm', 'em.id_manifiesto', '=', 'dm.id_manifiesto')
                ->join('guias AS g', 'dm.id_guia', '=', 'g.id')
                ->where(['em.id_embarque_ciudad' => $detalle->id])->get();
            }
            $detalle->nombreciudad = $detalle->tipo_base == 'distrito' ? mb_strtoupper((new Ubigeo)->getNameDistrito($detalle->ubigeo_distrito), 'UTF-8') : $detalle->nombre_ciudad;
        }
        return $embarque;
    }
    public function scopeEditarEmbarque($query,$actualizar,$empresas,$idEmbarque,$idUser)
    {
        DB::beginTransaction();
        try {
            $actualizar['date_update'] = now();
            $actualizar['user_update'] = $idUser;
            $actualizar['id_deal2'] = $actualizar['id_deal2'] == 'NINGUNO' ? null : $actualizar['id_deal2'];
            $query->where('id',$idEmbarque)->update($actualizar);
            DB::table('transporte_embarque')->where('id_list_emba',$idEmbarque)->delete();
            $nuevaEmpresas = [];
            for ($i=0; $i < count($empresas); $i++) {
                $nuevaEmpresas[$i]['id_list_emba'] = $idEmbarque;
                $nuevaEmpresas[$i]['id_empr_tran'] = $empresas[$i];
            }
            DB::table('transporte_embarque')->insert($nuevaEmpresas);
            DB::commit();
            return ['success' => 'Embarque actualizado correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function eliminarEmbarque($idEmbarque)
    {
        DB::beginTransaction();
        try {
            DB::table('embarque_ciudad_llegada AS ec')
            ->join('embarque_manifiesto AS em', 'em.id_embarque_ciudad', '=', 'ec.id')
            ->join("detalle_manifiesto AS dm","dm.id_manifiesto","=","em.id_manifiesto")
            ->join("manifiesto AS m","m.id","=","em.id_manifiesto")
            ->join('guias AS g', 'g.id', '=', 'dm.id_guia')
            ->where(['ec.id_embarque' => $idEmbarque])->update(['m.estado' => 1 ,'g.situ' => 2]);
            DB::table('embarque_manifiesto AS em')
            ->join('embarque_ciudad_llegada AS ec', 'em.id_embarque_ciudad', '=', 'ec.id')
            ->where(['ec.id_embarque' => $idEmbarque])->delete();
            DB::table('embarque_fletes AS em')
            ->join('embarque_ciudad_llegada AS ec', 'em.id_embarque_ciudad', '=', 'ec.id')
            ->where(['ec.id_embarque' => $idEmbarque])->delete();
            DB::table('embarque_ciudad_llegada AS ec')
            ->where(['ec.id_embarque' => $idEmbarque])->delete();
            DB::table('transporte_embarque AS te')
            ->join('embarque AS e', 'e.id', '=', 'te.id_list_emba')
            ->where(['e.id' => $idEmbarque])->delete();
            DB::table('embarque AS e')
            ->where(['e.id' => $idEmbarque])->delete();
            DB::commit();
            return ['success' => 'Embarque eliminado correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public function scopeEditarFlete($query,$idEmbarque,$fletes,$bulto,$total)
    {
        DB::beginTransaction();
        try {
            foreach ($fletes as $key => $value) {
                $actualizarFlete = [];
                foreach ($value as $k2 => $v2) {
                    if($k2 === 'detalle_otros' || $k2 === 'idFlete'){
                        continue;
                    }
                    $fletes[$key][$k2] = empty($v2) ? 0 : $v2;
                    $actualizarFlete['ef.' . $k2] = $fletes[$key][$k2];
                }
                DB::table('embarque AS e')->join('embarque_ciudad_llegada AS ecl', 'e.id', '=', 'ecl.id_embarque')
                ->join('embarque_fletes AS ef', 'ecl.id', '=', 'ef.id_embarque_ciudad')
                ->where(['ef.id' => $value['idFlete'],'e.id' => $idEmbarque])->update($actualizarFlete);
            }
            DB::table('embarque AS e')->where('id',$idEmbarque)->update([ 'e.flet_esti' => $total,'e.total_bultos_paquete' => $bulto]);
            DB::commit();
            return ['success' => 'Flete actualizado correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function mostrarEditarFletes($idEmbarque)
    {
        $fletes = DB::table('embarque AS e')->select('ef.id AS id_embarque_flete', 'ecl.tipo_trayecto AS via_envio','ef.id_cluster',DB::raw('fn_obtener_nombres_clusters(ef.id_cluster) AS nom_cluster'),'ecl.tipo_base', 'ecl.ubigeo_distrito','c.nombre_ciudad','ef.cajas','ef.jabas','ef.cajones','ef.sobres','ef.paquetes','ef.otros','ef.detalle_otros','ef.sub_total_bultos','ef.subtotal_fletes')
        ->join('embarque_ciudad_llegada AS ecl','e.id','=','ecl.id_embarque')
        ->join('embarque_fletes AS ef','ecl.id','=','ef.id_embarque_ciudad')
        ->leftJoin('ciudades AS c', 'ecl.id_ciudad','=','c.id')
        ->where(['e.id' => $idEmbarque])->get();
        foreach ($fletes as $fle) {
            $fle->nombreciudad = $fle->tipo_base == 'ciudad' ? $fle->nombre_ciudad : mb_strtoupper((new Ubigeo)->getNameDistrito($fle->ubigeo_distrito),'UTF-8');
        }
        $total = DB::table('embarque')->select('total_bultos_paquete','flet_esti')->where(['id' => $idEmbarque])->first();
        return ['ciudades' => $fletes,'totales' => $total];
    }
    public static function eliminarManifiestoEmbarque($idEmbarque,$idManifiesto)
    {
        if(DB::table('embarque_ciudad_llegada AS ecl')->join("embarque_manifiesto AS em","ecl.id","=", "em.id_embarque_ciudad")->where(['ecl.id_embarque' => $idEmbarque])->count() === 1){
            return ['error' => 'El embarque debe tener al menos un manifiesto'];
        }
        DB::select('CALL sp_embarque_eliminar_manifiesto(?,?)',[$idEmbarque,$idManifiesto]);
        return ['success' => 'Manifiesto eliminado correctamente'];
    }
    public static function agregarManifiestoEmbarque($idEmbarque, $idManifiesto)
    {
        DB::beginTransaction();
        try {
            $manifiesto = DB::table("manifiesto")->where(["id" => $idManifiesto,"estado" => 1])->first();
            if(empty($manifiesto)){
                return ['error' => 'El manifiesto no existe o ya a sido embarcado'];
            }
            $cluster = DB::table("ciudades_base_cluster")->where(["base" => 1 , "activo" => 1, "id_cluster" => $manifiesto->id_cluster])->first();
            if(empty($cluster)){
                return ['error' => 'No se encontró ciudad base para el cluster relacionado al manifiesto'];
            }
            $ciudadLlegada = DB::table("embarque_ciudad_llegada")->where(["id_ciudad" => $cluster->id_ciudad, "ubigeo_distrito" => $cluster->ubigeo_distrito, "tipo_base" => empty($cluster->id_ciudad) ? "distrito" : "ciudad","id_embarque" => $idEmbarque, 'tipo_trayecto' => $manifiesto->via_envio])->first();
            $idCiudadLlegada = null;
            if(empty($ciudadLlegada)){
                $idCiudadLlegada = DB::table("embarque_ciudad_llegada")->insertGetId([
                    'id_embarque' => $idEmbarque,
                    'id_ciudad' => $cluster->id_ciudad,
                    'ubigeo_distrito' => $cluster->ubigeo_distrito,
                    'tipo_trayecto' => $manifiesto->via_envio
                ]);
            }else{
                $idCiudadLlegada = $ciudadLlegada->id;
            }
            $flete = DB::table("embarque_fletes")->where(['id_embarque_ciudad' => $idCiudadLlegada,'id_cluster' => $cluster->id_cluster])->first();
            if(empty($flete)){
                DB::table("embarque_fletes")->insert([
                    'id_cluster' => $cluster->id_cluster,
                    'id_embarque_ciudad' => $idCiudadLlegada,
                    'estado' => 2
                ]);
            }
            DB::table("embarque_manifiesto")->insert(['id_embarque_ciudad' => $idCiudadLlegada,'id_manifiesto' => $idManifiesto]);
            DB::table("manifiesto AS m")->join("detalle_manifiesto AS dm","dm.id","=","m.id")
            ->join("guias AS g","g.id","=","dm.id_guia")->where(["m.id" => $idManifiesto,"m.estado" => 1])
            ->update(['g.situ' => 3,'m.estado' => 2]);
            DB::commit();
            return ['success' => 'Manifiesto agregado correctamente'];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }
    }
    public static function misEmbarquesFiltros($fechaInicio,$fechaFin,$driver,$dealer,$vehiculo,$estado,$idEmbarque)
    {
        $embarques = DB::select('CALL sp_embarque_obtener(?,?,?,?,?,?,?)',[$fechaInicio, $fechaFin, $driver, $dealer, $vehiculo, $estado, $idEmbarque]);
        foreach ($embarques as $emb) {
            $emb->manifiestos = json_decode($emb->manifiestos,true);
            if(!empty($idEmbarque)){
                $emb->transportes = DB::table('transporte_embarque')->select('id_empr_tran')->where('id_list_emba',$idEmbarque)->get();
            }
        }  
        return $embarques;
    }

    public static function actualizarEmbarque($id_embarque,$id_user,$empresas)
    {
        DB::beginTransaction();
        try {
            $hroInicio = DB::table('embarque')->select('hr_ini_emba')->where(function ($query) use ($id_user) {
                $query->orWhere(['id_cond' => $id_user, 'id_deal' => $id_user, 'id_deal2' => $id_user]);
            })->where(['completado' => 0, 'activo' => 1, 'id' => $id_embarque])->first();
            if(empty($hroInicio)){
                return ['error' => 'Embarque no actualizado debido a que no se le a asignado'];
            }
            if (is_null($hroInicio->hr_ini_emba)) {
                DB::table('embarque')->where(['id' => $id_embarque])->update(['hr_ini_emba' => now()->format('H:i:s'), 'user_update' => $id_user, 'date_update' => now()]);
                DB::commit();
                return ['mensaje' => 'Hora de inicio marcada correctamente'];
            }
            foreach (json_decode($empresas, true) as $empresa) {
                $valor = [
                    'e.completado' => 0,
                    'e.activo' => 1,
                    'ecl.id' => $empresa['ciudadBase'],
                    'e.id' => $id_embarque
                ];
                DB::table('embarque AS e')->join('embarque_ciudad_llegada AS ecl', 'e.id', '=', 'ecl.id_embarque')
                ->join('embarque_fletes AS ef', 'ef.id_embarque_ciudad', '=', 'ecl.id')->where(function ($query) use ($id_user) {
                    $query->orWhere(['e.id_cond' => $id_user, 'e.id_deal' => $id_user, 'e.id_deal2' => $id_user]);
                })->where($valor)->update(['ecl.id_empr_tran_emba' => $empresa['transporte'],'ef.estado' => 3]);
            }
            DB::table('embarque AS e')
            ->join('embarque_ciudad_llegada AS ecl', 'e.id', '=', 'ecl.id_embarque')
            ->join('embarque_manifiesto AS em', 'ecl.id','=','em.id_embarque_ciudad')
            ->join('detalle_manifiesto AS dm','em.id_manifiesto','=','dm.id_manifiesto')
            ->join('guias AS g','dm.id_guia','=','g.id')
            ->join('manifiesto AS m','em.id_manifiesto','=','m.id')
            ->where(function ($query) use ($id_user) {
                $query->orWhere(['id_cond' => $id_user, 'id_deal' => $id_user, 'id_deal2' => $id_user]);
            })->where(['e.completado' => 0, 'e.activo' => 1, 'e.id' => $id_embarque])
            ->update(['m.user_update' => $id_user, 'm.date_update' => now(),'e.fin_emba' => now(),'e.user_update' => $id_user, 'e.date_update' => now(),'e.completado' => 1,'m.estado' => 3 ,'g.situ' => 4]);
            DB::commit();
            return ['success' => 'Embarque exitoso'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function obtenerEmbarqueInforme($id_usuario)
    {
        return DB::select('CALL sp_obtener_embarques_informar(?)',[$id_usuario]);
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
                $guias->nombreciudad = mb_strtoupper((new Ubigeo)->getNameDistrito($guias->nombreciudad),'UTF-8');
            }
        }
        return $obtener_ciudades;
    }
    public static function agregarInformeEmbarque($datos,$usuario)
    {
        DB::beginTransaction();
        try {
            $datosInformes = json_decode($datos['ciudades_base'],true);
            $idEmbarque = $datos['id_embarque'];
            $competadoTodo = 0;
            foreach ($datosInformes as $ciudad) {
                $fecha_reparto = null;
                if(!empty($ciudad['fechaLlegada']) && !empty($ciudad['fechaLlegada'])){
                    $fecha_reparto = $ciudad['fechaLlegada'];
                    $limite_hora = date('N', strtotime($ciudad['fechaLlegada'])) == 6 ? strtotime('12:00') : strtotime("17:00");
                    $hora_llegada = strtotime($ciudad['hrLlegada']);
                    if ($hora_llegada > $limite_hora) {
                        $fecha_reparto = date('Y-m-d', strtotime($fecha_reparto . '+ 1 day'));
                    }
                    if (date('N', strtotime($fecha_reparto)) == 7) {
                        $fecha_reparto = date('Y-m-d', strtotime($fecha_reparto . '+ 1 day'));
                    }
                }
                
                $idEmbarqueCiudad = $ciudad['ciudadBase'];
                $datosEmbarqueCiudad = [
                    'ecl.fecha_llegada' => empty($ciudad['fechaLlegada']) ? null : $ciudad['fechaLlegada'],
                    'ecl.hr_llegada' =>  empty($ciudad['hrLlegada']) ? null : $ciudad['hrLlegada'],
                    'ecl.comprobantes' => null,
                    'ecl.nro_comprobantes' => null,
                    'ecl.importe_factura' => empty($ciudad['importeFactura']) ? null : $ciudad['importeFactura'],
                    'ef.estado' => 4
                ];
                if(!empty($ciudad['guiasEmbarque']) && $ciudad['trayecto'] == 'TERRESTRE'){
                    if (strlen(trim($ciudad['guiasEmbarque'])) > 11 && strpos($ciudad['guiasEmbarque'], '-') != 3) {
                        return ['error' => 'La guía de embarque N° ' . $ciudad['guiasEmbarque'] . ' no está con el formato solicitado'];
                    }
                    $nro_guia = explode('-', trim($ciudad['guiasEmbarque']));
                    if (DB::table('guias')->where(['nro_guia' => $nro_guia[1], 'prefijo' => $nro_guia[0]])->count() > 0) {
                        return ['error' => 'La guía de embarque N° <b>' . $ciudad['guiasEmbarque'] . '</b> ya se encuentra registrada, por favor intente con otro número de guía.'];
                    }
                    if (DB::table('embarque_ciudad_llegada')->where(['nro_guia_embarque' => $nro_guia[1], 'prefijo_guia_embarque' => $nro_guia[0]])->where('id','!=',$idEmbarqueCiudad)->count() > 0) {
                        return ['error' => 'La guía de embarque N° <b>' . $ciudad['guiasEmbarque'] . '</b> está pendiente por registrar, por favor intente con otro número de guía.'];
                    }
                    $datosEmbarqueCiudad['ecl.nro_guia_embarque'] = $nro_guia[1];
                    $datosEmbarqueCiudad['ecl.prefijo_guia_embarque'] = $nro_guia[0];
                }else if(empty($ciudad['guiasEmbarque']) && $ciudad['trayecto'] == 'TERRESTRE'){
                    $datosEmbarqueCiudad['ef.estado'] = 3;
                }
                if(!empty($ciudad['comprobantes'])){
                    $datosEmbarqueCiudad['ecl.comprobantes'] = implode(';', $ciudad['comprobantes']);
                }
                if (!empty($ciudad['nroComprobantes'])) {
                    $datosEmbarqueCiudad['ecl.nro_comprobantes'] = implode(';', $ciudad['nroComprobantes']);
                }
                foreach ($datosEmbarqueCiudad as $v) {
                    if(empty($v)){
                        $datosEmbarqueCiudad['ef.estado'] = 3;
                        break;
                    }
                }
                DB::table('embarque AS e')
                ->join('embarque_ciudad_llegada AS ecl','e.id','=', 'ecl.id_embarque')
                ->join('embarque_fletes AS ef','ecl.id','=','ef.id_embarque_ciudad')
                ->where(function ($query) use ($usuario) {
                    $query->orWhere(['e.id_deal' => $usuario, 'e.id_deal2' => $usuario]);
                })->where(['e.activo' => 1, 'e.id' => $idEmbarque,'ecl.id' => $idEmbarqueCiudad])
                ->whereIn('ef.estado',[3,4])->whereIn('e.completado',[1,2,3])->update($datosEmbarqueCiudad);
                if($datosEmbarqueCiudad['ef.estado'] == 4){
                    $competadoTodo++;
                    DB::table('embarque_manifiesto AS em')
                    ->join('manifiesto AS m', 'em.id_manifiesto', '=', 'm.id')
                    ->where(['m.activo' => 1, 'm.estado' => 3, 'em.id_embarque_ciudad' => $idEmbarqueCiudad])
                    ->update(['m.estado' => 4, 'date_update' => now(), 'user_update' => $usuario]);
                    //ACTUALIZAR GUIAS
                    DB::table('embarque_manifiesto AS em')
                    ->join('detalle_manifiesto AS dm', 'em.id_manifiesto', '=', 'dm.id_manifiesto')
                    ->join('guias AS g', 'dm.id_guia', '=', 'g.id')->where(['em.id_embarque_ciudad' => $idEmbarqueCiudad])
                    ->update(['g.fecha_reparto' => $fecha_reparto]);
                }
            }
            $completo = count($datosInformes) === $competadoTodo ? 3 : 2;
            DB::table('embarque')->where(['id' => $idEmbarque,'activo' => 1])->update(['completado' => $completo,'date_update' => now(),'user_update' => $usuario]);
            DB::commit();
            return ['success' => 'Informe procesado con éxito'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function obtenerEmbarqueArribado($id_embarque,$id_guia,$id_usuario,$tipo,$trayecto)
    {
        $result = DB::select('CALL sp_obtener_embarques_arribar(?,?,?,?,?)',[$id_embarque,$id_guia,$id_usuario,$tipo, $trayecto]);
        switch ($tipo) {
            case 'manifiestos':
                foreach ($result as $mani) {
                    $mani->prov_dest = (new Ubigeo)->getNameProvincia($mani->prov_dest);
                    $mani->prov_orig = (new Ubigeo)->getNameProvincia($mani->prov_orig);
                    $mani->guias = DB::select('CALL sp_obtener_embarques_arribar(?,?,?,?,?)',[$mani->id,0, $id_usuario, 'obtener-guias', $trayecto]);
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
            $cluster = DB::table("cluster")->where('id_usuario',$usuario)->first();
            $nuevo_retorno['id_cluster'] = $cluster->id;
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
    public static function actualizarGuiaLlegadas($id_embarque,$id_manifiesto,$id_guia,$estado, $texto,$situacion,$incidencia,$flete,$usuario)
    {
        return DB::select('CALL sp_actualizar_llegadas_guias(?,?,?,?,?,?,?,?,?)',[$id_embarque, $id_manifiesto,$id_guia,$estado,$texto, $situacion, $incidencia,$flete, $usuario]);
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
