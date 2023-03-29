<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>    
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('titulo')</title>
</head>
<body>
    <style>
        html{
        font-family: Arial, Helvetica, sans-serif;
        }
        @page {
            margin: 80px 30px;
        }
        header { 
            position: fixed;
            left: 0px;
            top: -60px;
            right: 0px;
            height: 50px;
        }
        .tabla{
            border-collapse: collapse;
            width: 100%;
            font-size: 11px;
        }
        td,th{
            padding: 2px;
        }
        .text-center{
            text-align: center;
        }
        .text-right{
            text-align: right;
        }
        .titlo-principal{
            font-size: 20px;
            text-align: center;
            display: block;
            margin-bottom: 20px;
        }
    </style>
    <header>
        @php
            $nombreImagen = public_path("asset/img/logo_sci_express.png");
            $imagenBase64 = "data:image/png;base64," . base64_encode(file_get_contents($nombreImagen));
        @endphp
        <table class="tabla">
            <tr>
                <td>
                    <img src="{{$imagenBase64}}" alt="" width="150px">
                </td>
                <td class="text-right">
                    <img src="{{$imagenBase64}}" alt="" width="150px">
                </td>
            </tr>
        </table>
    </header>
    {{-- <header class="header header2">
        @php
            $nombreImagen = public_path("asset/img/logo_sci_express.png");
            $imagenBase64 = "data:image/png;base64," . base64_encode(file_get_contents($nombreImagen));
        @endphp
        <img src="{{$imagenBase64}}" alt="" width="150px">
    </header> --}}
    @yield('body')
</body>
</html>