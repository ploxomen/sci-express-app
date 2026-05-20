<?php

namespace App\Http\Controllers;

use App\Models\ComprobantesSimpleFact;
use App\Models\ModeloGuias;
use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;

class MisComprobantesSUNATController extends Controller
{
    public $facturacionSUNATController;
    public function __construct()
    {
        $this->facturacionSUNATController = new FacturacionSUNATController();
    }
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
        $soloAceptados = $request->input('aceptados', 'false');
        $comprobantes = ModeloGuias::tipoComprobanteSUNAT($tipo)
            ->fechaEmision($fechaInicio, $fechaFin)
            ->soloComprobanteSUNAT();
        if ($soloAceptados === 'true') {
            $comprobantes = $comprobantes->whereNotNull('tipo_comprobante_sunat');
        }
        $comprobantes = $comprobantes->get();
        $comprobantes = $comprobantes->map(function ($item) {
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
        try {
            list($serie, $numero) = explode('-', $nroComprobanteSUNAT);
            $response = $this->facturacionSUNATController->consultaComprobantePDF($serie, $numero, $guia->fech_emis, $tipoComprobanteSUNAT, ComprobantesSimpleFact::IMPRESION_TICKET);
            if (!empty($response->errors) || empty($response->pdf_bytes)) {
                throw new \Exception('El comprobante SUNAT no existe ' . $response->errors);
            }
            $this->facturacionSUNATController->guardarArchivosServidor($response->pdf_bytes, $nroComprobanteSUNAT . ".pdf");
            $guia->tipo_comprobante_sunat = $tipoComprobanteSUNAT;
            $guia->comprobante_sunat = $nroComprobanteSUNAT;
            $guia->save();
            return response()->json(['success' => true, 'message' => 'Comprobante SUNAT asignado correctamente.']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 404);
        }
    }
    public function consultarComprobantesSinAsignar(Request $request)
    {
        return ModeloGuias::where('comprobante_sunat', ModeloGuias::NRO_COMP_ERROR)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'nro_guia' => $item->nro_guia_concatenado,
            ];
        });
    }
    public function anulacionComprobanteSUNAT($idGuia)
    {
        $guia = ModeloGuias::find($idGuia);
        if ($guia) {
            $guia->tipo_comprobante_sunat = null;
            $guia->comprobante_sunat = ModeloGuias::NRO_COMP_ERROR;
            $guia->save();
            return response()->json(['success' => true, 'message' => 'Comprobante SUNAT anulado correctamente.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Guía no encontrada.'], 404);
        }
    }
    public function consultaComprobantePDFSUNAT($tipoImpresion, $idGuia)
    {
        $guia = ModeloGuias::find($idGuia);
        try {
            $comprobanteSUNAT = $guia->comprobante_sunat;
            list($serie, $numero) = explode('-', $comprobanteSUNAT);
            $response = $this->facturacionSUNATController->consultaComprobantePDF($serie, $numero, $guia->fech_emis, $guia->tipo_comprobante_sunat, $tipoImpresion);
            if (!empty($response->pdf_bytes)) {
                $pdfContent = base64_decode($response->pdf_bytes);
                return response($pdfContent, 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="' . $comprobanteSUNAT . '.pdf"');
            }
            throw new \Exception('No se pudo obtener el PDF del comprobante desde SUNAT.' . $response->errors);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 404);
        }
    }
    public function impresionMasiva()
    {
        $fechaActual = date('Y-m-d');
        $fechaAnterior = date('Y-m-d', strtotime('-7 days'));
        return view('SUNAT.impresionGrupal', compact('fechaActual', 'fechaAnterior'));
    }
    public function unirComprobantes(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $comprobantes = $request->ids;
        $guias = ModeloGuias::query()->select('comprobante_sunat', 'fech_emis', 'tipo_comprobante_sunat')->whereIn('comprobante_sunat', $comprobantes)->get();
        $tipoImpresion = $request->input('tipo_impresion', ComprobantesSimpleFact::IMPRESION_TICKET);
        $pdf = new Fpdi();
        $pdf->SetCompression(true);
        foreach ($guias as $guia) {
            $comprobanteSUNAT = $guia->comprobante_sunat;
            list($serie, $numero) = explode('-', $comprobanteSUNAT);
            $response = $this->facturacionSUNATController->consultaComprobantePDF($serie, $numero, $guia->fech_emis, $guia->tipo_comprobante_sunat, $tipoImpresion);
            if (empty($response->pdf_bytes)) {
                continue;
            }
            $pdfContent = base64_decode($response->pdf_bytes);

            // Guardar temporalmente
            $tempFile = tempnam(sys_get_temp_dir(), 'pdf_');

            file_put_contents($tempFile, $pdfContent);
            // Importar PDF
            $pageCount = $pdf->setSourceFile($tempFile);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

                $template = $pdf->importPage($pageNo);

                $size = $pdf->getTemplateSize($template);

                $pdf->AddPage(
                    $size['orientation'],
                    [$size['width'], $size['height']]
                );

                $pdf->useTemplate($template);
            }

            // Eliminar temporal
            unlink($tempFile);
        }
        // Generar PDF final
        $output = $pdf->Output('S');

        return response($output, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="comprobantes.pdf"');
    }
}
