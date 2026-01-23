<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('iconos/fontawesome/css/all.css')}}">
    <link rel="stylesheet" href="{{asset('asset/general.css')}}">
    <link rel="stylesheet" href="{{asset('asset/auth/lestilos.css')}}">
    <link rel="stylesheet" href="{{asset('library/alertify/alertify.min.css')}}">
    <script src="{{asset('library/alertify/alertify.min.js')}}"></script>
    <meta name="csrf-token" content="{{csrf_token()}}">
    <script src="{{asset('asset/general.js?v2.5')}}"></script>
    <script src="{{asset('asset/auth/users.js')}}"></script>
    <title>Iniciar sesión</title>
</head>
<body>
    <section class="contenido-login">
        <article class="baner-log">
            <div class="system-info">
                <h3>Sistema integrado</h3>
                <p>
                    <strong>"ALMAR GROUP PERU”</strong> <br> Es una organización constituida con capitales peruanos, quien concibe y desarrolla Unidades de Producción como SCI Express, destinada a la prestación de servicios vinculados al traslado logístico de bienes dentro de nuestro país.
                </p>
            </div>
            <img src="{{asset('asset/img/log.svg')}}" alt="">
        </article>
        <article class="baner-init">
            <form class="children" id="login">
                <h1 class="title">Iniciar sesión</h1>
                <div class="box-input">
                    <div class="input-file">
                        <input type="text" name="email" class="text-minuscula" required placeholder="Usuario">
                        <i class="fas fa-user"></i>                    
                    </div>
                    <div class="input-file">
                        <input type="password" name="password" required placeholder="Contraseña">
                        <i class="fas fa-lock"></i>                    
                    </div>
                    <div class="input-file">
                        <label for="id-recordar" class="checkbox">
                            <input type="checkbox" name="remember" id="id-recordar" class="input-check">
                            <div class="icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div><b>Recordarme</b></div> 
                        </label>
                    </div>
                    {{-- <div class="input-file" style="text-align: right;">
                        <a href="#" class="restore-pass">¿Olvidaste tu contraseña?</a>
                    </div> --}}
                    <div>
                        <button type="submit" class="button-login"><i class="fas fa-sign-in-alt"></i>Ingresar</button>
                    </div>
                </div>
            </form>
        </article>
    </section>
    <footer class="footer-login">
        <span style="font-size: 0.6rem !important;">Desarrollado por <a href="https://gscreativas.com/" target="_blank">gscreativas.com</a></span>
    </footer>
</body>
</html>