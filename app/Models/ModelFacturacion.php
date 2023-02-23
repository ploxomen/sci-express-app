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
    public static function obtenerGuiasFactClis($cliente,$traslado,$fechaInicio,$fechaFin,$estado)
    {
        return DB::select('CALL sp_facturacion_ver_guias(?,?,?,?,?)',[$cliente, $fechaInicio,$fechaFin,$traslado,$estado]);
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
            ->whereRaw('? >= kg_minimo',[$pesoDetalle])->whereRaw("? < kg_maximo",[$pesoDetalle])->first();
            if(empty($cabecera1)){
                continue;
            }
            $valor1 = DB::table("facturacion_cliente_asignacion AS fc")->select("fcc.*")->join("facturacion_cabezera_cliente AS fcc","fc.id_cabecera_cliente","=","fcc.id")->where(['fc.id_tipo_envio' => $detalleGuia->tipo_envio,'fc.id_cabecera_envio' => $cabecera1->id])->first();
            if(empty($valor1)){
                continue;
            }
            
            $fact = DB::table("facturacion_cliente_valor")->where(['id_ciudad' => $ciudadFacturacion->id,'id_cabecera' => $valor1->id])->first();
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
    public static function agregarCabecera($idUsuario,$datos)
    {
        if($datos['numero'] == "adicional"){
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
    public static function mostrarMaximoCabecera(int $idCliente)
    {
        return DB::table("facturacion_cabezera_cliente")->where(['id_cliente' => $idCliente])->orderByDesc("numero")->first();
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
    public static function importarDatosCluster(int $cluster, int $cluster2, int $idUsuario)
    {
        $cabeceras = DB::table("facturacion_cluster_cabecera")->where('id_cluster',$cluster)->get();
        DB::beginTransaction();
        try {
            DB::table("facturacion_cluster_valor AS fc")->join("facturacion_cluster_cabecera AS fce","fc.id_cabecera",'=','fce.id')->where(['fce.id_cluster' => $cluster2])->delete();
            DB::table("facturacion_cluster_cabecera")->where(['id_cluster' => $cluster2])->delete();
            foreach ($cabeceras as $cabecera) {
                $cabeceraArray = [
                    'id_via_envio' => $cabecera->id_via_envio,
                    'id_cluster' => $cluster2,
                    'kg_minimo' => $cabecera->kg_minimo,
                    'kg_maximo' => $cabecera->kg_maximo,
                    'kg_base' => $cabecera->kg_base,
                    'tipo_calculo' => $cabecera->tipo_calculo,
                    'usuario_creado' => $idUsuario
                ];
                $idNewCabecera = DB::table("facturacion_cluster_cabecera")->insertGetId($cabeceraArray);
                $consultas = DB::table('facturacion_cluster_valor')->where('id_cabecera',$cabecera->id)->get();
                foreach ($consultas as $consulta) {
                    $valor = [
                        'id_cabecera' => $idNewCabecera,
                        'id_tipo_envio' => $consulta->id_tipo_envio,
                        'valor' => $consulta->valor
                    ];
                    DB::table('facturacion_cluster_valor')->insert($valor);
                }
            }
            DB::commit();
            return ['success' => 'valores importados correctamente'];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
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
    public static function obtenerFacturacionCluster($idCluster)
    {
        $filas = DB::table("facturacion_tipo_envio")->orderBy("tipo_envio")->get();
        $columnas = DB::table("facturacion_cluster_cabecera AS fce")->select("fce.*","ve.envio")
        ->join("via_envio AS ve","fce.id_via_envio","=","ve.id")->where("fce.id_cluster",$idCluster)
        ->orderBy("fce.id_via_envio")->orderBy("fce.kg_minimo")->get();
        $facturacion = [];
        foreach($filas as $fila){
            foreach($columnas as $columna){
                $valor = DB::table("facturacion_cluster_valor")->where(['id_cabecera' => $columna->id,'id_tipo_envio' => $fila->id])->first();
                $valorVal = empty($valor) ? ["columna" => $columna->id , "fila" => $fila->id, "valor" => "0.00"] : ["columna" => $columna->id, "fila" => $fila->id,"valor" => $valor->valor];
                $facturacion[] = $valorVal;
            }
        }
        return ['filaEnvio' => $filas, 'columnaEnvio' => $columnas, 'valorEnvio' => $facturacion];
    }
    public static function agregarCabeceraFacClus($datos,$idUsuario)
    {
        DB::beginTransaction();
        try {
            if($datos['tipo_calculo'] == "adicional"){
                $base = DB::table("facturacion_cluster_cabecera")->where(['id_cluster'=>$datos['id_cluster'],'tipo_calculo' => 'base'])->where('kg_minimo','<',$datos['kg_minimo'])->orderBy("kg_minimo","desc");
                if(empty($base->first())){
                    return ['error' => 'No se encontró ninguna columna base, por favor establesca una base para poder agregar una columna de tipo adicional'];
                }
            }
            if(isset($datos['active_infinito'])){
                $datos['kg_maximo'] = self::valorMaximo();
                unset($datos['active_infinito']);
            }
            $datos['usuario_creado'] = $idUsuario;
            $idCabecera = DB::table("facturacion_cluster_cabecera")->insertGetId($datos);
            $tipoEnvios = DB::table("facturacion_tipo_envio")->orderBy("tipo_envio")->get();
            foreach ($tipoEnvios as $tipoEnvio) {
                self::agregarValorCabeceraCluster($idCabecera,$tipoEnvio->id,"0.00");
            }
            DB::commit();
            return ['success' => 'columna agregada correctamente'];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }
        
    }
    public static function agregarValorCabeceraCluster($idCabecera,$idTipoEnvio,$valor)
    {
        return DB::table("facturacion_cluster_valor")->updateOrInsert(['id_cabecera' => $idCabecera, 'id_tipo_envio' => $idTipoEnvio],['valor' => $valor]);
    }
    public static function mostrarCabeceraEditarCostoCluster(int $idCluster, int $idCabecera)
    {
        return ['success' => DB::table("facturacion_cluster_cabecera")->select("kg_minimo","kg_maximo","kg_base","tipo_calculo","id_via_envio")->where(['id_cluster' => $idCluster,'id' => $idCabecera])->first()];
    }
    public static function editarCabeceraCluster(int $idUsuario, array $datos)
    {
        if($datos['tipo_calculo'] == "adicional"){
            $base = DB::table("facturacion_cluster_cabecera")->where(['id_cluster'=>$datos['id_cluster'],'tipo_calculo' => 'base'])->where('kg_minimo','<',$datos['kg_minimo'])->orderBy("kg_minimo","desc");
            if(empty($base->first())){
                return ['error' => 'No se encontró ninguna columna base, por favor establesca una base para poder agregar una columna de tipo adicional'];
            }
        }
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
