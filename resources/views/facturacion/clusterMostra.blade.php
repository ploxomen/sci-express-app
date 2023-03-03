@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/facturacionClusterFac.js')}}"></script>
    <title>Facturacion Cluster</title>
@endsection
@section('body')
<section class="p-2 bg-white mb-3">
    <h3 class="text-primary"><i class="fas fa-caret-right"></i> Mi Facturación Cluster</h3>
    <form class="form-row" id="frm-aplicar-filtros" action="{{route('exportExcelFacturacionGuias')}}">
        <div class="form-group col-12 col-md-6 col-lg-2">
            <label for="id_filtro_cluster">Cluster</label>
            <select name="cluster" id="id_filtro_cluster" class="form-control filtros simple-select2" required>
                @foreach ($clusters as $clu)
                    <option value="{{$clu->id_cluster}}">{{$clu->name . ' ' . $clu->last_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-2">
            <label for="id_estado_estado">Estado</label>
            <select name="estado" id="id_estado_estado" class="form-control filtros simple-select2" required>
                <option value="11" selected>CARGO EN CD</option>
                <option value="12">FACTURADO</option>
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-2">
            <label for="id_tray_env">Via Traslado (Facturación)</label>
            <select name="trayecto" id="id_tray_env" class="form-control filtros simple-select2" required>
                @foreach ($via_envios as $via)
                    <option value="{{$via->id}}">{{$via->envio}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-6 col-lg-3 col-xl-2">
            <label for="fecha_inicio">Fecha Inicio</label>
            <input type="date" class="form-control" name="fechaInicio" id="fecha_inicio" value="{{$fechaFiltros['finFiltro']}}" required>
        </div>
        <div class="form-group col-6 col-lg-3 col-xl-2">
            <label for="id_fecha_fin">Fecha Fin</label>
            <input type="date" class="form-control"  name="fechaFin"  id="id_fecha_fin" value="{{$fechaFiltros['inicioFiltro']}}" required>
        </div>
        <div class="form-group col-12 col-md-6 col-xl-2">
            <button type="button" class="btn btn-sm btn-primary mr-1" id="aplicar-filtros-btn" title="Aplicar los filtros"><i class="fas fa-search"></i></button>
            <button type="button" class="btn btn-sm btn-danger mr-1" id="recalcular-costos" title="Recalcular costos finales"><i class="fas fa-sync-alt"></i></button>
            <button type="submit" class="btn btn-sm btn-success" title="Descargar Excel"><i class="far fa-file-excel"></i></button>
        </div>
    </form>
</section>
<section class="py-1 px-2 mb-3" id="boxInfoCostos">
    <div class="d-flex justify-content-center flex-wrap text-center align-items-center" style="gap: 15px;">
        
        <div class="dinero bg-white p-2 border">
            <b class="d-block" title="Movilidad asignada al cluster">MOV 1</b>
            <i class="far fa-money-bill-alt text-secondary"></i>
            <span id="moneyMov1" style="font-size: 15px !important;"></span>
        </div>
        <div class="dinero bg-white p-2 border">
            <b class="d-block" title="Movilidad asignada al cluster en la facturacion">MOV 2</b>
            <i class="far fa-money-bill-alt text-info"></i>
            <span id="moneyMov2" style="font-size: 15px !important;"></span>
        </div>
        <div class="dinero bg-white p-2 border">
            <b class="d-block">SUBTOTAL</b>
            <i class="far fa-money-bill-alt text-success"></i>
            <span id="moneySubTotal" style="font-size: 15px !important;"></span>
        </div>
        <div class="dinero bg-white p-2 border">
            <b class="d-block">I.G.V</b>
            <i class="far fa-money-bill-alt text-danger"></i>
            <span id="moneyIgv" style="font-size: 15px !important;"></span>
        </div>
        <div class="dinero bg-white p-2 border">
            <b class="d-block">TOTAL</b>
            <i class="fas fa-hand-holding-usd text-primary"></i>
            <span id="moneyTotal" style="font-size: 15px !important;"></span>
        </div>
        <div class="dinero p-2">
            <button class="btn btn-sm btn-primary" id="btn-facturar">
                <i class="fas fa-shopping-cart"></i>
                Facturar
            </button>
        </div>
    </div>
</section>
<section class="p-2 bg-white">
    <div class="form-group" id="boxNumGuias">
        <p class="mb-0 text-primary"><b>Total guías a facturar: </b><span id="totalFacturar"></span></p>
        <p><b>Guías mostradas: </b><span id="guiasMostradas"></span></p>
    </div>
    <div class="table-responsive">
    <table class="table table-sm table-bordered table-sm" id="tabla-guias">
        <thead>
            <tr>
                <th id="boxCheck">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="selectAll">
                        <label class="custom-control-label" for="selectAll"></label>
                    </div>
                </th>
                <th>Fecha de<br>Emisión</th>
                <th>N° de Guía</th>
                <th>Cliente</th>
                <th>Comprobantes</th>
                <th>N° Comprobantes</th>
                <th>Destinatario</th>
                <th>Ciudad Base</th>
                <th>Ciudad Final</th>
                <th>Peso Kg</th>
                <th>MOV 1</th>
                <th>MOV 2</th>
                <th>FACT<br>Final<br>Servicio</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
</section>
@endsection