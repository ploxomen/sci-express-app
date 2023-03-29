@extends('general.indexReportePdf')
@section('titulo','Reporte de embarques')
@section('body')
    <span class="titlo-principal">LISTA DE EMBARQUES DESDE {{$fechaIni}} HASTA {{$fechaFi}}</span>
    <table border="1" class="tabla">
        <thead>
            <tr>
                <th>N°</th>
                <th>N° Embarque</th>
                <th>Fecha<br>Embarque</th>
                <th>Empresa Transporte</th>
                <th>Fecha Hr. <br>Llegada</th>
                <th>Cluster</th>
                <th>Comprobantes</th>
                <th>N° Comprobantes</th>
                <th>N° Manifiesto</th>
                <th>Fecha<br>Manifiesto</th>
                <th>Prov. <br>Origen</th>
                <th>Prov. <br>Destino</th>
                <th>Estado</th>
                <th>N° Guía/OS</th>
                <th>Situacion</th>
            </tr>
        </thead>
        <tbody>
            @empty($resultados)
                <tr>
                    <td colspan="100%" class="text-center">No se encontraron embarques programados</td>
                </tr>
            @endempty
             @foreach ($resultados as $k => $resultado)
        
                <tr>
                    <td class="text-center">{{$k + 1}}</td>
                    <td class="text-center">{{$resultado->nroEmbarque}}</td>
                    <td class="text-center">{{$resultado->fechaEmbarque}}</td>
                    <td class="text-center">{{$resultado->empresaTransporte}}</td>
                    <td>{{$resultado->fechaLlegada}} {{$resultado->hrLlegada}}</td>
                    <td>{{$resultado->nombreCluster}}</td>
                    <td>{{$resultado->comporbantes}}</td>
                    <td>{{$resultado->nroComprobantes}}</td>
                    <td>{{$resultado->nroManifiesto}}</td>
                    <td>{{$resultado->fechaManifiesto}}</td>
                    <td>{{$resultado->prov_orig}}</td>
                    <td>{{$resultado->prov_dest}}</td>
                    <td>{{$resultado->estado}}</td>
                    <td>{{$resultado->nroGuia}}</td>
                    <td>{{$resultado->situacionEnio}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection