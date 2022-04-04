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
                <label for="id_embarque_empresa">Empresa</label>
                <input type="text" id="id_embarque_empresa" class="form-control" disabled>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_comprobantes">Comprobantes</label>
                <select name="comprobantes[]" id="id_comprobantes" class="form-control simple-select2" multiple required>
                    <option value="GUIA TRANSPORTISTA">GUIA TRANSPORTISTA</option>
                    <option value="FACTURA">FACTURA</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_nro_comprobantes">N°</label>
                <select name="nro_comprobantes[]" id="id_nro_comprobantes" multiple class="form-control cb-select2-tags"></select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_importe_factura">Importe Factura</label>
                <input type="number" name="importe_factura" id="id_importe_factura" class="form-control" step="0.01" min="0">
            </div>
        </fieldset>
        <fieldset class="bg-white px-3 border mb-2 form-row">
            <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">HORARIO</legend>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_embarque_fch_emba">Fecha embarque</label>
                <input type="date" id="id_embarque_fch_emba" class="form-control" name="fch_emba" disabled>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_fecha_llegada">Fecha llegada</label>
                <input type="date" name="fecha_llegada" id="id_fecha_llegada" class="form-control" required>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_hr_llegada">Hr llegada</label>
                <input type="time" name="hr_llegada" id="id_hr_llegada" class="form-control" required>
            </div>
        </fieldset>
        <fieldset class="bg-white px-3 border mb-2 form-row">
            <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">BULTOS</legend>
            <div class="form-group col-12">
                <div style="max-width: 150px; margin-left: auto;">
                    <input type="number" id="total_bultos" min="0" class="form-control" readonly>
                </div>
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_nro_cajas">Cajas</label>
                <input type="number" name="nro_cajas" id="id_nro_cajas" class="form-control calc-total" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_nro_jabas">Jabas</label>
                <input type="number" name="nro_jabas" id="id_nro_jabas" class="form-control calc-total" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_nro_cajones">Cajones</label>
                <input type="number" name="nro_cajones" id="id_nro_cajones" class="form-control calc-total" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_nro_sobres">Sobres</label>
                <input type="number" name="nro_sobres" id="id_nro_sobres" class="form-control calc-total" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_nro_paquetes">Paquetes</label>
                <input type="number" name="nro_paquetes" id="id_nro_paquetes" class="form-control calc-total" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_num_otros">Otros</label>
                <input type="number" name="num_otros" id="id_num_otros" class="form-control calc-total" min="0">
            </div>
            <div class="form-group col-12">
                <label for="id_detalle_otro">Detallar otro</label>
                <textarea name="detalle_otro" maxlength="255" id="id_detalle_otro" class="form-control"></textarea>
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
            <button class="btn btn-primary" type="submit" id="btn_enviar_informe" hidden>Actualizar</button>
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
@endsection