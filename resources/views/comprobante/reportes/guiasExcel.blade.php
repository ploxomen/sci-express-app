<table border="1">
    <thead>
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="12" align="center">{{$titulo}}</td>
        </tr>
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
        @empty($datos)
            <tr>
                <td colspan="12">No se encontraron guías/OS</td>
            </tr>
        @endempty
            @foreach ($datos as $k => $resultado)
            @php
                $documentosRemitente = empty($resultado->comp_clie) ? "NO ESTABLECIDO" : str_replace(";","<br>",$resultado->comp_clie);
                $nroDocumentos = empty($resultado->nro_comp_cli) ? "NO ESTABLECIDO" : str_replace(";","<br>",$resultado->nro_comp_cli);
            @endphp
            <tr>
                <td>{{$k + 1}}</td>
                <td style="width: 200px;">{{$resultado->nro_guia}}</td>
                <td style="width: 200px;">{{$resultado->fecha_emision}}</td>
                <td style="width: 250px;">{!! $documentosRemitente !!}</td>
                <td style="width: 200px;">{!! $nroDocumentos !!}</td>
                <td style="width: 250px;">{{$resultado->nom_remi}}</td>
                <td style="width: 250px;">{{empty($resultado->nombreCluster) ? "NO ESTABLECIDO" : mb_strtoupper($resultado->nombreCluster,"UTF-8")}}</td>
                <td style="width: 200px;">{{empty($resultado->ciudad_base_cluster) ? "NO ESTABLECIDO" : mb_strtoupper($resultado->ciudad_base_cluster,"UTF-8")}}</td>
                <td style="width: 200px;">{{empty($resultado->ciudad_final) ? "NO ESTABLECIDO" : mb_strtoupper($resultado->ciudad_final,"UTF-8")}}</td>
                <td>{{$resultado->tot_kg}}</td>
                <td>{{$resultado->tot_vol}}</td>
                <td style="width: 200px;">{{$resultado->guia_tipo}}</td>
                <td style="width: 200px;">{{$resultado->nombreEstado}}</td>
            </tr>
        @endforeach
    </tbody>
</table>