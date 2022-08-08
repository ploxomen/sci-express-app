@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/informeembarque.js')}}"></script>
    <title>Informe de embarque</title>
@endsection
@section('body')
    <form id="form_embarque_informe" class="p-3 mb-3">
        <div class="my-4">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/manifiesto.png')}}" alt="Imagen de clientes">
            </div>
            <h3 class="text-primary text-center">Informe de embarque</h3>
        </div>
    </form>
    <section class="p-3 bg-white">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped" id="embarques-pendientes">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Fecha Emb</th>
                        <th>Hr Inicio</th>
                        <th>Hr Estimada</th>
                        <th>Hr Fin</th>
                        <th>Bulto</th>
                        <th>KPI</th>
                        <th>Flete</th>
                        <th>Tot Mani</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="listas-embarque"></tbody>
            </table>
        </div>
    </section>
    @include('modal.informeembarque')
@endsection