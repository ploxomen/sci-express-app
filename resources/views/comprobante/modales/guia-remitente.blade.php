<div class="modal fade" id="guia-remitente" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Guía remisión remitente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="guia-remitente-form" class="form-row">
                    <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                        <label for="txt-serie">Serie</label>
                        <select name="serie" class="form-control form-control-sm" id="txt-serie" required>
                            <option value="TT01" selected>TT01 - Terrestre</option>
                            <option value="TA01">TA01 - Aereo</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                        <label for="txt-fecha-emision">Fecha emisión</label>
                        <input name="fecha_emision" id="txt-fecha-emision" type="date"
                            class="form-control fom-control-sm" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                        <label for="txt-fecha-traslado">Fecha traslado</label>
                        <input name="fecha_traslado" id="txt-fecha-traslado" required type="date"
                            class="form-control fom-control-sm" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                        <label for="txt-motivo">Motivo</label>
                        <select name="motivo_traslado" class="form-control form-control-sm" id="txt-motivo" required>
                            <option value="01">VENTA</option>
                            <option value="05">CONSIGNACION</option>
                            <option value="14">VENTA SUJETA A CONFIRMACION DEL COMPRADOR</option>
                            <option value="02" selected>COMPRA</option>
                            <option value="04">TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA</option>
                            <option value="18">TRASLADO EMISOR ITINERANTE CP</option>
                            <option value="08">IMPORTACION</option>
                            <option value="09">EXPORTACION</option>
                            <option value="13">OTROS</option>
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label for="txt-partida">Punto partida</label>
                        <input type="text" name="punto_partida" class="form-control form-control-sm" id="txt-partida"
                            required>
                    </div>
                    <div class="form-group col-12">
                        <label for="txt-llegada">Punto llegada</label>
                        <input type="text" name="punto_llegada" class="form-control form-control-sm" id="txt-llegada"
                            required>
                    </div>
                    <div class="col-12">
                        <span class="!text-sm text-blue-500 !font-semibold">Datos del destinatario</span>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-3">
                        <label for="txt-dest-tipo-doc">Tipo Documento</label>
                        <select name="dest_tipo_doc" class="form-control form-control-sm" id="txt-dest-tipo-doc"
                            disabled required>
                            @foreach ($tipo_documentos as $tipo_docu)
                                <option value="{{ $tipo_docu->id }}">{{ $tipo_docu->tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-3">
                        <label for="txt-dest-num-doc">N° Documento</label>
                        <input type="text" name="dest_num_doc" class="form-control form-control-sm"
                            id="txt-dest-num-doc" disabled required>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="txt-dest-razon-social">Razón social</label>
                        <input type="text" name="dest_razon_social" class="form-control form-control-sm"
                            id="txt-dest-razon-social" disabled required>
                    </div>
                    <div class="col-12">
                        <span class="!text-sm text-blue-500 !font-semibold">Datos traslado</span>
                    </div>
                    <div class="form-group col-12">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="mod-trans-public" name="transporte"
                                class="custom-control-input cambio-mod-traslado" value="01" checked>
                            <label class="custom-control-label" for="mod-trans-public">Transporte público</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="mod-trans-priva" name="transporte"
                                class="custom-control-input cambio-mod-traslado" value="02">
                            <label class="custom-control-label" for="mod-trans-priva">Transporte privado</label>
                        </div>
                    </div>
                    <div class="form-group col-12">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="categoria_m1" value="1"
                                id="customCheck1">
                            <label class="custom-control-label" for="customCheck1">Traslado en vehículos de categoría
                                M1 o L</label>
                        </div>
                    </div>
                    <div class="col-12 mod-trans-public">
                        <span class="!text-sm text-blue-500 !font-semibold">Datos transporte público</span>
                    </div>
                    <div class="form-group mod-trans-public col-12 col-md-6">
                        <label for="cb-empre-nombre">Razon social</label>
                        <select name="empresa_transporte" class="form-control form-control-sm cb-relacionado" required
                            id="cb-empre-nombre" data-input="#ruc-empresa-trans" data-value="ruc">
                            <option value="" hidden>Seleccione una empresa de transporte</option>
                            @foreach ($empresasTransporte as $empresa)
                                <option value="{{ $empresa->id }}" data-ruc="{{ $empresa->nro_ruc }}">
                                    {{ $empresa->empresa }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mod-trans-public col-12 col-md-6">
                        <label for="ruc-empresa-trans">RUC</label>
                        <input required type="text" id="ruc-empresa-trans" class="form-control form-control-sm"
                            name="ruc_empresa_transporte">
                    </div>
                    <div class="col-12 mod-trans-priva" hidden>
                        <span class="!text-sm text-blue-500 !font-semibold">Datos transporte privado</span>
                    </div>
                    <div class="form-group mod-trans-priva col-12 col-md-6 col-lg-4" hidden>
                        <label for="cb-vehiculo-transporte">Vehículos - placa</label>
                        <select name="placa" class="form-control form-control-sm" id="cb-vehiculo-transporte" disabled required>
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
                        <input type="text" disabled required id="licencia-conductor" class="form-control form-control-sm"
                            name="licencia_conductor">
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
