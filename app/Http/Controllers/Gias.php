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
        $comprobantes = InformacionAdicional::obtenerComprobantes();
        $via_envios = InformacionAdicional::viaEnvio();
        $tipo_documentos = Documentos::where('estado','1')->get();
        $departamentos = (new Ubigeo)->getDepartamentosClientes();
        $trayecto = TrayectoEnvio::where(['estado' => 1])->get();
        $clientes = Clientes::obtenerCliConProvee();
        $hoy = date('Y-m-d');
        return view('comprobante.guias',compact('clientes','departamentos','hoy','trayecto','comprobantes','via_envios','tipo_documentos','modulos'));
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
            $id_cli_cli = $request->has('idcliente') ? $request->idcliente : $request->id;
            $cli_cli = Clientes::obtenerCliDeCli($id_cli_cli);
            if($request->tipo == 'cliente'){
                $datos = Clientes::getDetalleSoliCli($request->id);
                $provincias = Ubigeo::getProvinciasCliente($datos->dpto_so);
                $distritos = Ubigeo::getDistritosCliente($datos->prov_so);
            }else{
                $datos = Clientes::obtenerProveedor($request->id);
                $provincias = $datos->listProv;
                $distritos = $datos->listDist;
            }
            return response()->json(['datos' => $datos, 'prov' => $provincias, 'dist' => $distritos,'cli' => $cli_cli]);
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
