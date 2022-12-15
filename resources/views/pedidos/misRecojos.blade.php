@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/misRecojos.js')}}"></script>
    <title>Mis Recojos</title>
@endsection
@section('body')
<section class="p-3 bg-white mb-4">
    <h3 class="text-primary"><i class="fas fa-caret-right"></i> Mis Recojos</h3>
    <form class="row mb-4" id="filtro-ver-solicitudes">
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="filtro_solicitud_inicio">Fecha inicio</label>
            <input type="date" class="form-control" value="{{$filtros['finFiltro']}}" id="filtro_recojo_inicio">
        </div>
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="filtro_solicitud_fin">Fecha fin</label>
            <input type="date" class="form-control" value="{{$filtros['inicioFiltro']}}" id="filtro_recojo_fin">
        </div>
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="estado_soli">Estado</label>
            <select class="form-control cb-filtros" id="estado_recojo">
                <option value="4" selected>TODOS</option>
                <option value="1">PENDIENTE</option>
                <option value="2">EN PROCESO</option>
                <option value="3">TERMINADO</option>
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-3">
            <button type="submit" class="btn btn-sm btn-light mr-1" title="Aplicar los filtros"><i class="fas fa-search"></i></button>
        </div>
    </form>
</section>
<section class="p-3 bg-white">
   <table class="table table-sm table-bordered" id="tabla-recojos">
        <thead>
            <tr>
                <th>N° Recojo</th>
                <th>Fecha Recojo</th>
                <th>Responsable Recojo</th>
                <th>Cantidad Solicitud</th>
                <th>Usuario Programado</th>
                <th>Fecha Hr. Programado</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-cuerpo-recojos"></tbody>
    </table>
</section>
@include('modal.editarRecojo')
@endsection