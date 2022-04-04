<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\InformacionAdicional;
use App\Models\ModeloGuias;
use App\Models\Tipos\Documentos;
use App\Models\TrayectoEnvio;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Gias extends Controller
{
    public function viewGuias()
    {
        $modulos = User::getModulos(Auth::id());
        $via_envios = InformacionAdicional::viaEnvio();
        $tipo_documentos = Documentos::where('estado','1')->get();
        $departamentos = (new Ubigeo)->getDepartamentosClientes();
        $trayecto = TrayectoEnvio::where(['estado' => 1])->get();
        $clientes = Clientes::obtenerCliConProvee();
        $cli_cli = Clientes::obtenerCliDeCli();
        $hoy = date('Y-m-d');
        return view('comprobante.guias',compact('cli_cli','clientes','departamentos','hoy','trayecto','via_envios','tipo_documentos','modulos'));
    }
    public function detalleMisGuias(Request $request)
    {
        try {
            return response()->json(['datos' => ModeloGuias::obtenerDetalleMisGuias($request->guia)]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function misGuias()
    {
        $modulos = User::getModulos(Auth::id());
        $estado_guias = InformacionAdicional::situacionEstado();
        $nro_por_asignar = ModeloGuias::guiaPorAsignar();
        return view('comprobante.misguias',compact('modulos', 'estado_guias', 'nro_por_asignar'));
    }
    public function anularGuia(Request $request)
    {
        try {
            ModeloGuias::anularGuias($request->id_guia);
            return response()->json(['success' => 'Guia anulada correctamente']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function cambiarEstadoGuia(Request $request)
    {
        try {
            ModeloGuias::cambioEstadoGuia($request->id_guia, $request->estado);
            return response()->json(['success' => 'Estado de guia cambiada con éxito']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function asignarUsuario(Request $request)
    {
        try {
            $total = ModeloGuias::asignarPersona($request->id_user,$request->id_guia);
            $resta = isset($total[0]->total) ? $total[0]->total : 0;
            return response()->json(['success' => 'Guia asignada correctamente','procesar' => $resta]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerMisGuias(Request $request)
    {
        if($request->ajax()){
            $guias = ModeloGuias::mostrarTodasGuias($request->estado,$request->tipo);
            return datatables()->of($guias)->toJson();
        }
    }
    public function addGuia(Request $request)
    {
        try {
            $datos = $request->all();
            $add_cli = $request->has('add_cli_cli') ? $request->add_cli_cli : null;
            $add_cli_bool = $request->has('add_cli_bool') ? $request->add_cli_bool : null;
            if(isset($datos['comp_clie'])){
                $datos['comp_clie'] = implode(';', $datos['comp_clie']);
            }
            if (isset($datos['nro_comp_cli'])) {
                $datos['nro_comp_cli'] = implode(';', $datos['nro_comp_cli']);
            }
            if(isset($datos['detalle_guia'])){
                unset($datos['detalle_guia']);
            }
            $detalles = json_decode($request->detalle_guia,true);
            $response = ModeloGuias::agregar($datos,$detalles, $add_cli, $add_cli_bool);
            return response()->json($response);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerCliOrProvDeta(Request $request)
    {
        try {
            $provincias = [];
            $distritos = [];
            if($request->tipo == 'cliente'){
                $datos = Clientes::getDetalleSoliCli($request->id);
                $provincias = Ubigeo::getProvinciasCliente($datos->dpto_so);
                $distritos = Ubigeo::getDistritosCliente($datos->prov_so);
            }else{
                $datos = Clientes::obtenerProveedor($request->id);
                $provincias = $datos->listProv;
                $distritos = $datos->listDist;
            }
            return response()->json(['datos' => $datos, 'prov' => $provincias, 'dist' => $distritos]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function detalleCliDeCli(Request $request)
    {
        try {
            $datos = Clientes::detalleCliDeCli($request->id);
            $provincias = [];
            $distritos = [];
            if(!empty($datos)){
                $provincias = Ubigeo::getProvinciasCliente($datos->departamento);
                $distritos = Ubigeo::getDistritosCliente($datos->provincia);
            }           
            return response()->json(['datos' => $datos, 'prov' => $provincias, 'dist' => $distritos]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
}
