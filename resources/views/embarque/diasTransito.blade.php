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
    <script src="{{asset('asset/misDiasDeTransito.js')}}"></script>
    <title>Días de Tránsito</title>
@endsection
@section('body')
<section class="bg-white p-2">
    <form class="mb-4 form-row" id="frm-aplicar-filtros">
        <div class="my-4 col-12">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/transicion.png')}}" alt="Imagen días de tránsito">
            </div>
            <h3 class="text-primary text-center">Días de Tránsito</h3>
        </div>
    </form>
    <div class="form-group">
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-detalle-dias-transito">Agregar</button>
    </div>
    <div class="form-group">
        <table class="table table-sm table-bordered" id="tabla-dias-transito">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Departamento<br>Origen</th>
                    <th>Departamento<br>Destino</th>
                    <th>Ciudad<br>Base</th>
                    <th>Ciudad<br>Destino</th>
                    <th>Origen<br>Base<br>(Km)</th>
                    <th>Origen Base<br>Terreste<br>(Hr)</th>
                    <th>Origen Base<br>Aereo<br>(Hr)</th>
                    <th>Base<br>Destino<br>(Km)</th>
                    <th>Transito<br>Destino<br>(Hr)</th>
                    <th>Total Origen<br>Destino<br>(Km)</th>
                    <th>Total Origen<br>Destino<br>(Hr)</th>
                    <th>Total Origen<br>Base Aereo<br>(Hr)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
    </div>
</section>
@include('modal.detalleDiasTransito')
@endsection