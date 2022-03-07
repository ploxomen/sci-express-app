@extends('general.index')
@section('head')
    <script src="{{asset('asset/misclientes.js')}}"></script>
    <title>Mis clientes</title>
@endsection
@section('body')
<section>
    <form id="frm_filtros" class="p-2 bg-white mb-3">
        <div class="row">
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-4">
                <label for="id_nom_cli">Cliente</label>
                <input name="nom_cli" id="id_nom_cli" type="text" class="form-control" placeholder="Buscar por nombre">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                <label for="id_estado_cli">Estado</label>
                <select name="estado_cli" id="id_estado_cli" class="form-control">
                    <option value="">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                <label for="id_estado_doc_cli">Estado Doc</label>
                <select name="estado_doc_cli" id="id_estado_doc_cli" class="form-control">
                    <option value="">Todos</option>
                    <option value="1">Aprobado</option>
                    <option value="0">Pendiente</option>
                </select>
            </div>
        </div>
    </form>
    <div class="bg-white p-2">
        <div id="pagination-cliente" class="pagination-container"></div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
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
                <tbody id="data-clientes"></tbody>
            </table>
        </div>
    </div>
</section>
@endsection