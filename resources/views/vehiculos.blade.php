@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/vehiculos.js')}}"></script>
    <title>Vehículos de transporte</title>
@endsection
@section('body')
    <div class="my-4">
        <div class="baner-modulo">
            <img src="{{asset('asset/img/intranet/carro.png')}}" alt="Imagen de un carro">
        </div>
        <h3 class="text-primary text-center">Vehículos de transporte</h3>
    </div>
    <section class="bg-white p-2">
        <div class="form-group">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-nueva-vehiculo">Nuevo</button>
        </div>
        <table id="tabla-vehiculos" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Placa</th>
                    <th>Año</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="lista-vehiculos"></tbody>
        </table>
    </section>
    @include('modal.vehiculos')
@endsection