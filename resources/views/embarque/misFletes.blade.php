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
    <script src="{{asset('asset/misFletes.js')}}"></script>
    <title>Mis Fletes</title>
@endsection
@section('body')
<section class="bg-white p-2">
    <form class="mb-4 form-row" id="frm-aplicar-filtros">
        <div class="my-4 col-12">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/flete-aereo.png')}}" alt="Imagen de fletes aereos">
            </div>
            <h3 class="text-primary text-center">Mis Fletes</h3>
        </div>
    </form>
    <div class="form-group">
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-detalle-misfletes">Nuevo flete</button>
    </div>
    <div class="form-group">
        <table class="table table-sm table-bordered" id="tabla-fletes">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Ciudad Base</th>
                    <th>Sobres</th>
                    <th>Cajas</th>
                    <th>Cajones</th>
                    <th>Paquetes</th>
                    <th>Jabas</th>
                    <th>Otros</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
    </div>
</section>
@include('modal.detallefletes')
@endsection