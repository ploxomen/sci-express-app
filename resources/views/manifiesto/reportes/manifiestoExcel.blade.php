<table border="1">
    <thead>
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="16" align="center">{{$titulo}}</td>
        </tr>
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
        @empty($datos)
                <tr>
                    <td colspan="15">No se encontraron recojos programados</td>
                </tr>
            @endempty
             @foreach ($datos as $k => $resultado)
                <tr>
                    <td>{{$k + 1}}</td>
                    <td>{{$resultado->nroManifiesto}}</td>
                    <td>{{$resultado->fechaManifiesto}}</td>
                    <td>{{$resultado->trayectoManifiesto}}</td>
                    <td>{{$resultado->prov_orig}}</td>
                    <td>{{$resultado->prov_dest}}</td>
                    <td>{{$resultado->nombreBase}}</td>
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