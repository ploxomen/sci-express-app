@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/misclientes.js')}}"></script>
    <title>Mis clientes</title>
@endsection
@section('body')
<section>
    <div class="bg-white p-2">
        <h3 class="text-primary"><i class="fas fa-caret-right"></i> Mis clientes</h3>
        <form id="frm_filtros" class="p-2 bg-white mb-3">
            <div class="row">
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                    <label for="id_estado_doc_cli">Estado Doc</label>
                    <select name="estado_doc_cli" id="id_estado_doc_cli" class="form-control cb-filter">
                        <option value="">Todos</option>
                        <option value="1">Aprobado</option>
                        <option value="0">Pendiente</option>
                    </select>
                </div>
            </div>
        </form>
        <table class="table table-sm table-bordered" id="tabla-clientes">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Cliente</th>
                    <th>Direccion</th>
                    <th>Representante</th>
                    <th>Cel RL</th>
                    <th>Email RL</th>
                    <th>Estado Doc</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="table-cliente"></tbody>
        </table>
    </div>
</section>
@endsection