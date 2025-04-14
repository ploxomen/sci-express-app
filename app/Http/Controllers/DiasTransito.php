<?php

namespace App\Http\Controllers;

use App\Models\DiasTransito as ModelsDiasTransito;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiasTransito extends Controller
{
    public function viewDiasTransito(): View
    {
        $departamentos = (new Ubigeo)->getDepartamentosClientes();
        return view('embarque.diasTransito', compact('departamentos'));
    }
    public function getDiasTransito(Request $request)
    {
        if ($request->has('dias_transito')) {
            $diasTransito = ModelsDiasTransito::find($request->dias_transito);
            return response()->json(['transito' => $diasTransito]);
        } else {
            $diasTransito = ModelsDiasTransito::all();
            return datatables()->of($diasTransito)->toJson();
        }
    }
    public function addDiasTransito(Request $request)
    {
        $datos = $request->all();
        if(isset($datos['departamento_origen'])){
            $datos['departamento_origen'] = mb_strtoupper((new Ubigeo())->getNameDepartamento($datos['departamento_origen']),'UTF-8');
        }
        if (isset($datos['departamento_destino'])) {
            $datos['departamento_destino'] = mb_strtoupper((new Ubigeo())->getNameDepartamento($datos['departamento_destino']), 'UTF-8');
        }
        ModelsDiasTransito::create($datos);
        return response()->json(['success' => 'día de tránsito agregado correctamente']);
    }
    public function updateDiasTransito(Request $request)
    {
        $datos = $request->all();
        if (isset($datos['departamento_origen'])) {
            $datos['departamento_origen'] = mb_strtoupper((new Ubigeo())->getNameDepartamento($datos['departamento_origen']), 'UTF-8');
        }
        if (isset($datos['departamento_destino'])) {
            $datos['departamento_destino'] = mb_strtoupper((new Ubigeo())->getNameDepartamento($datos['departamento_destino']), 'UTF-8');
        }
        if (ModelsDiasTransito::where(['departamento_origen' => $datos['departamento_origen'], 'departamento_destino' => $datos['departamento_destino'], 'ciudad_base' => $request->ciudad_base, 'ciudad_destino' => $request->ciudad_destino])->where('id', '!=', $request->dias_transito)->count() >= 1) {
            return response()->json(['alerta' => 'Esta información de dias de trasnito ya se encuentra registrada']);
        }
        unset($datos['dias_transito']);
        ModelsDiasTransito::find($request->dias_transito)->update($datos);
        return response()->json(['success' => 'Datos actualizados correctamente']);
    }
    public function deleteDiaTransito(Request $request)
    {
        ModelsDiasTransito::find($request->dias_transito)->delete();
        return response()->json(['success' => 'Día de transito eliminado correctamente']);
    }
}
