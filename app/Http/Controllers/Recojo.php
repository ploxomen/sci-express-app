<?php

namespace App\Http\Controllers;

use App\Exports\Reportes;
use App\Models\Clientes;
use App\Models\EstadoSolicitud;
use App\Models\InformacionAdicional;
use App\Models\ModeloCluster;
use App\Models\ModeloGuias;
use App\Models\ModeloRecojo;
use App\Models\ModeloSolicitud;
use App\Models\TrayectoEnvio;
use App\Models\User;
use App\Models\Vehiculos;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class Recojo extends Controller
{
    public function viewPediReco(Request $request)
    {
        $modulos = User::getModulos(Auth::id());
        $clientes = Clientes::getClientesFiles();
        $conductores = User::obtenerUsuarioRol(6);
        $dealers = User::obtenerUsuarioRol(7);
        $vehiculos = Vehiculos::obtenerVehiculos();
        $trayectos = TrayectoEnvio::where(['estado' => 1])->get();
        $clusters = ModeloCluster::obtenerTodosClusters();
        return view('pedidos.recojos',compact('clusters','vehiculos','conductores', 'dealers','clientes', 'trayectos','modulos'));
    }
    public function reportesRecojo($tipo,$fechaInicio,$fechaFin)
    {
        ini_set('memory_limit', '1024M');
        switch ($tipo) {
            case 'pdf':
                
                $datos = ModeloRecojo::reporteRecojos($fechaInicio,$fechaFin);
                $reporte = Pdf::loadView("pedidos.reportes.recojosPdf",["resultados" => $datos,'fechaIni' => date('d/m/Y',strtotime($fechaInicio)),'fechaFi' => date('d/m/Y',strtotime($fechaFin))])->setPaper('a4', 'landscape');
                return $reporte->download("recojos_".$fechaInicio."_".$fechaFin.".pdf");
            break;
            case 'excel':
                $datos = ModeloRecojo::reporteRecojos($fechaInicio, $fechaFin);
                return Excel::download(new Reportes('pedidos.reportes.recojosExcel',$datos, "LISTA DE RECOJOS DESDE". date('d/m/Y', strtotime($fechaInicio)). " HASTA ". date('d/m/Y', strtotime($fechaFin))), "recojos_" . $fechaInicio . "_" . $fechaFin . ".xls");
            break;
        }
    }
    public function viewRecojoAdmin(Request $request)
    {
        $filtros = InformacionAdicional::obtenerDiasFiltros();
        $modulos = User::getModulos(Auth::id());
        return view('pedidos.misRecojos', compact('filtros', 'modulos'));
    }
    public function recojoRetornoCluster():View
    {
        $modulos = User::getModulos(Auth::id());
        $programaciones = ModeloGuias::retornosCargosProgramados(Auth::id());
        return view('retornos.recojerProgramacion',compact('modulos','programaciones'));
    }
    public function accionesRetornoEntrega(Request $request)
    {
        if($request->ajax()){
            switch ($request->tipo) {
                case 'update-retorno':
                    return response()->json(ModeloGuias::actualizarRetornosCluster($request->retorno,$request->recogo,Auth::id()));
                break;
                case 'finalizar-retorno':
                    return response()->json(ModeloGuias::terminarRetornosCluster($request->recogo,Auth::id()));
                break;
            }
        }
    }
    public function verRecojosAdmin(Request $request)
    {
        if($request->ajax()){
            $recojos = ModeloRecojo::verRecojos($request->fechaInicio,$request->fechaFin,$request->estado);
            return datatables()->of($recojos)->toJson();
        }
    }
    public function modificarAdminRecojo(Request $request)
    {
        if($request->ajax()){
            $resultado = ['error' => 'no se encontro la accion'];
            switch ($request->tipo) {
                case 'ver-actualizar':
                    $vehiculos = Vehiculos::obtenerVehiculos();
                    $dealers = User::obtenerUsuarioRol(7);
                    $conductores = User::obtenerUsuarioRol(6);
                    $clusters = ModeloCluster::obtenerTodosClusters();
                    $detalleRecojo = ModeloRecojo::verRecojosActualizar($request->recojo);
                    $resultado = [
                        'detalleRecojo' => $detalleRecojo,
                        'vehiculos' => $vehiculos,
                        'dealers' => $dealers,
                        'conductores' => $conductores,
                        'clusters' => $clusters
                    ];
                    break;
                case 'actualizar':
                    break;
                case 'delete-solicitud':
                    $resultado = ModeloRecojo::eliminarDetalleRecojo($request->detalleRecojo,$request->idRecojo,1, 'solicitud eliminada correctamente del detalle del recojo',Auth::id());
                    break;
                case 'eliminar-recojo':
                    $resultado = ModeloRecojo::eliminarRecojo($request->idRecojo,1,'recojo eliminado correctamente',Auth::id());
                    break;
                case 'reprogramar-solictudes':
                    $resultado = ModeloRecojo::eliminarRecojo($request->idRecojo,6,'solicitudes listas para reprogramar el recojo',Auth::id());
                    break;
                case 'reprogramar-solicitud':
                    $resultado = ModeloRecojo::eliminarDetalleRecojo($request->detalleRecojo,$request->idRecojo,1, 'solicitud lista para reprogramar el recojo',Auth::id());
                    break;
            }
            return response()->json($resultado);
        }
    }
    public function recojoPendiente(Request $request)
    {
        $id_trabajor = Auth::id();
        $modulos = User::getModulos($id_trabajor);
        $recojos_pend = ModeloRecojo::getRecojosDriDea($id_trabajor);
        // dd($recojos_pend);
        // dd($recojos_pend);
        return view('trabajadorrecojo',compact('modulos', 'recojos_pend', 'id_trabajor'));

    }
    public function actualizarRecoTra(Request $request)
    {
        try {
            $bulto = $request->has('cant_bulto') ? $request->cant_bulto : null;
            $doc = $request->has('doc_recojo') ? $request->doc_recojo : null;
            $nro = $request->has('nro_guias_conso') ? $request->nro_guias_conso : null;
            $id = $request->id_recojo;
            ModeloRecojo::updateRecoTraba($id,$bulto,$doc,$nro,Auth::id());
            return response()->json(['success' => true]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function agregarRecojo(Request $request)
    {
        try {
            $cluster = $request->has('id_cluster') ? $request->id_cluster : null;
            $dealer = $request->has('id_conductor') ? $request->id_conductor : null;
            $conductor = $request->has('id_dealer') ? $request->id_dealer : null;
            $resultado = ModeloRecojo::agregarRecojo($request->fecha_recojo,$request->responsable_recojo,$request->id_vehiculo,$cluster,$dealer,$conductor,Auth::id(),json_decode($request->solicitudes,true));
            return response()->json($resultado);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function filterStadoSoli($datos)
    {
        $estado = 1;
        if(!empty($datos['dia_recojo']) && !empty($datos['id_vehiculo']) && ((!empty($datos['id_conductor']) && !empty($datos['id_dealer'])) ||  !empty($datos['id_cluster']))){
            $estado = 2;
            if(!empty($datos['hr_llegada']) || !empty($datos['hr_despacho']) || !empty($datos['hr_salida'])){
                $estado = 3;
            }
            if (!empty($datos['hr_llegada']) && !empty($datos['hr_despacho']) && !empty($datos['hr_salida']) && !empty($datos['nro_guias_conso']) && !empty($datos['cant_bulto'])) {
                $estado = 4;
            }
        }
        return $estado;

    }
    public function modificarEstados(Request $request)
    {
        try {
            ModeloSolicitud::where(['id' => $request->id_solicitud])->update(['estado' => $request->estado_soli]);
            return response()->json(['success' => 'Estado actualizado correctamente']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function modificarRecojo(Request $request)
    {
        try {
            $datos = $request->only('id_vehiculo', 'id_cluster', 'id_dealer', 'id_conductor', 'hr_llegada', 'hr_despacho', 'hr_salida', 'doc_recojo', 'nro_guias_conso');
            ModeloRecojo::where(['id' => $request->id_recojo])->update($datos);
            $estadoReco = $this->filterStadoSoli($request->all());
            ModeloSolicitud::where(['id' => $request->id_solicitud])->update(['estado' => $estadoReco, 'dia_recojo' => $request->dia_recojo, 'cant_bulto' => $request->cant_bulto]);
            return response()->json(['success' => 'Recojo actualizaco correctamente']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerRecojo(Request $request)
    {
        try {
            $recojos = ModeloRecojo::getRecojos($request->id_solicitud);
            $clusters = null;
            if(!is_null($recojos) && ($recojos->trayecto == 2 || $recojos->trayecto == 4)){
                $clusters = ModeloCluster::obtenerClusterProvincia($recojos->ciudad_cli);
            }
            return response()->json(['recojos' => $recojos,'clusters' => $clusters]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
}
