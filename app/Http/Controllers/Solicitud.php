<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\EstadoSolicitud;
use App\Models\ModeloSolicitud;
use App\Models\Rol;
use Illuminate\Http\Request;
use App\Models\Tipos\Documentos;
use App\Models\TrayectoEnvio;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class Solicitud extends Controller
{

    public function viewSolicitud()
    {
        $tipo_documentos = Documentos::where('estado','1')->get();
        $modulos = User::getModulos(Auth::id());
        $hoy = date('Y-m-d');
        $trayectos = TrayectoEnvio::where(['estado' => 1])->get();
        $clientes = Clientes::getClientesFiles();
        $ultimo_id = ModeloSolicitud::getLastId();
        $dep = (new Ubigeo)->getDepartamentosClientes();
        if(is_null($ultimo_id)){
            $ultimo_id = 1;
        }else{
            $ultimo_id = $ultimo_id->id + 1;
        }
        return view('solicitud.nueva',compact('dep','ultimo_id','clientes','tipo_documentos','modulos','hoy','trayectos'));
    }
    public function obtenerSolicitud(Request $request)
    {
        try {
            if ($request->ajax()) {
                $id_user = $request->has('recojos') ? Auth::id() : 0;
                $detalles = ModeloSolicitud::getSolicitud(is_null($request->estadoSol) ? "" : $request->estadoSol, $id_user);
                return datatables()->of($detalles)->toJson();
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerProveedor(Request $request)
    {
        try {
            if ($request->ajax()) {
                $proovedor = ModeloSolicitud::obtenerProveedor($request->id_proveedor);
                return response()->json(['proveedor' => $proovedor]);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerDetalleCli(Request $request)
    {
        try {
            if($request->ajax()){
                $detalles = Clientes::getDetalleSoliCli($request->id);
                $provincias = Ubigeo::getProvinciasCliente($detalles->dpto_so);
                $distritos = Ubigeo::getDistritosCliente($detalles->prov_so);
                $proveedores = Clientes::obtenerProveedoresSoli($request->id);
                return response()->json(['cliente' => $detalles,'provincias' => $provincias,'distritos' => $distritos, 'proveedores' => $proveedores]);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function insertarNuevaSoli(Request $request)
    {
        try {
            if ($request->ajax()) {
                ModeloSolicitud::addSolicitud($request->all(),Auth::id());
                return response()->json(['success' => 'Solicitud registrada correctamente']);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function asignarPersonal(Request $request)
    {
        try {
            if ($request->ajax()) {
                $response = ModeloSolicitud::asignarPersonalSoli($request->all());
                $totalProces = (ModeloSolicitud::obtenerTotalPorProcesar())[0]->total;
                return response()->json(['success' => $response,'procesar' => $totalProces]);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function cambiarEstadoSoli(Request $request)
    {
        try {
            if ($request->ajax()) {
                ModeloSolicitud::where(['id' => $request->id_solicitud])->update(['estado' => $request->estado]);
                return response()->json(['success' => 'estado modificado']);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function deleteSolicitud(Request $request)
    {
        try {
            if ($request->ajax()) {
                ModeloSolicitud::where(['id' => $request->id_solicitud])->update(['activo' => 0]);
                return response()->json(['success' => 'Solicitud eliminada correctamente']);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function mostrarSolicitud(Request $request)
    {
        $estados_solicitud = EstadoSolicitud::where(['activado' => 1])->get();
        $por_procesar = ModeloSolicitud::obtenerTotalPorProcesar();
        $modulos = User::getModulos(Auth::id());
        $dep = (new Ubigeo)->getDepartamentosClientes();
        $tipo_documentos = Documentos::where('estado', '1')->get();
        $trayectos = TrayectoEnvio::where(['estado' => 1])->get();
        $clientes = Clientes::getClientesFiles();
        return view('solicitud.mostrar', compact('dep','tipo_documentos','trayectos','clientes','por_procesar','estados_solicitud','modulos'));

    }
    public function verDetalleSoli(Request $request)
    {
        try {
            if ($request->ajax()) {
                $detalle = ModeloSolicitud::obtenerDetalleSoli($request->id_solicitud);
                return response()->json($detalle);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
   
}
