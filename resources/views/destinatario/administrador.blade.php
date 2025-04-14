@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/destinatarios.js')}}"></script>
    <title>Mis destinatarios</title>
@endsection
@section('body')
<section>
    <div class="bg-white p-2">
        <h3 class="text-primary"><i class="fas fa-caret-right"></i> Mis destinatarios</h3>
        {{-- <form id="frm_filtros" class="p-2 bg-white mb-3">
            <div class="row">
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                    <label for="id_estado_doc_cli">Estado</label>
                    <select name="estado_doc_cli" id="id_estado_doc_cli" class="form-control cb-filter">
                        <option value="">Todos</option>
                        <option value="1">Aprobado</option>
                        <option value="0">Pendiente</option>
                    </select>
                </div>
            </div>
        </form> --}}
        <table class="table table-sm table-bordered" id="tabla-destinatario">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>N° Doc.</th>
                    <th>Destinatario</th>
                    <th>Direccion</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="contenido-destinatario"></tbody>
        </table>
    </div>
</section>
@endsection