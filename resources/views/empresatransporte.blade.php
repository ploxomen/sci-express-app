@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/empresatransporte.js')}}"></script>
    <title>Empresas de Transporte</title>
@endsection
@section('body')
    <div class="my-4">
        <div class="baner-modulo">
            <img src="{{asset('asset/img/intranet/transporte-publico.png')}}" alt="Imagen de transporte">
        </div>
        <h3 class="text-primary text-center">Empresas de Transportes</h3>
    </div>
    <section class="bg-white p-2">
        <div class="form-group">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-nueva-empresa">Nuevo</button>
        </div>
        <table id="tabla-empresas" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>RUC</th>
                    <th>Empresa</th>
                    <th>Contactos</th>
                    <th>Ciudades de atención</th>
                    <th>Dirección</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="lista-empresas"></tbody>
        </table>
    </section>
    <div class="modal fade" id="modal-nueva-empresa" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Empresa de Transporte</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_transporte" class="form-row">
                    <input type="hidden" id="empresa_id">
                    <div class="form-group col-12">
                        <label for="id_nro_ruc">RUC</label>
                        <input type="text" class="form-control" minlength="11" maxlength="11" id="id_nro_ruc" name="nro_ruc">
                    </div>
                    <div class="form-group col-12">
                        <label for="id_empresa">Empresa</label>
                        <input type="text" required class="form-control" id="id_empresa" name="empresa">
                    </div>
                    <div class="form-group col-12">
                        <label for="id_direccion">Dirección</label>
                        <textarea required class="form-control" id="id_direccion" name="direccion"></textarea>
                    </div>
                    <div class="form-group col-12">
                        <label for="id_ciudades_atencion">Ciudades de atención</label>
                        <select name="ciudades_atencion" id="id_ciudades_atencion" required multiple class="cb-mult-nuevo"></select>
                    </div>
                    <div class="form-group col-12">
                        <label for="id_nro_contacto">N° de Contactos</label>
                        <select name="nro_contacto[]" id="id_nro_contacto" class="form-control cb-mult-nuevo" multiple></select>
                    </div>
                    <div class="form-group col-12">
                        <label for="id_correos">Correos</label>
                        <select name="correos[]" id="id_correos" class="form-control cb-mult-nuevo" multiple></select>
                    </div>
                    <div class="form-group col-12">
                        <button type="submit" hidden id="btn-send-frm"></button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btn-save">Guardar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
            </div>
            </div>
        </div>
    </div>
@endsection