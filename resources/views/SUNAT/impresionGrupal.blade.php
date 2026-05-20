@extends('general.index')
@section('head')
    <script src="{{ asset('asset/homedatatable.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('library/datatable/datatable-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatable/responsive-bootstrap.min.css') }}">
    <script src="{{ asset('library/datatable/jq-datatable.min.js') }}"></script>
    <script src="{{ asset('library/datatable/datatable-bootstrap4.min.js') }}"></script>
    <script src="{{ asset('library/datatable/datatable-responsive.min.js') }}"></script>
    <script src="{{ asset('library/datatable/responsive-bootstrap.min.js') }}"></script>
    <script src="{{ asset('asset/SUNAT/impresionMasiva.js?v1') }}"></script>
    <title>Impresión Masiva - SUNAT</title>
@endsection
@section('body')
    <section class="p-2 bg-white">
        <h3 class="text-primary"><i class="fas fa-caret-right"></i> Impresión Masiva - SUNAT</h3>
        <form class="form-row" id="frm-aplicar-filtros">
            <div class="form-group col-6 col-lg-3 col-xl-2">
                <label for="fecha_inicio">Fecha Inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" value="{{ $fechaAnterior }}" required>
            </div>
            <div class="form-group col-6 col-lg-3 col-xl-2">
                <label for="id_fecha_fin">Fecha Fin</label>
                <input type="date" class="form-control" id="id_fecha_fin" value="{{ $fechaActual }}" required>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                <label for="tipo_comprobantes">Tipos</label>
                <select class="form-control cb-filtros" id="tipo_comprobantes" required>
                    <option value="0" selected>TODOS</option>
                    <option value="GUIA REMISION REMITENTE">GUIA REMISION REMITENTE</option>
                    <option value="GUIA REMISION TRANSPORTISTA">GUIA REMISION TRANSPORTISTA</option>
                </select>
            </div>
            
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-6">
                <button type="submit" class="btn btn-sm btn-light" data-toggle="modal" data-target="#asignacion-comprobantes" title="Aplicar los filtros"><i
                        class="fas fa-search"></i></button>
                <button type="button" class="btn btn-sm btn-primary" data-type="{{ App\Models\ComprobantesSimpleFact::IMPRESION_TICKET }}" title="Imprimir ticket" id="imprimir-comp-tic"><i class="fas fa-print"></i> TICKET</button>
                <button type="button" class="btn btn-sm btn-primary" data-type="{{ App\Models\ComprobantesSimpleFact::IMPRESION_A4 }}" title="Imprimir ticket" id="imprimir-comp-a4"><i class="fas fa-print"></i> A4</button>
            </div>
        </form>
        <section id="comprobantes-seleccionados" class="!flex gap-2 flex-wrap mb-2"></section>
        <table class="table table-sm table-bordered" id="tabla-guias">
            <thead>
                <tr>
                    <th>
                        Selec.
                    </th>
                    <th>N° Comprobante SUNAT</th>
                    <th>Fecha Emisión</th>
                    <th>N° Guia - SCI</th>
                    <th>RUC</th>
                    <th>RAZON SOCIAL</th>
                    <th>Tipo comprobante</th>
                </tr>
            </thead>
            <tbody id="tabla-detalle-guias">
                <tr>
                    <td colspan="100%" class="text-center">Sin resultados</td>
                </tr>
            </tbody>
        </table>
    </section>
    <x-loader></x-loader>
@endsection
