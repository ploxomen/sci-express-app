@extends('general.index')
@section('head')
    <script src="{{asset('asset/manicrear.js')}}"></script>
    <title>Programar embarque</title>
@endsection
@section('body')
    <form id="frm_embarque" class="p-3 mb-3">
        <div class="my-4">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/manifiesto.png')}}" alt="Imagen de clientes">
            </div>
            <h3 class="text-primary text-center">Programar embarque</h3>
        </div>
        <fieldset class="bg-white px-3 border mb-2 form-row">
            <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">EMPRESA</legend>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_trayecto">Empresa</label>
                <select name="trayecto" id="id_trayecto" class="form-control">
                    <option value="CRUZ DEL SUR">CRUZ DEL SUR</option>
                    <option value=""></option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_cluster">Comprobantes</label>
                <select name="cluster" id="id_cluster" class="form-control cb-multiple" multiple>
                    <option value="GUIA TRANSPORTISTA">GUIA TRANSPORTISTA</option>
                    <option value="FACTURA">FACTURA</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_trayecto">N°</label>
                <select name="" id="" multiple class="form-control cb-multiple"></select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_trayecto">Importe Factura</label>
                <input type="number" class="form-control" step="0.01" min="0">
            </div>
        </fieldset>
        <fieldset class="bg-white px-3 border mb-2 form-row">
            <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">HORARIO</legend>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_trayecto">Fecha embarque</label>
                <input type="date" class="form-control">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_trayecto">Fecha llegada</label>
                <input type="date" class="form-control">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_trayecto">Hr llegada</label>
                <input type="time" class="form-control">
            </div>
        </fieldset>
        <fieldset class="bg-white px-3 border mb-2 form-row">
            <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">BULTOS</legend>
            <div class="form-group col-12">
                <div style="max-width: 100px; margin-left: auto;">
                    <input type="number" class="form-control" disabled>
                </div>
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_trayecto">Cajas</label>
                <input type="number" class="form-control" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_trayecto">Jabas</label>
                <input type="number" class="form-control" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_trayecto">Cajones</label>
                <input type="number" class="form-control" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_trayecto">Sobres</label>
                <input type="number" class="form-control" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_trayecto">Paquetes</label>
                <input type="number" class="form-control" min="0">
            </div>
            <div class="form-group col-12 col-md-3 col-lg-2">
                <label for="id_trayecto">Otros</label>
                <input type="number" class="form-control" min="0">
            </div>
            <div class="form-group col-12">
                <label for="">Detallar otro</label>
                <textarea class="form-control"></textarea>
            </div>
        </fieldset>
        <fieldset class="bg-white px-3 border mb-2 form-row">
            <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">ADICIONAL</legend>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_trayecto">Flete estimado</label>
                <input type="number" class="form-control" min="0" step="0.01">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_trayecto">Contribución marginal</label>
                <input type="text" class="form-control" readonly>
            </div>
        </fieldset>
        <div class="form-group">
            <button class="btn btn-primary" type="submit">Actualizar</button>
            <button class="btn btn-secondary" type="reset">Cancelar</button>
        </div>
    </form>
    <section class="p-3 bg-white">
        <h4 class="text-primary">Manifiestos para embarcar</h4>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Fc. Emitida</th>
                        <th>Trayecto</th>
                        <th>Prov. Origen</th>
                        <th>Prov. Destino</th>
                        <th>Cluster</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

            </table>
        </div>
    </section>
@endsection