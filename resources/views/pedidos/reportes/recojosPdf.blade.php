<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>    
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de recojos</title>
</head>
<body>
    <h2 class="text-center">LISTA DE RECOJOS DESDE {{$fechaIni}} HASTA {{$fechaFi}}</h2>
    @include('pedidos.reportes.estilos')
    <table border="1" class="tabla">
        <thead>
            <tr>
                <th>N°</th>
                <th>N° Solicitud</th>
                <th>Fecha<br>Solicitud</th>
                <th>Fecha Hr. <br>Recojo Est.</th>
                <th>Fecha Hr. <br>Recojido</th>
                <th>KPI</th>
                <th>Nombre Solicitante</th>
                <th>N° Doc. Cliente</th>
                <th>Nombre Cliente</th>
                <th>Trayecto</th>
                <th>Dirección recojo</th>
                <th>Bulto<br>Total</th>
                <th>Estado</th>
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
                    <td class="text-center">{{$resultado->nroSolicitud}}</td>
                    <td class="text-center">{{$resultado->fechaSolicitud}}</td>
                    <td>{{$resultado->fechaRecojo}} {{$resultado->hrRecojoEstimado}}</td>
                    <td>{{$resultado->fechaRecojida}} {{$resultado->horaRecojida}}</td>
                    <td>{{$resultado->kpi}}</td>
                    <td>{{$resultado->nombreSolicitante}}</td>
                    <td>{{$resultado->nro_doc}}</td>
                    <td>{{$resultado->nom_cli}}</td>
                    <td>{{$resultado->nombreTrayectp}}</td>
                    <td>{{$resultado->direccion_recojo}}</td>
                    <td class="text-center">{{$resultado->total_bulto}}</td>
                    <td>{{$resultado->estadoSolicitud}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>