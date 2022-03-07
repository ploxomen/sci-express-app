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
        $departamentos = (new Ubigeo())->getDepartamentosClientes();
        $departamentoProv = [];
        $clusters = ModeloCluster::where(['activo' => 1])->get();
        foreach ($departamentos as $dep) {
            $provin = (new Ubigeo())->getProvinciasCliente($dep->id_ubigeo);
            $array = ['id_ubigeo_dep' => $dep->id_ubigeo,'nom_ubigeo_dep' => $dep->nombre_ubigeo, 'provincias' => $provin];
            $departamentoProv[] = $array;
        }
        return view('manifiesto.maninuevo', compact('clusters','departamentoProv','trayectos','hoy','tipo_documentos', 'modulos'));
    }
    public function viewGuias(Request $request)
    {
        try {
            $datos = $request->all();
            $guias = ModeloGuias::obtenerGuias($datos);
            return response()->json(['success' => $guias]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function viewProgEmba()
    {
        $comprobantes = InformacionAdicional::obtenerComprobantes();
        $modulos = User::getModulos(Auth::id());
        return view('manifiesto.programarembarque',compact('comprobantes','modulos'));
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
