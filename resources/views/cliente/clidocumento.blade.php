@extends('general.index')
@section('head')
    <script src="{{asset('asset/cliFiles.js')}}"></script>
    <title>Documentos de clientes</title>
@endsection
@section('body')
<section class="bg-white p-2">
    <form action="" class="mb-4 row">
        <div class="my-4 col-12">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/doccliente.png')}}" alt="Imagen de clientes">
            </div>
            <h3 class="text-primary text-center">Documentos de clientes</h3>
        </div>
        <div class="form-group col-12 mb-5">
            <div class="m-auto w-100" style="max-width: 500px;">
                <label for="id_documento">Nombre del cliente</label>
                <select name="nro_doc" id="id_documento" class="form-control">
                    <option></option>
                    @foreach ($clientes as $k => $cliente)
                        <option value="{{$cliente->nro_doc}}" data-nro="{{$cliente->nro_doc}}" {{$k === 0 ? 'selected' : ''}}>{{$cliente->nom_cli}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-12 col-lg-6 col-xl-4">
            <div class="border p-3">
                <div class="form-group row">
                    <div class="col-10">
                        <label for="id_docu_comer" class="text-primary">Documentos Comerciales</label>
                    </div>
                    <div class="col-2">
                        <input type="text" class="form-control form-control-sm" name="docu_comer" id="id_docu_comer" disabled value="NO" data-type="value">
                    </div>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input change-doc" id="id_ficha_regis" name="ficha_regis" data-type="checkbox" data-doc="docu_comer">
                    <label class="custom-control-label" for="id_ficha_regis">Ficha Registral</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input change-doc" id="id_poder_rep_legal" name="poder_rep_legal" data-type="checkbox" data-doc="docu_comer">
                    <label class="custom-control-label" for="id_poder_rep_legal">Poder de reparación legal</label>
                </div>
            </div>
            
        </div>
        <div class="form-group col-12 col-lg-6 col-xl-4">
            <div class="border p-3">
                <div class="form-group row">
                    <div class="col-10">
                        <label for="id_docu_suna" class="text-primary">Documentos Sunat</label>
                    </div>
                    <div class="col-2">
                        <input type="text" id="id_docu_suna" class="form-control form-control-sm" name="docu_suna" disabled value="NO" data-type="value">
                    </div>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input change-doc" name="reco_suna" id="id_reco_suna" data-type="checkbox" data-doc="docu_suna">
                    <label class="custom-control-label" for="id_reco_suna">Record Sunat</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input change-doc" name="otro_suna" id="id_otro_suna" data-type="checkbox" data-doc="docu_suna">
                    <label class="custom-control-label" for="id_otro_suna">Otros Sunat</label>
                </div>
            </div>
        </div>
        <div class="form-group col-12 col-xl-4">
            <div class="border p-3">
                <div class="form-group row">
                    <div class="col-10">
                        <label for="id_docu_cred" class="text-primary">Documentos Crediticios</label>
                    </div>
                    <div class="col-2">
                        <input type="text" class="form-control form-control-sm" id="id_docu_cred" name="docu_cred" disabled value="NO" data-type="value">
                    </div>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="info_cred" class="custom-control-input change-doc" id="id_info_cred" data-type="checkbox" data-doc="docu_cred">
                    <label class="custom-control-label" for="id_info_cred">Informe Crediticio</label>
                </div>
            </div>
        </div>
    </form>

    <div class="form-group">
        <div class="form-group">
            <h4 class="text-primary">Datos del Cliente</h4>
        </div>
        <div class="row">
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input change-doc" name="aprob_file" id="id_aprob_file" data-type="checkbox" data-doc="estado_cliente_file">
                    <label class="custom-control-label" for="id_aprob_file">Aprobación documento</label>
                </div>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input change-doc" name="aprob_cred" id="id_aprob_cred" data-type="checkbox" data-doc="estado_cliente_file">
                    <label class="custom-control-label" for="id_aprob_cred">Aprobación crédito</label>
                </div>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input change-doc" name="alta_clien" id="id_alta_clien" data-type="checkbox" data-doc="estado_cliente_file">
                    <label class="custom-control-label" for="id_alta_clien">Alta cliente</label>
                </div>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <span class="d-block text-info">Estado:</span>
                <b id="id_estado_cliente_file" data-type="text"></b>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <span class="d-block text-info">Tipo documento:</span>
                <span id="id_doc_ident" data-type="text"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <span class="d-block text-info">N° documento:</span>
                <span id="id_nro_doc" data-type="text"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-6">
                <span class="d-block text-info">Nombre:</span>
                <span id="id_nom_cli" data-type="text"></span>
            </div>
        </div>
        <div class="form-group">
            <h4 class="text-primary">Representante legal</h4>
        </div>
        <div class="row">
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <span class="d-block text-info">Tipo documento:</span>
                <span id="id_doc_ident_rl" data-type="text"></span>
            </div>
             <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <span class="d-block text-info">N° documento:</span>
                <span id="id_nro_doc_rl" data-type="text"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-6">
                <span class="d-block text-info">Nombre:</span>
                <span id="id_nom_rl" data-type="text"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <span class="d-block text-info">Teléfono:</span>
                <span id="id_tlf_anex_rl" data-type="text"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <span class="d-block text-info">Celular:</span>
                <span id="id_cel_rl" data-type="text"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-6">
                <span class="d-block text-info">Correo:</span>
                <span id="id_email_rl" data-type="text"></span>
            </div>
        </div>
        {{-- <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>Aprob_File</th>
                        <th>Aprob_Credito</th>
                        <th>Alta_Cliente</th>
                        <th>Estado_Cliente</th>
                        <th>Doc_Ident</th>
                        <th>Nro_Doc_Ident</th>
                        <th>Nomb_Cliente</th>
                        <th>Nomb_Rep_Legal</th>
                        <th>Doc_Ident_RL</th>
                        <th>Nro_Doc_Ident_RL</th>
                        <th>Tlf_Anx_RL</th>
                        <th>Cel_RL</th>
                        <th>Email_RL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input change-doc" name="aprob_file" id="id_aprob_file" data-type="checkbox" data-doc="estado_cliente_file">
                                <label class="custom-control-label" for="id_aprob_file"></label>
                            </div>
                        </td>
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input change-doc" name="aprob_cred" id="id_aprob_cred" data-type="checkbox" data-doc="estado_cliente_file">
                                <label class="custom-control-label" for="id_aprob_cred"></label>
                            </div>
                        </td>
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input change-doc" name="alta_clien" id="id_alta_clien" data-type="checkbox" data-doc="estado_cliente_file">
                                <label class="custom-control-label" for="id_alta_clien"></label>
                            </div>
                        </td>
                        <td>
                            <b id="id_estado_cliente_file" data-type="text"></b>
                        </td>
                        <td>
                            <span id="id_doc_ident" data-type="text"></span>
                        </td>
                        <td>
                            <span id="id_nro_doc" data-type="text"></span>
                        </td>
                        <td>
                            <span id="id_nom_cli" data-type="text"></span>
                        </td>
                        <td>
                            <span id="id_nom_rl" data-type="text"></span>
                        </td>
                        <td>
                            <span id="id_doc_ident_rl" data-type="text"></span>
                        </td>
                        <td>
                            <span id="id_nro_doc_rl" data-type="text"></span>
                        </td>
                        <td>
                            <span id="id_tlf_anex_rl" data-type="text"></span>
                        </td>
                        <td>
                            <span id="id_cel_rl" data-type="text"></span>
                        </td>
                        <td>
                            <span id="id_email_rl" data-type="text"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div> --}}
       
    </div>
</section>
@endsection