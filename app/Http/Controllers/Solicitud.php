<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\EstadoSolicitud;
use App\Models\InformacionAdicional;
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
                $detalles = ModeloSolicitud::getSolicitud(is_null($request->estadoSol) ? "" : $request->estadoSol, $id_user,$request->fechaInicio,$request->fechaFin);
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
                $proovedor = Clientes::obtenerProveedor($request->id_proveedor);
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
                $invitados = Clientes::obtenerInvitados($request->id);
                return response()->json(['cliente' => $detalles,'provincias' => $provincias,'distritos' => $distritos, 'proveedores' => $proveedores, 'invitados' => $invitados]);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtnerCentroCostos(Request $request)
    {
        if($request->ajax()){
            $centroCosto = Clientes::centrosCosto($request->usuario, $request->cliente);
            return response()->json(['centrosCostos' => $centroCosto]);
        }
    }
    public function obtenerDetalleCliInvitado(Request $request)
    {
        try {
            if ($request->ajax()) {
                $detalles = Clientes::getDetalleSoliCli($request->id);
                $provincias = Ubigeo::getProvinciasCliente($detalles->dpto_so);
                $distritos = Ubigeo::getDistritosCliente($detalles->prov_so);
                $centroCosto = Clientes::centrosCosto(Auth::id(),$request->id);
                $proveedores = Clientes::obtenerProveedoresSoli($request->id);
                return response()->json(['proveedores' => $proveedores,'cliente' => $detalles, 'provincias' => $provincias, 'distritos' => $distritos, 'centrosCostos' => $centroCosto]);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function insertarNuevaSoli(Request $request)
    {
        try {
            if ($request->ajax()) {
                $result = ModeloSolicitud::addSolicitud($request->all(),Auth::id());
                return response()->json($result);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function insertarNuevaSoliCliente(Request $request)
    {
        try {
            if ($request->ajax()) {
                $result = ModeloSolicitud::addSolicitudCliente($request->all(),Auth::id());
                return response()->json($result);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function asignarPersonal(Request $request)
    {
        try {
            if ($request->ajax()) {
                $response = ModeloSolicitud::asignarPersonalSoli($request->all(),Auth::id());
                $totalProces = ModeloSolicitud::obtenerTotalPorProcesar();
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
    public function editarSolicitud(Request $request)
    {
        if($request->ajax()){
            $datos = $request->all();
            if($request->has('editarDetalles')){
                $datos['editarDetalles'] = json_decode($request->editarDetalles,true);
            }
            if ($request->has('agregarDetalles')) {
                $datos['agregarDetalles'] = json_decode($request->agregarDetalles, true);
            }
            $resultados = ModeloSolicitud::editarSolicitud($datos,Auth::id());
            return response()->json($resultados);
        }
    }
    public function deleteDetalle(Request $request)
    {
        if($request->ajax()){
            $eliminarDetalle = ModeloSolicitud::eliminarDetalle($request->idDetalle);
            return response()->json($eliminarDetalle);
        }
    }
    public function deleteSolicitud(Request $request)
    {
        try {
            if ($request->ajax()) {
                $solicitud = ModeloSolicitud::where(['id' => $request->id_solicitud])->first();
                //ESTADO = 5 REPROGRAMADA
                if($solicitud->estado === 1 || $solicitud->estado === 5){
                    ModeloSolicitud::where(['id' => $request->id_solicitud])->update(['activo' => 0, 'estado' => 0]);
                    return response()->json(['success' => 'Solicitud eliminada correctamente']);
                }else{
                    return response()->json(['error' => 'No se puede eliminar la solicitud porque está asociado con un recojo']);
                }
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function asignacionGlobalSolicitud(Request $request)
    {
        if($request->ajax()){
            switch ($request->accion) {
                case 'mostrar-datos':
                    $datos = ['solicitudes' => ModeloSolicitud::selectRaw("date_format(fecha_soli,'%d/%m%/%Y') AS fecha_solicitud, LPAD(id,5,'0') AS nro_solicitud,id")->where(['usuario_responsable' => null,'estado' => 1 ,'activo' => 1])->get()];
                break;
                case 'asignar-solicitud':
                    $solicitudes = json_decode($request->solicitudes, true);
                    foreach ($solicitudes as $solicitud) {
                        ModeloSolicitud::where(['id' => $solicitud["id"], 'usuario_responsable' => null, 'activo' => 1, 'estado' => 1])->update(['usuario_responsable' => $request->id_usuario]);
                    }
                    $porAsignar = ModeloSolicitud::obtenerTotalPorProcesar();
                    $datos = ['success' => 'Solicitudes asignadas de forma correcta','porAsignar' => $porAsignar];
                break;
                default:
                    $datos = null;
                    break;
            }
            return response()->json($datos);
        }
    }
    public function modificarEstadoNotificacion(Request $request)
    {
        if($request->ajax()){
            $usuario = Auth::id();
            $fechaHr = now();
            foreach (json_decode($request->solicitudes,true) as $solicitud) {
                ModeloSolicitud::find($solicitud['solicitud'])->update(['last_modificacion' => $fechaHr,'last_user' => $usuario, 'confirmacion_solicitud' => 1]);
            }
            $nroPendientes = ModeloSolicitud::confirmacionRestante();
            return response()->json(['success' => 'solicitudes modificadas correctamente', 'pendientes' => $nroPendientes]);
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
        $confirmacion = ModeloSolicitud::visualizarNotificaciones();
        $usuarios = User::obtenerUsuarioRol(3);
        $filtros = InformacionAdicional::obtenerDiasFiltros();
        return view('solicitud.mostrar', compact('filtros','confirmacion','usuarios','dep','tipo_documentos','trayectos','clientes','por_procesar','estados_solicitud','modulos'));
    }
    public function obtenerParaProgramar(Request $request)
    {
        if(!$request->ajax()){
            return response()->json(['error' => 'No autorizado']);
        }
        $result = ['error' => 'sin resultados'];
        switch ($request->tipo) {
            case 'obter-fechas':
                $result = ['success' => ModeloSolicitud::getFechaFiltroSolicitud()];
            break;
            case 'obter-solicitudes':
                $result = ['success' => ModeloSolicitud::getSolicitudesProgramar($request->fechaRecojo,$request->trayecto,Auth::id())];
            break;
        }
        return response()->json($result);
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
