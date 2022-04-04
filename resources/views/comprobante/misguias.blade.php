@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/misguias.js')}}"></script>
    <title>Mis guias</title>
@endsection
@section('body')
<section class="p-2 bg-white">
    <h3 class="text-primary"><i class="fas fa-caret-right"></i> Mis guias</h3>
    <form class="row mb-4">
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="estado_guias">Estado</label>
            <select class="form-control cb-filtros" id="estado_guias">
                <option value="0" selected>TODOS</option>
                @foreach ($estado_guias as $estado)
                    <option value="{{$estado->id}}">{{$estado->tipo}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-xl-2">
            <label for="id_tipo">Tipo</label>
            <select class="form-control cb-filtros" id="id_tipo">
                <option value="0" selected>TODOS</option>
                <option value="1">ACTIVA</option>
                <option value="2">ANULADA</option>
                <option value="3">INACTIVA</option>
            </select>
        </div>
        <div class="form-group col-12">
            <span class="text-info">Total N° guias sin asignar: <span id="total-por-procesar-guias">{{$nro_por_asignar[0]->total}}</span></span>
        </div>
    </form>
    <div class="form-group">
        <div class="cuadro table-warning"></div>
        <span>Por asignar</span>
    </div>
   <table class="table table-sm table-bordered" id="tabla-guias">
        <thead>
            <tr>
                <th>N° Guia</th>
                <th>Fecha Emis</th>
                <th>Fecha Tras</th>
                <th>Prov Orig</th>
                <th>Prov Dest</th>
                <th>Nom Remi</th>
                <th>N° Docu Remi</th>
                <th>Nom Dest</th>
                <th>N° Docu Dest</th>
                <th>Responsable</th>
                <th>Estado</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-detalle-guias"></tbody>
    </table>
</section>
@include('modal.detalleguiatodos')
@endsection