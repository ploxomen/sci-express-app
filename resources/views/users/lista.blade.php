@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/auth/misusuarios.js?v2')}}"></script>
    <title>Mis usuarios</title>
@endsection
@section('body')
<div class="my-4">
    <div class="baner-modulo">
        <img src="{{asset('asset/img/intranet/man.png')}}" alt="Imagen de clientes">
    </div>
    <h3 class="text-primary text-center">Mis usuario</h3>
</div>
<section class="p-2">
    <div class="bg-white p-3">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm" id="tabla-usuarios">
                <thead>
                    <tr>
                        <th>N° Documento</th>
                        <th>Nombres y Apellidos</th>
                        <th>Celular</th>
                        <th>Correo</th>
                        <th>Área</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="lista-usuarios"></tbody>
            </table>
        </div>
    </div>
</section>
@include('modal.cambiarpassword')
@include('modal.editarusuario')
@include('modal.editarroles')
@endsection