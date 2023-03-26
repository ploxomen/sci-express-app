<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>    
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de recojos</title>
</head>
<body>
    <h2 class="text-center">LISTA DE ENTREGAS DESDE {{$fechaIni}} HASTA {{$fechaFi}}</h2>
    @include('pedidos.reportes.estilos')
    <table border="1" class="tabla">
        <thead>
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
                <th>Estado<br>Entrega</th>
                <th>N°<br>Visitas</th>
            </tr>
        </thead>
        <tbody>
            @empty($resultados)
                <tr>
                    <td colspan="13" class="text-center">No se encontraron entregas programadas</td>
                </tr>
            @endempty
             @foreach ($resultados as $k => $resultado)
        
                <tr>
                    <td class="text-center">{{$k + 1}}</td>
                    <td class="text-center">{{$resultado->nroGuia}}</td>
                    <td>{{$resultado->orde_comp}}</td>
                    <td>{{$resultado->fechaVencimiento}}</td>
                    <td>{{$resultado->remitente}}</td>
                    <td>{{$resultado->dest_nomb}}</td>
                    <td>{{$resultado->estadoGuia}}</td>
                    <td>{{$resultado->totalBultos}}</td>
                    <td>{{$resultado->fechaReparto}}</td>
                    <td>{{$resultado->fechaEntregado}}</td>
                    <td>{{$resultado->situacionDuranteEnvio}}</td>
                    <td>{{$resultado->estadoEntregado}}</td>
                    <td>{{$resultado->nroVisitas}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>