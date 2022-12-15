<?php

namespace App\Http\Controllers;

use App\Exports\FacturacionGuiaExport;
use App\Models\Clientes;
use App\Models\InformacionAdicional;
use App\Models\ModelFacturacion;
use App\Models\TrayectoEnvio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Facturacion extends Controller
{
    public function viewFacturacionGuia()
    {
        $fechaFiltros = InformacionAdicional::obtenerDiasFiltros();
        $modulos = User::getModulos(Auth::id());
        $clientes = Clientes::where(['estado' => 1])->get();
        $via_envios = InformacionAdicional::viaEnvio();
        return view('facturacion.guiaMostrar', compact('modulos', 'fechaFiltros', 'clientes', 'via_envios'));
    }
    public function obtenerFacturacionGuia(Request $request)
    {
        if($request->ajax()){
            $clientes = empty($request->cliente) ? 0 : $request->cliente;
            $trayectos = empty($request->trayecto) ? 0 : $request->trayecto;
            $fechaIncio = $request->fechaInicio;
            $fechaFin = $request->fechaFin;
            $guias = ModelFacturacion::obtenerGuiasFacturacion($clientes,$trayectos,$fechaIncio,$fechaFin);
            return datatables()->of($guias)->toJson();
        }
    }
    public function exportarExcelFacturacionGuia(Request $request)
    {
        $clientes = empty($request->cliente) ? 0 : $request->cliente;
        $trayectos = empty($request->tray_env) ? 0 : $request->tray_env;
        $fechaIncio = $request->fechaIncio;
        $fechaFin = $request->fechaFin;
        $guias = ModelFacturacion::obtenerGuiasFacturacion($clientes, $trayectos, $fechaIncio, $fechaFin);
        
        return \Excel::download(new FacturacionGuiaExport($guias), 'facturacion-guias.xlsx');
    }
}
