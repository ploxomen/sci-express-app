@extends('general.index')
@section('head')
    <link rel="stylesheet" href="/library/viewer/viewer.min.css">
    <script src="/library/viewer/viewer.min.js"></script>
    <script src="{{asset('asset/llegada.js')}}"></script>
    <title>Embarques por arribar</title>
@endsection
@section('body')
<style>
    .mw-100{
        max-width: 100px !important;
    }
    table tbody tr td{
        vertical-align: middle !important;
    }
</style>
<section class="">
    <div class="my-4">
        <div class="baner-modulo">
            <img src="{{asset('asset/img/intranet/arrived.png')}}" alt="Imagen de llegadas">
        </div>
        <h3 class="text-primary text-center">Embarques por arribar</h3>
    </div>
    <div class="bg-white p-2 mb-3">
        <div class="row">
            <div class="form-group col-12 col-sm-6 col-lg-5 col-xl-5">
                <h5 class="text-primary">De Embarques</h5>
            </div>
            <div class="form-group col-6 col-sm-3 col-lg-5 col-xl-5">
                <div class="ml-lg-auto" style="width: 100%; max-width: 350px;">
                    <label for="cb-clusters">Clusters</label>
                    <select id="cb-clusters" class="form-control">
                        <option value="0"  data-nro="Todos">Todos</option>
                        @foreach ($clusters as $clu)
                            <option value="{{$clu->id_cluster}}" data-nro="{{$clu->nombreProvincia}}">{{mb_strtoupper($clu->name,'UTF-8')}} {{mb_strtoupper($clu->last_name,'UTF-8')}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-6 col-sm-3 col-lg-2 col-xl-2">
                <div class="mr-auto" style="width: 100%; max-width: 150px;">
                    <label for="" class="mr-3">A confirmar</label>
                    <input type="text" readonly class="form-control" value="0" id="valor-embarques">
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr>
                        <th>N° Embarque</th>
                        <th>Fecha Embarque</th>
                        <th>Empresa</th>
                        <th>Fecha y Hr Llegada</th>
                        <th>Cluster</th>
                        <th>Comprobantes</th>
                        <th>N° Comp</th>
                        <th>Total Bulto</th>
                        <th>Acciónes</th>
                    </tr>
                </thead>
                <tbody id="tabla-embarques"></tbody>
            </table>
        </div>
    </div>
    <div class="bg-white p-2 mb-3">
        <div class="form-group d-flex justify-content-between flex-wrap">
            <h5 class="text-primary">De Manifiestos y guias</h5>
            <div class="d-flex align-items-center">
                <label for="" class="mr-3"># Guías a confirmar</label>
                <input type="text" readonly class="form-control mw-100" value="0" id="valor-manifiesto">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr>
                        <th>N° Manif</th>
                        <th>Fecha Manif</th>
                        <th>Prov Origen</th>
                        <th>Prov Destino</th>
                        <th>Total Guías</th>
                        <th>Estado</th>
                        <th>N° Guía</th>
                        <th>Fecha Reparto</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tabla-mani">
                    <tr>
                        <td colspan="9" class="text-center">Seleccione un embarque</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<div id="mi-galeria" class="d-none"></div>
@include('modal.detalleguia')
@include('modal.detallembarque')
@endsection