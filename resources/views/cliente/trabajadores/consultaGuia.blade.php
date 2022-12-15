@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nanogallery2/3.0.5/css/nanogallery2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nanogallery2/3.0.5/jquery.nanogallery2.min.js"></script>
    <script src="{{asset('asset/consultaguiaremitente.js?v1.1')}}"></script>
    <link rel="stylesheet" href="{{asset('asset/consultaguia.css')}}">
    <title>Consulta Guias</title>
@endsection
@section('body')
<section class="bg-white container py-5">
        <div class="guia border-guia">
            <div class="logo form-group">
                <img src="{{asset('asset/img/logo_sci_express.png')}}" alt="Logo de SCIEXPRESSPERU">
            </div>
            <form class="row px-3" id="from-consulta-remitentes">
                <div class="mb-1 col-12">
                    <label for="cb-clientes"><b>Clientes</b></label>
                    <select id="cb-clientes" class="simple-select2">
                        <option value="">Seleccione un cliente</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->nom_cli}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2 col-12 col-lg-6">
                    <label for="fechaInicio"><b>INICIO</b></label>
                    <input type="date" required class="form-control form-control-sm" id="fechaInicio" value="{{date('Y-m-d',strtotime(date('Y-m-d') . '- 7 day'))}}">
                </div>
                <div class="mb-2 col-12 col-lg-6">
                    <label for="fechaFin"><b>FIN</b></label>
                    <input type="date" required class="form-control form-control-sm" id="fechaFin" value="{{date('Y-m-d')}}">
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