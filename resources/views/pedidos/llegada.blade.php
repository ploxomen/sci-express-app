@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/llegada.js')}}"></script>
    <script src="{{asset('asset/llegadacluster.js')}}"></script>
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
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="boton-arribar" data-toggle="pill" href="#contenido-arribar" role="tab" aria-controls="pills-home" aria-selected="true">Embarques</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="boton-guias" data-toggle="pill" href="#contenido-guias" role="tab" aria-controls="pills-profile" aria-selected="false">Guias</a>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="contenido-arribar" role="tabpanel" aria-labelledby="pills-home-tab">
            <div class="my-4">
                <div class="baner-modulo">
                    <img src="{{asset('asset/img/intranet/arrived.png')}}" alt="Imagen de clientes">
                </div>
                <h3 class="text-primary text-center">Embarques por arribar</h3>
            </div>
            <div class="bg-white p-2 mb-3">
                <div class="form-group d-flex justify-content-between flex-wrap">
                    <h5 class="text-primary">De Embarques</h5>
                    <div class="d-flex align-items-center">
                        <label for="" class="mr-3">A confirmar</label>
                        <input type="text" readonly class="form-control mw-100" value="0" id="valor-embarques">
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
        </div>
        <div class="tab-pane fade" id="contenido-guias" role="tabpanel" aria-labelledby="pills-profile-tab">
            <div class="my-4">
                <div class="baner-modulo">
                    <img src="{{asset('asset/img/intranet/bill.png')}}" alt="Imagen de guia">
                </div>
                <h3 class="text-primary text-center">Guias asignadas</h3>
            </div>
            <div class="form-group bg-white p-2 mb-3">
                <div class="row">
                    <div class="form-group col-12 col-md-6 col-lg-3">
                        <label for="estado_guias">Estado</label>
                        <select class="form-control cb-filtros-guias" id="estado_guias">
                            <option value="0" selected>TODOS</option>
                            @foreach ($estado_guias as $k=>$estado)
                                <option value="{{$estado->id}}">{{$estado->tipo}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group bg-white p-2 mb-3">
                <table class="table table-sm table-striped table-bordered" id="tabla-asigancion-guias">
                    <thead>
                        <tr>
                            <th>N° Guia</th>
                            <th>Fecha Guia</th>
                            <th>N° Manif</th>
                            <th>Fecha Manif</th>
                            <th>Prov Manif Orig</th>
                            <th>Prov Manif Dest</th>
                            <th>Tipo Guia</th>
                            <th>Estado Guia</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-guias-cluster"></tbody>
                </table>
            </div>
            
        </div>
    </div>
    
</section>
@include('modal.detalleguia')
@include('modal.detalleguiacluster')
@include('modal.detallembarque')
@endsection