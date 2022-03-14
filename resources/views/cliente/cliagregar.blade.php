@extends('general.index')
@section('head')
    <script src="{{asset('asset/clientes.js')}}"></script>
    @if (!$agregar)
        <script src="{{asset('asset/editarclientes.js')}}"></script>
    @endif
    <title>Agregar cliente</title>
@endsection
@section('body')
<section class="p-2">
    <form id="frm_cliente" class="row">
        @if (!$agregar)
            <input type="hidden" value="{{$info_cli['nro_doc']}}" id="id_nro_doc_cli">
        @endif
        <div class="mb-4 col-12">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/addcliente.png')}}" alt="Imagen de clientes">
            </div>
            <h3 class="text-primary text-center">{{$agregar ? 'Agregar nuevo cliente' : 'Modificar cliente'}}</h3>
        </div>
        <div class="mb-4 col-12">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">DATOS DEL CLIENTE</legend>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_doc_ident">Tipo documento</label>
                    <select name="doc_ident" id="id_doc_ident" class="form-control">
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_nro_doc">N° documento</label>
                    <input type="text" name="nro_doc" id="id_nro_doc" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-6">
                    <label for="id_nom_cli">Nombre</label>
                    <input type="text" name="nom_cli" id="id_nom_cli" class="form-control">
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12 col-lg-6">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">SEDE ADMINISTRATIVA</legend>
                <div class="form-group col-12">
                    <label for="id_dir_sede_adr">Dirección</label>
                    <input type="text" name="dir_sede_adr" id="id_dir_sede_adr" class="form-control">
                </div>
                <div class="form-group col-12 col-md-4 col-lg-3">
                    <label for="id_dpto_sa">Departamento</label>
                    <select name="dpto_sa" id="id_dpto_sa" class="form-control departamentos cb-ubigeo" data-consulta="provincia" data-info="id_prov_sa" data-info-distrito="id_dist_sa">
                        <option value=""></option>
                        @foreach ($dep as $d)
                            <option value="{{$d->id_ubigeo}}">{{$d->nombre_ubigeo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_prov_sa">Provincia</label>
                    <select name="prov_sa" id="id_prov_sa" class="form-control cb-ubigeo" data-consulta="distrito" data-info="id_dist_sa"></select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_dist_sa">Distrito</label>
                    <select name="dist_sa" id="id_dist_sa" class="form-control cb-ubigeo"></select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_ctii_sa">CTLF</label>
                    <input type="text" name="ctii_sa" id="id_ctii_sa" class="form-control">
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12 col-lg-6">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">SEDE OPERATIVA</legend>
                <div class="form-group col-12">
                    <label for="id_dir_sede_so">Dirección</label>
                    <input type="text" name="dir_sede_so" id="id_dir_sede_so" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_dpto_so">Departamento</label>
                    <select name="dpto_so" id="id_dpto_so" class="form-control cb-ubigeo" data-consulta="provincia" data-info="id_prov_so" data-info-distrito="id_dist_so">
                        <option value=""></option>
                        @foreach ($dep as $d)
                            <option value="{{$d->id_ubigeo}}">{{$d->nombre_ubigeo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_prov_so">Provincia</label>
                    <select name="prov_so" id="id_prov_so" class="form-control cb-ubigeo" data-consulta="distrito" data-info="id_dist_so"></select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_dist_so">Distrito</label>
                    <select name="dist_so" id="id_dist_so" class="form-control cb-ubigeo"></select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_ctii_so">CTLF</label>
                    <input type="text" name="ctii_so" id="id_ctii_so" class="form-control">
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12 col-lg-6">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">REPRESENTANTE LEGAL</legend>
                <div class="form-group col-12">
                    <label for="id_nom_rl">Nombres</label>
                    <input type="text" name="nom_rl" id="id_nom_rl" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="id_doc_ident_rl">Tipo documento</label>
                    <select name="doc_ident_rl" id="id_doc_ident_rl" class="form-control">
                        <option value=""></option>
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="id_nro_doc_rl">N° documento</label>
                    <input type="text" name="nro_doc_rl" id="id_nro_doc_rl" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_tlf_anex_rl">Teléfono</label>
                    <input type="tel" name="tlf_anex_rl" id="id_tlf_anex_rl" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_cel_rl">Celular</label>
                    <input type="tel" name="cel_rl" id="id_cel_rl" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_email_rl">Correo</label>
                    <input type="email" name="email_rl" id="id_email_rl" class="form-control">
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12 col-lg-6">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">CONTACTO ADMINISTRATIVO</legend>
                <div class="form-group col-12">
                    <label for="id_nom_ad">Nombres</label>
                    <input type="text" name="nom_ad" id="id_nom_ad" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="id_doc_ident_ad">Tipo documento</label>
                    <select name="doc_ident_ad" id="id_doc_ident_ad" class="form-control">
                        <option value=""></option>
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="id_nro_doc_ad">N° documento</label>
                    <input type="text" name="nro_doc_ad" id="id_nro_doc_ad" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_tlf_anex_ad">Teléfono</label>
                    <input type="tel" name="tlf_anex_ad" id="id_tlf_anex_ad" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_cel_ad">Celular</label>
                    <input type="tel" name="cel_ad" id="id_cel_ad" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_email_ad">Correo</label>
                    <input type="email" name="email_ad" id="id_email_ad" class="form-control">
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12 col-lg-6">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">CONTACTO OPERATIVO</legend>
                <div class="form-group col-12">
                    <label for="id_nom_op">Nombres</label>
                    <input type="text" name="nom_op" id="id_nom_op" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="id_doc_ident_op">Tipo documento</label>
                    <select name="doc_ident_op" id="id_doc_ident_op" class="form-control">
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="id_nro_doc_op">N° documento</label>
                    <input type="text" name="nro_doc_op" id="id_nro_doc_op" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_tlf_anex_op">Teléfono</label>
                    <input type="tel" name="tlf_anex_op" id="id_tlf_anex_op" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_cel_op">Celular</label>
                    <input type="tel" name="cel_op" id="id_cel_op" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_email_op">Correo</label>
                    <input type="email" name="email_op" id="id_email_op" class="form-control">
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12 col-lg-6">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">CONTACTO PAGOS</legend>
                <div class="form-group col-12">
                    <label for="id_nom_pg">Nombres</label>
                    <input type="text" name="nom_pg" id="id_nom_pg" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="id_doc_ident_pg">Tipo documento</label>
                    <select name="doc_ident_pg" id="id_doc_ident_pg" class="form-control">
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="id_nro_doc_pg">N° documento</label>
                    <input type="text" name="nro_doc_pg" id="id_nro_doc_pg" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_tlf_anex_pg">Teléfono</label>
                    <input type="tel" name="tlf_anex_pg" id="id_tlf_anex_pg" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_cel_pg">Celular</label>
                    <input type="tel" name="cel_pg" id="id_cel_pg" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_email_pg">Correo</label>
                    <input type="email" name="email_pg" id="id_email_pg" class="form-control">
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12">
            <fieldset class="bg-white px-3 border">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">PROVEEDORES</legend>
                <div class="form-group text-right">
                    <button type="button" id="btn-add-prov" class="btn btn-primary"><i class="fas fa-plus"></i></button>
                </div>
                <ol class="ml-4" id="list-proveedores" data-time="{{$id_new_prov}}">
                    <li data-tipo="nuevo" hidden>
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="id_tipo_docu_prov_{{$id_new_prov}}" data-selected="true">Tipo documento</label>
                                <select disabled data-name="tipo_docu_prov" required id="id_tipo_docu_prov_{{$id_new_prov}}" class="form-control form-control-sm" data-selected="true">
                                    <option value="" hidden>Seleccione una opción</option>
                                    @foreach ($tipo_documentos as $tipo_docu)
                                        <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="id_nro_docu_prov_{{$id_new_prov}}" data-selected="true">N° documento</label>
                                <input disabled type="text" data-name="nro_docu_prov" required id="id_nro_docu_prov_{{$id_new_prov}}" class="form-control form-control-sm" data-selected="true">
                            </div>
                            <div class="form-group col-12 col-lg-4">
                                <label for="id_nomb_prov_{{$id_new_prov}}" data-selected="true">Nombres</label>
                                <input disabled type="text" data-name="nomb_prov" required id="id_nomb_prov_{{$id_new_prov}}" class="form-control form-control-sm" data-selected="true">
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="id_depa_prov_{{$id_new_prov}}" data-selected="true">Departamento</label>
                                <select disabled data-name="depa_prov" id="id_depa_prov_{{$id_new_prov}}" required data-selected="true" class="form-control form-control-sm cb-ubigeo" data-consulta="provincia" data-info="id_prov_prov_{{$id_new_prov}}" data-info-distrito="id_dist_prov_{{$id_new_prov}}">
                                    <option value=""></option>
                                    @foreach ($dep as $d)
                                        <option value="{{$d->id_ubigeo}}">{{$d->nombre_ubigeo}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="id_prov_prov_{{$id_new_prov}}" data-selected="true">Provincia</label>
                                <select disabled class="form-control form-control-sm cb-ubigeo" required data-selected="true" data-name="prov_prov" id="id_prov_prov_{{$id_new_prov}}" data-consulta="distrito" data-info="id_dist_prov_{{$id_new_prov}}"></select>
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="id_dist_prov_{{$id_new_prov}}" data-selected="true">Distrito</label>
                                <select disabled class="form-control form-control-sm cb-ubigeo" required data-selected="true" data-name="dist_prov" id="id_dist_prov_{{$id_new_prov}}"></select>
                            </div>
                            <div class="form-group col-12">
                                <label for="id_dire_prov_{{$id_new_prov}}" data-selected="true">Dirección</label>
                                <input disabled type="text" data-name="dire_prov" required id="id_dire_prov_{{$id_new_prov}}" data-selected="true" class="form-control form-control-sm">
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-6">
                                <label for="id_tele_prov_{{$id_new_prov}}" data-selected="true">Contactos</label>
                                <select disabled data-name="tele_prov" id="id_tele_prov_{{$id_new_prov}}" data-selected="true" class="form-control from-control-sm cb-mult-nuevo" multiple></select>
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-6">
                                <label for="id_email_prov_{{$id_new_prov}}" data-selected="true">Correos</label>
                                <select disabled data-name="email_prov" id="id_email_prov_{{$id_new_prov}}" data-selected="true" class="form-control from-control-sm cb-mult-nuevo" multiple></select>
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-2 col-xl-12 d-flex justify-content-end align-items-center">
                                <button type="button" class="btn btn-danger btn-delete-proveedor"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                    </li>
                </ol>
            </fieldset>
        </div>
        <div class="mb-4 col-12">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">OTROS</legend>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_facturador">Facturador</label>
                    <select name="facturador" id="id_facturador" class="form-control">
                        <option value="CON CARGO">CON CARGO</option>
                        <option value="OTROS">OTROS</option>
                        <option value="SIN CARGO">SIN CARGO</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_transaciones_al">Transacciones al</label>
                    <select name="transaciones_al" id="id_transaciones_al" class="form-control">
                        <option value="CONTADO">CONTADO</option>
                        <option value="CRED 15 DIAS">CRED 15 DIAS</option>
                        <option value="CRED 30 DIAS">CRED 30 DIAS</option>
                        <option value="CRED 45 DIAS">CRED 45 DIAS</option>
                        <option value="CREDITO">CREDITO</option>
                    </select>
                </div>
                @if (!$agregar)
                    <div class="form-group col-12">
                        <div class="d-flex flex-wrap" style="gap: 10px;">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input change-doc" name="aprob_file" id="id_aprob_file" data-type="checkbox" data-doc="estado_cliente_file">
                                <label class="custom-control-label" for="id_aprob_file">Aprobación documento</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input change-doc" name="aprob_cred" id="id_aprob_cred" data-type="checkbox" data-doc="estado_cliente_file">
                                <label class="custom-control-label" for="id_aprob_cred">Aprobación crédito</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input change-doc" name="alta_clien" id="id_alta_clien" data-type="checkbox" data-doc="estado_cliente_file">
                                <label class="custom-control-label" for="id_alta_clien">Alta cliente</label>
                            </div>
                        </div>
                    </div>  
                @endif
            </fieldset>
        </div>
        <div class="form-group col-12">
            @if (!$agregar)
                <button type="submit" class="btn btn-primary">Modificar</button>
                <a href="{{route('viewMisClientes')}}" class="btn btn-secondary">Cancelar</a>
            @else
                <button type="submit" class="btn btn-primary">Agregar</button>
                <button type="reset" class="btn btn-secondary">Limpiar</button>
            @endif
            
        </div>
    </form>
</section>
@endsection