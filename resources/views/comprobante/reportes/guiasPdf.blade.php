@extends('general.indexReportePdf')
@section('titulo','Reporte de Guías/OS')
@section('body')
    <span class="titlo-principal">LISTA DE GUÍAS/OS DESDE {{$fechaIni}} HASTA {{$fechaFi}}</span>
    <table border="1" class="tabla">
        <thead>
            <tr>
                <th>N°</th>
                <th>N° Guía/OS SCI</th>
                <th>Fecha<br>Emisión</th>
                <th>Tipo Documentos Remitente</th>
                <th>N° Documentos Remitente</th>
                <th>Remitente</th>
                <th>Cluster</th>
                <th>Ciudad Base</th>
                <th>Ciudad Final</th>
                <th>Peso</th>
                <th>Volumen</th>
                <th>Típo</th>
                <th>Situación</th>
            </tr>
        </thead>
        <tbody>
            @empty($resultados)
                <tr>
                    <td colspan="100%" class="text-center">No se encontraron guías/OS</td>
                </tr>
            @endempty
             @foreach ($resultados as $k => $resultado)
                @php
                    $documentosRemitente = empty($resultado->comp_clie) ? "NO ESTABLECIDO" : str_replace(";","<br>",$resultado->comp_clie);
                    $nroDocumentos = empty($resultado->nro_comp_cli) ? "NO ESTABLECIDO" : str_replace(";","<br>",$resultado->nro_comp_cli);
                @endphp
                <tr>
                    <td class="text-center">{{$k + 1}}</td>
                    <td class="text-center">{{$resultado->nro_guia}}</td>
                    <td class="text-center">{{$resultado->fecha_emision}}</td>
                    <td>{!! $documentosRemitente !!}</td>
                    <td>{!! $nroDocumentos !!}</td>
                    <td>{{$resultado->nom_remi}}</td>
                    <td>{{empty($resultado->nombreCluster) ? "NO ESTABLECIDO" : mb_strtoupper($resultado->nombreCluster,"UTF-8")}}</td>
                    <td>{{empty($resultado->ciudad_base_cluster) ? "NO ESTABLECIDO" : mb_strtoupper($resultado->ciudad_base_cluster,"UTF-8")}}</td>
                    <td>{{empty($resultado->ciudad_final) ? "NO ESTABLECIDO" : mb_strtoupper($resultado->ciudad_final,"UTF-8")}}</td>
                    <td>{{$resultado->tot_kg}}</td>
                    <td>{{$resultado->tot_vol}}</td>
                    <td>{{$resultado->guia_tipo}}</td>
                    <td>{{$resultado->nombreEstado}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection