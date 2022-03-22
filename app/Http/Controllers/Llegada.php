<?php

namespace App\Http\Controllers;

use App\Models\ModeloEmbarque;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Llegada extends Controller
{
    public function viewLlegada()
    {
        $modulos = User::getModulos(Auth::id());
        return view('pedidos.llegada',compact('modulos'));
    }
    public function obtenerEmbarqueArribar(Request $request)
    {
        try {
            $id_embarque = $request->has('embarque') ? $request->embarque : 0;
            $id_guia = $request->has('guia') ? $request->guia : 0;
            return response()->json(['resultados' => ModeloEmbarque::obtenerEmbarqueArribado($id_embarque,$id_guia,Auth::id(),$request->tipo)]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function actualizarGuiasLlegada(Request $request)
    {
        try {
            ModeloEmbarque::actualizarGuiaLlegadas($request->embarque,$request->manifiesto,$request->guia,$request->conforme,$request->texto);
            return response()->json(['success' => 'El estado de la guía se cambio con éxito']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
}
