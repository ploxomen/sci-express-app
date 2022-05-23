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
    <script src="{{asset('asset/consultaguia.js')}}"></script>
    <title>Consultas de guías</title>
</head>
<body>
    <section class="container py-5">
        <form class="guia border-guia" id="consulta-guia">
            <div class="logo form-group">
                <img src="{{asset('asset/img/logo_sci_express.png')}}" alt="Logo de SCIEXPRESSPERU">
            </div>
            <div class="row px-3">
                <div class="form-group col-12 col-md-8 d-flex align-items-center" style="gap: 10px;">
                    <div>
                        <input class="form-check-input tipo-consulta" type="radio" name="tipoguia" checked id="selectsciguia" value="guia">
                        <label for="selectsciguia"><b>GUIA SCI</b></label>
                    </div>
                    <div>
                        <input required id="nro_guia_sci" type="text" class="form-control form-control-sm" name="nro_guia_sci">
                    </div>
                </div>
                <div class="form-group col-12 col-md-4">
                    <input class="form-check-input tipo-consulta" type="radio" name="tipoguia" id="selectdocremitente" value="remitente">
                    <label for="selectdocremitente"><b>DOC. REMITENTE</b></label>
                </div>
            </div>
            <div class="row" hidden id="seleccionar-documentos">
                <div class="form-group col-12 col-lg-6 d-flex align-items-center" style="gap: 5px;">
                    <div style="max-width: 130px;">
                        <label for="rucdniremitenteone"><b>INGRESE RUC O DNI REMITENTE</b></label>
                    </div>
                    <div class="w-100">
                        <input type="text" name="documento_remitente" id="rucdniremitenteone" maxlength="15" class="form-control form-control-sm remitente-required">
                    </div>
                </div>
                <div class="form-group col-12 pl-lg-4 pl-5 col-lg-6 d-flex align-items-center" style="gap: 10px;">
                    <div>
                        <div>
                            <input checked class="form-check-input remitente-required" type="radio" name="selectguia" id="tipoguiaguia" value="G">
                            <label for="tipoguiaguia" class="mb-0"><b>GUIA</b></label>       
                        </div>
                        <div>
                            <input class="form-check-input remitente-required" type="radio" name="selectguia" id="tipoguiafactura" value="F">
                            <label for="tipoguiafactura" class="mb-0"><b>FACTURA</b></label>
                        </div>
                        <div>
                            <input class="form-check-input remitente-required" type="radio" name="selectguia" id="tipoguiboleta" value="B">
                            <label for="tipoguiboleta" class="mb-0"><b>BOLETA</b></label>
                        </div>
                    </div>
                    <div>
                        <input type="text" name="nro_guia_remitente" class="form-control form-control-sm remitente-required">
                    </div>
                </div>
            </div>
            <div class="form-group text-center">
                <button type="submit" title="Consultar guía" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
            </div>
            <div class="border-guia p-2 mb-4 ocultar-box" hidden>
                <h6 class="text-center text-primary"><b>REMITENTE</b></h6>
                <div class="mb-1">
                    <b>Nombre: </b>
                    <span id="info_nombre_remitente"></span>
                </div>
                <div>
                    <b>Dirección: </b>
                    <span id="info_direccion_remitente"></span>
                </div>
                <div class="mb-1">
                    <span id="info_departamento_remitente"></span> - <span id="info_provincia_remitente"></span> - <span id="info_distrito_remitente"></span>
                </div>
                <h6 class="text-primary"><b>DOCUMENTOS ENVIADOS</b></h6>
                <div class="row">
                    <div class="mb-1 col-6 col-sm-4 text-center">
                        <b>N° Guía: </b>
                        <div id="info_nro_guias" class="mb-0 text-guias"></div>
                    </div>
                    <div class="mb-1 col-6 col-sm-4 text-center">
                        <b>N° Factura: </b>
                        <div id="info_nro_facturas" class="mb-0 text-guias"></div>
                    </div>
                    <div class="mb-1 col-12 col-sm-4 text-center">
                        <b>N° Boleta: </b>
                        <div id="info_nro_boletas" class="mb-0 text-guias"></div>
                    </div>
                </div>
            </div>
            <div class="border-guia p-2 mb-4 ocultar-box" hidden>
                <h6 class="text-center text-primary"><b>DESTINATARIO</b></h6>
                <div class="mb-1">
                    <b>Nombre: </b>
                    <span id="info_nombre_destinatario"></span>
                </div>
                <div>
                    <b>Dirección: </b>
                    <span id="info_direccion_destinatario"></span>
                </div>
                <div class="mb-1">
                    <span id="info_departamento_destinatario"></span> - <span id="info_provincia_destinatario"></span> - <span id="info_distrito_destinatario"></span>
                </div>
            </div>
            <div class="border-guia p-2 ocultar-box" hidden>
                <h6 class="text-center text-primary"><b>ESTADO - RASTREO</b></h6>
                <div class="row">
                    <div class="form-group col-12 col-sm-6">
                        <b>Registrada el <span id="info_fecha_rastreo_1" class="text-proceso"></span></b>
                    </div>
                    <div class="form-group col-12 col-sm-6">
                        <b>En Tránsito el <span id="info_fecha_rastreo_5" class="text-proceso"></span></b>
                    </div>
                    <div class="form-group col-12 col-sm-6">
                        <b>En reparto el <span id="info_fecha_rastreo_7" class="text-proceso"></span></b>
                    </div>
                    <div class="form-group col-12 col-sm-6">
                        <b>Entregado el <span id="info_fecha_rastreo_8" class="text-proceso"></span></b>
                    </div>
                    <div class="form-group col-12 text-center">
                        <b>Cargo entregado <span id="info_fecha_rastreo_13" class="text-proceso"></span></b>
                    </div>
                    <div class="form-group col-12">
                        <div class="bg-estado">
                            <span>ESTADO ACTUAL: </span>
                            <span id="info_estado_actual"></span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</body>
</html>