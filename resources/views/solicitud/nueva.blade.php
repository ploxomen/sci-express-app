@extends('general.index')
@section('head')
    <script src="{{asset('asset/solicitudcompartida.js')}}"></script>
    <script src="{{asset('asset/solicitud.js')}}"></script>
    <title>Solicitud</title>
@endsection
@section('body')
<section class="p-2">
    <form id="agregar-solicitud" class="mb-4 row">
        <div class="my-4 col-12">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/request.png')}}" alt="Imagen de clientes">
            </div>
            <h3 class="text-primary text-center">Solicitud</h3>
        </div>
        <div class="col-12 mb-4">
            <div class="bg-white p-3 form-row">
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_nro_solicitud">N° solicitud</label>
                    <input type="text" readonly id="id_nro_solicitud" class="form-control text-right" value="{{str_pad($ultimo_id,5,'0',STR_PAD_LEFT)}}">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_fecha_soli">Fecha de solicitud</label>
                    <input type="date" required class="form-control" id="id_fecha_soli" value="{{$hoy}}" name="fecha_soli" max="{{$hoy}}">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_trayecto">Trayecto de envio</label>
                    <select name="trayecto" required id="id_trayecto" class="form-control">
                        @foreach ($trayectos as $trayecto)
                            <option value="{{$trayecto->id}}">{{$trayecto->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_responsable_solicitud">Responsable</label>
                    <select name="responsable" required id="id_responsable_solicitud" class="form-control">
                        <option value="CLIENTE">CLIENTE</option>
                        <option value="PROVEEDOR">PROVEEDOR</option>
                        <option value="INVITADO">INVITADO</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="mb-4 col-12 col-lg-6">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">DATOS DEL CLIENTE</legend>
                <div class="form-group col-12">
                    <label for="id_documento_cli">Nombre</label>
                    <select id="id_documento_cli" required class="form-control datos-cliente">
                        <option></option>
                        @foreach ($clientes as $k => $cliente)
                            <option data-nro="{{$cliente->nro_doc}}" value="{{$cliente->id}}">{{$cliente->nom_cli}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_doc_iden_cli">Tipo Documento</label>
                    <select name="doc_iden_cli" required id="id_doc_iden_cli" class="form-control text-clear simple-select2 cb-tipo-doc datos-cliente" data-cliente="doc_ident" data-input="id_nro_doc_cli">
                        <option value="" hidden>Seleccione una opción</option>
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_nro_doc_cli">N° Documento</label>
                    <input type="text" required class="form-control text-clear datos-cliente" id="id_nro_doc_cli" name="nro_doc_cli" data-cliente="nro_doc">
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12 col-lg-6">
            <fieldset class="px-3 bg-white border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">DATOS DEL PROVEEDOR</legend>
                <div class="form-group col-12">
                    <label for="id_nom_soli">Nombre</label>
                    <select id="id_nom_soli" name="id_soli" class="form-control limitar-proveedor datos-proveedor">
                        <option value=""></option>
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_doc_iden_soli">Tipo Documento</label>
                    <select name="doc_iden_soli" id="id_doc_iden_soli" class="limitar-proveedor form-control text-clear simple-select2 cb-tipo-doc datos-proveedor" data-input="id_nro_doc_soli">
                        <option value="" hidden>Seleccione una opción</option>
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_nro_doc_soli">N° Documento</label>
                    <input type="text" class="limitar-proveedor form-control text-clear datos-proveedor" id="id_nro_doc_soli" name="nro_doc_soli">
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12 col-lg-6">
            <fieldset class="px-3 bg-white border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">DATOS DEL INVITADO</legend>
                <div class="form-group col-12">
                    <label for="id_nom_soli">Nombre</label>
                    <select name="id_invitado" class="simple-select2 datos-invitado" id="id_solicitante"></select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_centro_costo">Centro Costo</label>
                    <select name="centro_costo_invitado" class="simple-select2 datos-invitado" id="id_centro_costo"></select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_cuenta_invitado">Cuenta</label>
                    <select name="cuenta_invitado" class="cb-select2-tags datos-invitado" id="id_cuenta_invitado"></select>
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12 col-lg-6">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">PROGRAMACION RECOJO</legend>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_dia_recojo">Día del recojo</label>
                    <input type="date" required class="form-control" value="{{$hoy}}" name="dia_recojo" id="id_dia_recojo">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_hr_recojo_estimado">Hr Recojo estimado</label>
                    <input type="time" required class="form-control" name="hr_recojo_estimado" id="id_hr_recojo_estimado">        
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_cant_bulto">Cant de bulto</label>
                    <input type="number" class="form-control" name="cant_bulto" id="id_cant_bulto">         
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">DATOS DEL RECOJO</legend>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_depart_cli_recojo">Departamento</label>
                    <select name="depart_cli_recojo" data-cliente="dpto_so" required id="id_depart_cli_recojo" class="form-control cb-ubigeo" data-proveedor="depa_prov" data-consulta="provincia" data-info="id_ciudad_cli_recojo" data-info-distrito="id_distrito_cli_recojo">
                        <option></option>
                        @foreach ($dep as $d)
                            <option value="{{$d->id_ubigeo}}">{{$d->nombre_ubigeo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_ciudad_cli_1" data-replace="id_ciudad_cli">Provincia</label>
                    <select name="ciudad_cli_recojo" data-replace="id_ciudad_cli" required id="id_ciudad_cli_recojo" class="form-control cb-ubigeo" data-cliente="prov_so" data-proveedor="prov_prov" data-consulta="distrito" data-info="id_distrito_cli_recojo">
                        <option></option>
                    </select>            
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_distrito_cli_1" data-replace="id_distrito_cli">Distrito</label>
                    <select name="distrito_cli_recojo" data-replace="id_distrito_cli" required id="id_distrito_cli_recojo" class="form-control cb-ubigeo" data-proveedor="dist_prov" data-cliente="dist_so">
                        <option></option>
                    </select>            
                </div>
                <div class="form-group col-12">
                    <label for="id_dir_sede_so">Dirección</label>
                    <input type="text" data-cliente="dir_sede_so" data-proveedor="dire_prov" name="direccion_recojo" maxlength="255" class="form-control form-control-sm" id="id_dir_sede_so">
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">DATOS DEL ENVIO</legend>
                <div class="col-12 form-group text-right">
                    <button type="button" class="btn btn-sm btn-primary mr-2" id="agregar-datos-recojo" title="Agregar dirección"><i class="fas fa-plus"></i></button>
                    <button type="button" class="btn btn-sm btn-danger" id="eliminar-ultimo-dato-recojo" title="Eliminar ultima dirección"><i class="fas fa-minus"></i></button>
                </div>
                <ol class="pl-4 w-100" id="lista-detalles-bultos">
                    <li class="mb-3">
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="id_depar_cli_1" data-replace="id_depar_cli">Departamento</label>
                                <select name="depar_cli[]" data-replace="id_depar_cli" required id="id_depar_cli_1" class="form-control cb-ubigeo" data-proveedor="depa_prov" data-consulta="provincia" data-info="id_ciudad_cli_1" data-info-distrito="id_distrito_cli_1">
                                    <option></option>
                                    @foreach ($dep as $d)
                                        <option value="{{$d->id_ubigeo}}">{{$d->nombre_ubigeo}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="id_ciudad_cli_1" data-replace="id_ciudad_cli">Provincia</label>
                                <select name="ciudad_cli[]" data-replace="id_ciudad_cli" required id="id_ciudad_cli_1" class="form-control cb-ubigeo" data-proveedor="prov_prov" data-consulta="distrito" data-info="id_distrito_cli_1">
                                    <option></option>
                                </select>            
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="id_distrito_cli_1" data-replace="id_distrito_cli">Distrito</label>
                                <select name="distrito_cli[]" data-replace="id_distrito_cli" required id="id_distrito_cli_1" class="form-control cb-ubigeo" data-proveedor="dist_prov">
                                    <option></option>
                                </select>            
                            </div>
                            <div class="col-12"></div>
                            <div class="form-grop col-12 col-md-4 col-lg-3">
                                <label for="id_sobres_1" data-replace="id_sobres">Sobres</label>
                                <input type="number" data-replace="id_sobres" name="sobres[]" id="id_sobres_1" class="form-control form-control-sm calcular-bultos">
                            </div>
                            <div class="form-grop col-12 col-md-4 col-lg-3">
                                <label for="id_cajas_1" data-replace="id_cajas">Cajas</label>
                                <input type="number" data-replace="id_cajas" name="cajas[]" id="id_cajas_1" class="form-control form-control-sm calcular-bultos">
                            </div>
                            <div class="form-grop col-12 col-md-4 col-lg-3">
                                <label for="id_paquetes_1" data-replace="id_paquetes">Paquetes</label>
                                <input type="number" data-replace="id_paquetes" name="paquetes[]" id="id_paquetes_1" class="form-control form-control-sm calcular-bultos">
                            </div>
                            <div class="form-grop col-12 col-md-4 col-lg-3">
                                <label for="id_otros_1" data-replace="id_otros">Otros</label>
                                <input type="number" data-replace="id_otros" name="otros[]" id="id_otros_1" class="form-control form-control-sm calcular-bultos">
                            </div>
                            <div class="form-grop col-12">
                                <label for="id_detalle_otros_1" data-replace="id_detalle_otros">Detalle Otros</label>
                                <textarea name="detalle_otros[]" data-replace="id_detalle_otros" id="id_detalle_otros_1" class="form-control form-control-sm"></textarea>
                            </div>
                        </div>
                    </li>
                </ol>
            </fieldset>
        </div>
        <div class="mb-4 col-12">
            <button type="submit" class="btn btn-primary">Agregar</button>
        </div>
    </form>
        
</section>
@endsection