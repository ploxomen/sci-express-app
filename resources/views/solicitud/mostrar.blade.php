@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/missolicitud.js')}}"></script>
    <title>Mis solicitudes</title>
@endsection
@section('body')
<style>
    .form-info span{
        display: block;
    }
</style>
<section class="p-2 bg-white">
    <h3 class="text-primary"><i class="fas fa-caret-right"></i> Mis solicitudes</h3>
    <form class="row mb-4">
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="estado_soli">Estado</label>
            <select class="form-control cb-filtros" id="estado_soli">
                <option value="" selected>TODOS</option>
                @foreach ($estados_solicitud as $estado)
                    <option value="{{$estado->id}}">{{$estado->estado}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12">
            <span class="text-info">Solicitudes pendientes por asignar: <span id="total-por-procesar">{{$por_procesar[0]->total}}</span></span>
        </div>
    </form>
   <table class="table table-sm table-bordered" id="tabla-solicitud">
        <thead>
            <tr>
                <th>N° soli</th>
                <th>Fecha Soli</th>
                <th>Día Recojo</th>
                <th>Hr Reco</th>
                <th>Nom Cliente</th>
                <th>Direc Recojo</th>
                <th>Bulto</th>
                <th>Nom Soli</th>
                <th>Trayecto</th>
                <th>Responsable</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-solicitudes"></tbody>
    </table>
</section>
@include('modal.versolicitud');
@endsection