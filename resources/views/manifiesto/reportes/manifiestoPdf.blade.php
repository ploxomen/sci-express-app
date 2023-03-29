@extends('general.indexReportePdf')
@section('titulo','Reporte de manifiestos')
@section('body')
    <span class="titlo-principal">LISTA DE MANIFIESTOS DESDE {{$fechaIni}} HASTA {{$fechaFi}}</span>
    <table border="1" class="tabla">
        <thead>
            <tr>
                <th>N°</th>
                <th>N° Manifiesto</th>
                <th>Fecha<br>Manifiesto</th>
                <th>Trayecto</th>
                <th>Prov.<br>Origen</th>
                <th>Prov.<br>Destino</th>
                <th>Ciudad Base</th>
                <th>Cluster</th>
                <th>Total<br>Guías</th>
                <th>Total<br>Bulto</th>
                <th>Conductor</th>
                <th>Responsable</th>
                <th>Fecha Hr. <br>Embarque Est.</th>
                <th>Fecha Hr. <br>Embarcado</th>
                <th>KPI</th>
            </tr>
        </thead>
        <tbody>
            @empty($resultados)
                <tr>
                    <td colspan="100%" class="text-center">No se encontraron recojos programados</td>
                </tr>
            @endempty
             @foreach ($resultados as $k => $resultado)
        
                <tr>
                    <td class="text-center">{{$k + 1}}</td>
                    <td class="text-center">{{$resultado->nroManifiesto}}</td>
                    <td class="text-center">{{$resultado->fechaManifiesto}}</td>
                    <td class="text-center">{{$resultado->trayectoManifiesto}}</td>
                    <td class="text-center">{{$resultado->prov_orig}}</td>
                    <td class="text-center">{{$resultado->prov_dest}}</td>
                    <td class="text-center">{{$resultado->nombreBase}}</td>
                    <td>{{$resultado->nombreCluster}}</td>
                    <td>{{$resultado->nroGuias}}</td>
                    <td>{{$resultado->bulto}}</td>
                    <td>{{$resultado->nombreConducor}}</td>  
                    <td>{{$resultado->nombreResponsable}}</td>
                    <td>{{$resultado->fechaEmbarque}} {{$resultado->finEstimadoEmbarque}}</td>
                    <td>{{$resultado->fechaEmbarque}} {{$resultado->finEmbarque}}</td>
                    <td>{{$resultado->kpi}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
