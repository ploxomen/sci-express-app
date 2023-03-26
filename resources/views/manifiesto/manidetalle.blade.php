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
    <script src="{{asset('asset/mismanifiestos.js')}}"></script>
    <title>Mis Manifiestos</title>
@endsection
@section('body')
<section class="bg-white p-2">
    <form class="mb-4 form-row" id="filtro-manifiesto">
        <div class="my-4 col-12">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/declaracion.png')}}" alt="Imagen manifiesto detalle">
            </div>
            <h3 class="text-primary text-center">Mis manifiestos</h3>
        </div>
        <div class="form-group col-6 col-md-4 col-lg-2">
            <label for="fecha_inicio">Fecha Inicio</label>
            <input type="date" class="form-control" id="fecha_inicio" value="{{$fechaFiltros['finFiltro']}}" required>
        </div>
        <div class="form-group col-6 col-md-4 col-lg-2">
            <label for="id_fecha_fin">Fecha Fin</label>
            <input type="date" class="form-control" id="id_fecha_fin" value="{{$fechaFiltros['inicioFiltro']}}" required>
        </div>
        <div class="form-group col-12 col-md-4 col-lg-2">
            <label for="id_status">Trayecto</label>
            <select id="cb-trayectos" class="form-control filtro simple-select2">
                <option value="todos">TODOS</option>
                @foreach ($trayectos as $trayecto)
                    <option value="{{$trayecto->id}}">{{$trayecto->tipo}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-6 col-md-4 col-lg-2">
            <label for="id_status">Provincia Origen</label>
            <select id="cb-provincia-origen" class="form-control filtro simple-select2">
                <option value="todos">TODOS</option>
                @foreach ($departamentoProv as $depart)
                    <optgroup label="{{$depart['nom_ubigeo_dep']}}">
                        @foreach ($depart['provincias'] as $prov)
                            <option value="{{$prov->id_ubigeo}}">{{$prov->nombre_ubigeo}}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div class="form-group col-6 col-md-4 col-lg-2">
            <label for="id_status">Provincia Destino</label>
            <select id="cb-provincia-final" class="form-control filtro simple-select2">
                <option value="todos">TODOS</option>
                @foreach ($departamentoProv as $depart)
                    <optgroup label="{{$depart['nom_ubigeo_dep']}}">
                        @foreach ($depart['provincias'] as $prov)
                            <option value="{{$prov->id_ubigeo}}">{{$prov->nombre_ubigeo}}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12 col-md-4 col-lg-2">
            <label for="id_situacion_g">Nombre de Cluster</label>
            <select id="cb-cluster" class="form-control filtro simple-select2">
                <option value="todos">TODOS</option>
                @foreach ($clusters as $clu)
                    <option value="{{$clu->id}}">{{$clu->name}} {{$clu->last_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12 col-md-4 col-lg-2">
            <label for="id_situacion_g">Situación Manifiesto</label>
            <select id="cb-estado" class="form-control filtro simple-select2">
                <option value="todos">TODOS</option>
                @foreach ($situacionManifiesto as $mani)
                    <option value="{{$mani->id}}">{{$mani->tipo}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12">
            <div class="d-flex" style="gap: 5px;">
                <div>
                    <button type="submit" class="btn btn-sm btn-primary" title="Aplicar los filtros"><i class="fas fa-search"></i></button>
                </div>
                <div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-info dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            <i class="far fa-folder"></i> Reportes
                        </button>
                        <div class="dropdown-menu">
                        <a class="dropdown-item reporte-general" data-tipo="excel" href="#"><i class="far fa-file-excel text-success"></i> Excel</a>
                        <a class="dropdown-item reporte-general" data-tipo="pdf" href="#"><i class="far fa-file-pdf text-danger"></i> PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="form-group">
        <table class="table table-sm table-bordered" id="tabla-manifiestos">
            <thead>
                <tr>
                    <th>N° Mani</th>
                    <th>Fch Emisión</th>
                    <th>Trayecto</th>
                    <th>Envio</th>
                    <th>Guias</th>
                    <th>Prov Orig</th>
                    <th>Prov Dest</th>
                    <th>Cluster</th>
                    <th>Situación</th>
                    <th>Responsable</th>
                    <th>N° Guía</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
    </div>
</section>
@include('modal.detalleguiatodos')
@include('modal.detallemani')
@endsection