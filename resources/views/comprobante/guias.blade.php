@extends('general.index')
@section('head')
    <script src="{{asset('asset/guias.js')}}"></script>
    <title>Guias</title>
@endsection
@section('body')
<style>
    .title-label{
        font-size: 20px;
    }
</style>
<section class="p-3">
    <form id="frm_guia" class="mb-4 row">
        <div class="my-3 col-12">
            <div class="baner-modulo form-group">
                <img src="{{asset('asset/img/intranet/bill.png')}}" alt="Imagen de guia">
            </div>
            <div class="d-flex justify-content-center flex-wrap" style="gap: 0 20px;">
                <div>
                    <input type="radio" id="select-guia" value="nro_guia" name="select_comprobantes" checked data-value="{{$nro_guia_result}}" data-action="guia">
                    <label for="select-guia" class="text-primary title-label">Guías</label>
                </div>
                <div>
                    <input type="radio" value="nro_or" name="select_comprobantes" id="select-os" data-value="{{$nro_os}}" data-action="os">
                    <label for="select-os" class="text-primary title-label">Ordenes de Servicio</label>
                </div>
            </div>
        </div>
        <div class="col-12 p-0 px-lg-2 mb-4">
            <div class="bg-white p-3 form-row">
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_nro_guia">N° Guía <small class="text-danger">*</small></label>
                    <input type="text" id="id_nro_guia" pattern="[0-9]{3}[-][0-9]{7}" required name="nro_guia" maxlength="11" class="form-control" value="{{$nro_guia_result}}">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_resp_pago">Responsable del pago <small class="text-danger">*</small></label>
                    <select name="resp_pago" id="id_resp_pago" class="form-control" required>
                        <option value="REMITENTE">REMITENTE</option>
                        <option value="DESTINATARIO">DESTINATARIO</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_estado">Tipo <small class="text-danger">*</small></label>
                    <select name="estado" id="id_estado" class="form-control" required>
                        <option value="1">ACTIVA</option>
                        <option value="3">INACTIVA</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_fech_emis">Fecha Emisión <small class="text-danger">*</small></label>
                    <input type="date" class="form-control" value="{{$hoy}}" min="{{$antes}}" required max="{{$hoy}}" name="fech_emis" id="id_fech_emis">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_comp_clie">Comprobantes <small class="text-danger data-anulada-oculta">*</small></label>
                    <select name="comp_clie[]" id="id_comp_clie" required class="form-control data-anulada" multiple>
                        <option value="BOLETA ELECTRÓNICA">BOLETA ELECTRÓNICA</option>
                        <option value="FACTURA ELECTRÓNICA">FACTURA ELECTRÓNICA</option>
                        <option value="GUIA DE REMISIÓN (R) ELECTRÓNICA">GUIA DE REMISIÓN (R) ELECTRÓNICA</option>
                        <option value="GUIA DE REMISIÓN (T) ELECTRONICA">GUIA DE REMISIÓN (T) ELECTRÓNICA</option>
                        <option value="GUIA DE INTERNAMIENTO">GUIA DE INTERNAMIENTO</option>
                        <option value="ORDEN DE COMPRA">ORDEN DE COMPRA</option>
                        <option value="OTRO" class="delete-option">OTRO</option>
                        <option value="NINGUNO" class="delete-option">NINGUNO</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_nro_comp_cli">N° comprobante <small class="text-danger data-anulada-oculta">*</small></label>
                    <select name="nro_comp_cli[]" id="id_nro_comp_cli" required class="form-control data-anulada" multiple></select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_cant_comp">Cantidad Comprobante <small class="text-danger data-anulada-oculta">*</small></label>
                    <input type="text" class="form-control data-anulada" value="0" name="cant_comp" required id="id_cant_comp">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_orde_comp">Orden de compra</label>
                    <input type="text" class="form-control" name="orde_comp" id="id_orde_comp">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_fech_venc_orde_comp">Vto OC</label>
                    <input type="date" class="form-control" name="fech_venc_orde_comp" id="id_fech_venc_orde_comp">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_fech_tras">Fecha Traslado <small class="text-danger">*</small></label>
                    <input type="date" class="form-control" min="{{$antes}}" value="{{$hoy}}" required name="fech_tras" id="id_fech_tras">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_tray_env">Trayecto Envio <small class="text-danger">*</small></label>
                    <select name="tray_env" id="id_tray_env" class="form-control" required>
                        @foreach ($trayecto as $tra)
                            <option value="{{$tra->id}}">{{$tra->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_nro_manifiesto">Vía Envío <small class="text-danger">*</small></label>
                    <select name="via_envi" id="id_via_envi" class="form-control" required>
                        @foreach ($via_envios as $via)
                            <option value="{{$via->id}}">{{$via->envio}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_via_tras">Via Traslado(Facturación) <small class="text-danger data-anulada-oculta">*</small></label>
                    <select name="via_tras" id="id_via_tras" class="form-control data-anulada" required>
                        @foreach ($via_envios as $via)
                            <option value="{{$via->id}}">{{$via->envio}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-12 p-0 px-lg-2 col-lg-6 mb-4">
            <fieldset class="px-3 bg-white border form-row">
                <legend class="d-inline-block bg-white w-auto px-2 border shadow-sm text-left legend-add text-info">REMITENTE</legend>
                <div class="form-group col-12">
                    <label for="id_remi_nomb">Nombres <small class="text-danger">*</small></label>
                    <select class="form-control simple-select2" name="id_remitente" id="id_remi_nomb" required>
                        <option></option>
                        @foreach ($clientes as $cli)
                            <option data-type="cliente" value="{{$cli->id}}">{{$cli->nom_cli}}</option>
                            @if (!empty($cli->proveedores))
                                <optgroup label="{{$cli->nom_cli}}">
                                @foreach ($cli->proveedores as $prov)
                                    <option data-type="proveedor" data-id-cliente="{{$cli->id}}" value="{{$prov->id}}">{{$prov->nomb_prov}}</option>
                                @endforeach
                                </optgroup>

                            @endif                               
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_remi_tipo_docu">Tipo documento <small class="text-danger label-disabled-os">*</small></label>
                    <select name="remi_tipo_docu" id="id_remi_tipo_docu" required class="form-control simple-select2 cb-tipo-doc input-disabled-os" data-input="id_remi_nro_docu" data-valor-cliente="doc_ident" data-valor-proveedor="tipo_docu_prov">
                        <option></option>
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_remi_nro_docu">N° documento <small class="text-danger label-disabled-os">*</small></label>
                    <input type="text" class="form-control input-disabled-os" name="remi_nro_docu" required id="id_remi_nro_docu" data-valor-cliente="nro_doc" data-valor-proveedor="nro_docu_prov">
                </div>
                <div class="form-group col-12">
                    <label for="id_remi_dire">Dirección <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" name="remi_dire" required id="id_remi_dire" data-valor-cliente="dir_sede_so" data-valor-proveedor="dire_prov">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_remi_depa">Departamento <small class="text-danger">*</small></label>
                    <select class="form-control cb-ubigeo" name="remi_depa" required id="id_remi_depa" data-consulta="provincia" data-info="id_remi_prov" data-valor-proveedor="depa_prov" data-info-distrito="id_remi_dist" data-valor-cliente="dpto_so">
                        <option value="" hidden>Seleccione un departamento</option>
                        @foreach ($departamentos as $depar)
                            <option value="{{$depar->id_ubigeo}}">{{$depar->nombre_ubigeo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_remi_prov">Provincia <small class="text-danger">*</small></label>
                    <select class="form-control cb-ubigeo" name="remi_prov" required id="id_remi_prov" data-consulta="distrito" data-info="id_remi_dist" data-valor-proveedor="prov_prov" data-valor-cliente="prov_so">
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="">Distrito <small class="text-danger">*</small></label>
                    <select class="form-control cb-ubigeo" name="remi_dist" required id="id_remi_dist" data-valor-cliente="dist_so" data-valor-proveedor="dist_prov">
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="id-invitado-remitente">Invitado</label>
                    <select name="id_invitado" id="id-invitado-remitente" data-cuenta="#cc-invitado-remitente" class="simple-select2">
                        <option value="ninguno" selected>Ninguno</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="cc-invitado-remitente">CC. Invitado</label>
                    <select name="cc_invitado" id="cc-invitado-remitente" required disabled class="simple-select2"></select>
                </div>
            </fieldset>
        </div>
        <div class="col-12 p-0 px-lg-2 col-lg-6 mb-4">
            <fieldset class="px-3 bg-white border form-row">
                <legend class="d-inline-block bg-white w-auto px-2 border shadow-sm text-left legend-add text-info">DESTINATARIO</legend>
                <div class="form-group col-12">
                    <label for="id_dest_nomb">Nombres <small class="text-danger">*</small></label>
                    <select class="form-control cb-select2-tags" required name="id_destinatario" id="id_dest_nomb">
                        <option></option>
                        @foreach ($cli_cli as $c)
                            <option value="{{$c->id}}">{{$c->nombres}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_dest_tipo_docu">Tipo documento <small class="text-danger label-disabled-os">*</small></label>
                    <select name="dest_tipo_docu" id="id_dest_tipo_docu" required class="form-control simple-select2 cb-tipo-doc input-disabled-os" data-input="id_dest_nro_docu" data-cli-cli="tipo_doc">
                        <option></option>                        
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_dest_nro_docu">N° documento <small class="text-danger label-disabled-os">*</small></label>
                    <input type="text" class="form-control input-disabled-os" name="dest_nro_docu" required id="id_dest_nro_docu" data-cli-cli="nro_doc">
                </div>
                
                <div class="form-group col-12">
                    <label for="id_dest_dire">Dirección <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" name="dest_dire" required id="id_dest_dire" data-cli-cli="direccion">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_dest_depa">Departamento <small class="text-danger">*</small></label>
                    <select name="dest_depa" id="id_dest_depa" required class="form-control cb-ubigeo cb-dest" data-cli-cli="departamento" data-consulta="provincia" data-info="id_dest_prov" data-info-distrito="id_dest_dist">
                        <option value="" hidden>Seleccione un departamento</option>
                        @foreach ($departamentos as $depar)
                            <option value="{{$depar->id_ubigeo}}">{{$depar->nombre_ubigeo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_dest_prov">Provincia <small class="text-danger">*</small></label>
                    <select name="dest_prov" id="id_dest_prov" required class="form-control cb-ubigeo cb-dest" data-cli-cli="provincia" data-consulta="distrito" data-info="id_dest_dist"></select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_dest_dist">Distrito <small class="text-danger">*</small></label>
                    <select name="dest_dist" id="id_dest_dist" required class="form-control cb-ubigeo cb-dest calculo-dia" data-ciudades="#id_ciudad_destino_final" data-cli-cli="distrito">
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_ciudad_destino_final">Ciudad Final</label>
                    <select name="id_ciudad_final" id="id_ciudad_destino_final" data-distrito="#id_dest_dist" data-cli-cli="id_ciudad_final" class="form-control simple-select2 cb-dest calculo-dia">
                    </select>
                </div>
            </fieldset>
        </div>
        <div class="col-12 p-0 px-lg-2 mb-4">
            <div class="bg-white p-3 form-row">
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_dias_tran">Días de tránsito <small class="text-danger">*</small></label>
                    <input type="number" min="1" class="form-control" required name="dias_tran" id="id_dias_tran">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3" id="box-complejo-distante" data-texto="#text-complejo-distante" data-input="#id-tipo-complejo-distante" hidden>
                    <label for="id-tipo-complejo-distante"><span id="text-complejo-distante"></span> <small class="text-danger">*</small></label>
                    <input type="number" min="0" class="form-control" name="valor_complejo_distante" id="id-tipo-complejo-distante" disabled>
                </div>
            </div>
        </div>
        <div class="col-12 p-0 px-lg-2 form-group">
            <div class="form-group text-right">
                <button type="button" class="btn btn-primary mr-2" title="Agregar detalle" id="add-new-detalle"><i class="fas fa-plus"></i></button>
                <button type="button" class="btn btn-danger" title="Eliminar detalle" id="add-delete-detalle"><i class="fas fa-minus"></i></button>
            </div>
            <div class="p-3 bg-white table-responsive">
                <table class="table table-sm table-bordered" style="min-width: 1350px;">
                    <thead>
                        <tr>
                            <th style="width: 200px;">Grupo Envio</th>
                            <th style="width: 100px;">Tipo Envio</th>
                            <th style="min-width: 250px;">Descripción</th>
                            <th style="min-width: 200px;">Envase o <br> Embalaje</th>
                            <th style="width: 100px;">Cantidad</th>
                            <th style="width: 100px;">Kg</th>
                            <th style="width: 100px;">Lar / cm</th>
                            <th style="width: 100px;">Anc / cm</th>
                            <th style="width: 100px;">Alt / cm</th>
                            <th style="width: 100px;">Peso Vol / cm<sup>3</sup></th>
                        </tr>
                    </thead>
                    <tbody id="tabla-detalle">
                            <tr>
                                <td>
                                    <select required data-name="grupo_envio" class="data-anulada form-control form-control-sm simple-select2">
                                        <option value="" hidden>Seleccione una opción</option>
                                        @foreach ($grupoEnvio as $grupo)
                                            <option value="{{$grupo}}">{{$grupo}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select required data-name="tipo_envio" class="data-anulada form-control form-control-sm simple-select2">
                                        <option value="" hidden>Seleccione una opción</option>
                                        @foreach ($tipoEnvios as $tipoEnvio)
                                            <option value="{{$tipoEnvio->id}}">{{$tipoEnvio->tipo_envio}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <textarea data-name="descripcion" class="form-control form-control-sm data-texto" rows="1"></textarea>
                                </td>
                                <td>
                                    <select required data-name="embase_embalaje" class="data-anulada form-control form-control-sm simple-select2">
                                        <option></option>
                                        @foreach ($embaseEmbalaje as $embase)
                                            <option value="{{$embase}}">{{$embase}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input required data-name="cantidad" type="number" value="0" class="data-anulada data-anulada-int form-control form-control-sm" onchange="calcTotal(event,4,'total-cantidad')" min="1">
                                </td>
                                <td>
                                    <input required data-name="kg" type="number" value="0.00" class="data-anulada data-anulada-decimal form-control form-control-sm" onchange="calcTotal(event,5,'total-kg')" min="0.01" step="0.01">
                                </td>
                                <td>
                                    <input required data-name="largo" type="number" value="0.00" class="data-anulada data-anulada-decimal form-control form-control-sm" onchange="calcVolumen(event)" min="0.01" step="0.01">
                                </td>
                                <td>
                                    <input required data-name="ancho" type="number" value="0.00" class="data-anulada data-anulada-decimal form-control form-control-sm" onchange="calcVolumen(event)" min="0.01" step="0.01">
                                </td>
                                <td>
                                    <input required data-name="altura" type="number" value="0.00" class="data-anulada data-anulada-decimal form-control form-control-sm" onchange="calcVolumen(event)" min="0.01" step="0.01">
                                </td>
                                <td>
                                    <input required data-name="volumen" type="number" value="0.00" class="data-anulada data-anulada-decimal form-control form-control-sm" onchange="calcTotal(event,9,'total-vol')" min="0.01" step="0.01">
                                </td>
                            </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right">
                                Totales
                            </td>
                            <td>
                                <input type="number" value="0" id="total-cantidad" class="form-control form-control-sm" min="0">
                            </td>
                            <td>
                                <input type="number" value="0.00" id="total-kg" class="form-control form-control-sm" min="0" step="0.01">
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <input type="number" value="0.00" id="total-vol" class="form-control form-control-sm" min="0" step="0.01">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="col-12 form-group p-0 px-lg-2">
            <button type="submit" class="btn btn-success">Generar</button>
        </div>
    </form>
</section>
@endsection