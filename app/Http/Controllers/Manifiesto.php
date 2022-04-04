<?php

namespace App\Http\Controllers;

use App\Models\InformacionAdicional;
use App\Models\ModeloCluster;
use App\Models\ModeloGuias;
use App\Models\ModeloManifiesto;
use App\Models\Tipos\Documentos;
use App\Models\TrayectoEnvio;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Manifiesto extends Controller
{
    public function viewManifiesto()
    {
        $tipo_documentos = Documentos::where('estado','1')->get();
        $modulos = User::getModulos(Auth::id());
        return view('manifiesto.manidetalle',compact('tipo_documentos','modulos'));
    }
    public function viewNuevoManifiesto()
    {
        $tipo_documentos = Documentos::where('estado', '1')->get();
        $modulos = User::getModulos(Auth::id());
        $trayectos = TrayectoEnvio::where(['estado' => 1])->get();
        $hoy = date('Y-m-d');
        $departamentoProv = $this->arrayDepartamentosProvincias();
        return view('manifiesto.maninuevo', compact('departamentoProv','trayectos','hoy','tipo_documentos', 'modulos'));
    }
    public function arrayDepartamentosProvincias()
    {
        $departamentos = (new Ubigeo())->getDepartamentosClientes();
        $departamentoProv = [];
        foreach ($departamentos as $dep) {
            $provin = (new Ubigeo())->getProvinciasCliente($dep->id_ubigeo);
            $array = ['id_ubigeo_dep' => $dep->id_ubigeo, 'nom_ubigeo_dep' => $dep->nombre_ubigeo, 'provincias' => $provin];
            $departamentoProv[] = $array;
        }
        return $departamentoProv;
    }
    public function obtenerClusterProv(Request $request)
    {
        try {
            $clusters = ModeloCluster::select('usuarios.name', 'usuarios.last_name','cluster.id')->join('usuarios','cluster.id_usuario','=','usuarios.id')->where(['cluster.activo' => 1, 'cluster.provincia_clu' => $request->provincia])->get();
            return response()->json(['cluster' => $clusters]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function viewGuias(Request $request)
    {
        try {
            $datos = $request->all();
            $guias = ModeloGuias::obtenerGuias($datos,Auth::id());
            return response()->json(['success' => $guias]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function viewProgEmba()
    {
        $modulos = User::getModulos(Auth::id());
        $hoy = date('Y-m-d');
        return view('manifiesto.programarembarque',compact('hoy','modulos'));
    }
    public function agregarManifiesto(Request $request)
    {
        try {
            $datos = $request->all();
            if(isset($datos['guias'])){
                unset($datos['guias']);
            }
            $guias = json_decode($request->guias,true);
            $response = ModeloManifiesto::agregarManifiesto($datos,$guias);
            return response()->json($response);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
}
