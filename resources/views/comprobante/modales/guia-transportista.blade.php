<div class="modal fade" id="guia-transportista" data-backdrop="static" data-keyboard="false" tabindex="-1"
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
                <form id="guia-transportista-form" class="form-row">
                    <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                        <label for="txt-serie">Serie</label>
                        <select name="serie" class="form-control form-control-sm" id="txt-serie" required>
                            <option value="VT01" selected>VT01 - Terrestre</option>
                            <option value="VA01">VA01 - Aereo</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                        <label for="txt-fecha-emision-trans">Fecha emisión</label>
                        <input name="fecha_emision" id="txt-fecha-emision-trans" type="date"
                            class="form-control fom-control-sm" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                        <label for="txt-fecha-traslado-trans">Fecha traslado</label>
                        <input name="fecha_traslado" id="txt-fecha-traslado-trans" required type="date"
                            class="form-control fom-control-sm" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                        <label for="txt-transportista-impresion">Impresión</label>
                        <select name="impresion" id="txt-transportista-impresion" required
                            class="form-control fom-control-sm">
                            <option value="001">A4</option>
                            <option value="004" selected>TICKET 80mm</option>
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label for="cb-comprobantes-transportista">Comprobantes</label>
                        <select name="comprobantes[]" id="cb-comprobantes-transportista" disabled required
                            class="form-control comprobantes-guias" multiple></select>
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
                    <div class="form-group col-12 col-md-6">
                        <label for="cb-placa-prin">Placas</label>
                        <select name="placa_principal" class="form-control form-control-sm cb-relacionado" data-input="#txt-tuse-principal" data-value="tuse" id="cb-placa-prin"
                            required>
                            <option value="" hidden>Seleccione un vehículo</option>
                            @foreach ($vehiculos as $vehiculo)
                                <option value="{{ $vehiculo->placa }}" data-tuse="{{ $vehiculo->TUSE }}">{{ $vehiculo->placa }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="txt-tuse-principal">TUSE</label>
                        <input type="text" required id="txt-tuse-principal"
                            class="form-control form-control-sm" name="tuse_principal">
                    </div>
                    <div class="col-12">
                        <span class="!text-sm text-blue-500 !font-semibold">Vehículo secundario</span>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="cb-placa-secun">Placas</label>
                        <select name="placas_secun" class="form-control form-control-sm cb-relacionado" data-input="#txt-tuse-secun" data-value="tuse" id="cb-placa-secun"
                            >
                            <option value="" hidden>Seleccione un vehículo</option>
                            @foreach ($vehiculos as $vehiculo)
                                <option value="{{ $vehiculo->placa }}" data-tuse="{{ $vehiculo->TUSE }}">{{ $vehiculo->placa }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="txt-tuse-secun">TUSE</label>
                        <input type="text" id="txt-tuse-secun"
                            class="form-control form-control-sm" name="tuse_secun">
                    </div>
                    <div class="col-12">
                        <span class="!text-sm text-blue-500 !font-semibold">Conductor principal</span>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="cb-conductor-principal">Nombres completos</label>
                        <select name="conductor_principal" required class="form-control form-control-sm cb-relacionado"
                            data-input="#licencia-cond-prin,#numero-doc-cond-prin" data-value="licencia,documento" id="cb-conductor-principal">
                            <option value="" hidden>Seleccione un conductor</option>
                            @foreach ($conductores as $conductor)
                                <option value="{{ $conductor->id }}"
                                    data-licencia="{{ $conductor->driver_license }}" data-documento="{{ $conductor->n_document }}">{{ $conductor->name }}
                                    {{ $conductor->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="numero-doc-cond-prin" id="tipo-documento-conductor-prin">N° documento</label>
                        <input type="text" required id="numero-doc-cond-prin"
                            class="form-control form-control-sm" name="num_doc_cond_prin">
                    </div>
                    <div class="form-group mod-trans-priva col-12 col-md-6 col-lg-4">
                        <label for="licencia-cond-prin">Licencia</label>
                        <input type="text" required id="licencia-cond-prin" class="form-control form-control-sm"
                            name="licencia_conductor_principal">
                    </div>
                    <div class="col-12">
                        <span class="!text-sm text-blue-500 !font-semibold">Conductor secundario</span>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="cb-conductor-secundario">Nombres completos</label>
                        <select name="conductor_secundario" class="form-control form-control-sm cb-relacionado"
                            data-input="#licencia-cond-secun,#numero-doc-cond-secun" data-value="licencia,documento" id="cb-conductor-secundario">
                            <option value="" hidden>Seleccione un conductor</option>
                            @foreach ($conductores as $conductor)
                                <option value="{{ $conductor->id }}"
                                    data-licencia="{{ $conductor->driver_license }}" data-documento="{{ $conductor->n_document }}">{{ $conductor->name }}
                                    {{ $conductor->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="numero-doc-cond-secun" id="tipo-documento-conductor-secun">N° documento</label>
                        <input type="text" id="numero-doc-cond-secun"
                            class="form-control form-control-sm" name="num_doc_cond_secun">
                    </div>
                    <div class="form-group mod-trans-priva col-12 col-md-6 col-lg-4">
                        <label for="licencia-cond-secun">Licencia</label>
                        <input type="text" id="licencia-cond-secun" class="form-control form-control-sm"
                            name="licencia_conductor_secundario">
                    </div>
                    <div class="form-group col-12">
                        <label for="txt-observaciones">Observaciones</label>
                        <textarea name="observaciones" class="form-control form-control-sm" id="txt-observaciones" rows="5"></textarea>
                    </div>
                    <div class="form-group col-12 mod-trans-public">
                        <span class="!text-sm !font-semibold text-blue-500">Items</span>
                    </div>
                    <div class="form-group col-12">
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
                                <tbody id="items-guia-transportista"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 text-right">
                        <span class="text-blue-500">Bultos totales (KG)</span>
                        <span class="text-black !text-base" id="bulto-total-transportista"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="guia-transportista-form">Generar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
