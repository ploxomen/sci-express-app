<?php

namespace App\Http\Controllers;

use App\Exports\FacturacionGuiaExport;
use App\Models\Clientes;
use App\Models\InformacionAdicional;
use App\Models\ModelFacturacion;
use App\Models\ModeloCluster;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Facturacion extends Controller
{
    public function viewFacturacionGuia()
    {
        $fechaFiltros = InformacionAdicional::obtenerDiasFiltros();
        $modulos = User::getModulos(Auth::id());
        $clientes = Clientes::where(['estado' => 1])->get();
        $via_envios = InformacionAdicional::viaEnvio();
        return view('facturacion.guiaMostrar', compact('modulos', 'fechaFiltros', 'clientes', 'via_envios'));
    }
    public function viewConfiguracionCliente()
    {
        // $ciudades = ModeloCluster::obtenerCiudadesBase();
        // $newCiudades = [];
        // foreach($ciudades as $ciudad){
        //     $newCiudades[] = ["ciudad" => $ciudad->nombre_ciudad];
        // }
        // ModelFacturacion::agregarCiudades($newCiudades);
        // $ciudades = ModelFacturacion::mostrarCiudades();
        $clientes = Clientes::getClientesFiles();
        $viaEnvio = InformacionAdicional::viaEnvio();
        $modulos = User::getModulos(Auth::id());
        // dd($clientes);
        return view("facturacion.configuracionCliente",compact("modulos","clientes","viaEnvio"));
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
                    $response = ModelFacturacion::agregarModificarValorFactCli($request->cabecera,$request->ciudad,$request->monto);
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
            }
            return response()->json($response);
        }
    }
    public function obtenerFacturacionGuia(Request $request)
    {
        if($request->ajax()){
            $clientes = empty($request->cliente) ? 0 : $request->cliente;
            $trayectos = empty($request->trayecto) ? 0 : $request->trayecto;
            $fechaIncio = $request->fechaInicio;
            $fechaFin = $request->fechaFin;
            $guias = ModelFacturacion::obtenerGuiasFacturacion($clientes,$trayectos,$fechaIncio,$fechaFin);
            return datatables()->of($guias)->toJson();
        }
    }
    public function actualizarCostosFacturacion(Request $request)
    {
        if($request->ajax()){
            $guias = ModelFacturacion::obtenerGuiasFacturacion($request->clientes,$request->trayectos,$request->fechaIncio,$request->fechaFin);
            
        }
    }
    public function retornoCostosGuiasFacturacion(int $idGuia)
    {
        
    }
    public function exportarExcelFacturacionGuia(Request $request)
    {
        $clientes = empty($request->cliente) ? 0 : $request->cliente;
        $trayectos = empty($request->tray_env) ? 0 : $request->tray_env;
        $fechaIncio = $request->fechaIncio;
        $fechaFin = $request->fechaFin;
        $guias = ModelFacturacion::obtenerGuiasFacturacion($clientes, $trayectos, $fechaIncio, $fechaFin);
        return \Excel::download(new FacturacionGuiaExport($guias), 'facturacion-guias.xlsx');
    }
}
