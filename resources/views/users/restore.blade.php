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
    <link rel="stylesheet" href="{{asset('library/alertify/alertify.min.css')}}">
    <script src="{{asset('library/alertify/alertify.min.js')}}"></script>
    <script src="{{asset('library/jquery/external/jquery/jquery.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/bootstrap/bootstrap.min.css')}}">
    <script src="{{asset('library/bootstrap/bootstrap.min.js')}}"></script>
    <meta name="csrf-token" content="{{csrf_token()}}">
    <script src="{{asset('asset/general.js')}}"></script>
    <script src="{{asset('asset/auth/restore.js')}}"></script>
    <title>Restaurar cuenta</title>
</head>
<body>
    <div class="container">
        <div class="py-5">
            <div class="form-group text-center">
                <img src="{{asset('asset/img/intranet/new_user.png')}}" width="150px" class="img-fluid" alt="Restaurar usuario">
            </div>
            <form id="frm-add-user" class="row">
                <div class="form-group col-12">
                <span class="text-info">Escriba su contraseña temporal asignada por el administrador y cree una nueva contraseña para tener acceso al sistema integrado.</span>
                </div>
                <div class="form-group col-12">
                    <label for="password_temp">Contraseña temporal asignada</label>
                    <input type="text" class="form-control text-minuscula" required id="id_password_temp" name="password_temp">
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="password_temp">Nueva contraseña</label>
                    <input type="password" class="form-control" required id="id_password_1" name="password" minlength="8">
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="password_temp">Repetir contraseña</label>
                    <input type="password" class="form-control" required id="id_password_2" minlength="8">
                </div>
                <div class="form-group col-12">
                    <button type="submit" class="btn btn-danger">Siguiente</button>
                    <button type="button" class="btn btn-primary" id="cancelar-restore">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>