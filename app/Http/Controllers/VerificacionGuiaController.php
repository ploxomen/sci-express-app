<?php

namespace App\Http\Controllers;

use App\Models\DocumentosObligatorios;
use App\Models\DocumentosObligatoriosExcluidos;
use App\Models\InformacionAdicional;
use App\Models\ModelFacturacion;
use App\Models\ModeloGuias;
use App\Models\RechazoGuiaRetornada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificacionGuiaController extends Controller
{
    public function indexVerificacion() {
        $fechaFiltros = InformacionAdicional::obtenerDiasFiltros();
        return view('comprobante.verificacion',compact('fechaFiltros'));
    }
    public function obtenerGuias(Request $request) {
        $guias = ModeloGuias::mostrarTodasGuias(11, 0, $request->fechaInicio, $request->fechaFin);
        return datatables()->of($guias)->toJson();   
    }
    public function opcionesDenegar(Request $request) {
        $guia = ModeloGuias::find($request->guia);
        $exclusionAlternativa = DocumentosObligatoriosExcluidos::where('cliente_id',$guia->id_remitente)->pluck('doc_oblig_id');
        $alternativas = DocumentosObligatorios::select('id','opcion')->whereNotIn('id',$exclusionAlternativa)->get();
        return response()->json(['alternativas' => $alternativas]);
    }
    public function aprobarGuia(Request $request) {
        ModeloGuias::find($request->guia)->update(['situ' => 15]);
        ModelFacturacion::calcularGuias(ModeloGuias::where('id',$request->guia)->get());
        return response()->json(['success' => 'Guía aprobada correctamente correctamante']);
    }
    public function desaprobarGuia(Request $request) {
        ModeloGuias::find($request->guia)->update(['situ' => 8]);
        $documentos = DocumentosObligatorios::whereIn('id',$request->opciones)->select('opcion')->get()->pluck('opcion')->toArray();
        RechazoGuiaRetornada::create([
            'detalles' => $request->detalle,
            'guia_id' => $request->guia,
            'usuario_id' => Auth::id(),
            'opciones_denegado' => $request->has('opciones') ? implode(";",$documentos) : ""
        ]);
        return response()->json(['success' => 'Guía rechazada correctamante']);

    }
}
