<?php

namespace App\Http\Controllers;

use App\Models\EmpresaTransporte;
use App\Models\ModeloGuias;
use App\Models\User;
use App\Services\UbigeoService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class FacturacionSUNATController extends Controller
{
    public $urlGenerarComprobante = 'https://demo.mifact.net.pe/api/GuiaRemision.svc/SendGuia';
    public $token = 'gN8zNRBV+/FVxTLwdaZx0w==';
    public function guiaRemisionTransportista(Request $request, UbigeoService $ubigeoService, Ubigeo $ubigeo)
    {
        $guia = ModeloGuias::find(7251);
        $remitente = $guia->tipo_guia == 'proveedor' ? $guia->remitenteProveedor : $guia->remitenteCliente;
        $destinatario = $guia->destinatario;
        $conductorPrincipal = User::find($request->conductor_principal);
        $departamentoRemitente = $ubigeo->getNameDepartamento($guia->remi_depa);
        $provinciaRemitente = $ubigeo->getNameProvincia($guia->remi_prov);
        $distritoRemitente = $ubigeo->getNameDistrito($guia->remi_dist);
        $ubigeoRemitente = $ubigeoService->obtenerNumeroUbigeo($departamentoRemitente, $provinciaRemitente, $distritoRemitente);
        $departamentoDestinatario = $ubigeo->getNameDepartamento($guia->dest_depa);
        $provinciaDestinatario = $ubigeo->getNameProvincia($guia->dest_prov);
        $distritoDestinatario = $ubigeo->getNameDistrito($guia->dest_dist);
        $ubigeoDestinatario = $ubigeoService->obtenerNumeroUbigeo($departamentoDestinatario, $provinciaDestinatario, $distritoDestinatario);
        $data = [
            'observaciones' => $request->observaciones,
            'tipo_docu_remi' => '6',
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
            'tot_kg' => $guia->tot_kg,
        ];
        if ($request->filled('conductor_secundario')) {
            $conductorSecundario = User::find($request->conductor_secundario);
            $data['cond_secu_nom'] = $conductorSecundario->name . ' ' . $conductorSecundario->last_name;
            $data['cond_secu_tipo_doc'] = $conductorSecundario->tipoDocumento->sunat_cod;
            $data['cond_secu_num_doc'] = $conductorSecundario->n_document;
            $data['cond_secu_licencia'] = $conductorSecundario->driver_license;
        }
        $detalles = $guia->detalles;
        $dataJSON = (object)array_merge($data, $request->all());
        $parametros = $this->construccionJSONGuiaRemisionTransportista($dataJSON, $detalles);
        return $this->enviarAPI($parametros);
    }
    public function guiaRemisionRemitente(Request $request, UbigeoService $ubigeoService, Ubigeo $ubigeo)
    {
        $guia = ModeloGuias::find(7251);
        $destinatario = $guia->destinatario;
        $departamentoRemitente = $ubigeo->getNameDepartamento($guia->remi_depa);
        $provinciaRemitente = $ubigeo->getNameProvincia($guia->remi_prov);
        $distritoRemitente = $ubigeo->getNameDistrito($guia->remi_dist);
        $ubigeoRemitente = $ubigeoService->obtenerNumeroUbigeo($departamentoRemitente, $provinciaRemitente, $distritoRemitente);
        $departamentoDestinatario = $ubigeo->getNameDepartamento($guia->dest_depa);
        $provinciaDestinatario = $ubigeo->getNameProvincia($guia->dest_prov);
        $distritoDestinatario = $ubigeo->getNameDistrito($guia->dest_dist);
        $ubigeoDestinatario = $ubigeoService->obtenerNumeroUbigeo($departamentoDestinatario, $provinciaDestinatario, $distritoDestinatario);
        $data = [
            'observaciones' => $request->observaciones,
            'tipo_docu_dest' => $guia->tipoDocumentoDestinatario->sunat_cod,
            'num_doc_dest' => $guia->dest_nro_docu,
            'nombre_dest' => $destinatario->nombres,
            'ubigeo_remi' => $ubigeoRemitente,
            'ubigeo_dest' => $ubigeoDestinatario,
            'tot_kg' => $guia->tot_kg,
        ];
        //TRANSPORTE PUBLICO
        if($request->transporte == "01"){
            $empresaTransporte = EmpresaTransporte::find($request->empresa_transporte);
            $data['razon_social_transporte'] = $empresaTransporte->empresa;
            $data['ruc_transporte'] =  $empresaTransporte->nro_ruc;
        }else{
            //TRANSPORTE PRIVADO
            $conductorPrincipal = User::find($request->conductor_principal);
            $data['cond_prin_nom'] = $conductorPrincipal->name . ' ' . $conductorPrincipal->last_name;
            $data['cond_prin_tipo_doc'] = $conductorPrincipal->tipoDocumento->sunat_cod;
            $data['cond_prin_num_doc'] = $conductorPrincipal->n_document;
            $data['cond_prin_licencia'] = $conductorPrincipal->driver_license;
            $data['placa_princial'] = $conductorPrincipal->placa;
        }
        $detalles = $guia->detalles;
        $dataJSON = (object)array_merge($data, $request->all());
        $parametros = $this->construccionJSONGuiaRemisionRemitente($dataJSON, $detalles);
        return $this->enviarAPI($parametros);
    }
    public function enviarAPI($parametros)
    {
        $client = new Client();
        $body = json_encode($parametros);
        $response = $client->post($this->urlGenerarComprobante, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => $body
        ]);
        $data = $response->getBody()->getContents();
        return json_decode($data);
    }
    public function JSONGuiaRemision($data, $detalles)
    {
        return [
            "TOKEN" => $this->token,
            "RETORNA_XML_ENVIO" => false,
            "RETORNA_XML_CDR" => false,
            "FEC_EMIS_GUR" => $data->fecha_emision,
            "FEC_TRASLADO" => $data->fecha_traslado,
            "COD_PRCD_CARGA" => "001",
            "OBSERVACIONES" => $data->observaciones,
            "COD_TIP_NIF_EMIS" => '6',
            "NUM_NIF_EMIS" => env('SIMPLE_RUC'),
            "NOM_COMER_EMIS" => env('SIMPLE_NOMBRE_COMERCIAL'),
            "TXT_DMCL_FISC_EMIS" => env('SIMPLE_FISC_EMIS'),
            "NOM_RZN_SOC_EMIS" => env('SIMPLE_RAZON_SOCIAL'),
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
            "TXT_VERS_ESTRUCT_UBL" => "2.0",
            "UND_MEDIDA" => "KGM",
            "PESO_BRUTO" => $data->tot_kg,
            "items" => $this->detalleRemision($detalles)
        ];
    }
    public function construccionJSONGuiaRemisionTransportista($data, $detalles)
    {
        $dataPrincipal = $this->JSONGuiaRemision($data, $detalles);
        $dataPrincipal = array_merge($dataPrincipal, [
            "NRO_REGISTRO_MTC" => env('SIMPLE_NRO_REGISTRO_MTC'), // CONSULTAR ELLO ALAIN SI ES TRANSPORTISTA NO DEBE SER VACIO,
            "NUM_SERIE_GUR" => "V001",
            "INDICADOR_PAGADOR_FLETE_REMITENTE" => "1",
            "INDICADOR_TRASLADO_TOTAL_BIENES" => "1",
            "INDICADOR_TRASLADO_SUB_CONTRATADO" => "0",
            "INDICADOR_PAGADOR_FLETE_SUB_CONTRATADOR" => "0",
            "INDICADOR_PAGADOR_FLETE_TERCERO" => "0",
            "NUM_CORRE_GUR" => '',
            "COD_TIP_GUR" => "31", // TRANSPORTISTA 31 / REMITENTE 09
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
    public function construccionJSONGuiaRemisionRemitente($data, $detalles)
    {
        $dataPrincipal = $this->JSONGuiaRemision($data, $detalles);
        $dataPrincipal = array_merge($dataPrincipal, [
            "NUM_SERIE_GUR" => "V001",
            "NUM_CORRE_GUR" => '10000',
            "INDICADOR_M1_L" => isset($data->categoria_m1) ? "1" : "0",
            "COD_TIP_GUR" => "09", // TRANSPORTISTA 31 / REMITENTE 09
            "MOT_TRASLADO" => $data->motivo_traslado,
            "MOD_TRASLADO" => $data->transporte,
        ]);
        if($data->transporte == "01"){
            $dataPrincipal['NUM_NIF_TRANSP'] = $data->ruc_transporte;
            $dataPrincipal['COD_TIP_NIF_TRANSP'] = "6";
            $dataPrincipal['NOM_RZN_SOC_TRANSP'] = $data->razon_social_transporte;
        }else{
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
                'COD_ITEM' => 'P00' . $key,
                'COD_UND_MEDIDA_ITEM' => 'NIU',
                'DESC_ITEM' => $detalle->descripcion,
                'CANT_ITEM' => $detalle->cantidad,
                'PESO_ITEM' => $detalle->kg
            ];
        }
        return $detallePersonalizado;
    }
}
