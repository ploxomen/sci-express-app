<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\EstadoSolicitud;
use App\Models\ModeloCluster;
use App\Models\ModeloRecojo;
use App\Models\ModeloSolicitud;
use App\Models\User;
use App\Models\Vehiculos;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Recojo extends Controller
{
    public function viewPediReco(Request $request)
    {
        $modulos = User::getModulos(Auth::id());
        $clientes = Clientes::getClientesFiles();
        $estados_solicitud = EstadoSolicitud::where(['activado' => 1])->get();
        $conductores = User::obtenerUsuarioRol(6);
        $dealers = User::obtenerUsuarioRol(7);
        $vehiculos = Vehiculos::obtenerVehiculos();
        return view('pedidos.recojos',compact('vehiculos','conductores', 'dealers','clientes','estados_solicitud','modulos'));
    }
    public function recojoPendiente(Request $request)
    {
        $id_trabajor = Auth::id();
        $modulos = User::getModulos($id_trabajor);
        $recojos_pend = ModeloRecojo::getRecojosDriDea($id_trabajor);
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
            $datos = $request->only('id_solicitud', 'id_cluster','id_vehiculo', 'id_conductor', 'id_dealer', 'hr_llegada', 'hr_despacho', 'hr_salida', 'doc_recojo', 'nro_guias_conso');
            ModeloRecojo::insert($datos);
            $estadoReco = $this->filterStadoSoli($request->all());
            ModeloSolicitud::where(['id' => $request->id_solicitud])->update(['estado' => $estadoReco, 'dia_recojo' => $request->dia_recojo, 'cant_bultos' => $request->cant_bulto]);
            return response()->json(['success' => 'Recojo registrado correctamente']);
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
