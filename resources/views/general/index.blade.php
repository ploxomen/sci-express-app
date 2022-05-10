<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="stylesheet" href="{{asset('iconos/fontawesome/css/all.css')}}">
    <link rel="stylesheet" href="{{asset('asset/general.css')}}">
    <link rel="stylesheet" href="{{asset('library/bootstrap/bootstrap.min.css')}}">
    <script src="{{asset('library/jquery/external/jquery/jquery.js')}}"></script>
    <script src="{{asset('library/bootstrap/popper.min.js')}}"></script>
    <script src="{{asset('library/bootstrap/bootstrap.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/select2/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/select2/select2-bootstrap.min.css')}}">
    <script src="{{asset('library/select2/select2.min.js')}}"></script>
    <script src="{{asset('library/select2/es.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/alertify/alertify.min.css')}}">
    <script src="{{asset('library/alertify/alertify.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/pagination/pagination.css')}}">
    <script src="{{asset('library/pagination/pagination.js')}}"></script>
    <script src="{{asset('asset/general.js')}}"></script>
    <script src="{{asset('asset/home.js')}}"></script>
    @yield('head')
    <title>@yield('title')</title>
</head>
<body>
    @php
        $url_actual = Request::url();
    @endphp
    <div class="contenedor">
        <div class="navegacion" id="navegacion">
            <div class="mi-logo">
            </div>
            <ul class="menu">
                <li class="hover-menu {{$url_actual == route('home') ? 'activeli' : ''}}">
                    <a href="{{route('home')}}" class="menu-prin" title="Inicio">
                        <i class="fas fa-home icono"></i>
                        <span class="title">Inicio</span>
                    </a>
                </li>
                @foreach ($modulos as $modulo)
                    @if ($modulo->tipo == 'agrupado')
                        @php
                            $modulo->submodulos = json_decode($modulo->submodulos);
                            $active_sub = false;
                            foreach ($modulo->submodulos as $m) {
                                if(route($m->nomRuta) == $url_actual){
                                    $active_sub = true;
                                    break;
                                }
                            }
                        @endphp
                        <li class="menu-desplegable">
                            <a href="javascript:void(0)" title="{{$modulo->grupo}}" class="active-panel {{$active_sub ? 'active-menu' : ''}}">
                                <i class="icono {{$modulo->icono}}"></i>
                                <span class="title">{{$modulo->grupo}}</span>
                                <i class="fas fa-angle-down"></i>                            
                            </a>
                            <ul class="sub-menu">
                                @foreach ($modulo->submodulos as $submodulo)
                                <li class="hover-menu {{$url_actual == route($submodulo->nomRuta) ? 'activeli' : ''}}">
                                    <a href="{{route($submodulo->nomRuta)}}">
                                        <i class="icono {{$submodulo->icono}}"></i>
                                        <span class="title">{{$submodulo->nombre}}</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        
                    @else
                    <li class="hover-menu {{$url_actual == route($modulo->nomRuta) ? 'activeli' : ''}}">
                        <a href="{{route($modulo->nomRuta)}}" title="{{$modulo->grupo}}">
                            <i class="icono {{$modulo->icono}}"></i>
                            <span class="title">{{$modulo->grupo}}</span>
                        </a>
                    </li>
                    @endif
                @endforeach
                <li class="hover-menu">
                    <a href="{{route('closeSesion')}}" class="menu-prin" title="Cerrar sesión">
                        <i class="icono fas fa-door-closed"></i>                        
                        <span class="title">Cerrar sesión</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="panel">
            <div class="box-serch" hidden>
                <input type="search">
            </div>
            <div class="info-person">
                <div class="btn-menu">
                    <button type="button" id="btn-menu" class="btn-info-menu">
                        <i class="fas fa-bars"></i>                    
                    </button>
                </div>
                <div class="box-my">
                    <span class="text-primary">{{Auth::user()->name}}</span>
                    <div class="btn-group">
                        <button class="btn-info-menu dropdown-toggle"  data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <span class="dropdown-item-text"><span class="text-primary">Área:</span><br><span id="text_area_index"></span></span>
                            <div class="dropdown-divider"></div>
                            <h6 class="dropdown-header" id="lista_roles_index">Roles</h6>
                            <h6 class="dropdown-header">Otros</h6>
                            <a class="dropdown-item" href="{{route('miPerfil')}}">Mi perfil</a>
                            <a href="{{route('closeSesion')}}" class="dropdown-item">Cerrar sesión</a>
                        </div>
                    </div>
                    
                </div>
               
            </div>
        </div>
        <div class="cuerpo-panel">
            @yield('body')
        </div>
    </div>
    <div class="content-mi-info" id="contenido-submenu"></div>
</body>
</html>