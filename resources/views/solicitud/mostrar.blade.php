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
<section class="p-3 bg-white mb-4">
    <h3 class="text-primary"><i class="fas fa-caret-right"></i> Mis solicitudes</h3>
    <form class="row mb-4" id="filtro-ver-solicitudes">
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="filtro_solicitud_inicio">Fecha inicio</label>
            <input type="date" class="form-control" value="{{$filtros['finFiltro']}}" id="filtro_solicitud_inicio">
        </div>
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="filtro_solicitud_fin">Fecha fin</label>
            <input type="date" class="form-control" value="{{$filtros['inicioFiltro']}}" id="filtro_solicitud_fin">
        </div>
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="estado_soli">Estado</label>
            <select class="form-control cb-filtros" id="estado_soli">
                <option value="" selected>TODOS</option>
                @foreach ($estados_solicitud as $estado)
                    <option value="{{$estado->id}}">{{$estado->estado}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-3">
            <button type="submit" class="btn btn-sm btn-light mr-1" title="Aplicar los filtros"><i class="fas fa-search"></i></button>
            <button class="btn btn-sm btn-light position-relative mr-1 {{$confirmacion->count() !== 0 ? 'badge-notification' : ''}}" data-info="{{$confirmacion->count()}}" type="button" title="Confirmación nuevas solicitudes" data-toggle="modal" data-target="#modal-notificar-whatsapp" id="btn-notificar-whatsapp"><i class="fab fa-whatsapp"></i></button>
            <button class="btn btn-sm btn-light position-relative {{$por_procesar !== 0 ? 'badge-notification' : ''}}" data-info="{{$por_procesar}}" type="button" title="Asignación masiva" id="btn-asignacion-masiva"><i class="fas fa-globe-asia"></i></button>
        </div>
        <div class="form-group col-12">
            <span class="text-info">Solicitudes pendientes por asignar: <span id="total-por-procesar">{{$por_procesar}}</span></span>
        </div>
    </form>
</section>
<section class="p-3 bg-white">
   <table class="table table-sm table-bordered" id="tabla-solicitud">
        <thead>
            <tr>
                <th>N° soli</th>
                <th>N° Recojo</th>
                <th>Fecha Soli</th>
                <th>Día Recojo</th>
                <th>Hr Reco</th>
                <th>N° Doc</th>
                <th>Nom Cliente</th>
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
@include('modal.asignarPersonalMasivoSolicitud')
@include('modal.notificarWhatsappSolicitud')
@include('modal.editarSolicitud')
@endsection