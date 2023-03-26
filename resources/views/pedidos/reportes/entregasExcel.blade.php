<table border="1">
    <thead>
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="16" align="center">{{$titulo}}</td>
        </tr>
        <tr>
            <th>N°</th>
            <th>N° Guía/OS</th>
            <th>N°<br>Orden Compra</th>
            <th>Vto. Orden<br>Compra</th>
            <th>Remitente</th>
            <th>Destinatario</th>
            <th>Estado Guía/OS</th>
            <th>Total<br>Bultos</th>
            <th>Fecha Reparto</th>
            <th>Fecha Entrega</th>
            <th>Situación<br>Reparto</th>
            <th>Incidencia Reparto</th>
            <th>Detalle Incidencia Reparto</th>
            <th>Estado<br>Entrega</th>
            <th>N°<br>Visitas</th>
            <th>Motivo No Etregado</th>
        </tr>
    </thead>
    <tbody>
        @empty($datos)
            <tr>
                <td colspan="16">No se encontraron entregas programados</td>
            </tr>
        @endempty
            @foreach ($datos as $k => $resultado)
            <tr>
                <td>{{$k + 1}}</td>
                <td>{{$resultado->nroGuia}}</td>
                <td>{{$resultado->orde_comp}}</td>
                <td>{{$resultado->fechaVencimiento}}</td>
                <td>{{$resultado->remitente}}</td>
                <td>{{$resultado->dest_nomb}}</td>
                <td>{{$resultado->estadoGuia}}</td>
                <td>{{$resultado->totalBultos}}</td>
                <td>{{$resultado->fechaReparto}}</td>
                <td>{{$resultado->fechaEntregado}}</td>
                <td>{{$resultado->situacionDuranteEnvio}}</td>
                <td>{{$resultado->incidenciaDuranteEnvio}}</td>
                <td>{{$resultado->textoIncidencia}}</td>
                <td>{{$resultado->estadoEntregado}}</td>
                <td>{{$resultado->nroVisitas}}</td>
                <td>{{$resultado->motivoNoEntregado}}</td>
            </tr>
        @endforeach
    </tbody>
</table>