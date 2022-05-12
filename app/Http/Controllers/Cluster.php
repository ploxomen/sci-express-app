<?php

namespace App\Http\Controllers;

use App\Models\ModeloCluster;
use App\Models\ModeloEmbarque;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Cluster extends Controller
{
    public function crearCluster()
    {
        $modulos = User::getModulos(Auth::id());
        $departamentos = (new Ubigeo)->getDepartamentosClientes();
        return view('cluster.crear',compact('departamentos','modulos'));
    }
    public function mostrarClusters()
    {
        $modulos = User::getModulos(Auth::id());
        $departamentos = (new Ubigeo)->getDepartamentosClientes();
        return view('cluster.misclusters', compact('departamentos', 'modulos'));
    }
    public function entregasClusters(Request $request)
    {
        $modulos = User::getModulos(Auth::id());
        $id_cluster = ModeloCluster::getCodigoCluster(Auth::id());
        if(empty($id_cluster)){
            return abort(404);
        }
        $ciudadesFinales = ModeloCluster::obtenerCiudadesFinales($id_cluster->id);
        $nro_pendients = 0;
        $kpis = ModeloEmbarque::calculoKpi($id_cluster->id);
        return view('pedidos.entregascluster', compact('ciudadesFinales', 'modulos', 'nro_pendients', 'kpis'));

    }
    public function agregarNuevoCluster(Request $request)
    {
        try {
            if(!$request->ajax()){
                return response()->json(['error' => 'Petición invalida']);
            }
            $datos = $request->all();
            $datos_ciudades = json_decode($request->destino_final);
            $datoUsuario = [];
            foreach ($datos as $key => $value) {
                if($key == 'n_document' || $key == 'type_document' || $key == 'name' || $key == 'last_name' || $key == 'email' || $key == 'phone' || $key == 'password'){
                    $datoUsuario[$key] = $key == 'password' ? Hash::make($value) : $value;
                }
            }
            if($request->has('destino_final')){
                unset($datos['destino_final']);
            }
            foreach (array_keys($datoUsuario) as $key) {
               unset($datos[$key]);
            }
            return response()->json(ModeloCluster::agregarNuevoCluster($datoUsuario, $datos, $datos_ciudades));
        } catch (Exception $ex){
           return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function actualizarCluster(Request $request)
    {
        try {
            if (!$request->ajax()) {
                return response()->json(['error' => 'Petición invalida']);
            }
            $id_cluster = 0;
            $actualizar_ciudades = [];
            $agregar_ciudades = [];
            $eliminar_ciudades = [];
            $datos = $request->all();
            if($request->has('cluster_id')){
                $id_cluster = $request->cluster_id;
                unset($datos['cluster_id']);
            }
            if ($request->has('consignados')) {
                $datos['consignados'] = implode(";",$request->consignados);
            }
            if ($request->has('agregar_ciudades')) {
                $agregar_ciudades = json_decode($request->agregar_ciudades);
                unset($datos['agregar_ciudades']);
            }
            if ($request->has('actualizar_ciudades')) {
                $actualizar_ciudades = json_decode($request->actualizar_ciudades);
                unset($datos['actualizar_ciudades']);
            }
            if ($request->has('eliminar_ciudades')) {
                $eliminar_ciudades = json_decode($request->eliminar_ciudades);
                unset($datos['eliminar_ciudades']);
            }
            if ($request->has('destino_final')) {
                unset($datos['destino_final']);
            }
            $resultado = ModeloCluster::updateCluster($id_cluster,$datos,$actualizar_ciudades,$eliminar_ciudades,$agregar_ciudades);
            return response()->json($resultado);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function mostrarCluster(Request $request)
    {
        try {
            if (!$request->ajax()) {
                return response()->json(['error' => 'Petición invalida']);
            }
            $resultado = ModeloCluster::obtenerCluster($request->has('id') ? $request->id : 0);
            if($request->has('id')){
                return response()->json(['cluster' => $resultado]);
            }
            return datatables()->of($resultado)->toJson();
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function eliminarCluster(Request $request)
    {
        try {
            if (!$request->ajax()) {
                return response()->json(['error' => 'Petición invalida']);
            }
            ModeloCluster::eliminarCluster($request->id);

            return response()->json(['success' => 'Cluster eliminado correctamente']);
            
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
}
