<?php

namespace App\Http\Controllers;

use App\Exports\FacturacionGuiaExport;
use App\Models\Clientes;
use App\Models\InformacionAdicional;
use App\Models\ModelFacturacion;
use App\Models\ModeloCluster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class Facturacion extends Controller
{
    public function viewFacturacionGuia()
    {
        $fechaFiltros = InformacionAdicional::obtenerDiasFiltros();
        $clientes = Clientes::where(['estado' => 1])->get();
        $via_envios = InformacionAdicional::viaEnvio();
        return view('facturacion.guiaMostrar', compact( 'fechaFiltros', 'clientes', 'via_envios'));
    }
    public function viewFacturacionCluster()
    {
        $fechaFiltros = InformacionAdicional::obtenerDiasFiltros();
        $via_envios = InformacionAdicional::viaEnvio();
        $clusters = ModeloCluster::obtenerTodosClustersFacturacion();
        return view("facturacion.clusterMostra",compact( 'fechaFiltros', 'clusters', 'via_envios'));
    }
    public function viewConfiguracionCliente()
    {
        $clientes = Clientes::getClientesFacturacion();
        $viaEnvio = InformacionAdicional::viaEnvio();
        return view("facturacion.configuracionCliente",compact("clientes","viaEnvio"));
    }
    public function viewConfiguracionCluster()
    {
        $viaEnvio = InformacionAdicional::viaEnvio();
        $clusters = ModeloCluster::obtenerTodosClustersFacturacion();
        return view("facturacion.configuracionCluster",compact("clusters","viaEnvio"));
    }
    public function facturacionCluster(Request $request)
    {
        if(!$request->ajax()){
            return response()->json(['error' => 'peticion erronea']);
        }
        $response = [];
        switch ($request->tipo) {
            case 'obtenerFacturacion':
                $response = ModelFacturacion::obtenerFacturacionCluster($request->cluster);
            break;
            case 'agregar-cabecera':
                $datos = $request->all();
                unset($datos['tipo']);
                $response = ModelFacturacion::agregarCabeceraFacClus($datos,Auth::id());
            break;
            case 'mostrar-editar-cluster':
                $response = ModelFacturacion::mostrarCabeceraEditarCluster($request->cabecera,$request->cluster);
            break;
            case 'editar-cabecera-cluster':
                $datos = $request->all();
                unset($datos['tipo']);
                $response = ModelFacturacion::editarCabeceraClusterCiudad(Auth::id(),$datos);
            break;
            case 'agregar-cabecera-cluster':
                $datos = $request->all();
                unset($datos['tipo']);
                $response = ModelFacturacion::agregarCabeceraClusterCiudad(Auth::id(),$datos);
            break;
            case 'agregar-valor':
                $resultado = ModelFacturacion::agregarValorCabeceraCluster($request->cabecera,$request->ciudad,$request->monto);
                $response = ['success' => $resultado ? 'monto actualizado correctamente' : 'no se actualizo el monto'];
            break;
            case 'mostrar-costo-editar':
                $response = ModelFacturacion::mostrarCabeceraEditarCostoCluster($request->cluster,$request->cabecera);
            break;
            case 'eliminar-cabecera-cluster':
                $response = ModelFacturacion::eliminarCabeceraCluster($request->cabecera,$request->cluster);
            break;
            case 'editar-cabecera':
                $datos = $request->all();
                unset($datos['tipo']);
                $response = ModelFacturacion::editarCabeceraCluster(Auth::id(),$datos);
            break;
            case 'eliminar-costo-editar':
                $response = ModelFacturacion::eliminarCabeceraCostoCluster($request->cabecera,$request->cluster);
            break;
            case 'cambiar-valor-costo':
                $response = ModelFacturacion::cambiarValorCostoCluster($request->columna,$request->fila,$request->valor);
            break;
            case 'importarData':
                $response = ModelFacturacion::importarDatosCluster($request->cluster,$request->cluster2,Auth::id(),$request->has("cabecera"));
            break;
        }
        return response()->json($response);
    }
    public function obtenerFacturacionCliente(Request $request){
        if($request->ajax()){
            $response = [];
            switch ($request->tipo) {
                case 'mostrar':
                    $response = ModelFacturacion::mostrarFacturacionClientes($request->cliente);
                    break;
                case 'agregar-cabecera':
                    $datos = $request->all();
                    unset($datos['tipo']);
                    $response = ModelFacturacion::agregarCabecera(Auth::id(),$datos);
                break;
                case 'agregar-valor':
                    $datos = $request->all();
                    unset($datos['tipo']);
                    $response = ModelFacturacion::agregarModificarValorFactCli($request->cabecera,$request->ciudad,$request->monto,'0.00',0);
                    if($response){
                        $response = ['success' => 'valor modificado correctamente'];
                    }
                break;
                case 'mostrar-editar':
                    $response = ModelFacturacion::mostrarCabeceraEditar($request->cabecera,$request->cliente);
                break;
                case 'editar-cabecera':
                    $datos = $request->all();
                    unset($datos['tipo']);
                    $response = ModelFacturacion::editarCabecera(Auth::id(),$datos);
                break;
                case 'eliminar-cabecera':
                    $response = ModelFacturacion::eliminarCabecera($request->cabecera,$request->cliente);
                break;
                case 'sincronizar-ciudades':
                    $response = ModelFacturacion::sincronizarCiudadesBase();
                break;
                case 'agregar-costo-cabecera':
                    $datos = $request->all();
                    unset($datos['tipo']);
                    $response = ModelFacturacion::agregarCabeceraCosto(Auth::id(),$datos);
                break;
                case 'mostrar-costo-editar':
                    $response = ModelFacturacion::mostrarCabeceraEditarCosto($request->cliente,$request->cabecera);
                break;
                case 'editar-costo-cabecera':
                    $datos = $request->all();
                    unset($datos['tipo']);
                    $response = ModelFacturacion::editarCabeceraCosto(Auth::id(),$datos);
                break;
                case 'eliminar-costo-editar':
                    $response = ModelFacturacion::eliminarCabeceraCosto($request->cabecera,$request->cliente);
                break;
                case 'cambiar-valor-costo':
                    $response = ModelFacturacion::cambiarValorCosto($request->columna,$request->fila,$request->valor);
                break;
                case 'importarData':
                    $response = ModelFacturacion::importarDatos($request->cliente,$request->cliente2,Auth::id(),$request->has('cabecera'));
                break;
            }
            return response()->json($response);
        }
    }
    public function obtenerFacturacionClusterFac(Request $request)
    {
        if(!$request->ajax()){
            return response()->json(['error' => 'peticion invalida']);
        }
        switch ($request->tipo) {
            case 'consultaProvincias':
                $response = ModelFacturacion::obtenerProvinciasCluster($request->cluster);
            break;
            case 'consulta':
                $response = ModelFacturacion::obtenerProvinciasCluster($request->cluster);
                $guias = ModelFacturacion::obtenerGuiasFacturacionCluster($request->cluster,$request->trayecto,$request->fechaInicio,$request->fechaFin,$request->estado);
                return $request->response == "normal" ? response()->json(["guias" => $guias, "totalGuias" => ModelFacturacion::totalGuiasAfacturarCluster($request->cluster)]) : datatables()->of($guias)->toJson();
            break;
            case 'calcular':
                ModelFacturacion::sincronizarFacturacionCluster($request->cluster);
                return response()->json(['success' => 'montos calculados correctamente']);
            break;
            case 'facturar':
                $datos = $request->only("subtotal", "total", "igv", "mov_1", "mov_2","nro_factura","fecha_emision");
                $datos['usuario_creado'] = Auth::id();
                $response = ModelFacturacion::facturarGuiasClusters(json_decode($request->guias,true), $datos);
                return response()->json($response);
            break;
            case 'detalle':
                $guias = ModelFacturacion::verDetalleGuia($request->guia);
                return response()->json(['success' => $guias]);
            break;
        }
        return response()->json($response);
    }
    public function obtenerFacturacionGuia(Request $request)
    {
        if($request->ajax()){
            switch ($request->tipo) {
                case 'consulta':
                    $clientes = empty($request->cliente) ? 0 : $request->cliente;
                    $trayectos = empty($request->trayecto) ? 0 : $request->trayecto;
                    $fechaIncio = $request->fechaInicio;
                    $fechaFin = $request->fechaFin;
                    $guias = ModelFacturacion::obtenerGuiasFacturacion($clientes,$trayectos,$fechaIncio,$fechaFin,$request->estado);
                    return $request->response == "normal" ? response()->json(["guias" => $guias, "totalGuias" => ModelFacturacion::cantidadGuiasAFacturar($clientes,$trayectos)->count()]) : datatables()->of($guias)->toJson();
                break;
                case 'calcular':
                    ModelFacturacion::sincronizarFacturacionCliente($request->cliente);
                    return response()->json(['success' => 'montos calculados correctamente']);
                break;
                case 'facturar':
                    $data = ModelFacturacion::facturarGuiasClientes(json_decode($request->guias,true),$request->nroFactura,$request->fechaFactura,$request->cliente,$request->subtotal,$request->igv,$request->total,Auth::id());
                    return response()->json($data);
                break;
            }
            
        }
    }

    public function exportarExcelFacturacionGuia(Request $request)
    {
        $clientes = empty($request->cliente) ? 0 : $request->cliente;
        $trayectos = empty($request->tray_env) ? 0 : $request->tray_env;
        $fechaIncio = $request->fechaInicio;
        $fechaFin = $request->fechaFin;
        $estado = $request->estado;
        $guias = ModelFacturacion::obtenerGuiasFacturacion($clientes, $trayectos, $fechaIncio, $fechaFin,$estado);
        return Excel::download(new FacturacionGuiaExport($guias), 'facturacion-guias.xlsx');
    }
}
