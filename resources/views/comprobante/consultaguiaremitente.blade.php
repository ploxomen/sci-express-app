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
    <link rel="stylesheet" href="{{asset('library/alertify/alertify.min.css')}}">
    <script src="{{asset('library/alertify/alertify.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('asset/consultaguia.css')}}">
    <link rel="icon" href="{{asset('asset/img/icono.png')}}">
    <meta name="theme-color" content="#2e405e">
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/consultaguiaremitente.js')}}"></script>
    <title>Consultas de guías remitente</title>
</head>
<body>
    <section class="container py-5">
        <div class="guia border-guia">
            <div class="logo form-group">
                <img src="{{asset('asset/img/logo_sci_express.png')}}" alt="Logo de SCIEXPRESSPERU">
            </div>
            <form class="row px-3" id="from-consulta-remitentes">
                <div class="mb-1 col-12 col-lg-6 pl-lg-0 d-flex align-items-center" style="gap: 5px;">
                    <label for="rucdniremi" class="mb-0"><b>INGRESE RUC O DNI DEL REMITENTE</b></label>
                    <input type="text" required class="form-control form-control-sm" id="rucdniremi" maxlength="11">
                </div>
                <div class="mb-1 col-12 col-lg-6 pr-lg-0 d-flex align-items-center" style="gap: 5px;">
                    <label for="fechadepacho" class="mb-0"><b>FECHA DE DESPACHO</b></label>
                    <input type="date" class="form-control form-control-sm" id="fechadepacho">
                </div>
                <div class="mb-1 col-12">
                    <div class="row mx-auto" style="max-width: 450px;">
                        <label for="nameremitente" class="col-sm-3 col-form-label"><b>REMITENTE</b></label>
                        <div class="col-sm-9">
                            <input type="text" disabled class="form-control form-control-sm" id="nameremitente">
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 text-center">
                    <button type="submit" title="Consultar guía" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
                </div>
            </form>
                    <table class="table table-sm table-bordered table-striped table-traking" id="tabla-guias">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>TIPO DE DOCUMENTO</th>
                                <th>N° DOCUMENTO DEL REMITENTE</th>
                                <th>N° GUIA SCI</th>
                                <th>ACCION</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-body-guias">
                        </tbody>
                    </table>
        </div>
    </section>
    @include('modal.detalleconsultasguias')
</body>
</html>