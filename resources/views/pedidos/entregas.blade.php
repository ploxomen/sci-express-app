@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/entregas.js')}}"></script>
    <title>Entregas</title>
@endsection
@section('body')
<style>
     .mw-100{
        max-width: 100px !important;
    }
    .gap-10{
        gap: 10px;
    }
</style>
<section class="">
    <div class="my-4">
        <div class="baner-modulo">
            <img src="{{asset('asset/img/intranet/delivery.png')}}" alt="Imagen de entregas">
        </div>
        <h3 class="text-primary text-center">Entregas</h3>
    </div>
    <form class="bg-white p-2 mb-3">
        <div class="form-row">
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="fecha_confirmacion">Fecha de Confirmación</label>
                <input id="fecha_confirmacion" type="date" class="form-control change-entrega-fecha">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="fecha_reparto">Fecha de Reparto</label>
                <input id="fecha_reparto" type="date" class="form-control change-entrega-fecha">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="estado_guia">Situación Guía</label>
                <select id="estado_guia" class="form-control simple-select2 cb-filtros">
                    <option value="0" selected>TODOS</option>
                    <option value="7">REPARTO</option>
                    <option value="8">ENTREG Y CONF</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="nombre_cluster">Clusters</label>
                <select id="nombre_cluster" class="form-control simple-select2 cb-filtros">
                    <option value="0">TODOS</option>
                    @foreach ($clusters as $clu)
                        <option value="{{$clu->id_cluster}}">{{mb_strtoupper($clu->name,'UTF-8')}} {{mb_strtoupper($clu->last_name,'UTF-8')}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
    <div class="bg-white p-2 mb-3">
        <div class="form-group d-flex justify-content-between flex-wrap">
            <h3 class="text-primary">De Guías</h3>
            <div class="d-flex align-items-center">
                <label for="" class="mr-3">A confirmar</label>
                <input type="text" readonly class="form-control mw-100" value="{{$nro_pendients}}">
            </div>
        </div>
        <div class="form-group">
            <div class="cuadro table-warning"></div>
            <span>Por confirmar</span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="tabla-entrega">
                <thead>
                    <tr>
                        <th>N° Guía</th>
                        <th>N° Orden Compra</th>
                        <th>Vto Orden Compra</th>
                        <th>Remitente</th>
                        <th>Destinatario</th>
                        <th>Situación Guía</th>
                        <th>Total Bultos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="cuerpo-entregas"></tbody>
            </table>
        </div>
    </div>
    <form action="" class="bg-white form-group p-2">
        <fieldset class="px-3 border mb-4">
            <legend class="d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">KPI'S</legend>
            <div class="row" id="kpis_clusters">
                <div class="form-group col-12 text-center"><span class="text-danger">Por favor seleccione a un cluster</span></div>
            </div>
        </fieldset>
    </form>
</section>
@include('modal.detalleentregas')
@endsection