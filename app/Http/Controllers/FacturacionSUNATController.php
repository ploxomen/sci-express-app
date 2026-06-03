<?php

namespace App\Http\Controllers;

use App\Models\ComprobantesSimpleFact;
use App\Models\EmpresaTransporte;
use App\Models\ModeloGuias;
use App\Models\User;
use App\Services\UbigeoService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacturacionSUNATController extends Controller
{
    public $urlGenerarComprobante = '';
    public $urlConsultaPDFXML = '';

    public function __construct()
    {
        $baseUrl = env('SIMPLE_URL');
        $this->urlGenerarComprobante = $baseUrl . '/GuiaRemision.svc/SendGuia';
        $this->urlConsultaPDFXML = $baseUrl . '/GuiaRemision.svc/GetGuia';
    }

    public function guiaRemisionTransportista(Request $request)
    {
        $guia = ModeloGuias::find($request->guia);
        $remitente = $guia->tipo_guia == 'proveedor' ? $guia->remitenteProveedor : $guia->remitenteCliente;
        $destinatario = $guia->destinatario;
        $conductorPrincipal = User::find($request->conductor_principal);
        $ubigeoRemitente = $this->obtenerUBIGEOSunatNombres($guia->remi_depa, $guia->remi_prov, $guia->remi_dist);
        $ubigeoDestinatario = $this->obtenerUBIGEOSunatNombres($guia->dest_depa, $guia->dest_prov, $guia->dest_dist);
        $data = [
            'observaciones' => $request->observaciones,
            'tipo_docu_remi' => '6',
            'impresion' => $request->impresion,
            'comprobantes' => $guia->nro_comp_cli,
            'num_doc_remi' => $guia->remi_nro_docu,
            'nombre_remi' => $remitente->nomb_prov ?? $remitente->nom_cli,
            'tipo_docu_dest' => $guia->tipoDocumentoDestinatario->sunat_cod,
            'num_doc_dest' => $guia->dest_nro_docu,
            'nombre_dest' => $destinatario->nombres,
            'cond_prin_nom' => $conductorPrincipal->name . ' ' . $conductorPrincipal->last_name,
            'cond_prin_tipo_doc' => $conductorPrincipal->tipoDocumento->sunat_cod,
            'cond_prin_num_doc' => $conductorPrincipal->n_document,
            'cond_prin_licencia' => $conductorPrincipal->driver_license,
            'ubigeo_remi' => $ubigeoRemitente,
            'ubigeo_dest' => $ubigeoDestinatario,
            'tot_kg' => $request->bulto_total_kg,
        ];
        if ($request->filled('conductor_secundario')) {
            $conductorSecundario = User::find($request->conductor_secundario);
            $data['cond_secu_nom'] = $conductorSecundario->name . ' ' . $conductorSecundario->last_name;
            $data['cond_secu_tipo_doc'] = $conductorSecundario->tipoDocumento->sunat_cod;
            $data['cond_secu_num_doc'] = $conductorSecundario->n_document;
            $data['cond_secu_licencia'] = $conductorSecundario->driver_license;
        }
        $serieDocumentoSunat = $request->serie;
        $detalles = $guia->detalles;
        $dataJSON = (object)array_merge($data, $request->all());
        $parametros = $this->construccionJSONGuiaRemisionTransportista($dataJSON, $detalles, $serieDocumentoSunat);
        $response = $this->enviarAPI($parametros, $this->urlGenerarComprobante);
        $this->responseAPI($response, $guia, $response->serie_cpe, $response->correlativo_cpe, $serieDocumentoSunat, ComprobantesSimpleFact::NRO_REMISION_TRANSPORTISTA, !$request->has('return_guia'));
        return $response;
    }
    public function documentosReferencia($textoReferencia)
    {
        $comprobantes = explode(';', $textoReferencia);
        $documentosReferencia = [];
        foreach ($comprobantes as $comprobante) {
            $documentosReferencia[] = ComprobantesSimpleFact::comprobanteSUNAT($comprobante);
        }
        return $documentosReferencia;
    }
    public function consultaComprobantePDF($serie, $correlativo, $fechaEmision, $tipoDocumento, $tipoComprobanteImpreso)
    {
        $parametros = array_merge($this->retornoCredencialesSIMPLEFACT(), [
            "NUM_SERIE_GUR" => $serie,
            "NUM_CORRE_GUR" => $correlativo,
            "COD_TIP_GUR" => $tipoDocumento,
            "FEC_EMIS_GUR" => $fechaEmision,
            "RETORNA_PDF" => true,
            "COD_FORM_IMPR" => $tipoComprobanteImpreso
        ]);
        unset($parametros['NOM_COMER_EMIS']);
        unset($parametros['TXT_DMCL_FISC_EMIS']);
        unset($parametros['NOM_RZN_SOC_EMIS']);
        $response = $this->enviarAPI($parametros, $this->urlConsultaPDFXML);
        return $response;
    }
    public function guiaRemisionRemitente(Request $request)
    {
        $guia = ModeloGuias::find($request->guia);
        $destinatario = $guia->destinatario;
        // $ubigeoRemitente = $this->obtenerUBIGEOSunatNombres($guia->remi_depa, $guia->remi_prov, $guia->remi_dist);
        $ubigeoRemitente = $request->ubigeo_partida;
        $ubigeoDestinatario = $this->obtenerUBIGEOSunatNombres($guia->dest_depa, $guia->dest_prov, $guia->dest_dist);
        $data = [
            'observaciones' => $request->observaciones,
            'tipo_docu_dest' => $guia->tipoDocumentoDestinatario->sunat_cod,
            'num_doc_dest' => $guia->dest_nro_docu,
            'nombre_dest' => $destinatario->nombres,
            'impresion' => $request->impresion,
            'ubigeo_remi' => $ubigeoRemitente,
            'ubigeo_dest' => $ubigeoDestinatario,
            'tot_kg' => $request->bulto_total_kg,
        ];
        //TRANSPORTE PUBLICO
        if ($request->transporte == "01") {
            $empresaTransporte = EmpresaTransporte::find($request->empresa_transporte);
            $data['razon_social_transporte'] = $empresaTransporte->empresa;
            $data['ruc_transporte'] =  $empresaTransporte->nro_ruc;
        } else {
            //TRANSPORTE PRIVADO
            $conductorPrincipal = User::find($request->conductor_principal);
            $data['cond_prin_nom'] = $conductorPrincipal->name . ' ' . $conductorPrincipal->last_name;
            $data['cond_prin_tipo_doc'] = $conductorPrincipal->tipoDocumento->sunat_cod;
            $data['cond_prin_num_doc'] = $conductorPrincipal->n_document;
            $data['cond_prin_licencia'] = $conductorPrincipal->driver_license;
            $data['placa_princial'] = $conductorPrincipal->placa;
        }
        $serieDocumentoSunat = $request->serie;
        $detalles = $guia->detalles;
        $dataJSON = (object)array_merge($data, $request->all());
        $parametros = $this->construccionJSONGuiaRemisionRemitente($dataJSON, $detalles, $serieDocumentoSunat);
        $response = $this->enviarAPI($parametros, $this->urlGenerarComprobante);
        $this->responseAPI($response, $guia, $response->serie_cpe, $response->correlativo_cpe, $serieDocumentoSunat, ComprobantesSimpleFact::NRO_REMISION_REMITENTE, !$request->has('return_guia'));
        return $response;
    }
    public function obtenerUBIGEOSunatNombres($ubiDepartamento, $ubiProvincia, $ubiDistrito)
    {
        $ubigeoService = new UbigeoService();
        $ubigeo = new Ubigeo();
        $departamentoRemitente = $ubigeo->getNameDepartamento($ubiDepartamento);
        $provinciaRemitente = $ubigeo->getNameProvincia($ubiProvincia);
        $distritoRemitente = $ubigeo->getNameDistrito($ubiDistrito);
        return $ubigeoService->obtenerNumeroUbigeo($departamentoRemitente, $provinciaRemitente, $distritoRemitente);
    }
    public function guardarArchivosServidor($base64, $nombre, $ruta = 'documentos')
    {
        $pdfContent = base64_decode($base64);
        $filename = $ruta . "/" . $nombre;
        return Storage::disk('public')->put($filename, $pdfContent);
    }
    public function responseAPI($response, ModeloGuias $guia, $numeroSerie, $correlativo, $tipo, $tipoDocumentoSUNAT, $deleteGuia = false)
    {
        if (!empty($response->errors) && $deleteGuia) {
            $guia->procesos()->delete();
            $guia->detalles()->delete();
            $guia->delete();
            return false;
        }
        ComprobantesSimpleFact::firstWhere('serie', $tipo)->increment('correlativo');
        $numeroComprobante = empty($numeroSerie) || empty($correlativo) ? ModeloGuias::NRO_COMP_ERROR : $numeroSerie . "-" . $correlativo;
        $this->guardarArchivosServidor($response->pdf_bytes, $numeroComprobante . ".pdf");
        return $guia->update(['comprobante_sunat' => $numeroComprobante, 'tipo_comprobante_sunat' => $tipoDocumentoSUNAT]);
    }
    public function enviarAPI($parametros, $url)
    {
        $client = new Client();
        $body = json_encode($parametros);
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => $body
        ]);
        $data = $response->getBody()->getContents();
        return json_decode($data);
    }
    public function retornoCredencialesSIMPLEFACT()
    {
        return [
            "NUM_NIF_EMIS" => env('SIMPLE_RUC'),
            "NOM_COMER_EMIS" => env('SIMPLE_NOMBRE_COMERCIAL'),
            "TXT_DMCL_FISC_EMIS" => env('SIMPLE_FISC_EMIS'),
            "NOM_RZN_SOC_EMIS" => env('SIMPLE_RAZON_SOCIAL'),
            "TOKEN" => env('SIMPLE_TOKEN'),
            "TXT_VERS_ESTRUCT_UBL" => "2.0",
        ];
    }
    public function JSONGuiaRemision($tipo, $data, $detalles)
    {
        $comprobanteSimple = ComprobantesSimpleFact::porSerie($tipo)->first();
        return array_merge($this->retornoCredencialesSIMPLEFACT(), [
            "COD_FORM_IMPR" => $data->impresion,
            "RETORNA_XML_ENVIO" => false,
            "RETORNA_PDF" => true,
            "NUM_SERIE_GUR" => $comprobanteSimple->serie,
            "NUM_CORRE_GUR" => $comprobanteSimple->correlativo + 1,
            "RETORNA_XML_CDR" => false,
            "FEC_EMIS_GUR" => $data->fecha_emision,
            "FEC_TRASLADO" => $data->fecha_traslado,
            "COD_PRCD_CARGA" => "001",
            "OBSERVACIONES" => $data->observaciones,
            "COD_TIP_NIF_EMIS" => '6',
            "COD_UBI_EMIS" => "",
            "UBI_PARTIDA" => $data->ubigeo_remi,
            "UBI_LLEGADA" => $data->ubigeo_dest,
            "DIR_PARTIDA" => $data->punto_partida,
            "DIR_LLEGADA" => $data->punto_llegada,
            "COD_TIP_NIF_DEST" => $data->tipo_docu_dest,
            "NOM_RZN_SOC_DEST" => $data->nombre_dest,
            "NUM_NIF_DEST" => $data->num_doc_dest,
            "IND_TRANSBORDO" => false,
            "TXT_VERS_UBL" => "2.1",
            "UND_MEDIDA" => "KGM",
            "PESO_BRUTO" => $data->tot_kg,
            "items" => $this->detalleRemision($detalles),
            "docs_referenciado" => isset($data->comprobantes) ? $this->documentosReferencia($data->comprobantes) : [],
        ]);
    }
    public function construccionJSONGuiaRemisionTransportista($data, $detalles, $tipo)
    {
        $dataPrincipal = $this->JSONGuiaRemision($tipo, $data, $detalles);
        $dataPrincipal = array_merge($dataPrincipal, [
            "NRO_REGISTRO_MTC" => env('SIMPLE_NRO_REGISTRO_MTC'), // CONSULTAR ELLO ALAIN SI ES TRANSPORTISTA NO DEBE SER VACIO,
            "INDICADOR_PAGADOR_FLETE_REMITENTE" => "1",
            "INDICADOR_TRASLADO_TOTAL_BIENES" => "1",
            "INDICADOR_TRASLADO_SUB_CONTRATADO" => "0",
            "INDICADOR_PAGADOR_FLETE_SUB_CONTRATADOR" => "0",
            "INDICADOR_PAGADOR_FLETE_TERCERO" => "0",
            "COD_TIP_GUR" => ComprobantesSimpleFact::NRO_REMISION_TRANSPORTISTA, // TRANSPORTISTA 31 / REMITENTE 09
            "COD_TIP_NIF_REMIT" => $data->tipo_docu_remi,
            "NOM_RZN_SOC_REMITENTE" => $data->tipo_docu_remi,
            "NUM_NIF_REMITENTE" => $data->num_doc_remi,
            "NRO_LICENCIA_CONDUCT" => $data->cond_prin_licencia,
            "COD_TIP_NIF_CONDUCT" => $data->cond_prin_tipo_doc,
            "NUM_NIF_CONDUCT" => $data->cond_prin_num_doc,
            "NOM_RZN_SOC_CONDUCT" => $data->cond_prin_nom,
            "PLACA" => $data->placa_principal, // placa vehiculo principal
            "CONSTANCIA_VEHICULAR_TUC" => $data->tuse_principal, // dato opcional del vehiculo principal, hasta que sunat lo permita (se recomienda siempre enviar)
        ]);
        if (!empty($data->placas_secun)) {
            $dataPrincipal['vehiculos_secundarios'] = [
                'PLACA' => $data->placas_secun,
                'CONSTANCIA_VEHICULAR_TUC' => $data->tuse_secun
            ];
        }
        if (!empty($data->conductor_secundario)) {
            $dataPrincipal['conductores_secundarios'] = [
                'COD_TIP_NIF_CONDUCT' => $data->cond_secu_tipo_doc,
                'NUM_NIF_CONDUCT' => $data->cond_secu_num_doc,
                'NOM_RZN_SOC_CONDUCT' => $data->cond_secu_nom,
                'NRO_LICENCIA_CONDUCT' => $data->cond_secu_licencia,
            ];
        }
        return $dataPrincipal;
    }
    public function construccionJSONGuiaRemisionRemitente($data, $detalles, $tipo)
    {
        $dataPrincipal = $this->JSONGuiaRemision($tipo, $data, $detalles);
        $dataPrincipal = array_merge($dataPrincipal, [
            "INDICADOR_M1_L" => isset($data->categoria_m1) ? "1" : "0",
            "COD_TIP_GUR" => ComprobantesSimpleFact::NRO_REMISION_REMITENTE, // TRANSPORTISTA 31 / REMITENTE 09
            "MOT_TRASLADO" => $data->motivo_traslado,
            "MOD_TRASLADO" => $data->transporte,
        ]);
        if ($data->transporte == "01") {
            $dataPrincipal['NUM_NIF_TRANSP'] = $data->ruc_transporte;
            $dataPrincipal['COD_TIP_NIF_TRANSP'] = "6";
            $dataPrincipal['NOM_RZN_SOC_TRANSP'] = $data->razon_social_transporte;
        } else {
            $dataPrincipal['COD_TIP_NIF_CONDUCT'] = $data->cond_prin_tipo_doc;
            $dataPrincipal['NUM_NIF_CONDUCT'] = $data->cond_prin_num_doc;
            $dataPrincipal['NOM_RZN_SOC_CONDUCT'] = $data->cond_prin_nom;
            $dataPrincipal['NRO_LICENCIA_CONDUCT'] = $data->cond_prin_licencia;
            $dataPrincipal['PLACA'] = $data->placa_princial;
        }
        return $dataPrincipal;
    }
    public function detalleRemision($detalles)
    {
        $detallePersonalizado = [];
        foreach ($detalles as $key => $detalle) {
            $detallePersonalizado[] = [
                'NUM_LINEA' => $key + 1,
                'COD_ITEM' => 'P00' . ($key + 1),
                'COD_UND_MEDIDA_ITEM' => $detalle->embase_embalaje_SUNAT ?? 'NIU',
                'DESC_ITEM' => empty($detalle->descripcion) ? "{$detalle->grupo_envio} - {$detalle->tipoEnvio->tipo_envio}" : "{$detalle->grupo_envio} - {$detalle->tipoEnvio->tipo_envio} - {$detalle->descripcion}",
                'CANT_ITEM' => $detalle->cantidad,
                'PESO_ITEM' => $detalle->kg
            ];
        }
        return $detallePersonalizado;
    }
}
