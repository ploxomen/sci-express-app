<?php

namespace App\Http\Controllers;

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
        $situacion = InformacionAdicional::situacionEstado();
        $comprobantes = InformacionAdicional::obtenerComprobantes();
        $via_envios = InformacionAdicional::viaEnvio();
        $tipo_documentos = Documentos::where('estado','1')->get();
        $departamentos = (new Ubigeo)->getDepartamentosClientes();
        $trayecto = TrayectoEnvio::where(['estado' => 1])->get();
        $hoy = date('Y-m-d');
        return view('comprobante.guias',compact('departamentos','hoy','trayecto','comprobantes','via_envios','situacion','tipo_documentos','modulos'));
    }
    public function addGuia(Request $request)
    {
        try {
            $datos = $request->all();
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
            $response = ModeloGuias::agregar($datos,$detalles);
            return response()->json($response);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
}
