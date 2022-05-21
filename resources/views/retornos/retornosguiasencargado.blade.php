@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/retornoguias.js')}}"></script>
    <title>Guías retornadas</title>
@endsection
@section('body')
<div class="my-4">
    <div class="baner-modulo">
        <img src="{{asset('asset/img/intranet/agreement.png')}}" alt="Imagen de retorno de guías">
    </div>
    <h3 class="text-primary text-center">Guías Retornadas</h3>
</div>
<section class="p-2 bg-white">
    <form class="row">
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
            <label for="cb-clusters">Clusters</label>
            <select id="cb-clusters" class="simple-select2">
                <option value="0">Todos</option>
                @foreach ($clusters as $clu)
                    <option value="{{$clu->id}}">{{$clu->name}} {{$clu->last_name}}</option>
                @endforeach
            </select>
        </div>
    </form>
    <table class="table table-sm table-bordered" id="tabla-retorno">
        <thead>
            <tr>
                <th>N° Embarque</th>
                <th>Fecha Emb</th>
                <th>Empresa</th>
                <th>Fecha y Hr Lle</th>
                <th>Cluster</th>
                <th>Import Fact</th>
                <th>Comprobantes</th>
                <th>N° Comp</th>
                <th>N° Guía</th>
                <th>Total Bulto</th>
                <th>Acciónes</th>
            </tr>
        </thead>
        <tbody id="cuerpo-detalle-guias"></tbody>
    </table>
</section>
@include('modal.detallembarque')
@endsection