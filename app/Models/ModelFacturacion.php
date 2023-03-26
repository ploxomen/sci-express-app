<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Illuminate\Database\Eloquent\Model;
use App\Models\ModeloCluster;
use Illuminate\Support\Facades\DB;

class ModelFacturacion extends Model
{
    public static function obtenerGuiasFacturacion($cliente,$traslado,$fechaInicio,$fechaFin,$estado)
    {
        $guias = self::obtenerGuiasFactClis($cliente,$traslado,$fechaInicio,$fechaFin,$estado);
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
    public static function verDetalleGuia($idGuia)
    {
        return DB::table("detalle_guias AS dg")->select("dg.grupo_envio","dg.descripcion","dg.embase_embalaje","ft.tipo_envio","dg.cantidad","dg.kg","dg.volumen")
        ->join("facturacion_tipo_envio AS ft","ft.id","=","dg.tipo_envio")
        ->where(['dg.id_guia'=>$idGuia])->get();
    }
    public static function totalGuiasAfacturarCluster($cluster)
    {
        $idsCluster = self::obtenerClustersIguales($cluster);
        return DB::table("guias AS g")->whereIn("g.id_cluster",$idsCluster)->where(['g.activo' => 1,'g.estado' => 1])->where('g.situ','>=',11)->where('g.situ','!=',14)->where("g.estado_fact_cluster",0)->count();
    }
    public static function facturarGuiasClusters($guias)
    {
        foreach ($guias as $guia) {
            $query = DB::table("guias")->where(['id' => $guia['idGuia'],'activo' => 1,'estado' => 1, 'estado_fact_cluster' => 0])->where('situ','>=',11);
            $query->increment('fact_cluster_total',floatval($guia['mov2']));
            $query->update(["fact_cluster_mov2" => $guia['mov2'],'estado_fact_cluster' => 1]);            
        }
        return ['success' => 'guias facturadas correctamente'];
    }
    public static function obtenerGuiasFacturacionCluster($cluster,$traslado,$fechaInicio,$fechaFin,$estado)
    {
        $idsCluster = self::obtenerClustersIguales($cluster);
        $guias = DB::table("guias AS g")->select("g.id","ec.tipo_base AS tipoBaseCluster","g.comp_clie","g.nro_comp_cli","cc.nombres AS nomDest","c.nom_cli","g.tot_kg","g.tot_vol AS pesoVolumen","g.fact_cluster_mov1","g.fact_cluster_mov2","g.fact_cluster_sub","g.fact_cluster_total","g.tot_cant")->selectRaw("DATE_FORMAT(g.fech_emis,'%d/%m/%Y') AS fechaEmision,IF(g.prefijo IS NULL, LPAD(g.nro_guia,7,'0'), CONCAT(LPAD(g.prefijo,3,'0'),'-',LPAD(g.nro_guia,7,'0'))) AS nroGuia,IF(ec.tipo_base = 'ciudad',ci.nombre_ciudad,ec.ubigeo_distrito) AS baseCluster,IF(g.ciudad_destino_final IS NULL,g.dest_dist,ci2.nombre_ciudad) AS nomCiudadFinal,IF(g.prefijo IS NULL, 'OS', 'GUIA') AS tipoGuiaNom,IF(g.ciudad_destino_final IS NULL,'distrito','ciudad') AS tipoFinal")
        ->leftJoin("clientes_de_clientes AS cc","g.id_destinatario","=","cc.id")
        ->leftJoin("clientes AS c","c.id","=","g.id_cliente_facturacion")
        ->leftJoin("detalle_manifiesto AS dm","dm.id_guia","=","g.id")
        ->leftJoin("embarque_manifiesto AS em","em.id_manifiesto","=","dm.id_manifiesto")
        ->leftJoin("embarque_ciudad_llegada AS ec","ec.id","=","em.id_embarque_ciudad")
        ->leftJoin("ciudades AS ci","ci.id","=","ec.id_ciudad")
        ->leftJoin("ciudades AS ci2","ci2.id","=","g.ciudad_destino_final")
        ->whereIn("g.id_cluster",$idsCluster)->where(['g.activo' => 1,'g.estado' => 1,'g.via_tras' => $traslado])->where('g.situ','>=',11)->where('g.situ','!=',14)->where("g.estado_fact_cluster",$estado == "11" ? 0 : 1)->whereBetween('g.fech_emis',[$fechaInicio,$fechaFin])->groupBy("g.id")->get();
        foreach ($guias as $guia) {
            $guia->baseCluster = $guia->tipoBaseCluster == "distrito" ? (new Ubigeo)->getNameDistrito($guia->baseCluster) : $guia->baseCluster;
            $guia->nomCiudadFinal = $guia->tipoFinal == "distrito" ? (new Ubigeo)->getNameDistrito($guia->nomCiudadFinal) : $guia->nomCiudadFinal;
        }
        return $guias;

    }
    public static function obtenerGuiasFactClis($cliente,$traslado,$fechaInicio,$fechaFin,$estado)
    {
        return DB::select('CALL sp_facturacion_ver_guias(?,?,?,?,?)',[$cliente, $fechaInicio,$fechaFin,$traslado,$estado]);
    }
    public static function calcularGuiaFacturacionCluster($idGuia,$distritoDest,$ciudadDest,$idsCluster,$viaFacturacion,$pesoFact)
    {
        $tipoCiudad = empty($ciudadDest) ? "distrito" : "ciudad";
        $ciudadesCluster = DB::table("ciudades_base_cluster")->where(["tipo_base" => $tipoCiudad,"activo" => 1,"ubigeo_distrito" => $distritoDest])->whereIn("id_cluster",$idsCluster);
        if($tipoCiudad == "ciudad"){
            $ciudadesCluster = $ciudadesCluster->where("id_ciudad",$idsCluster);
        }
        $ciudadesCluster = $ciudadesCluster->first();
        if(empty($ciudadesCluster)){
            return 0;
        }
        $detallesGuias = DB::table("detalle_guias")->where('id_guia',$idGuia)->get();
        $total = 0;
        
        foreach ($detallesGuias as $detalleGuia) {
            $pesoDetalle = $detalleGuia->volumen > $detalleGuia->kg ? floatval($detalleGuia->volumen) : floatval($detalleGuia->kg);
            if($pesoFact === 1){
                $pesoDetalle = floatval($detalleGuia->kg);
            }
            $cabecera1 = DB::table("facturacion_cluster_cabecera AS fa")->select("fcc.*")
            ->join("facturacion_cluster_valor AS fc","fa.id","=","fc.id_cabecera")
            ->join("facturacion_cluster_ciudades_cabecera AS fcc","fc.id_cabecera_ciudades","=","fcc.id")
            ->whereIn('fa.id_cluster',$idsCluster)->where('fa.id_via_envio',$viaFacturacion)
            ->whereRaw('? > fa.kg_minimo',[$pesoDetalle])->whereRaw("? <= fa.kg_maximo",[$pesoDetalle])->where(['fc.id_tipo_envio' => $detalleGuia->tipo_envio])->first();
            if(empty($cabecera1)){
                continue;
            }
            $fact = DB::table("facturacion_cluster_ciudades_valor")->where(['id_ciudad' => $ciudadesCluster->id,'id_cabecera' => $cabecera1->id])->first();
            if(empty($fact)){
                continue;
            }
            switch ($cabecera1->tipo_calculo) {
                case 'adicional':
                    $fila2 = DB::table("facturacion_cluster_ciudades_cabecera AS fc")->select("fcc.valor","fc.peso_maximo")
                    ->join("facturacion_cluster_ciudades_valor AS fcc","fc.id","=","fcc.id_cabecera")->where(['fc.tipo_calculo' => 'base','fc.id_via_envio' => $viaFacturacion,'fcc.id_ciudad' => $ciudadesCluster->id])->whereIn("fc.id_cluster",$idsCluster)->where('fc.numero','<',$cabecera1->numero)->orderBy("fc.numero","desc")->first();
                    if(!empty($fila2)){
                        $total = $total + ($fila2->valor + (($pesoDetalle - $fila2->peso_maximo) * $fact->valor));
                    }
                break;
                default:
                    $total = $total + $fact->valor;
                break;
            }
        }
        return $total;
    }
    public static function calcularGuiasFacturacionCli($idGuia,$idCliente,$viaFacturacion,$distrito,$ciudad)
    {
        if(empty($ciudad)){
            $nombreCiudad = (new Ubigeo)->getNameDistrito($distrito);
        }else{
            $ciu = DB::table("ciudades")->where('id',$ciudad)->first();
            $nombreCiudad = empty($ciu) ? null : $ciu->nombre_ciudad;
        }
        $ciudadFacturacion = DB::table("facturacion_ciudades")->where("ciudad",mb_strtoupper($nombreCiudad,"UTF-8"))->first();
        if(empty($ciudadFacturacion)){
            return 0;
        }
        $cli = DB::table("clientes")->where('id',$idCliente)->first();
        $idCliente = $cli->doc_ident != 4 ? Clientes::idClienteFacturacionNatural() : $cli->id;
        $detallesGuias = DB::table("detalle_guias")->where('id_guia',$idGuia)->get();
        $total = 0;
        foreach ($detallesGuias as $detalleGuia) {
            $pesoDetalle = $detalleGuia->volumen > $detalleGuia->kg ? floatval($detalleGuia->volumen) : floatval($detalleGuia->kg);
            $cabecera1 = DB::table("facturacion_cabecera_envio")->where('id_cliente',$idCliente)->where('id_via_envio',$viaFacturacion)
            ->whereRaw('? > kg_minimo',[$pesoDetalle])->whereRaw("? <= kg_maximo",[$pesoDetalle])->first();
            if(empty($cabecera1)){
                continue;
            }
            $valor1 = DB::table("facturacion_cliente_asignacion AS fc")->select("fcc.*")->join("facturacion_cabezera_cliente AS fcc","fc.id_cabecera_cliente","=","fcc.id")->where(['fc.id_tipo_envio' => $detalleGuia->tipo_envio,'fc.id_cabecera_envio' => $cabecera1->id])->first();
            if(empty($valor1)){
                continue;
            }
            
            $fact = DB::table("facturacion_cliente_valor")->where(['id_ciudad' => $ciudadFacturacion->id,'id_cabecera' => $valor1->id])->first();
            if(empty($fact)){
                continue;
            }
            switch ($valor1->tipo_calculo) {
                case 'adicional':
                    $fila2 = DB::table("facturacion_cabezera_cliente AS fc")->select("fcc.valor","fc.peso_maximo")
                    ->join("facturacion_cliente_valor AS fcc","fc.id","=","fcc.id_cabecera")->where(['fc.id_cliente' => $idCliente,'fc.tipo_calculo' => 'base','fc.id_via_envio' => $viaFacturacion,'fcc.id_ciudad' => $ciudadFacturacion->id])->where('numero','<',$valor1->numero)->orderBy("fc.numero","desc")->first();
                    if(!empty($fila2)){
                        $total = $total + ($fila2->valor + (($pesoDetalle - $fila2->peso_maximo) * $fact->valor));
                    }
                break;
                default:
                    $total = $total + $fact->valor;
                break;
            }
        }
        return $total;
    }
    public static function sincronizarFacturacionCluster($idCluster)
    {
        $idsCluster = self::obtenerClustersIguales($idCluster);
        $clusterKg = DB::table("cluster")->find($idCluster);
        $guias = DB::table("guias")->where(['activo' => 1,'estado' => 1, 'situ' => 11,'estado_fact_cluster' => 0])->whereIn("id_cluster",$idsCluster)->get();
        self::calcularGuiasClu($guias,$idsCluster,$clusterKg->facturacion_peso);
    }
    public static function sincronizarFacturacionCliente($cliente)
    {
        $guias = DB::table("guias")->where(['activo' => 1,'estado' => 1, 'situ' => 11, 'id_cliente_facturacion' => $cliente])->get();
        self::calcularGuias($guias);
    }
    public static function facturarGuiasClientes($guias,$cliente,$subtotal,$igv,$total, $idUsuario)
    {
        DB::beginTransaction();
        try {
            $guiasAdd = [];
            foreach ($guias as $guia) {
                $g = DB::table("guias")->where(['id' => $guia['idGuia'],'id_cliente_facturacion' => $cliente,'activo' => 1, 'situ' => 11, 'estado' => 1]);
                $gg = $g->first();
                if(!empty($gg)){
                    $guiasAdd[] = ['id_cliente' => $gg->id];
                    $g->update(['situ' => 12]);
                }
            }
            if(count($guiasAdd) > 0){
                $idFactFianl = DB::table("facturacion_cliente_final")->insertGetId([
                    'sub_total' => $subtotal,
                    'igv' => $igv,
                    'total' => $total,
                    'usuario_creado' => $idUsuario
                ]);
                foreach ($guiasAdd as $key => $value) {
                    $guiasAdd[$key]['id_facturacion_final'] = $idFactFianl;
                }
                DB::table("facturacion_cliente_final_detalle")->insert($guiasAdd);
            }
            DB::commit();
            return ['success' => 'guias facturadas de forma correcta'];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }
    }
    
    public static function calcularGuiasClu($guias,$idsCluster,$pesoFact)
    {
        foreach ($guias as $guia) {
            $subTotal = self::calcularGuiaFacturacionCluster($guia->id,$guia->dest_dist,$guia->ciudad_destino_final,$idsCluster,$guia->via_tras,$pesoFact);
            $total2 = $subTotal + floatval($guia->fact_cluster_mov1);
            $total = $total2 + floatval($guia->fact_cluster_mov2);
            DB::table("guias")->where('id',$guia->id)->update(["fact_cluster_sub" => $subTotal, 'fact_cluster_total' => $total]);
        }
    }
    public static function calcularGuias($guias)
    {
        foreach ($guias as $guia) {
            $subTotal = self::calcularGuiasFacturacionCli($guia->id,$guia->id_cliente_facturacion,$guia->via_tras,$guia->dest_dist,$guia->ciudad_destino_final);
            $total = 0;
            if($guia->ciudad_alejada == "Complejo - Distante" && !empty($guia->valor_complejo_distante)){
                $total = $subTotal + (($guia->valor_complejo_distante * $subTotal) / 100);
            }else if($guia->ciudad_alejada == "Rural - Alejada" && !empty($guia->valor_complejo_distante)){
                $total = $subTotal + $guia->valor_complejo_distante;
            }else{
                $total = $subTotal;
            }
            DB::table("guias")->where('id',$guia->id)->update(["fact_final" => $total, 'fact_sub' => $subTotal]);
        }
    }
    public static function agregarCiudades(array $ciudades){
        return DB::table("facturacion_ciudades")->insert($ciudades);
    }
    public static function mostrarCiudades(){
        return DB::table("facturacion_ciudades")->orderBy("ciudad")->get();
    }
    public static function cantidadGuiasAFacturar($idCliente,$viaTraslado)
    {
        return DB::table("guias")->where(["situ" => 11,"activo" => 1, "estado" => 1,"id_cliente_facturacion" => $idCliente, "via_tras" => $viaTraslado]);
    }
    public static function mostrarFacturacionClientes($idCliente){
        $ciudadesBase = self::mostrarCiudades();
        $cabeceras = DB::table("facturacion_cabezera_cliente AS fc")
        ->select("fc.id","fc.id_cliente","fc.id_via_envio","fc.numero","fc.tipo_calculo","fc.peso_maximo","fc.descripcion","ve.envio")
        ->join("via_envio AS ve","fc.id_via_envio","=","ve.id")->where("fc.id_cliente",$idCliente)->orderBy("fc.numero")->get();
        $facturacion = [];
        foreach($ciudadesBase as $ciudad){
            foreach($cabeceras as $cabecera){
                $valor = DB::table("facturacion_cliente_valor")->where(['id_cabecera' => $cabecera->id,'id_ciudad' => $ciudad->id])->first();
                $valorVal = empty($valor) ? ["tipo" => "nuevo","valor" => "0.00"] : ["tipo" => "antiguo", "valor" => $valor->valor, "id" => $valor->id];
                $valorVal["ciudad"] = $ciudad->id;
                $valorVal["cabecera"] = $cabecera->id;
                $facturacion[] = $valorVal;
            }
        }
        $facturacionTipoEnvio = self::facturacionSegunda($idCliente);
        $response = ["ciudadesBase" => $ciudadesBase, "cabeceras" => $cabeceras, "valores" => $facturacion];
        return array_merge($facturacionTipoEnvio,$response);
    }
    public static function obtenerCabeceraBase($idCliente,$tipoEnvio,$numero)
    {
        return DB::table("facturacion_cabezera_cliente")->where(['id_cliente' => $idCliente,'id_via_envio' => $tipoEnvio,'tipo_calculo' => 'base'])->where('numero','<',$numero)->orderByDesc("numero");
    }
    public static function obtenerCabeceraBaseCluster($idCluster,$tipoEnvio,$numero)
    {
        return DB::table("facturacion_cluster_ciudades_cabecera")->where(['id_cluster' => $idCluster,'id_via_envio' => $tipoEnvio,'tipo_calculo' => 'base'])->where('numero','<',$numero)->orderByDesc("numero");
    }
    public static function agregarCabeceraClusterCiudad($idUsuario,$datos)
    {
        DB::beginTransaction();
        try {
            if($datos['tipo_calculo'] == "adicional"){
                $base = self::obtenerCabeceraBaseCluster($datos['id_cluster'],$datos['id_via_envio'],$datos['numero']);
                if(empty($base->first())){
                    return ['error' => 'No se encontró ningun número base, por favor establesca una base para poder agregar una columna de tipo adicional'];
                }
            }
            $montoFau = $datos['montofau'];
            $monto = $datos['monto'];
            $datos['fecha_creada'] = now();
            $datos['usuario_creado'] = $idUsuario;
            $idCabecera = DB::table("facturacion_cluster_ciudades_cabecera")->insertGetId($datos);
            unset($datos['montofau']);
            unset($datos['monto']);
            $idsClusters = self::obtenerClustersIguales($datos['id_cluster']);
            if(is_null($idsClusters)){
                return ['error' => 'cluster no encontrado'];
            }
            $ciudadesBase = ModeloCluster::ciudadesClusters($idsClusters);
            foreach ($ciudadesBase as $ciudad) {
                self::agregarModificarValorFactClus($idCabecera,$ciudad->id,$monto,$montoFau,$ciudad->ciudad_alejada);
            }
            DB::commit();
            return ['success' => 'Columna agregada correctamente'];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }
    }
    public static function agregarModificarValorFactClus($idCabecera,$idCiudad,$monto,$montoFau,$fau)
    {
        $response =  DB::table("facturacion_cluster_ciudades_valor")->updateOrInsert(['id_cabecera' => $idCabecera, 'id_ciudad' => $idCiudad],['valor' => $fau == "Rural - Alejada" || $fau == "Complejo - Distante" ? $montoFau : $monto]);
        return $response;
    }
    public static function agregarCabecera($idUsuario,$datos)
    {
        if($datos['tipo_calculo'] == "adicional"){
            $base = self::obtenerCabeceraBase($datos['id_cliente'],$datos['id_via_envio'],$datos['numero']);
            if(empty($base->first())){
                return ['error' => 'No se encontró ningun número base, por favor establesca una base para poder agregar una columna de tipo adicional'];
            }
        }
        $montoFau = $datos['montofau'];
        $monto = $datos['monto'];
        $datos['fecha_creada'] = now();
        $datos['usuario_creado'] = $idUsuario;
        unset($datos['montofau']);
        unset($datos['monto']);
        $idCabecera = DB::table("facturacion_cabezera_cliente")->insertGetId($datos);
        foreach (self::mostrarCiudades() as $ciudad) {
            self::agregarModificarValorFactCli($idCabecera,$ciudad->id,$monto,$montoFau,$ciudad->fau);
        }
        return ['success' => 'Columna agregada correctamente'];
    }
    public static function agregarModificarValorFactCli($idCabecera,$idCiudad,$monto,$montoFau,$fau)
    {
        $response =  DB::table("facturacion_cliente_valor")->updateOrInsert(['id_cabecera' => $idCabecera, 'id_ciudad' => $idCiudad],['valor' => $fau ? $montoFau : $monto]);
        return $response;
    }
    public static function mostrarCabeceraEditar(int $idCabecera, int $idCliente)
    {
        return ["datos" => DB::table("facturacion_cabezera_cliente")->selectRaw("id_via_envio AS 'lista-via-envio',numero AS 'id_cabecera_numero', tipo_calculo AS 'lista-tipo-cabecera',peso_maximo AS 'id-kg-base',descripcion AS 'id_descripcion'")->where(['id' => $idCabecera, 'id_cliente' => $idCliente])->first(), "maximo" => self::mostrarMaximoCabecera($idCliente)->numero];
    }
    public static function mostrarCabeceraEditarCluster(int $idCabecera, int $idCluster)
    {
        return ["datos" => DB::table("facturacion_cluster_ciudades_cabecera")->selectRaw("id_via_envio AS 'lista-via-envio',numero AS 'id_cabecera_numero', tipo_calculo AS 'lista-tipo-cabecera',peso_maximo AS 'id-kg-base',descripcion AS 'id_descripcion'")->where(['id' => $idCabecera, 'id_cluster' => $idCluster])->first(), "maximo" => self::mostrarMaximoCabeceraCluster($idCluster)->numero];
    }
    public static function mostrarMaximoCabecera(int $idCliente)
    {
        return DB::table("facturacion_cabezera_cliente")->where(['id_cliente' => $idCliente])->orderByDesc("numero")->first();
    }
    public static function mostrarMaximoCabeceraCluster(int $idCluster)
    {
        return DB::table("facturacion_cluster_ciudades_cabecera")->where(['id_cluster' => $idCluster])->orderByDesc("numero")->first();
    }
    public static function editarCabecera(int $idUsuario, array $datos)
    {
        $idCabecera = $datos['cabecera'];
        $idCliente = $datos['id_cliente'];
        unset($datos['cabecera']);
        unset($datos['id_cliente']);
        $datos['usuario_modificado'] = $idUsuario;
        $datos['fecha_modificada'] = now();
        DB::table("facturacion_cabezera_cliente")->where(['id' => $idCabecera, 'id_cliente' => $idCliente])->update($datos);
        return ["success" => 'cabecera modificada correctamente'];
    }
    public static function editarCabeceraClusterCiudad(int $idUsuario, array $datos)
    {
        $idCabecera = $datos['cabecera'];
        $idCluster = $datos['id_cluster'];
        unset($datos['cabecera']);
        unset($datos['id_cluster']);
        $datos['usuario_modificado'] = $idUsuario;
        $datos['fecha_modificada'] = now();
        DB::table("facturacion_cluster_ciudades_cabecera")->where(['id' => $idCabecera, 'id_cluster' => $idCluster])->update($datos);
        return ["success" => 'cabecera modificada correctamente'];
    }
    public static function eliminarCabecera(int $idCabecera, int $idCliente)
    {
        DB::beginTransaction();
        try {
            DB::table("facturacion_cliente_asignacion AS fa")->join("facturacion_cabezera_cliente AS fc","fa.id_cabecera_cliente","=",'fc.id')
            ->where(['fc.id' => $idCabecera,'fc.id_cliente' => $idCliente])->delete();
            DB::table("facturacion_cliente_valor AS fv")->join("facturacion_cabezera_cliente AS fc","fv.id_cabecera","=",'fc.id')
            ->where(['fc.id' => $idCabecera,'fc.id_cliente' => $idCliente])->delete();
            DB::table("facturacion_cabezera_cliente")->where(['id' => $idCabecera,'id_cliente' => $idCliente])->delete();
            DB::commit();
            return ['success' => 'cabecera eliminada correctamente'];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }
    }
    public static function eliminarCabeceraCluster(int $idCabecera, int $idCluster)
    {
        DB::beginTransaction();
        try {
            DB::table("facturacion_cluster_valor AS fa")->join("facturacion_cluster_ciudades_cabecera AS fc","fa.id_cabecera_ciudades","=",'fc.id')
            ->where(['fc.id' => $idCabecera,'fc.id_cluster' => $idCluster])->delete();
            DB::table("facturacion_cluster_ciudades_valor AS fv")->join("facturacion_cluster_ciudades_cabecera AS fc","fv.id_cabecera","=",'fc.id')
            ->where(['fc.id' => $idCabecera,'fc.id_cluster' => $idCluster])->delete();
            DB::table("facturacion_cluster_ciudades_cabecera")->where(['id' => $idCabecera,'id_cluster' => $idCluster])->delete();
            DB::commit();
            return ['success' => 'cabecera eliminada correctamente'];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }
    }
    public static function sincronizarCiudadesBase()
    {
        $ciudades = ModeloCluster::obtenerCiudadesBase();
        $ciudadesBase = self::mostrarCiudades();
        $ciudadesAgregar = [];
        $ciudadEliminar = [];
        foreach ($ciudades as $ciudad) {
            $nomCiudad = $ciudad->nombre_ciudad;
            $exist = array_filter($ciudadesBase->toArray(),function($c)use($nomCiudad){
                return $c->ciudad == $nomCiudad;
            });
            if(empty($exist)){
                $ciudadesAgregar[] = ['ciudad' => $nomCiudad];
            }
            DB::table("facturacion_ciudades")->updateOrInsert([
                'ciudad' => $nomCiudad
            ],['fau' => $ciudad->ciudad_alejada != 'Ninguno' ? 1 : 0]);
        }
        foreach ($ciudadesBase as $ciudad) {
            $nomCiudad = $ciudad->ciudad;
            $exist = array_filter($ciudades->toArray(),function($c)use($nomCiudad){
                return $c->nombre_ciudad == $nomCiudad;
            });
            if(empty($exist)){
                $ciudadEliminar[] = ['ciudad' => $nomCiudad];
                DB::table("facturacion_cliente_valor")->where('id_ciudad',$ciudad->id)->delete();
                DB::table("facturacion_ciudades")->where('id',$ciudad->id)->delete();
            }
        }
        return ['ciudades' => $ciudadesAgregar,'ciudadesEliminar' => $ciudadEliminar];
    }
    public static function valorMaximo()
    {
        return 999999999.99;
    }
    public static function facturacionSegunda(int $idCliente)
    {
        $filas = DB::table("facturacion_tipo_envio")->orderBy("tipo_envio")->get();
        $columnas = DB::table("facturacion_cabecera_envio AS fce")->select("fce.id","ve.envio","fce.kg_minimo","fce.kg_maximo","fce.id_via_envio")
        ->join("via_envio AS ve","fce.id_via_envio","=","ve.id")->where("fce.id_cliente",$idCliente)->orderBy("fce.id_via_envio")->orderBy("fce.kg_minimo")->get();
        $facturacion = [];
        $cabeceraChange = null;
        $lista = [];
        foreach($filas as $fila){
            foreach($columnas as $columna){
                if($columna->id_via_envio != $cabeceraChange){
                    $lista = DB::table("facturacion_cabezera_cliente")->select("id","numero")->where(["id_cliente" => $idCliente, "id_via_envio" => $columna->id_via_envio])->orderBy("numero")->get()->toArray();
                    $cabeceraChange = $columna->id_via_envio;
                }
                $valor = DB::table("facturacion_cliente_asignacion")->where(['id_cabecera_envio' => $columna->id,'id_tipo_envio' => $fila->id])->first();
                $valorVal = empty($valor) ? ["id" => 0, "columna" => $columna->id , "fila" => $fila->id] : ["id" => $valor->id_cabecera_cliente, "columna" => $columna->id, "fila" => $fila->id];
                $facturacion[] = [
                    'listaAsignacion' => $lista,
                    'valor' => $valorVal
                ];
            }
        }
        return ['filaEnvio' => $filas, 'columnaEnvio' => $columnas, 'valorEnvio' => $facturacion];
    }
    public static function agregarCabeceraCosto(int $idUsuario, array $datos)
    {
        $datos['user_create'] = $idUsuario;
        $datos['date_create'] = now();
        if(isset($datos['active_infinito'])){
            $datos['kg_maximo'] = self::valorMaximo();
            unset($datos['active_infinito']);
        }
        DB::table("facturacion_cabecera_envio")->insert($datos);
        return array_merge(['success' => 'cabecera agregada correctamente'],self::facturacionSegunda($datos['id_cliente']));
    }
    public static function mostrarCabeceraEditarCosto(int $idCliente, int $idCabecera)
    {
        return ['success' => DB::table("facturacion_cabecera_envio")->selectRaw("id_via_envio AS id_via_envio_costo,kg_minimo AS id_cabecera_minimo_costo,kg_maximo AS id_cabecera_maximo_costo")->where(['id_cliente' => $idCliente,'id' => $idCabecera])->first()];
    }
    public static function editarCabeceraCosto(int $idUsuario, array $datos)
    {
        $idCliente = $datos['id_cliente'];
        $idCabecera = $datos['cabecera'];
        unset($datos['id_cliente']);
        unset($datos['cabecera']);
        $datos['user_update'] = $idUsuario;
        $datos['date_update'] = now();
        if(isset($datos['active_infinito'])){
            $datos['kg_maximo'] = self::valorMaximo();
            unset($datos['active_infinito']);
        }
        DB::table("facturacion_cabecera_envio")->where(['id' => $idCabecera,'id_cliente' => $idCliente])->update($datos);
        return array_merge(['success' => 'cabecera actualizada correctamente'],self::facturacionSegunda($idCliente));
    }
    public static function eliminarCabeceraCosto(int $cabecera, int $idCliente)
    {
        DB::beginTransaction();
        try {   
            DB::table("facturacion_cliente_asignacion AS fc")->join("facturacion_cabecera_envio AS fce","fc.id_cabecera_envio",'=','fce.id')
            ->where(['fc.id_cabecera_envio' => $cabecera, 'fce.id_cliente' => $idCliente])->delete();
            DB::table("facturacion_cabecera_envio")->where(['id' => $cabecera, 'id_cliente' => $idCliente])->delete();
            DB::commit();
            return array_merge(['success' => 'cabecera eliminada correctamente'],self::facturacionSegunda($idCliente));
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' =>$th->getMessage()];
        }
    }
    public static function cambiarValorCosto(int $cabecera, int $tipoEnvio, int $valor)
    {
        if($valor == 0){
            DB::table("facturacion_cliente_asignacion")->where(['id_cabecera_envio' => $cabecera,'id_tipo_envio' => $tipoEnvio])->delete();
        }
        if($valor != 0){
            DB::table("facturacion_cliente_asignacion")->updateOrInsert(['id_tipo_envio' => $tipoEnvio, 'id_cabecera_envio' => $cabecera],['id_cabecera_cliente' => $valor]);
        }
        return ['success' => true];
    }
    public static function cambiarValorCostoCluster(int $cabecera, int $tipoEnvio, int $valor)
    {
        if($valor == 0){
            DB::table("facturacion_cluster_valor")->where(['id_cabecera' => $cabecera,'id_tipo_envio' => $tipoEnvio])->delete();
        }
        if($valor != 0){
            DB::table("facturacion_cluster_valor")->updateOrInsert(['id_tipo_envio' => $tipoEnvio, 'id_cabecera' => $cabecera],['id_cabecera_ciudades' => $valor]);
        }
        return ['success' => true];
    }
    public static function importarDatosCluster(int $cluster, int $cluster2, int $idUsuario,bool $cabeceraBool)
    {
        
        $cabeceras = DB::table("facturacion_cluster_ciudades_cabecera")->where('id_cluster',$cluster)->get();
        $saveCabeceras = [];
        $idsClusters = self::obtenerClustersIguales($cluster2);
        if(is_null($idsClusters)){
            return ['error' => 'cluster no encontrado'];
        }
        $ciudadesBase = ModeloCluster::ciudadesClusters($idsClusters);
        DB::beginTransaction();
        try {
            DB::table("facturacion_cluster_valor AS fc")->join("facturacion_cluster_cabecera AS fce","fc.id_cabecera",'=','fce.id')->where(['fce.id_cluster' => $cluster2])->delete();
            DB::table("facturacion_cluster_ciudades_valor AS fv")->join("facturacion_cluster_ciudades_cabecera AS fc","fv.id_cabecera","=",'fc.id')
            ->where(['fc.id_cluster' => $cluster2])->delete();
            DB::table("facturacion_cluster_ciudades_cabecera")->where(['id_cluster' => $cluster2])->delete();
            foreach ($cabeceras as $cabecera) {
                $cabeceraArray = [
                    'id_cluster' => $cluster2,
                    'id_via_envio' => $cabecera->id_via_envio,
                    'numero' => $cabecera->numero,
                    'tipo_calculo' => $cabecera->tipo_calculo,
                    'peso_maximo' => $cabecera->peso_maximo,
                    'monto' => $cabecera->monto,
                    'montofau' => $cabecera->montofau,
                    'descripcion' => $cabecera->descripcion,
                    'usuario_creado' => $idUsuario,
                    'fecha_creada' => now()
                ];
                $idNewCabecera = DB::table("facturacion_cluster_ciudades_cabecera")->insertGetId($cabeceraArray);
                foreach ($ciudadesBase as $ciudad) {
                    $valor = [
                        'id_cabecera' => $idNewCabecera,
                        'id_ciudad' => $ciudad->id,
                        'valor' => $ciudad->ciudad_alejada == "Complejo - Distante" || $ciudad->ciudad_alejada == "Rural - Alejada" ? $cabecera->montofau : $cabecera->monto
                    ];
                    DB::table('facturacion_cluster_ciudades_valor')->insert($valor);
                }
                if($cabeceraBool){
                    $saveCabeceras[] = ['cabeceraAnt' => $cabecera->id, 'cabeceraSig' => $idNewCabecera];
                }
            }
            if($cabeceraBool){
                DB::table("facturacion_cluster_cabecera")->where(['id_cluster' => $cluster2])->delete();
                $cabeceraEnvios = DB::table("facturacion_cluster_cabecera")->where(['id_cluster'=>$cluster])->get();
                foreach ($cabeceraEnvios as $cabeceraEnvi) {
                    $newCabeceraEnv = [
                        'id_cluster' => $cluster2,
                        'id_via_envio' => $cabeceraEnvi->id_via_envio,
                        'kg_maximo' => $cabeceraEnvi->kg_maximo,
                        'kg_minimo' => $cabeceraEnvi->kg_minimo,
                        'usuario_creado' => $idUsuario,
                        'fecha_creada' => now()
                    ];
                    $idCabeceraEnvio = DB::table("facturacion_cluster_cabecera")->insertGetId($newCabeceraEnv);
                    $valoresEnviosSig = DB::table("facturacion_cluster_valor")->where('id_cabecera',$cabeceraEnvi->id)->get();
                    foreach ($valoresEnviosSig as $valEnSig) {
                        $filtro = array_filter($saveCabeceras,function($val)use($valEnSig){
                            return $val['cabeceraAnt'] == $valEnSig->id_cabecera_ciudades;
                        });
                        if(empty($filtro)){
                            $valorColumn = null;
                        }else{
                            $valorColumn = $saveCabeceras[key($filtro)]['cabeceraSig'];
                        }
                        DB::table("facturacion_cluster_valor")->insert([
                            'id_tipo_envio' => $valEnSig->id_tipo_envio,
                            'id_cabecera' => $idCabeceraEnvio,
                            'id_cabecera_ciudades' => $valorColumn
                        ]);
                        // dd($saveCabeceras,$saveCabeceras[key($filtro)]['cabeceraSig']);
                    }
                }
            }
            DB::commit();
            return ['success' => 'valores importados correctamente'];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' => $th->getMessage(),'line' => $th->getLine()];
        }
    }
    public static function importarDatos(int $cliente, int $cliente2, int $idUsuario, bool $cabeceraBool)
    {
        $cabeceras = DB::table("facturacion_cabezera_cliente")->where('id_cliente',$cliente)->get();
        $saveCabeceras = [];
        DB::beginTransaction();
        try {
            DB::table("facturacion_cliente_asignacion AS fc")->join("facturacion_cabecera_envio AS fce","fc.id_cabecera_envio",'=','fce.id')->where(['fce.id_cliente' => $cliente2])->delete();
            DB::table("facturacion_cliente_valor AS fv")->join("facturacion_cabezera_cliente AS fc","fv.id_cabecera","=",'fc.id')
            ->where(['fc.id_cliente' => $cliente2])->delete();
            DB::table("facturacion_cabezera_cliente")->where(['id_cliente' => $cliente2])->delete();
            foreach ($cabeceras as $cabecera) {
                $cabeceraArray = [
                    'id_cliente' => $cliente2,
                    'id_via_envio' => $cabecera->id_via_envio,
                    'numero' => $cabecera->numero,
                    'tipo_calculo' => $cabecera->tipo_calculo,
                    'peso_maximo' => $cabecera->peso_maximo,
                    'descripcion' => $cabecera->descripcion,
                    'usuario_creado' => $idUsuario,
                    'fecha_creada' => now()
                ];
                $idNewCabecera = DB::table("facturacion_cabezera_cliente")->insertGetId($cabeceraArray);
                $consultas = DB::table('facturacion_cliente_valor')->where('id_cabecera',$cabecera->id)->get();
                foreach ($consultas as $consulta) {
                    $valor = [
                        'id_cabecera' => $idNewCabecera,
                        'id_ciudad' => $consulta->id_ciudad,
                        'valor' => $consulta->valor
                    ];
                    DB::table('facturacion_cliente_valor')->insert($valor);
                }
                if($cabeceraBool){
                    $saveCabeceras[] = ['cabeceraAnt' => $cabecera->id, 'cabeceraSig' => $idNewCabecera];
                }
            }
            if($cabeceraBool){
                DB::table("facturacion_cabecera_envio")->where(['id_cliente' => $cliente2])->delete();
                $cabeceraEnvios = DB::table("facturacion_cabecera_envio")->where(['id_cliente'=>$cliente])->get();
                foreach ($cabeceraEnvios as $cabeceraEnvi) {
                    $newCabeceraEnv = [
                        'id_cliente' => $cliente2,
                        'id_via_envio' => $cabeceraEnvi->id_via_envio,
                        'kg_maximo' => $cabeceraEnvi->kg_maximo,
                        'kg_minimo' => $cabeceraEnvi->kg_minimo,
                        'user_create' => $idUsuario,
                        'date_create' => now()
                    ];
                    $idCabeceraEnvio = DB::table("facturacion_cabecera_envio")->insertGetId($newCabeceraEnv);
                    $valoresEnviosSig = DB::table("facturacion_cliente_asignacion")->where('id_cabecera_envio',$cabeceraEnvi->id)->get();
                    foreach ($valoresEnviosSig as $valEnSig) {
                        $filtro = array_filter($saveCabeceras,function($val)use($valEnSig){
                            return $val['cabeceraAnt'] == $valEnSig->id_cabecera_cliente;
                        });
                        DB::table("facturacion_cliente_asignacion")->insert([
                            'id_tipo_envio' => $valEnSig->id_tipo_envio,
                            'id_cabecera_envio' => $idCabeceraEnvio,
                            'id_cabecera_cliente' => $saveCabeceras[key($filtro)]['cabeceraSig']
                        ]);
                    }
                }
            }
            DB::commit();
            return ['success' => 'valores importados correctamente'];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }
    }
    public static function obtenerClustersIguales($idCluster)
    {
        $nombreCluster = DB::table("usuarios AS u")->select("u.*","c.id AS idCluster")->join("cluster AS c","u.id","=","c.id_usuario")->where("c.id",$idCluster)->where("u.status","!=",0)->first();
        if(empty($nombreCluster)){
            return null;
        }
        $clustes = DB::table("usuarios AS u")->select("u.*","c.id AS idCluster")->join("cluster AS c","u.id","=","c.id_usuario")->where(['u.name' => $nombreCluster->name,'u.last_name' => $nombreCluster->last_name,'u.n_document' => $nombreCluster->n_document, 'u.type_document' => $nombreCluster->type_document])->get();
        $idsClusters = [];
        foreach ($clustes as $cluster) {
            $idsClusters[] = $cluster->idCluster;
        }
        return $idsClusters;
    }
    public static function obtenerGuiasFacturarCluster($cluster,$provincia,$viaTraslado,$fechaInicio,$fechaFin,$estadoCluster)
    {
        DB::table("guias AS g")->join("detalle_guias AS dg","g.id","=","dg.id_guia")->leftJoin("clientes_de_clientes AS cc","g.id_destinatario","=","cc.id")->where(["g.activo" => 1, "g.estado" => 1,"g.via_tras" => $viaTraslado,'g.estado_fact_cluster' => $estadoCluster])->where("g.situ",">=",11)->where("g.situ","!=",14)->whereBetween("g.fech_emis",[$fechaInicio,$fechaFin]);
    }
    public static function obtenerProvinciasCluster(int $idCluster)
    {
        $clusters = self::obtenerClustersIguales($idCluster);
        $provincias = DB::table("cluster")->whereIn("id",$clusters)->get();
        $provinciasNom = [];
        foreach ($provincias as $prop) {
            $provinciasNom[] = [
                'idCluster' => $prop->id,
                'nombreProvincia' => mb_strtoupper((new Ubigeo)->getNameProvincia($prop->provincia_clu),'UTF-8')
            ];
        }
        return ['provincias' => $provinciasNom];
    }
    
    public static function obtenerFacturacionCluster($idCluster)
    {
        $idsClusters = self::obtenerClustersIguales($idCluster);
        if(is_null($idsClusters)){
            return ['error' => 'cluster no encontrado'];
        }
        $ciudadesBase = ModeloCluster::ciudadesClusters($idsClusters);
        $cabeceras = DB::table("facturacion_cluster_ciudades_cabecera AS fc")
        ->select("fc.id","fc.id_cluster","fc.id_via_envio","fc.numero","fc.tipo_calculo","fc.peso_maximo","fc.descripcion","ve.envio")
        ->join("via_envio AS ve","fc.id_via_envio","=","ve.id")->where("fc.id_cluster",$idCluster)->orderBy("fc.numero")->get();
        $facturacion = [];
        foreach($ciudadesBase as $ciudad){
            foreach($cabeceras as $cabecera){
                $valor = DB::table("facturacion_cluster_ciudades_valor")->where(['id_cabecera' => $cabecera->id,'id_ciudad' => $ciudad->id])->first();
                $valorVal = empty($valor) ? ["tipo" => "nuevo","valor" => "0.00"] : ["tipo" => "antiguo", "valor" => $valor->valor, "id" => $valor->id];
                $valorVal["ciudad"] = $ciudad->id;
                $valorVal["cabecera"] = $cabecera->id;
                $facturacion[] = $valorVal;
            }
        }
        $facturacionTipoEnvio = self::facturacionClusterSegunda($idCluster);
        $response = ["ciudadesBase" => $ciudadesBase, "cabeceras" => $cabeceras, "valores" => $facturacion];
        return array_merge($facturacionTipoEnvio,$response);
    }
    public static function facturacionClusterSegunda($idCluster)
    {
        $filas = DB::table("facturacion_tipo_envio")->orderBy("tipo_envio")->get();
        $columnas = DB::table("facturacion_cluster_cabecera AS fce")->select("fce.*","ve.envio")
        ->join("via_envio AS ve","fce.id_via_envio","=","ve.id")->where("fce.id_cluster",$idCluster)
        ->orderBy("fce.id_via_envio")->orderBy("fce.kg_minimo")->get();
        $facturacion = [];
        $cabeceraChange = null;
        $lista = [];
        foreach($filas as $fila){
            foreach($columnas as $columna){
                if($columna->id_via_envio != $cabeceraChange){
                    $lista = DB::table("facturacion_cluster_ciudades_cabecera")->select("id","numero")->where(["id_cluster" => $idCluster, "id_via_envio" => $columna->id_via_envio])->orderBy("numero")->get()->toArray();
                    $cabeceraChange = $columna->id_via_envio;
                }
                $valor = DB::table("facturacion_cluster_valor")->where(['id_cabecera' => $columna->id,'id_tipo_envio' => $fila->id])->first();
                $valorVal = empty($valor) ? ["columna" => $columna->id , "fila" => $fila->id] : ["columna" => $columna->id, "fila" => $fila->id,"id" => $valor->id_cabecera_ciudades];
                $facturacion[] = [
                    'listaAsignacion' => $lista,
                    'valor' => $valorVal
                ];
            }
        }
        return ['filaEnvio' => $filas, 'columnaEnvio' => $columnas, 'valorEnvio' => $facturacion];
    }
    public static function agregarCabeceraFacClus($datos,$idUsuario)
    {
        DB::beginTransaction();
        try {
            if(isset($datos['active_infinito'])){
                $datos['kg_maximo'] = self::valorMaximo();
                unset($datos['active_infinito']);
            }
            $datos['usuario_creado'] = $idUsuario;
            $idCabecera = DB::table("facturacion_cluster_cabecera")->insertGetId($datos);
            $tipoEnvios = DB::table("facturacion_tipo_envio")->orderBy("tipo_envio")->get();
            foreach ($tipoEnvios as $tipoEnvio) {
                self::agregarValorCabeceraCluster($idCabecera,$tipoEnvio->id,null);
            }
            DB::commit();
            return ['success' => 'columna agregada correctamente'];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }
    }
    public static function agregarValorCabeceraCluster($idCabecera,$tipoEnvio,$valor)
    {
        return DB::table("facturacion_cluster_valor")->updateOrInsert(['id_cabecera' => $idCabecera, 'id_tipo_envio' => $tipoEnvio],['id_cabecera_ciudades' => $valor]);
    }
    public static function mostrarCabeceraEditarCostoCluster(int $idCluster, int $idCabecera)
    {
        return ['success' => DB::table("facturacion_cluster_cabecera")->select("kg_minimo","kg_maximo","id_via_envio")->where(['id_cluster' => $idCluster,'id' => $idCabecera])->first()];
    }
    public static function editarCabeceraCluster(int $idUsuario, array $datos)
    {
        if(isset($datos['active_infinito'])){
            $datos['kg_maximo'] = self::valorMaximo();
            unset($datos['active_infinito']);
        }
        $idCabecera = $datos['cabecera'];
        $idCluster = $datos['id_cluster'];
        unset($datos['cabecera']);
        unset($datos['id_cluster']);
        $datos['usuario_modificado'] = $idUsuario;
        $datos['fecha_actualizada'] = now();
        DB::table("facturacion_cluster_cabecera")->where(['id' => $idCabecera, 'id_cluster' => $idCluster])->update($datos);
        return ["success" => 'cabecera modificada correctamente'];
    }
    public static function eliminarCabeceraCostoCluster(int $cabecera, int $idCluster)
    {
        DB::beginTransaction();
        try {   
            DB::table("facturacion_cluster_valor AS fc")->join("facturacion_cluster_cabecera AS fce","fc.id_cabecera",'=','fce.id')
            ->where(['fc.id_cabecera' => $cabecera, 'fce.id_cluster' => $idCluster])->delete();
            DB::table("facturacion_cluster_cabecera")->where(['id' => $cabecera, 'id_cluster' => $idCluster])->delete();
            DB::commit();
            return ['success' => 'cabecera eliminada correctamente'];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' =>$th->getMessage()];
        }
    }
}
