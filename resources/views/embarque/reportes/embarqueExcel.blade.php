<table border="1">
    <thead>
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="21" align="center">{{$titulo}}</td>
        </tr>
        <tr>
            <th>N°</th>
            <th>N° Embarque</th>
            <th>Fecha<br>Embarque</th>
            <th>Empresa Transporte</th>
            <th>Fecha<br>Llegada</th>
            <th>Hr.<br>Llegada</th>
            <th>Cluster</th>
            <th>Comprobantes</th>
            <th>N° Comprobantes</th>
            <th>Total <br>Bultos</th>
            <th>N° Manifiesto</th>
            <th>Fecha<br>Manifiesto</th>
            <th>Prov. <br>Origen</th>
            <th>Prov. <br>Destino</th>
            <th>Estado</th>
            <th>Total Guías/OS</th>
            <th>N° Guía/OS</th>
            <th>Fecha Reparto</th>
            <th>Situacion</th>
            <th>Incidencia</th>
            <th>Descripción incidencia</th>
        </tr>
    </thead>
    <tbody>
        @empty($datos)
            <tr>
                <td colspan="21">No se encontraron embarques programados</td>
            </tr>
        @endempty
            @foreach ($datos as $k => $resultado)
            <tr>
                <td>{{$k + 1}}</td>
                <td>{{$resultado->nroEmbarque}}</td>
                <td>{{$resultado->fechaEmbarque}}</td>
                <td>{{$resultado->empresaTransporte}}</td>
                <td>{{$resultado->fechaLlegada}}</td>
                <th>{{$resultado->hrLlegada}}</th>
                <td>{{$resultado->nombreCluster}}</td>
                <td>{{$resultado->comporbantes}}</td>
                <td>{{$resultado->nroComprobantes}}</td>
                <td>{{$resultado->totalBultos}}</td>
                <td>{{$resultado->nroManifiesto}}</td>
                <td>{{$resultado->fechaManifiesto}}</td>
                <td>{{$resultado->prov_orig}}</td>
                <td>{{$resultado->prov_dest}}</td>
                <td>{{$resultado->estado}}</td>
                <td>{{$resultado->nroGuias}}</td>
                <td>{{$resultado->nroGuia}}</td>
                <td>{{$resultado->fechaReparto}}</td>
                <td>{{$resultado->situacionEnio}}</td>
                <td>{{$resultado->incidenciasEnvio}}</td>
                <td>{{$resultado->textoIncidencias}}</td>
            </tr>
        @endforeach
    </tbody>
</table>