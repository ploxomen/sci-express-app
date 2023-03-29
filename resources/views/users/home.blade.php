@extends('general.index')
@section('head')
    <title>Inicio</title>
    @if ($rol_user->id_rol == 1)
        <script src="{{asset('asset/home/manager.js')}}"></script>
    @endif
@endsection
@section('body')
    <style>
        .carta{
            position: relative;
            background: var(--color-secundario);
            padding: 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
        }
        .carta .line{
            width: 6px;
            height: 100%;
            position: absolute;
            left: 0;
        }
        .carta .principal .tipo{
            font-size: 15px;
            display: block;
            margin-bottom: 5px;
        }
        .carta .icono .material-icons{
            color: #3F80EA;
            background: #E0EAFC;
            padding: 10px;
            border-radius: 50%;
        }
        .carta .principal .valor{
            color: var(--color-letra-title);
        }
        .graficos{
            width: 100%;
            min-height: 350px !important;
        }
    </style>
    <h5>Panel de inicio</h5>
    <div class="p-3">
        @switch($rol_user->id_rol)
            @case(1)
                @include('home.manager')
            @break
            @case(2)
                @include('home.supervisor')
            @break
            @case(3)
                @include('home.digitador')
            @break
            @case(4)
                @include('home.boss')
            @break
            @case(5)
                @include('home.sectorista')
            @break
            @case(6)
                @include('home.conductordealer')
            @break
            @case(7)
                @include('home.conductordealer')
            @break
            @case(8)
                @include('home.cluster')
            @break
            @default
                @include('home.default')
        @endswitch
    </div>
@endsection