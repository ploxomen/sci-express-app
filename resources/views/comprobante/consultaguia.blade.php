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
    <title>Consultas de guías</title>
</head>
<body>
    <section class="container py-5">
        <div class="guia border-guia">
            <div class="logo form-group">
                <img src="{{asset('asset/img/logo_sci_express.png')}}" alt="Logo de SCIEXPRESSPERU">
            </div>
            <div class="row px-3">
                <div class="form-group col-12 col-md-8 d-flex align-items-center" style="gap: 10px;">
                    <div>
                        <input class="form-check-input" type="radio" name="tipoguia" checked id="selectsciguia" value="guia">
                        <label for="selectsciguia"><b>GUIA SCI</b></label>
                    </div>
                    <div>
                        <input type="text" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="form-group col-12 col-md-4">
                    <input class="form-check-input" type="radio" name="tipoguia" id="selectdocremitente" value="remitente">
                    <label for="selectdocremitente"><b>DOC. REMITENTE</b></label>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12 col-lg-6 d-flex align-items-center" style="gap: 5px;">
                    <div style="max-width: 130px;">
                        <label for="rucdniremitenteone"><b>INGRESE RUC O DNI REMITENTE</b></label>
                    </div>
                    <div class="w-100">
                        <input type="text" id="rucdniremitenteone" maxlength="11" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="form-group col-12 pl-lg-4 pl-5 col-lg-6 d-flex align-items-center" style="gap: 10px;">
                    <div>
                        <div>
                            <input class="form-check-input" type="radio" name="selectguia" id="tipoguiaguia" value="guia">
                            <label for="tipoguiaguia" class="mb-0"><b>GUIA</b></label>       
                        </div>
                        <div>
                            <input class="form-check-input" type="radio" name="selectguia" id="tipoguiafactura" value="factura">
                            <label for="tipoguiafactura" class="mb-0"><b>FACTURA</b></label>
                        </div>
                        <div>
                            <input class="form-check-input" type="radio" name="selectguia" id="tipoguiboleta" value="boleta">
                            <label for="tipoguiboleta" class="mb-0"><b>BOLETA</b></label>
                        </div>
                    </div>
                    <div>
                        <input type="text" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
            <div class="border-guia p-2 mb-4">
                <h6 class="text-center text-primary"><b>REMITENTE</b></h6>
                <div class="form-group">
                    <b>Nombre:</b>
                </div>
                <div class="form-group">
                    <b>Dirección:</b>
                    <span>Dpto - Prov - Dest</span>
                </div>
                <h6 class="text-primary"><b>DOCUMENTOS ENVIADOS</b></h6>
            </div>
            <div class="border-guia p-2 mb-4">
                <h6 class="text-center text-primary"><b>DESTINATARIO</b></h6>
                <div class="form-group">
                    <b>Nombre:</b>
                </div>
                <div class="form-group">
                    <b>Dirección:</b>
                    <span>Dpto - Prov - Dest Final</span>
                </div>
            </div>
            <div class="border-guia p-2">
                <h6 class="text-center text-primary"><b>ESTADO - RASTREO</b></h6>
                <div class="row">
                    <div class="form-group col-12 col-lg-6">
                        <b>Registrada el <span id="fecha-registrada"></span></b>
                    </div>
                    <div class="form-group col-12 col-lg-6">
                        <b>En Tránsito el <span id="fecha-transito"></span></b>
                    </div>
                    <div class="form-group col-12 col-lg-6">
                        <b>En reparto el <span id="fecha-reparto"></span></b>
                    </div>
                    <div class="form-group col-12 col-lg-6">
                        <b>Entregado el <span id="fecha-entregado"></span></b>
                    </div>
                    <div class="form-group col-12 text-center">
                        <b>Cargo entregado <span id="fecha-cargo-entregado"></span></b>
                    </div>
                    <div class="form-group col-12">
                        <div class="bg-estado">
                            <span>ESTADO ACTUAL:</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="guia border-guia">
            <div class="logo form-group">
                <img src="{{asset('asset/img/logo_sci_express.png')}}" alt="Logo de SCIEXPRESSPERU">
            </div>
            <div class="row px-3">
                <div class="form-group col-12 col-lg-6 pl-lg-0 d-flex align-items-center" style="gap: 5px;">
                    <label for="rucdniremi" class="mb-0"><b>INGRESE RUC O DNI DEL REMITENTE</b></label>
                    <input type="text" required class="form-control form-control-sm" id="rucdniremi" maxlength="11">
                </div>
                <div class="form-group col-12 col-lg-6 pr-lg-0 d-flex align-items-center" style="gap: 5px;">
                    <label for="fechadepacho" class="mb-0"><b>FECHA DE DESPACHO</b></label>
                    <input type="date" required class="form-control form-control-sm" id="fechadepacho">
                </div>
                <div class="form-group col-12">
                    <div class="row mx-auto" style="max-width: 450px;">
                        <label for="nameremitente" class="col-sm-3 col-form-label"><b>REMITENTE</b></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" id="nameremitente">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped table-traking">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>TIPO DE DOCUMENTO</th>
                                <th>N° DOCUMENTO DEL REMITENTE</th>
                                <th>N° GUIA SCI</th>
                                <th>ACCION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>DNI</td>
                                <td>73700496</td>
                                <td>001-0015123</td>
                                <td><button class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></button></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                
            </div>
            
        </div>
    </section>
</body>
</html>