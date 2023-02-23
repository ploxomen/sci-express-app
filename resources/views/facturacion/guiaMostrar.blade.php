@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/facturacionMisGuias.js')}}"></script>
    <title>Facturacion Guias</title>
@endsection
@section('body')
<section class="p-2 bg-white mb-3">
    <h3 class="text-primary"><i class="fas fa-caret-right"></i> Mis Facturacion de guías</h3>
    <form class="form-row" id="frm-aplicar-filtros" action="{{route('exportExcelFacturacionGuias')}}">
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="id_filtro_cliente">Cliente</label>
            <select name="cliente" id="id_filtro_cliente" class="form-control filtros simple-select2" required>
                @foreach ($clientes as $cli)
                    <option value="{{$cli->id}}">{{$cli->nom_cli}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-2">
            <label for="id_estado_estado">Via Traslado</label>
            <select name="estado" id="id_estado_estado" class="form-control filtros simple-select2" required>
                <option value="11" selected>CARGO EN CD</option>
                <option value="12">FACTURADO</option>
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-2">
            <label for="id_tray_env">Via Traslado</label>
            <select name="tray_env" id="id_tray_env" class="form-control filtros simple-select2" required>
                @foreach ($via_envios as $via)
                    <option value="{{$via->id}}">{{$via->envio}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-6 col-lg-3 col-xl-2">
            <label for="fecha_inicio">Fecha Inicio</label>
            <input type="date" class="form-control" name="fechaIncio" id="fecha_inicio" value="{{$fechaFiltros['finFiltro']}}" required>
        </div>
        <div class="form-group col-6 col-lg-3 col-xl-2">
            <label for="id_fecha_fin">Fecha Fin</label>
            <input type="date" class="form-control"  name="fechaFin"  id="id_fecha_fin" value="{{$fechaFiltros['inicioFiltro']}}" required>
        </div>
        <div class="form-group col-12 col-md-6 col-xl-1">
            <button type="button" class="btn btn-sm btn-primary mr-1" id="aplicar-filtros-btn" title="Aplicar los filtros"><i class="fas fa-search"></i></button>
            <button type="button" class="btn btn-sm btn-danger mr-1" id="recalcular-costos" title="Recalcular costos finales"><i class="fas fa-sync-alt"></i></button>
            <button type="submit" class="btn btn-sm btn-success" title="Descargar Excel"><i class="far fa-file-excel"></i></button>
        </div>
    </form>
</section>
    <section class="p-2 bg-white">
   <table class="table table-sm table-bordered table-sm" id="tabla-guias">
        <thead>
            <tr>
                <th>Fecha de<br>Emision</th>
                <th>N° de Guía</th>
                <th>Destinatario / Nombres</th>
                <th>Comprobantes</th>
                <th>N° Comprobantes</th>
                <th>Base Cluster</th>
                <th>Ciudad Final</th>
                <th>Complejo Distante</th>
                <th>Descripcion</th>
                <th>Peso</th>
                <th>Peso Volumen</th>
                <th>Costo<br>Adicional<br>(FAU)</th>
                <th>FACT<br>Final<br>Servicio</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</section>
@endsection