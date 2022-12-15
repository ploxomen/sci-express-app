<table border="1">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th></th>
            <th colspan="9" style="text-align: center;">FACTURACION DE GUÍAS</th>
        </tr>
        <tr>
            <th></th>
            <th>N°</th>
            <th>Fecha de <br>Servicio</th>
            <th>G_SCI</th>
            <th>Consig_Prov</th>
            <th>Tipo_Doc_Cliente</th>
            <th>No_Doc_Cliente</th>
            <th>Base_Final_Consig</th>
            <th>Dest_Final_Consig</th>
            <th>Acceso_Dest_Final</th>
            <th>Descripción</th>
            <th>Peso</th>
            <th>Peso<br>Volumen </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($guias as $kg => $guia)
            <tr>
                <td></td>
                <td>{{$kg + 1}}</td>
                <td>{{$guia->fechaEmision}}</td>
                <td>{{$guia->nroGuia}}</td>
                <td>{{$guia->destinatario}}</td>
                <td>{{$guia->comp_clie}}</td>
                <td>{{$guia->nro_comp_cli}}</td>
                <td>{{$guia->baseCluster}}</td>
                <td>{{$guia->ciudadFinal}}</td>
                <td>{{$guia->esAlejado}}</td>
                <td>
                    @php
                        $template = "No definido";
                        if(!empty($guia->descripcionGuia) && count($guia->descripcionGuia) > 0){
                            $template = "";
                            foreach ($guia->descripcionGuia as $kd => $d) {
                                if($d != 'null' && $d != null){
                                    $template .= $d ;
                                    if(($kd + 1) < count($guia->descripcionGuia)){
                                        $template .= "<br>";
                                    }
                                }
                            }
                            if($template == ""){
                                $template = "No definido";
                            }
                        }
                    @endphp
                    {!! $template !!}
                </td>
                <td>
                    {{$guia->pesoGuia}}
                </td>
                <td>
                    @if ($guia->pesoVolumen > $guia->pesoGuia)
                        {{'(en blanco)'}}
                    @else
                        {{$guia->pesoVolumen}}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>