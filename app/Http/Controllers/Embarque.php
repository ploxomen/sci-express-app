<?php

namespace App\Http\Controllers;

use App\Models\EmpresaTransporte;
use App\Models\ModeloEmbarque;
use App\Models\ModeloManifiesto;
use App\Models\User;
use App\Models\Vehiculos;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Embarque extends Controller
{
    public function viewListEmba(Request $request)
    {
        $hoy = date('Y-m-d');
        $modulos = User::getModulos(Auth::id());
        $departamentoProv = (new Manifiesto)->arrayDepartamentosProvincias();
        $dealers = Usuario::obtenerUsuarioRol(4);
        $conductores = Usuario::obtenerUsuarioRol(5);
        $vehiculos = Vehiculos::obtenerVehiculos();
        $transportes = EmpresaTransporte::obtenerTransportes();

        return view('pedidos.listaembarque',compact('dealers', 'conductores','departamentoProv', 'vehiculos', 'transportes','hoy','modulos'));
    }
    public function obtenerManiPend(Request $request)
    {
        try {
            $manifiestos = ModeloManifiesto::obtenerEmbarcar($request->provincia);
            return datatables()->of($manifiestos)->toJson();
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function transporteEmbarque(Request $request)
    {
        $embarques = ModeloEmbarque::embarcarTrabajador(Auth::id());
        $modulos = User::getModulos(Auth::id());
        $hoy = strtotime(date('Y-m-d'));
        return view('pedidos.embarquemanifiesto',compact('hoy','modulos','embarques'));
    }
    public function actualizarEmbarque(Request $request)
    {
        try {
            ModeloEmbarque::actualizarEmbarque($request->id_embarque,Auth::id(),$request->has('empresa') ? $request->empresa : null);
            return response()->json(['success' => true]);
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
    public function obtenerInfoEmbDeta(Request $request)
    {
        try {
            $embarque = ModeloEmbarque::obtenerEmbarqueDetalle($request->id_embarque,Auth::id());
            return response()->json(['embarque' => isset($embarque[0]) ? $embarque[0] : null]);
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
            ModeloEmbarque::agregarInformeEmbarque($datos,Auth::id());
            return response()->json(['success' => 'Informe agregado con éxito']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
}
