<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap"
        rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('iconos/fontawesome/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap/bootstrap.min.css') }}">
    <script src="{{ asset('library/jquery/external/jquery/jquery.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('asset/general.css?v.1.3') }}">
    <script src="{{ asset('library/bootstrap/popper.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap/bootstrap.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('library/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/select2-bootstrap.min.css') }}">
    <script src="{{ asset('library/select2/select2.min.js') }}"></script>
    <script src="{{ asset('library/select2/es.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('library/alertify/alertify.min.css') }}">
    <script src="{{ asset('library/alertify/alertify.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('library/pagination/pagination.css') }}">
    <script src="{{ asset('library/pagination/pagination.js') }}"></script>
    <script src="{{ asset('asset/general.js?v2.8') }}"></script>
    <script src="{{ asset('asset/home.js?v.1.4') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="icon" href="{{ asset('asset/img/icono.png') }}">
    <meta name="theme-color" content="#2e405e">
    @yield('head')
    <title>@yield('title')</title>
</head>

<body>
    <style>
        .collapse {
            visibility: visible !important;
        }
    </style>
    @php
        $url_actual = Request::url();
    @endphp
    <div class="contenedor">
        <div class="navegacion" id="navegacion">
            <div class="mi-logo">
                <img class="d-block img-fluid m-auto" src="{{ asset('asset/img/favivon.png') }}"
                    alt="Icono SCI Express" width="120px">
            </div>
            <ul class="menu accordion" id="accodion-nav">
                <li class="hover-menu {{ $url_actual == route('home') ? 'activeli' : '' }}">
                    <a href="{{ route('home') }}" class="menu-prin d-flex gap-1" title="Inicio">
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
                                if (route($m->nomRuta) == $url_actual) {
                                    $active_sub = true;
                                    break;
                                }
                            }
                        @endphp
                        <li class="menu-desplegable">
                            <a href="#collapse{{ $modulo->grupo }}"
                                class="active-panel d-flex gap-1 {{ $active_sub ? 'active-menu' : '' }}"
                                title="{{ $modulo->grupo }}" data-toggle="collapse"
                                data-target="#collapse{{ $modulo->grupo }}">
                                <i class="icono {{ $modulo->icono }}"></i>
                                <span class="title">{{ $modulo->grupo }}</span>
                                <i class="fas fa-angle-down"></i>
                            </a>
                            <ul id="collapse{{ $modulo->grupo }}" class="collapse {{ $active_sub ? 'show' : '' }}"
                                data-parent="#accodion-nav">
                                @foreach ($modulo->submodulos as $submodulo)
                                    <li
                                        class="hover-menu {{ $url_actual == route($submodulo->nomRuta) ? 'activeli' : '' }}">
                                        <a href="{{ route($submodulo->nomRuta) }}" class="d-flex gap-1"
                                            title="{{ $submodulo->nombre }}">
                                            <i class="icono {{ $submodulo->icono }}"></i>
                                            <span class="title">{{ $submodulo->nombre }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                        </li>
                    @else
                        <li class="hover-menu {{ $url_actual == route($modulo->nomRuta) ? 'activeli' : '' }}">
                            <a href="{{ route($modulo->nomRuta) }}" title="{{ $modulo->grupo }}">
                                <i class="icono {{ $modulo->icono }}"></i>
                                <span class="title">{{ $modulo->grupo }}</span>
                            </a>
                        </li>
                    @endif
                @endforeach
                <li class="hover-menu">
                    <a href="{{ route('closeSesion') }}" class="menu-prin d-flex gap-1" title="Cerrar sesión">
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
                    <span class="text-primary">{{ Auth::user()->name }}</span>
                    <div class="btn-group">
                        <button class="btn-info-menu dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i>
                        </button>
                        @php
                            if (!isset($_COOKIE['roles'])) {
                                \Auth::logout();
                                \Request::session()->invalidate();
                                \Request::session()->regenerateToken();
                                redirect(route('login'));
                                die();
                            }
                            $roles = json_decode($_COOKIE['roles']);
                        @endphp

                        <div class="dropdown-menu dropdown-menu-right">
                            <span class="dropdown-item-text"><span class="text-primary">Área:</span><br><span
                                    id="text_area_index">{{ empty($roles->area) ? 'No definido' : $roles->area->area }}</span></span>
                            <div class="dropdown-divider"></div>
                            <h6 class="dropdown-header" id="lista_roles_index">Roles</h6>
                            @foreach ($roles->roles as $rol)
                                <a class="dropdown-item {{ $rol->active ? 'active' : '' }}"
                                    href="{{ route('cambiarRol', $rol->id) }}">
                                    <span>{{ $rol->rol }}</span>
                                </a>
                            @endforeach
                            <h6 class="dropdown-header">Otros</h6>
                            <a class="dropdown-item" href="{{ route('miPerfil') }}"><span>Mi perfil</span></a>
                            <a href="{{ route('closeSesion') }}" class="dropdown-item"><span>Cerrar sesión</span></a>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        <div class="cuerpo-panel">
            @yield('body')

        </div>
        <footer class="footer-login">
            <span style="font-size: 0.6rem !important;">Desarrollado por <a href="https://gscreativas.com/"
                    target="_blank">gscreativas.com</a></span>
        </footer>
    </div>
    <div class="content-mi-info" id="contenido-submenu"></div>
</body>

</html>
