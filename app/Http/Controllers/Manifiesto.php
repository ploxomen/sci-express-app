<?php

namespace App\Http\Controllers;

use App\Exports\Reportes;
use App\Models\InformacionAdicional;
use App\Models\ModeloCluster;
use App\Models\ModeloGuias;
use App\Models\ModeloManifiesto;
use App\Models\Tipos\Documentos;
use App\Models\TrayectoEnvio;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class Manifiesto extends Controller
{

    public function viewManifiestosTodos()
    {
        $modulos = User::getModulos(Auth::id());
        $trayectos = TrayectoEnvio::where(['estado' => 1])->get();
        $departamentoProv = $this->arrayDepartamentosProvincias();
        $clusters = ModeloCluster::obtenerTodosClusters();
        $situacionManifiesto = ModeloManifiesto::obtenerSituacionManifiesto();
        $fechaFiltros = InformacionAdicional::obtenerDiasFiltros();
        return view('manifiesto.manidetalle',compact('fechaFiltros','modulos', 'trayectos', 'departamentoProv', 'clusters', 'situacionManifiesto'));

    }
    public function viewNuevoManifiesto()
    {
        $tipo_documentos = Documentos::where('estado', '1')->get();
        $modulos = User::getModulos(Auth::id());
        $trayectos = TrayectoEnvio::where(['estado' => 1])->get();
        $hoy = date('Y-m-d');
        $antes = date('Y-m-d',strtotime($hoy . '-2 days'));
        $departamentoProv = $this->arrayDepartamentosProvincias();
        return view('manifiesto.maninuevo', compact('antes','departamentoProv','trayectos','hoy','tipo_documentos', 'modulos'));
    }
    public function obtenerTodosManifiestos(Request $request)
    {
        try {
            $manifiestos = ModeloManifiesto::obtenerTodosManifiestos($request->fechaInicio, $request->fechaFin,$request->trayecto,$request->provOrigen,$request->provFinal,$request->cluster,$request->estado,'todos');
            return datatables()->of($manifiestos)->toJson();

        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function eliminarManifiesto(Request $request)
    {
        try {
            $result = ModeloManifiesto::eliminarManifiesto($request->manifiesto);
            return response()->json($result);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }    
    }
    public function actualizarManifiesto(Request $request)
    {
        try {
            $manifiesto = ModeloManifiesto::where(['id_cluster' => $request->id_cluster, 'id' => $request->manifiesto]);
            $manifiesto->update([
                'user_update' => Auth::id(),
                'fech_mani' => $request->fech_mani,
                'date_update' => date('Y-m-d H:i:s')
            ]);
            if($request->has("num_estado")){
                $manifiesto->update(['estado' => $request->num_estado]);
                ModeloManifiesto::actualizarGuiasAtravezManifiesto($request->manifiesto,intval($request->num_estado));
            }
            return response()->json(['success' => 'Manifiesto modificado correctamente']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function agregarGuiaManual(Request $request)
    {
        try {
            $result = ModeloManifiesto::agregarGuiaManifiestoManual($request->manifiesto, $request->guia);
            return response()->json($result);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function reportesManifiesto($tipo, $fechaInicio, $fechaFin)
    {
        ini_set('memory_limit', '1024M');
        switch ($tipo) {
            case 'pdf':
                $datos = ModeloManifiesto::reportesManifiestos($fechaInicio, $fechaFin);
                $reporte = Pdf::loadView("manifiesto.reportes.manifiestoPdf", ["resultados" => $datos, 'fechaIni' => date('d/m/Y', strtotime($fechaInicio)), 'fechaFi' => date('d/m/Y', strtotime($fechaFin))])->setPaper('a4', 'landscape');
                return $reporte->download("manifiestos_" . $fechaInicio . "_" . $fechaFin . ".pdf");
                break;
            case 'excel':
                $datos = ModeloManifiesto::reportesManifiestos($fechaInicio, $fechaFin);
                return Excel::download(new Reportes('manifiesto.reportes.manifiestoExcel', $datos, "LISTA DE MANIFIESTOS DESDE" . date('d/m/Y', strtotime($fechaInicio)) . " HASTA " . date('d/m/Y', strtotime($fechaFin))), "recojos_" . $fechaInicio . "_" . $fechaFin . ".xls");
                break;
        }
    }
    public function eliminarGuiaManifiesto(Request $request)
    {
        try {
            $result = ModeloManifiesto::eliminarGuiaManifiesto($request->manifiesto,$request->guia);
            return response()->json($result);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerUnManifiesto(Request $request)
    {
        try {
            $manifiesto = ModeloManifiesto::obtenerTodosManifiestos(null,null,"todos","todos","todos","todos","todos",$request->manifiesto);
            $manifiestoPendiente = ModeloManifiesto::manifiestoPendientes($request->cluster);
            return response()->json(['success' => $manifiesto,'guias' => $manifiestoPendiente]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
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
            $clusters = ModeloCluster::obtenerUnClusterProvincia($request->provincia);
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
            $datos['id_responsable'] = Auth::id();
            $response = ModeloManifiesto::agregarManifiesto($datos,$guias);
            return response()->json($response);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
}
