@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/informeembarque.js')}}"></script>
    <title>Informe de embarque</title>
@endsection
@section('body')
    <form id="form_embarque_informe" class="p-3 mb-3">
        <div class="my-4">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/manifiesto.png')}}" alt="Imagen de clientes">
            </div>
            <h3 class="text-primary text-center">Informe de embarque</h3>
        </div>
        <fieldset class="bg-white px-3 border mb-2 form-row">
            <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">TRANSPORTE</legend>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_embarque_empresa">Empresa <small class="text-danger">*</small></label>
                <input type="text" id="id_embarque_empresa" class="form-control" disabled required>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_comprobantes">Comprobantes <small class="text-danger">*</small></label>
                <select name="comprobantes[]" id="id_comprobantes" class="form-control simple-select2" multiple required>
                    <option value="GUIA TRANSPORTISTA">GUIA TRANSPORTISTA</option>
                    <option value="FACTURA">FACTURA</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_nro_comprobantes">N° Comprobantes <small class="text-danger">*</small></label>
                <select name="nro_comprobantes[]" id="id_nro_comprobantes" required multiple class="form-control cb-select2-tags"></select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_importe_factura">Importe Factura <small class="text-danger">*</small></label>
                <input type="number" name="importe_factura" required id="id_importe_factura" class="form-control" step="0.01" min="0">
            </div>
        </fieldset>
        <fieldset class="bg-white px-3 border mb-2 form-row">
            <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">HORARIO</legend>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_embarque_fch_emba">Fecha embarque <small class="text-danger">*</small></label>
                <input type="date" id="id_embarque_fch_emba" required class="form-control" name="fch_emba" disabled>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_fecha_llegada">Fecha llegada <small class="text-danger">*</small></label>
                <input type="date" name="fecha_llegada" id="id_fecha_llegada" class="form-control" required>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_hr_llegada">Hr llegada <small class="text-danger">*</small></label>
                <input type="time" name="hr_llegada" id="id_hr_llegada" class="form-control" required>
            </div>
        </fieldset>
        <fieldset class="bg-white px-3 border mb-2 form-row">
            <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">BULTOS TOTAL</legend>
            <div class="form-group col-12">
                <div style="max-width: 150px; margin-left: auto;">
                    <input type="number" id="id_embarque_total_bultos_paquete" min="1" class="form-control" readonly>
                </div>
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_nro_cajas">Cajas</label>
                <input type="number" disabled id="id_embarque_nro_cajas" class="form-control calc-total" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_nro_jabas">Jabas</label>
                <input type="number" disabled id="id_embarque_nro_jabas" class="form-control calc-total" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_nro_cajones">Cajones</label>
                <input type="number" disabled id="id_embarque_nro_cajones" class="form-control calc-total" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_nro_sobres">Sobres</label>
                <input type="number" disabled id="id_embarque_nro_sobres" class="form-control calc-total" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_nro_paquetes">Paquetes</label>
                <input type="number" disabled id="id_embarque_nro_paquetes" class="form-control calc-total" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_num_otros">Otros</label>
                <input type="number" disabled id="id_embarque_num_otros" class="form-control calc-total" min="0">
            </div>
            <div class="form-group col-12">
                <label for="id_detalle_otro">Detallar otro</label>
                <textarea disabled maxlength="255" id="id_embarque_detalle_otro" class="form-control"></textarea>
            </div>
        </fieldset>
        {{-- <fieldset class="bg-white px-3 border mb-2 form-row">
            <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">ADICIONAL</legend>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_embarque_flet_esti">Flete estimado</label>
                <input type="number" name="flet_esti" id="id_embarque_flet_esti" class="form-control" min="0" step="0.01" required>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_contribucion_marginal">Contribución marginal</label>
                <input type="text" name="contribucion_marginal" id="id_contribucion_marginal" class="form-control" required>
            </div>
        </fieldset> --}}
        <div class="form-group">
            <button class="btn btn-primary" type="submit" id="btn_enviar_informe" hidden>Siguiente</button>
            <button class="btn btn-secondary" type="reset" id="btn_clear_informe" hidden>Cancelar</button>
        </div>
    </form>
    <section class="p-3 bg-white">
        <div class="form-group">
            <h4 class="text-primary">Informe de embarques pendientes</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped" id="embarques-pendientes">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Transporte</th>
                        <th>Fecha Emb</th>
                        <th>Hr Inicio</th>
                        <th>Hr Estimada</th>
                        <th>Hr Fin</th>
                        <th>Bulto</th>
                        <th>KPI</th>
                        <th>Flete</th>
                        <th>Tot Mani</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="listas-embarque"></tbody>
            </table>
        </div>
    </section>
    @include('modal.insertarguiasembarque')
@endsection