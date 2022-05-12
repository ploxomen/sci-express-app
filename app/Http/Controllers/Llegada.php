<?php

namespace App\Http\Controllers;

use App\Models\InformacionAdicional;
use App\Models\ModeloCluster;
use App\Models\ModeloEmbarque;
use App\Models\ModeloGuias;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Llegada extends Controller
{
    public function viewLlegada()
    {
        $modulos = User::getModulos(Auth::id());
        $estado_guias = InformacionAdicional::situacionGuiaCluster();
        return view('pedidos.llegada',compact('modulos', 'estado_guias'));
    }
    public function viewLlegadaManual()
    {
        $modulos = User::getModulos(Auth::id());
        $clusters = ModeloCluster::obtenerTodosClusters();
        return view('pedidos.duplicadoarribo', compact('modulos', 'clusters'));
    }
    public function obtenerEmbarqueArribar(Request $request)
    {
        try {
            $id_embarque = $request->has('embarque') ? $request->embarque : 0;
            $id_guia = $request->has('guia') ? $request->guia : 0;
            $id_usuario = $request->has('id_cluster') ? $request->id_cluster : Auth::id();
            return response()->json(['resultados' => ModeloEmbarque::obtenerEmbarqueArribado($id_embarque,$id_guia, $id_usuario,$request->tipo)]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function detalleMisGuias(Request $request)
    {
        try {
            $guias = ModeloGuias::obtenerGuiasClusterDetalle($request->id_guia, Auth::id(),0, 'detalle-guias');
            return response()->json(['resultados' => $guias]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function changeEstadoGuias(Request $request)
    {
        try {
            ModeloGuias::actualizarEstadoGuiasCluster($request->id_guia,Auth::id(),$request->estado);
            return response()->json(['success' => 'el estado de la guia se acutalizó correctamente']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function clusterMisGuias(Request $request)
    {
        try {
            $guias = ModeloGuias::obtenerGuiasCluster(0,Auth::id(),$request->estado, 'guias-todos');
            return datatables()->of($guias)->toJson();
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function actualizarGuiasLlegada(Request $request)
    {
        try {
            ModeloEmbarque::actualizarGuiaLlegadas($request->embarque,$request->manifiesto,$request->guia,$request->conforme,$request->texto,$request->situacion,$request->incidencia,Auth::id());
            return response()->json(['success' => 'El estado de la guía se cambio con éxito']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
}
