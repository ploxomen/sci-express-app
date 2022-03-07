<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\EstadoSolicitud;
use App\Models\ModeloRecojo;
use App\Models\ModeloSolicitud;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Recojo extends Controller
{
    public function viewPediReco(Request $request)
    {
        $modulos = User::getModulos(Auth::id());
        $clientes = Clientes::getClientesFiles();
        $estados_solicitud = EstadoSolicitud::where(['activado' => 1])->get();
        return view('pedidos.recojos',compact('clientes','estados_solicitud','modulos'));
    }
    public function agregarRecojo(Request $request)
    {
        try {
            $datos = $request->only('id_solicitud', 'fech_recojo', 'placa_vehiculo', 'nom_driver', 'nom_dealer', 'hr_llegada', 'hr_despacho', 'hr_salida', 'doc_recojo', 'nro_guias_conso', 'cant_bultos');
            ModeloRecojo::insert($datos);
            ModeloSolicitud::where(['id' => $request->id_solicitud])->update(['estado' => $request->estado_soli]);
            return response()->json(['success' => 'Recojo registrado correctamente']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function modificarEstados(Request $request)
    {
        try {
            ModeloSolicitud::where(['id' => $request->id_solicitud])->update(['estado' => $request->estado_soli]);
            return response()->json(['success' => 'Estado actualizado correctamente']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function modificarRecojo(Request $request)
    {
        try {
            $datos = $request->only('fech_recojo', 'placa_vehiculo', 'nom_driver', 'nom_dealer', 'hr_llegada', 'hr_despacho', 'hr_salida', 'doc_recojo', 'nro_guias_conso', 'cant_bultos');
            ModeloRecojo::where(['id' => $request->id_recojo])->update($datos);
            ModeloSolicitud::where(['id' => $request->id_solicitud])->update(['estado' => $request->estado_soli]);
            return response()->json(['success' => 'Recojo actualizaco correctamente']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerRecojo(Request $request)
    {
        try {
            $recojos = ModeloRecojo::getRecojos($request->id_solicitud);
            return response()->json(['recojos' => $recojos]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
}
