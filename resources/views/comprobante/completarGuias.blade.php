@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/completarguias.js')}}"></script>
    <title>Completar Guías/OS</title>
@endsection
@section('body')
    <section class="bg-white p-2">
        <div class="my-3">
            <div class="baner-modulo form-group">
                <img src="{{asset('asset/img/intranet/bill.png')}}" alt="Imagen de guia">
            </div>
            <h3 class="text-primary text-center mb-3">Completar Guías/OS</h3>
        </div>
        <table class="table table-sm table-bordered" id="tabla-guias">
            <thead>
                <tr>
                    <th>N° Guia</th>
                    <th>Fecha Emis</th>
                    <th>Nom Remi</th>
                    <th>Ciudad Base</th>
                    <th>Ciudad Final</th>
                    <th>Clusters</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-detalle-guias"></tbody>
        </table>
    </section>
    @include('modal.completarPreGuias')
@endsection