<table border="1">
    <thead>
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="18" align="center">{{$titulo}}</td>
        </tr>
        <tr>
            <th>N°</th>
            <th>N° Solicitud</th>
            <th>Fecha<br>Solicitud</th>
            <th>Fecha Recojo </th>
            <th>Hr. Recojo Est.</th>
            <th>Fecha Recojido</th>
            <th>Hr. Recojido</th>
            <th>KPI</th>
            <th>Nombre Solicitante</th>
            <th>N° Doc. Cliente</th>
            <th>Nombre Cliente</th>
            <th>Trayecto</th>
            <th>Dirección recojo</th>
            <th>Cluster</th>
            <th>Conductor</th>
            <th>Cluster</th>
            <th>Bulto<br>Total</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @empty($datos)
            <tr>
                <td colspan="100%">No se encontraron recojos programados</td>
            </tr>
        @endempty
            @foreach ($datos as $k => $resultado)
            <tr>
                <td>{{$k + 1}}</td>
                <td>{{$resultado->nroSolicitud}}</td>
                <td>{{$resultado->fechaSolicitud}}</td>
                <td>{{$resultado->fechaRecojo}}</td>
                <td>{{$resultado->hrRecojoEstimado}}</td>
                <td>{{$resultado->fechaRecojida}}</td>
                <td>{{$resultado->horaRecojida}}</td>
                <td>{{$resultado->kpi}}</td>
                <td>{{$resultado->nombreSolicitante}}</td>
                <td>{{$resultado->nro_doc}}</td>
                <td>{{$resultado->nom_cli}}</td>
                <td>{{$resultado->nombreTrayectp}}</td>
                <td>{{$resultado->direccion_recojo}}</td>
                <td>{{$resultado->nombreCluster}}</td>
                <td>{{$resultado->nombreDealer}}</td>
                <td>{{$resultado->nombreConductor}}</td>
                <td>{{$resultado->total_bulto}}</td>
                <td>{{$resultado->estadoSolicitud}}</td>
            </tr>
        @endforeach
    </tbody>
</table>