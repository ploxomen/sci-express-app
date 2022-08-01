@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('asset/consultaguia.css')}}">
    <script src="{{asset('asset/consultaguiaremitente.js?v1.1')}}"></script>
    <title>Guias</title>
@endsection
@section('body')
<section class="bg-white container py-5">
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
                    <input type="date" required class="form-control form-control-sm" id="fechadepacho">
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
@endsection