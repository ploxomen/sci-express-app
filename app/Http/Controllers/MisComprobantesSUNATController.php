<?php

namespace App\Http\Controllers;

use App\Models\ModeloGuias;
use Illuminate\Http\Request;

class MisComprobantesSUNATController extends Controller
{
    public function index()
    {
        $fechaActual = date('Y-m-d');
        $fechaAnterior = date('Y-m-d', strtotime('-30 days'));
        return view('SUNAT.comprobantes', compact('fechaActual', 'fechaAnterior'));
    }
    public function obtenerComprobantes(Request $request)
    {
        $fechaInicio = $request->input('fechaInicio');
        $fechaFin = $request->input('fechaFin');
        $tipo = $request->input('tipo');
        $comprobantes = ModeloGuias::tipoComprobanteSUNAT($tipo)
            ->fechaEmision($fechaInicio, $fechaFin)
            ->soloComprobanteSUNAT()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nro_guia' => $item->nro_guia_concatenado,
                    'fech_emis' => $item->fech_emis,
                    'comprobante_sunat' => $item->comprobante_sunat,
                    'ruc_destinatario' => $item->dest_nro_docu,
                    'nombre_destinatario' => $item->destinatario->nombres,
                    'tipo_comprobante_sunat' => $item->nombre_tipo_comprobante,
                ];
            });
        return datatables()->of($comprobantes)->toJson();
    }
    public function asignarComprobanteSUNAT(Request $request)
    {
        $idGuia = $request->input('id_guia');
        $tipoComprobanteSUNAT = $request->input('tipo_comprobante_sunat');
        $nroComprobanteSUNAT = $request->input('nro_comprobante_sunat');
        $guia = ModeloGuias::find($idGuia);
        if ($guia) {
            $guia->tipo_comprobante_sunat = $tipoComprobanteSUNAT;
            $guia->comprobante_sunat = $nroComprobanteSUNAT;
            $guia->save();
            return response()->json(['success' => true, 'message' => 'Comprobante SUNAT asignado correctamente.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Guía no  encontrada.'], 404);
        }
    }
    public function anulacionComprobanteSUNAT(Request $request)
    {
        $idGuia = $request->input('id_guia');
        $guia = ModeloGuias::find($idGuia);
        if ($guia) {
            $guia->tipo_comprobante_sunat = null;
            $guia->comprobante_sunat = null;
            $guia->save();
            return response()->json(['success' => true, 'message' => 'Comprobante SUNAT anulado correctamente.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Guía no encontrada.'], 404);
        }
    }
    public function consultaComprobantePDFSUNAT($tipoImpresion,$idGuia)
    {
        $guia = ModeloGuias::find($idGuia);
        $facturacionSUNATController = new FacturacionSUNATController();
        if ($guia) {
            $comprobanteSUNAT = $guia->comprobante_sunat;
            list($serie, $numero) = explode('-', $comprobanteSUNAT);
            $facturacionSUNATController->consultaComprobantePDF($serie, $numero, $guia->fech_emis, $guia->tipo_comprobante_sunat, $tipoImpresion);
            return response()->json(['success' => true, 'message' => 'Consulta realizada correctamente.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Guía no encontrada.'], 404);
        }
    }
}
