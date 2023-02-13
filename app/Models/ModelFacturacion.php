<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Illuminate\Database\Eloquent\Model;
use App\Models\ModeloCluster;
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
    public static function agregarCiudades(array $ciudades){
        return DB::table("facturacion_ciudades")->insert($ciudades);
    }
    public static function mostrarCiudades(){
        return DB::table("facturacion_ciudades")->orderBy("ciudad")->get();
    }
    public static function mostrarFacturacionClientes($idCliente){
        $ciudadesBase = self::mostrarCiudades();
        $cabeceras = DB::table("facturacion_cabezera_cliente AS fc")
        ->select("fc.id","fc.id_cliente","fc.id_via_envio","fc.numero","fc.tipo_calculo","fc.monto","fc.peso_maximo","fc.descripcion","ve.envio")
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
        $datos['fecha_creada'] = now();
        $datos['usuario_creado'] = $idUsuario;
        $idCabecera = DB::table("facturacion_cabezera_cliente")->insertGetId($datos);
        foreach (self::mostrarCiudades() as $ciudad) {
            self::agregarModificarValorFactCli($idCabecera,$ciudad->id,$datos['monto']);
        }
        return ['success' => 'Columna agregada correctamente'];
    }
    public static function agregarModificarValorFactCli($idCabecera,$idCiudad,$monto)
    {
        $valor = DB::table("facturacion_cliente_valor")->where(['id_cabecera' => $idCabecera, 'id_ciudad' => $idCiudad]);
        $response = false;
        if(!empty($valor->first())){
            $response = $valor->update(['valor' => $valor]);
        }else{
            $response = $valor->insert(['id_cabecera' => $idCabecera, 'id_ciudad' => $idCiudad,'valor' => $monto]);
        }
        return $response;
    }
    public static function mostrarCabeceraEditar(int $idCabecera, int $idCliente)
    {
        return ["datos" => DB::table("facturacion_cabezera_cliente")->selectRaw("id_via_envio AS 'lista-via-envio',numero AS 'id_cabecera_numero', tipo_calculo AS 'lista-tipo-cabecera',monto AS 'id-monto-default',peso_maximo AS 'id-kg-base',descripcion AS 'id_descripcion'")->where(['id' => $idCabecera, 'id_cliente' => $idCliente])->first(), "maximo" => self::mostrarMaximoCabecera($idCliente)->numero];
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
        foreach ($ciudades as $ciudad) {
            $nomCiudad = $ciudad->nombre_ciudad;
            $exist = array_filter($ciudadesBase->toArray(),function($c)use($nomCiudad){
                return $c->ciudad == $nomCiudad;
            });
            if(empty($exist)){
                $ciudadesAgregar[] = ['ciudad' => $nomCiudad];
            }
        }
        DB::table("facturacion_ciudades")->insert($ciudadesAgregar);
        return ['ciudades' => $ciudadesAgregar];
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
    
}
