@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/devolverguias.js')}}"></script>
    <script src="{{asset('asset/retornoguias.js')}}"></script>
    <title>Retorno de Cluster</title>
@endsection
@section('body')
<section class="">
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="boton-retorno" data-toggle="pill" href="#contenido-retornar" role="tab" aria-controls="pills-home" aria-selected="true">Retornar</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="boton-misretornos" data-toggle="pill" href="#contenido-misretornos" role="tab" aria-controls="pills-profile" aria-selected="false">Mis Retornos</a>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="contenido-retornar" role="tabpanel" aria-labelledby="pills-home-tab">
            <div class="my-4">
                <div class="baner-modulo">
                    <img src="{{asset('asset/img/intranet/embarque.png')}}" alt="Imagen de embarque">
                </div>
                <h3 class="text-primary text-center">Retorno de Cluster</h3>
            </div>
            <div class="bg-white p-2">
                <div class="form-group">
                    <button class="btn btn-danger btn-sm" type="button" id="guardar_guias" title="Guardar guías para embarcarlas luego">Guardar</button>
                    <button class="btn btn-primary btn-sm" type="button" id="embarcar_guias" title="Embarcar guías guardadas y/o seleccionadas">Embarcar</button>
                </div>
                <h4 class="text-primary">Guías a retornar</h4>
                <div class="form-group d-flex">
                    <div class="cuadro table-danger mr-1"></div><span>Incidencia en la entrega</span>
                </div>
                <table class="table table-sm table-bordered" id="tabla-retorno-guia">
                    <thead>
                        <tr>
                            <th>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="id_check_todo">
                                    <label class="custom-control-label" for="id_check_todo"></label>
                                </div>
                            </th>
                            <th>N° Guia</th>
                            <th>N° Doc Remi</th>
                            <th>Nom Remi</th>
                            <th>N° Doc Dest</th>
                            <th>Nom Dest</th>
                            <th>Trayecto</th>
                            <th>Prov Orig</th>
                            <th>Prov Dest</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-cuerpo-guias"></tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="contenido-misretornos" role="tabpanel" aria-labelledby="pills-profile-tab">
            <div class="my-4">
                <div class="baner-modulo">
                    <img src="{{asset('asset/img/intranet/agreement.png')}}" alt="Imagen de retorno de guías">
                </div>
                <h3 class="text-primary text-center">Guías Retornadas</h3>
            </div>
            <div class="bg-white p-2">
                <div class="table-responsive"> 

                <table class="table table-sm table-bordered" id="tabla-retorno">
                    <thead>
                        <tr>
                            <th>N° Embarque</th>
                           <th>Fecha Emb</th>
                            <th>Empresa</th>
                            <th>Fecha y Hr Lle</th>
                            <th>Import Fact</th>
                            <th>Comprobantes</th>
                            <th>N° Comp</th>
                            <th>N° Guías</th>
                            <th>Total Bulto</th>
                            <th>Acciónes</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpo-detalle-guias"></tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</section>
@include('modal.detalleguia')
@include('modal.devolverguiasmodal')
@include('modal.detallembarque')
@endsection