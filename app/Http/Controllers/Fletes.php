<?php

namespace App\Http\Controllers;

use App\Models\Fletes as ModelsFletes;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Fletes extends Controller
{
    public function viewMisFletes() : View
    {
        $modulos = User::getModulos(Auth::id());
        return view('embarque.misFletes',compact('modulos'));
    }
    public function getFletes(Request $request)
    {
        if($request->has('flete')){
            $fletes = ModelsFletes::find($request->flete);
            return response()->json(['flete' => $fletes]);
        }else{
            $fletes = ModelsFletes::all();
            return datatables()->of($fletes)->toJson();
        }
    }
    public function deleteFlete(Request $request)
    {
        ModelsFletes::find($request->flete)->delete();
        return response()->json(['success' => 'Flete eliminado correctamente']);
    }
    public function updateFlete(Request $request)
    {
        if(ModelsFletes::where('ciudad_base', $request->ciudad_base)->where('id','!=',$request->flete)->count() >= 1) {
            return response()->json(['alerta' => 'La ciudad base ' . $request->ciudad_base . ' ya se encuentra registrada, por favor intente con otra ciudad']);
        }
        $datos = $request->all();
        unset($datos['flete']);
        ModelsFletes::find($request->flete)->update($datos);
        return response()->json(['success' => 'Datos actualizados correctamente']);
    }
    public function addFlete(Request $request)
    {
        if (ModelsFletes::where('ciudad_base', $request->ciudad_base)->count() >= 1) {
            return response()->json(['alerta' => 'La ciudad base ' . $request->ciudad_base . ' ya se encuentra registrada, por favor intente con otra ciudad']);
        }
        ModelsFletes::create($request->all());
        return response()->json(['success' => 'Flete agregado correctamente']);
    }
}
