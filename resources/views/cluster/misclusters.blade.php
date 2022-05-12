@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/miscluster.js')}}"></script>
    <title>Mis Clusters</title>
@endsection
@section('body')
    <section class="p-2">
        <h3 class="text-primary"><i class="fas fa-caret-right"></i> Mis Clusters</h3>
        <table id="tabla-cluster" class="table table-sm table-bordered table-striped">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>N° D.N.I</th>
                    <th>Nombres y apellidos</th>
                    <th>N° R.U.C</th>
                    <th>Razón Social</th>
                    <th>Departamento</th>
                    <th>Provincia</th>
                    <th>Ciudad<br>Base</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="lista-clusters"></tbody>
        </table>
    </section>
@include('modal.editarcluster')
@endsection