@extends('general.index')
@section('head')
    <script src="{{ asset('asset/homedatatable.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('library/datatable/datatable-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatable/responsive-bootstrap.min.css') }}">
    <script src="{{ asset('library/datatable/jq-datatable.min.js') }}"></script>
    <script src="{{ asset('library/datatable/datatable-bootstrap4.min.js') }}"></script>
    <script src="{{ asset('library/datatable/datatable-responsive.min.js') }}"></script>
    <script src="{{ asset('library/datatable/responsive-bootstrap.min.js') }}"></script>
    <script src="{{asset('asset/verificacionGuias.js')}}"></script>
@endsection
<style>
    .form-editar-guia label {
        font-size: 12px !important;
    }
</style>
@section('body')
    <section class="p-2 bg-white">
        <h3 class="text-primary"><i class="fas fa-caret-right"></i> Verificación</h3>
        <form class="form-row">
            <div class="form-group col-6 col-lg-4 col-xl-2">
                <label for="fecha_inicio">Fecha Inicio</label>
                <input type="date" class="form-control filtro" id="fecha_inicio" value="{{$fechaFiltros['finFiltro']}}" required>
            </div>
            <div class="form-group col-6 col-lg-4 col-xl-2">
                <label for="id_fecha_fin">Fecha Fin</label>
                <input type="date" class="form-control filtro" id="id_fecha_fin" value="{{$fechaFiltros['inicioFiltro']}}" required>
            </div>
        </form>
        <table class="table table-sm table-bordered" id="tabla-guias">
            <thead>
                <tr>
                    <th>N° Guia</th>
                    <th>Fecha Emis</th>
                    <th>Fecha Tras</th>
                    <th>Comprobantes</th>
                    <th>Días Trans</th>
                    <th>Nom Remi</th>
                    <th>Nom Consig</th>
                    <th>Ciudad Base</th>
                    <th>Ciudad Final</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-detalle-guias"></tbody>
        </table>
    </section>
    @include('modal.rechazoGuias')
@endsection
