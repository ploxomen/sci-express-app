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
    <script src="{{asset('asset/misguias.js?v2.3')}}"></script>
    <title>Mis guías</title>
@endsection
<style>
    .form-editar-guia label{
        font-size: 12px !important;
    }
</style>
@section('body')
<section class="p-2 bg-white">
    <h3 class="text-primary"><i class="fas fa-caret-right"></i> Mis guias</h3>
    <form class="form-row" id="frm-aplicar-filtros">
        <div class="form-group col-6 col-lg-4 col-xl-2">
            <label for="fecha_inicio">Fecha Inicio</label>
            <input type="date" class="form-control" id="fecha_inicio" value="{{$fechaFiltros['finFiltro']}}" required>
        </div>
        <div class="form-group col-6 col-lg-4 col-xl-2">
            <label for="id_fecha_fin">Fecha Fin</label>
            <input type="date" class="form-control" id="id_fecha_fin" value="{{$fechaFiltros['inicioFiltro']}}" required>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-4">
            <label for="estado_guias">Status</label>
            <select class="form-control cb-filtros" id="estado_guias">
                <option value="0" selected>TODOS</option>
                @foreach ($estado_guias as $estado)
                    <option value="{{$estado->id}}">{{$estado->tipo}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-4 col-xl-2">
            <label for="id_tipo">Tipo</label>
            <select class="form-control cb-filtros" id="id_tipo">
                <option value="0" selected>TODOS</option>
                <option value="1">ACTIVA</option>
                <option value="2">ANULADA</option>
                <option value="3">INACTIVA</option>
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-4 col-xl-2">
            <button type="submit" class="btn btn-sm btn-light" title="Aplicar los filtros"><i class="fas fa-search"></i></button>
            <button type="button" class="btn btn-sm btn-light" title="Asignación masiva" id="view-lista-guias"><i class="fas fa-globe-asia"></i></button>
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
        <div class="form-group col-12">
            <span class="text-info">Total N° Guías/OS sin asignar: <span id="total-por-procesar-guias">{{$nro_por_asignar[0]->total}}</span></span>
        </div>
    </form>
    <div class="form-group">
        <div class="cuadro table-warning"></div>
        <span>Por asignar</span>
    </div>
   <table class="table table-sm table-bordered" id="tabla-guias">
        <thead>
            <tr>
                <th>N° Guia</th>
                <th>N° Comprobante SUNAT</th>
                <th>Fecha Emis</th>
                <th>Fecha Tras</th>
                <th>Comprobantes</th>
                <th>Días Trans</th>
                <th>Nom Remi</th>
                <th>Nom Consig</th>
                <th>Ciudad Base</th>
                <th>Ciudad Final</th>
                <th>Clusters</th>
                <th>Resp Embar</th>
                <th>Estado</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-detalle-guias">
            <tr>
                <td colspan="100%" class="text-center">Sin resultados</td>
            </tr>
        </tbody>
    </table>
</section>
@include('modal.detalleguiatodos')
@include('modal.editarguia')
@include('modal.asignarPersonalMasivoGuia')
@include('comprobante.modales.seleccion-guias')
@include('comprobante.modales.guia-remitente')
@include('comprobante.modales.guia-transportista')
<x-loader />
@endsection