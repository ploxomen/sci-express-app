<div class="modal fade" id="guia-remitente" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Guía remisión transportista</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="guia-remitente-form" class="form-row">
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="txt-fecha-emision-trans">Fecha emisión</label>
                        <input name="fecha_emision" id="txt-fecha-emision-trans" type="date"
                            class="form-control fom-control-sm" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="txt-fecha-traslado-trans">Fecha traslado</label>
                        <input name="fecha_traslado" id="txt-fecha-traslado-trans" required type="date"
                            class="form-control fom-control-sm" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="cb-comprobantes-transportista">Comprobantes</label>
                        <select name="comprobantes[]" id="cb-comprobantes-transportista" disabled required
                            class="form-control data-anulada" multiple></select>
                    </div>
                    <div class="col-12">
                        <span class="!text-sm text-blue-500 !font-semibold">Datos del remitente</span>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-3">
                        <label for="txt-emi-tipo-doc-trans">Tipo Documento</label>
                        <select name="emi_tipo_doc" class="form-control form-control-sm" id="txt-emi-tipo-doc-trans" disabled
                            required>
                            @foreach ($tipo_documentos as $tipo_docu)
                                <option value="{{ $tipo_docu->id }}">{{ $tipo_docu->tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-3">
                        <label for="txt-emi-num-doc-trans">N° Documento</label>
                        <input type="text" name="emi_num_doc" class="form-control form-control-sm"
                            id="txt-emi-num-doc-trans" disabled required>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="txt-emi-razon-social-trans">Razón social</label>
                        <input type="text" name="emi_razon_social" class="form-control form-control-sm"
                            id="txt-emi-razon-social-trans" disabled required>
                    </div>
                    <div class="form-group col-12">
                        <label for="txt-partida-trans">Punto partida</label>
                        <input type="text" name="punto_partida" class="form-control form-control-sm" id="txt-partida-trans"
                            required>
                    </div>
                    <div class="col-12">
                        <span class="!text-sm text-blue-500 !font-semibold">Datos del destinatario</span>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-3">
                        <label for="txt-dest-tipo-doc-trans">Tipo Documento</label>
                        <select name="dest_tipo_doc" class="form-control form-control-sm" id="txt-dest-tipo-doc-trans"
                            disabled required>
                            @foreach ($tipo_documentos as $tipo_docu)
                                <option value="{{ $tipo_docu->id }}">{{ $tipo_docu->tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-3">
                        <label for="txt-dest-num-doc-trans">N° Documento</label>
                        <input type="text" name="dest_num_doc" class="form-control form-control-sm"
                            id="txt-dest-num-doc-trans" disabled required>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="txt-dest-razon-socia-transl">Razón social</label>
                        <input type="text" name="dest_razon_social" class="form-control form-control-sm"
                            id="txt-dest-razon-social-trans" disabled required>
                    </div>
                    <div class="form-group col-12">
                        <label for="txt-llegada-trans">Punto llegada</label>
                        <input type="text" name="punto_llegada" class="form-control form-control-sm" id="txt-llegada-trans"
                            required>
                    </div>
                    <div class="col-12">
                        <span class="!text-sm text-blue-500 !font-semibold">Vehículo principal</span>
                    </div>
                    <div class="form-group mod-trans-priva col-12 col-md-6">
                        <label for="cb-vehiculo-transporte-trans">Placas</label>
                        <select name="placa" class="form-control form-control-sm" id="cb-vehiculo-transporte-trans"
                            disabled required>
                            <option value="" hidden>Seleccione un vehículo</option>
                            @foreach ($vehiculos as $vehiculo)
                                <option value="{{ $vehiculo->placa }}">{{ $vehiculo->placa }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mod-trans-priva col-12 col-md-6 col-lg-4" hidden>
                        <label for="cb-conductor-transporte">Conductor</label>
                        <select name="conductor" disabled required class="form-control form-control-sm cb-relacionado"
                            data-input="#licencia-conductor" data-value="licencia" id="cb-conductor-transporte">
                            <option value="" hidden>Seleccione un conductor</option>
                            @foreach ($conductores as $conductor)
                                <option value="{{ $conductor->id }}"
                                    data-licencia="{{ $conductor->driver_license }}">{{ $conductor->name }}
                                    {{ $conductor->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mod-trans-priva col-12 col-md-6 col-lg-4" hidden>
                        <label for="licencia-conductor">Licencia</label>
                        <input type="text" disabled required id="licencia-conductor"
                            class="form-control form-control-sm" name="licencia_conductor">
                    </div>
                    <div class="col-12 mod-trans-public">
                        <span class="!text-sm !font-semibold text-blue-500">Items</span>
                    </div>
                    <div class="form-group col-12 mt-4">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>DESCRIPCION</th>
                                        <th>CANTIDAD</th>
                                        <th>UNI</th>
                                    </tr>
                                </thead>
                                <tbody id="items-guia-remitente"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 text-right">
                        <span class="text-blue-500">Bultos totales (KG)</span>
                        <span class="text-black !text-base" id="bulto-total-remitente"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="guia-remitente-form">Generar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
