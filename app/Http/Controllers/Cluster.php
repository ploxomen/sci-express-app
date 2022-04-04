<?php

namespace App\Http\Controllers;

use App\Models\ModeloCluster;
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
    public function agregarNuevoCluster(Request $request)
    {
        try {
            if(!$request->ajax()){
                return response()->json(['error' => 'Petición invalida']);
            }
            $datos = $request->all();
            $datoUsuario = [];
            foreach ($datos as $key => $value) {
                if($key == 'n_document' || $key == 'type_document' || $key == 'name' || $key == 'last_name' || $key == 'email' || $key == 'phone' || $key == 'password'){
                    $datoUsuario[$key] = $key == 'password' ? Hash::make($value) : $value;
                }
            }
            foreach (array_keys($datoUsuario) as $key) {
               unset($datos[$key]);
            }
            return response()->json(ModeloCluster::agregarNuevoCluster($datoUsuario, $datos));
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
            $datos = $request->all();
            if($request->has('cluster_id')){
                $id_cluster = $request->cluster_id;
                unset($datos['cluster_id']);
            }
            ModeloCluster::updateCluster($id_cluster,$datos);
            return response()->json(['success' => 'Cluster actualizado correctamente']);
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
