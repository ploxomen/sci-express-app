<?php

namespace App\Http\Controllers;

use App\Exports\Reportes;
use App\Models\EmpresaTransporte;
use App\Models\InformacionAdicional;
use App\Models\ModeloEmbarque;
use App\Models\ModeloGuias;
use App\Models\ModeloManifiesto;
use App\Models\User;
use App\Models\Vehiculos;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class Embarque extends Controller
{
    public function viewListEmba(Request $request)
    {
        $hoy = date('Y-m-d');
        $modulos = User::getModulos(Auth::id());
        $ciudadesBase = InformacionAdicional::obtenerCiudadBaseEmbarcar(Auth::id());
        $dealers = User::obtenerUsuarioRol(7);
        $conductores = User::obtenerUsuarioRol(6);
        $vehiculos = Vehiculos::obtenerVehiculos();
        $transportes = EmpresaTransporte::obtenerTransportes();
        $diasAtras = date('Y-m-d',strtotime(date('Y-m-d') . "- 2 days"));
        return view('pedidos.listaembarque',compact('diasAtras','dealers', 'conductores', 'ciudadesBase', 'vehiculos', 'transportes','hoy','modulos'));
    }
    public function viewEmbarquesTodo()
    {
        $modulos = User::getModulos(Auth::id());
        $fechaFiltros = InformacionAdicional::obtenerDiasFiltros();
        $dealers = User::obtenerUsuarioRol(7);
        $conductores = User::obtenerUsuarioRol(6);
        $vehiculos = Vehiculos::obtenerVehiculos();
        $transportes = EmpresaTransporte::obtenerTransportes();
        return view('embarque.misEmbarques',compact('modulos', 'transportes','fechaFiltros', 'dealers', 'conductores', 'vehiculos'));
    }
    public function agregarManifiestoEmbarque(Request $request)
    {
        try {
            $manifiestos = ModeloEmbarque::agregarManifiestoEmbarque($request->embarque, $request->manifiesto);
            return response()->json($manifiestos);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function reportesEmbarque($tipo,$fechaInicio,$fechaFin)
    {
        ini_set('memory_limit', '1024M');
        switch ($tipo) {
            case 'pdf':
                $datos = ModeloEmbarque::reportesEmbarque($fechaInicio, $fechaFin);
                $reporte = Pdf::loadView("embarque.reportes.embarquePdf", ["resultados" => $datos, 'fechaIni' => date('d/m/Y', strtotime($fechaInicio)), 'fechaFi' => date('d/m/Y', strtotime($fechaFin))])->setPaper('a4', 'landscape');
                return $reporte->stream("embarques_" . $fechaInicio . "_" . $fechaFin . ".pdf");
                break;
            case 'excel':
                $datos = ModeloEmbarque::reportesEmbarque($fechaInicio, $fechaFin);
                return Excel::download(new Reportes('embarque.reportes.embarqueExcel', $datos, "LISTA DE EMBARQUE DESDE" . date('d/m/Y', strtotime($fechaInicio)) . " HASTA " . date('d/m/Y', strtotime($fechaFin))), "embarques_" . $fechaInicio . "_" . $fechaFin . ".xls");
                break;
        }
    }
    public function eliminarManifiestoEmbarque(Request $request)
    {
        try {
            $manifiestos = ModeloEmbarque::eliminarManifiestoEmbarque($request->embarque,$request->manifiesto);
            return response()->json($manifiestos);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function editarFletes(Request $request)
    {
       $fletes = json_decode($request->fletes,true);
       $editarFlete = ModeloEmbarque::editarFlete($request->embarque,$fletes,$request->tota_bult,$request->flet_esti);
        return response()->json($editarFlete);
    }
    public function editarEmbarque(Request $request)
    {
        $actualizadDatos = $request->all();
        $empresasTransporte = $request->id_empr_tran;
        unset($actualizadDatos['embarque']);
        unset($actualizadDatos['id_empr_tran']);
        $result = ModeloEmbarque::editarEmbarque($actualizadDatos,$empresasTransporte,$request->embarque,Auth::id());
        return response()->json($result);
    }
    public function mostrarEditarEmbarque(Request $request)
    {
        try {
            $guias = ModeloEmbarque::misEmbarquesFiltros(null,null,'todos','todos','todos','todos',$request->embarque);
            $manifiestos = ModeloManifiesto::manifiestoAgregarManual();
            return response()->json(['success' => $guias,'manifiesto' => $manifiestos]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function eliminarEmbarque(Request $request)
    {
        try {
            $guias = ModeloEmbarque::eliminarEmbarque($request->embarque);
            return response()->json($guias);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function mostrarEditarFletes(Request $request)
    {
        try {
            $result = ModeloEmbarque::mostrarEditarFletes($request->embarque);
            return response()->json($result);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function misEmbarques(Request $request)
    {
        try {
            $guias = ModeloEmbarque::misEmbarquesFiltros($request->fechaInicio, $request->fechaFin, $request->driver, $request->dealer, $request->vehiculo, $request->estado, 'todos');
            return datatables()->of($guias)->toJson();
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerDevolucionGuias(Request $request)
    {
        try {
            $guias = ModeloEmbarque::devolucionCargo(0, 'ver-cargos-pendientes',Auth::id());
            return datatables()->of($guias)->toJson();
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function vistaEmpresa()
    {
        $modulos = User::getModulos(Auth::id());
        return view('empresatransporte', compact('modulos'));
    }
    public function obtenerEmpresas(Request $request)
    {
        try {
            if($request->has('empresa_id')){
                return response()->json(['empresas' => InformacionAdicional::obtenerEmpresaTrasnporte($request->empresa_id)]);
            }else{
                return datatables()->of(InformacionAdicional::obtenerEmpresasTrasnportes())->toJson();
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function agregarEmpresa(Request $request)
    {
        try {
            $datos = $request->all();
            if(isset($datos['nro_contacto']) && !empty($datos['nro_contacto'])){
                $datos['nro_contacto'] = implode(';', $datos['nro_contacto']);
            }
            if (isset($datos['correos']) && !empty($datos['correos'])) {
                $datos['correos'] = implode(';', $datos['correos']);
            }
            InformacionAdicional::agregarEmpresaTransporte($datos);
            return response()->json(['success' => true]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function actualizarEmpresa(Request $request)
    {
        try {
            $id = $request->id_empresa;
            $datos = $request->all();
            if(isset($datos['id_empresa'])){
                unset($datos['id_empresa']);
            }
            if (isset($datos['nro_contacto']) && !empty($datos['nro_contacto'])) {
                $datos['nro_contacto'] = implode(';', $datos['nro_contacto']);
            }
            if (isset($datos['correos']) && !empty($datos['correos'])) {
                $datos['correos'] = implode(';', $datos['correos']);
            }
            InformacionAdicional::actualizarEmpresaTransporte($datos,$id);
            return response()->json(['success' => true]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function retornarGuias(Request $request)
    {
        try {
            $guias = [];
            $datos = $request->all();
            if($request->has('guias')){
                $guias = json_decode($request->guias, true);
                unset($datos['guias']);
            }
            $result = [];
            switch ($request->tipo) {
                case 'guardar_guias':
                    $result = ModeloEmbarque::guardarDevolucion($guias,Auth::id());
                break;
                case 'consultas_guias':
                    $result = ModeloEmbarque::obtenerEmbarquesGuardados(Auth::id());
                break;
                case 'retornar_guias':
                    $guias_nuevas = [];
                    $guias_guardadas = [];
                    unset($datos['tipo']);
                    if($request->has('guias_guardadas')){
                        unset($datos['guias_guardadas']);
                        $guias_guardadas = json_decode($request->guias_guardadas, true);
                    }
                    if ($request->has('guias_nuevas')) {
                        unset($datos['guias_nuevas']);
                        $guias_nuevas = json_decode($request->guias_nuevas,true);
                    }
                    $result = ModeloEmbarque::actualizarDevolucionCargo($datos, $guias_nuevas,$guias_guardadas, Auth::id());
                break;
                
            }
            return response()->json($result);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function eliminarEmpresa(Request $request)
    {
        try {
            InformacionAdicional::eliminarEmpresaTransporte($request->empresa_id);
            return response()->json(['success' => 'Empresa de transporte eliminada correctamente']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerManiPend(Request $request)
    {
        try {
            $manifiestos = ModeloManifiesto::obtenerEmbarcar($request->ciudad_base, $request->tipo == 'undefined' ? null : $request->tipo,Auth::id());
            return response()->json($manifiestos);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function transporteEmbarque(Request $request)
    {
        $embarques = ModeloEmbarque::embarcarTrabajador(0,Auth::id(),'embarque');
        $modulos = User::getModulos(Auth::id());
        $hoy = strtotime(date('Y-m-d'));
        return view('pedidos.embarquemanifiesto',compact('hoy','modulos','embarques'));
    }
    public function actualizarEmbarque(Request $request)
    {
        try {
            $resultado = ModeloEmbarque::actualizarEmbarque($request->id_embarque,Auth::id(),$request->has('ciudades') ? $request->ciudades : null);
            return response()->json($resultado);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerInformeEmbarque(Request $request)
    {
        try {
            $embarques = ModeloEmbarque::obtenerEmbarqueInforme(Auth::id());
            return datatables()->of($embarques)->toJson();
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function editarInforme(Request $request)
    {
        $datos = $request->all();
        $idEmbarque = $request->embarque;
        $actualizar = ModeloEmbarque::actualizarInforme($datos,$idEmbarque,Auth::id());
        return response()->json($actualizar);
    }
    public function verEditarInformeEmbarque(Request $request)
    {
        $embarque = ModeloEmbarque::verEmbarqueInforme($request->embarque);
        return response()->json(['embarque' => $embarque]);
    }
    public function obtenerInfoEmbDeta(Request $request)
    {
        try {
            $embarque = ModeloEmbarque::embarcarTrabajador($request->id_embarque,Auth::id(),'informe');
            return response()->json(['embarque' => isset($embarque[0]) ? $embarque[0] : null]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerFletes(Request $request)
    {
        try {
            return response()->json(['fletes' => InformacionAdicional::obtenerFletes()]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function verCiudadesFlestes(Request $request)
    {
        try {
            $manifiestos = implode(',', json_decode($request->lista_manifiestos, true));
            return response()->json(['ciudades' => ModeloEmbarque::obtenerCiudadesBaseFlete($manifiestos,Auth::id())]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function embarcarMani(Request $request)
    {
        try {
            $datos = $request->all();
            $embarque = ModeloManifiesto::embarcarManifiesto($datos,Auth::id());
            return response()->json($embarque);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function agregarInforme(Request $request)
    {
        try {
            $datos = $request->all();
            return response()->json(ModeloEmbarque::agregarInformeEmbarque($datos, Auth::id()));
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function viewVehiculos(Request $request)
    {
        $modulos = User::getModulos(Auth::id());
        return view('vehiculos',compact('modulos'));
    }
    public function obtenerVehiculosTransporte(Request $request)
    {
        try {
            if ($request->has('vehiculo_id')) {
                return response()->json(['vehiculo' => Vehiculos::where(['activo' => 1,'id' => $request->vehiculo_id])->first()]);
            } else {
                $vehiculos = Vehiculos::obtenerVehiculos();
                return datatables()->of($vehiculos)->toJson();
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function eliminarVehiculoTransporte(Request $request)
    {
        try {
            Vehiculos::where(['activo' => 1, 'id' => $request->vehiculo_id])->update(['activo' => 0]);
            return response()->json(['success' => 'Vehículo eliminado correctamente']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function agregarVehiculoTransporte(Request $request)
    {
        try {
            $datos = $request->all();
            Vehiculos::insert($datos);
            return response()->json(['success' => true]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function actualizarVehiculoTransporte(Request $request)
    {
        try {
            $id = $request->id_vehiculo;
            $datos = $request->all();
            if (isset($datos['id_vehiculo'])) {
                unset($datos['id_vehiculo']);
            }
            Vehiculos::where(['activo' => 1, 'id' => $id])->update($datos);
            return response()->json(['success' => true]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
}
