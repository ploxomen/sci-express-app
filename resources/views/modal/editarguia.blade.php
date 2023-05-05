<div class="modal fade" id="editar-guias" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <span class="modal-title" id="titulo-guia-editar"></span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form enctype="multipart/form-data">
            <input type="file" hidden id="sendNewImg" accept=".png, .jpg, .jpeg" multiple>
        </form>
        <form class="form-row form-editar-guia" id="update-guia-complete">
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                <label for="id_editar__nro_guia" id="label_nro_guia"></label>
                <input type="text" id="id_editar__nro_guia" required name="nro_guia" maxlength="11" class="form-control-sm form-control">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                <label for="id_orden_servicio">Formato <small class="text-danger">*</small></label>
                <select class="form-control form-control-sm disable_estado_2 simple-select2" required id="id_orden_servicio" name="select_comprobantes_update">
                    <option value="1">Guía</option>
                    <option value="2">Orden de Servicio</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                <label for="id_editar_fech_emis">Fecha Emisión <small class="text-danger">*</small></label>
                <input type="date" class="form-control form-control-sm" required name="fech_emis" id="id_editar_fech_emis">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                <label for="id_editar_resp_pago">Responsable del pago <small class="text-danger">*</small></label>
                <select name="resp_pago" id="id_editar_resp_pago" class="form-control form-control-sm" required>
                    <option value="REMITENTE">REMITENTE</option>
                    <option value="DESTINATARIO">DESTINATARIO</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                <label for="id_editar_estado">Tipo <small class="text-danger">*</small></label>
                <select name="estado" id="id_editar_estado" class="form-control simple-select2 form-control-sm disable_estado_2" required>
                    <option value="1">ACTIVA</option>
                    <option value="3">INACTIVA</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                <label for="id_editar_fech_venc_orde_comp">Vto OC</label>
                <input type="date" class="form-control form-control-sm" name="fech_venc_orde_comp" id="id_editar_fech_venc_orde_comp">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-6 col-xl-4">
                <label for="id_editar_comp_clie">Comprobantes <small class="text-danger data-anulada-oculta">*</small></label>
                <select name="comp_clie[]" id="id_editar_comp_clie" required class="form-control-sm form-control simple-select2 data-anulada" multiple>
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
            <div class="form-group col-12 col-md-6 col-lg-6 col-xl-4">
                <label for="id_editar_nro_comp_cli">N° comprobante <small class="text-danger data-anulada-oculta">*</small></label>
                <select name="nro_comp_cli[]" id="id_editar_nro_comp_cli" required class="form-control-sm cb-mult-nuevo form-control data-anulada" multiple></select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                <label for="id_editar_cant_comp">Cantidad Comprobante <small class="text-danger data-anulada-oculta">*</small></label>
                <input type="text" class="form-control-sm form-control data-anulada" value="0" name="cant_comp" required id="id_editar_cant_comp">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                <label for="id_editar_orde_comp">Orden de compra</label>
                <input type="text" class="form-control-sm form-control" name="orde_comp" id="id_editar_orde_comp">
            </div>
            <div class="col-12">
                <h5 class="text-info">TRASLADO</h5>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                <label for="id_editar_fech_tras">Fecha Traslado <small class="text-danger">*</small></label>
                <input type="date" class="form-control form-control-sm" required name="fech_tras" id="id_editar_fech_tras">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                <label for="id_editar_fecha_reparto">Fecha Reparto</label>
                <input type="date" class="form-control form-control-sm disable_inactiva disable-estado-1 enable-estado-4" required name="fecha_reparto" id="id_editar_fecha_reparto">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                <label for="id_editar_tray_env">Trayecto Envio <small class="text-danger">*</small></label>
                <select name="tray_env" id="id_editar_tray_env" class="form-control form-control-sm disable_estado_2" required>
                    @foreach ($trayecto as $tra)
                        <option value="{{$tra->id}}">{{$tra->tipo}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-3">
                <label for="id_editar_via_envi">Vía Envío <small class="text-danger">*</small></label>
                <select name="via_envi" id="id_editar_via_envi" class="form-control form-control-sm disable_estado_2" required>
                    @foreach ($via_envios as $via)
                        <option value="{{$via->id}}">{{$via->envio}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3 col-xl-3">
                <label for="id_editar_via_tras">Via Traslado(Facturación) <small class="text-danger data-anulada-oculta">*</small></label>
                <select name="via_tras" id="id_editar_via_tras" class="form-control form-control-sm data-anulada" required>
                    @foreach ($via_envios as $via)
                        <option value="{{$via->id}}">{{$via->envio}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <h5 class="text-info">REMITENTE</h5>
            </div>
            <div class="form-group col-12 col-lg-5 col-xl-6">
                <label for="id_editar_id_remitente">Nombres <small class="text-danger">*</small></label>
                <select class="form-control simple-select2 disable_estado_2" name="id_remitente" id="id_editar_id_remitente" required>
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
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <label for="id_editar_remi_tipo_docu">Tipo documento <small class="text-danger label-disabled-os">*</small></label>
                <select name="remi_tipo_docu" id="id_editar_remi_tipo_docu" required class="form-control simple-select2 cb-tipo-doc input-disabled-os" data-input="id_editar_remi_nro_docu" data-valor-cliente="doc_ident" data-valor-proveedor="tipo_docu_prov">
                    <option></option>
                    @foreach ($tipo_documentos as $tipo_docu)
                        <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_editar_remi_nro_docu">N° documento <small class="text-danger label-disabled-os">*</small></label>
                <input type="text" class="form-control form-control-sm input-disabled-os" name="remi_nro_docu" required id="id_editar_remi_nro_docu" data-valor-cliente="nro_doc" data-valor-proveedor="nro_docu_prov">
            </div>
            <div class="form-group col-12">
                <label for="id_editar_remi_dire">Dirección <small class="text-danger">*</small></label>
                <input type="text" class="form-control form-control-sm" name="remi_dire" required id="id_editar_remi_dire" data-valor-cliente="dir_sede_so" data-valor-proveedor="dire_prov">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <label for="id_editar_remi_depa">Departamento <small class="text-danger">*</small></label>
                <select class="form-control cb-ubigeo disable_estado_2" name="remi_depa" required id="id_editar_remi_depa" data-consulta="provincia" data-info="id_editar_remi_prov" data-valor-proveedor="depa_prov" data-info-distrito="id_editar_remi_dist" data-valor-cliente="dpto_so">
                    <option value="" hidden>Seleccione un departamento</option>
                    @foreach ($departamentos as $depar)
                        <option value="{{$depar->id_ubigeo}}">{{$depar->nombre_ubigeo}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <label for="id_editar_remi_prov">Provincia <small class="text-danger">*</small></label>
                <select class="form-control cb-ubigeo disable_estado_2" name="remi_prov" required id="id_editar_remi_prov" data-consulta="distrito" data-info="id_editar_remi_dist" data-valor-proveedor="prov_prov" data-valor-cliente="prov_so">
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <label for="id_editar_remi_dist">Distrito <small class="text-danger">*</small></label>
                <select class="form-control cb-ubigeo disable_estado_2" name="remi_dist" required id="id_editar_remi_dist" data-valor-cliente="dist_so" data-valor-proveedor="dist_prov">
                </select>
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="id_editar_id_invitado">Invitado</label>
                <select name="id_invitado" id="id_editar_id_invitado" data-cuenta="#id_editar_cc_invitado" class="simple-select2-2"></select>
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="id_editar_cc_invitado">CC. Invitado</label>
                <select name="cc_invitado" id="id_editar_cc_invitado" required disabled class="cb-select2-tags"></select>
            </div>
            <div class="col-12">
                <h5 class="text-info">DESTINATARIO</h5>
            </div>
            <div class="form-group col-12 col-lg-5 col-xl-6">
                <label for="id_editar_id_destinatario">Nombres <small class="text-danger">*</small></label>
                <select class="form-control cb-select2-tags" required name="id_destinatario" id="id_editar_id_destinatario">
                    <option></option>
                    @foreach ($cli_cli as $c)
                        <option value="{{$c->id}}">{{$c->nombres}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <label for="id_editar_dest_tipo_docu">Tipo documento <small class="text-danger label-disabled-os">*</small></label>
                <select name="dest_tipo_docu" id="id_editar_dest_tipo_docu" required class="form-control simple-select2 cb-tipo-doc input-disabled-os" data-input="id_editar_dest_nro_docu" data-cli-cli="tipo_doc">
                    <option></option>                        
                    @foreach ($tipo_documentos as $tipo_docu)
                        <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_editar_dest_nro_docu">N° documento <small class="text-danger label-disabled-os">*</small></label>
                <input type="text" class="form-control form-control-sm input-disabled-os" name="dest_nro_docu" required id="id_editar_dest_nro_docu" data-cli-cli="nro_doc">
            </div>
            <div class="form-group col-12">
                <label for="id_editar_dest_dire">Dirección <small class="text-danger">*</small></label>
                <input type="text" class="form-control form-control-sm" name="dest_dire" required id="id_editar_dest_dire" data-cli-cli="direccion">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <label for="id_editar_dest_depa">Departamento <small class="text-danger">*</small></label>
                <select name="dest_depa" id="id_editar_dest_depa" required class="form-control cb-ubigeo cb-dest disable_estado_2" data-cli-cli="departamento" data-consulta="provincia" data-info="id_editar_dest_prov" data-info-distrito="id_editar_dest_dist">
                    <option value="" hidden>Seleccione un departamento</option>
                    @foreach ($departamentos as $depar)
                        <option value="{{$depar->id_ubigeo}}">{{$depar->nombre_ubigeo}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <label for="id_editar_dest_prov">Provincia <small class="text-danger">*</small></label>
                <select name="dest_prov" id="id_editar_dest_prov" required class="form-control cb-ubigeo cb-dest disable_estado_2" data-cli-cli="provincia" data-consulta="distrito" data-info="id_editar_dest_dist"></select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <label for="id_editar_dest_dist">Distrito <small class="text-danger">*</small></label>
                <select name="dest_dist" id="id_editar_dest_dist" required class="form-control cb-ubigeo cb-dest disable_estado_2 calculo-dia" data-ciudades="#id_editar_ciudad_destino_final" data-cli-cli="distrito">
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <label for="id_editar_ciudad_destino_final">Ciudad Final</label>
                <select name="id_ciudad_final" id="id_editar_ciudad_destino_final" data-distrito="#id_editar_dest_dist" data-cli-cli="id_ciudad_final" class="disable_estado_2 form-control simple-select2 cb-dest calculo-dia">
                </select>
            </div>
            <div class="col-12">
                <h5 class="text-info">ADICIONAL</h5>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_editar_dias_tran">Días de tránsito <small class="text-danger">*</small></label>
                <input type="number" min="1" class="form-control form-control-sm" required name="dias_tran" id="id_editar_dias_tran">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3" id="id-editar-box-complejo-distante" data-texto="#id_editar_ciudad_alejada" data-input="#id_editar_valor_complejo_distante" hidden>
                <label for="id-tipo-complejo-distante"><span id="id_editar_ciudad_alejada"></span> <small class="text-danger">*</small></label>
                <input type="number" min="0" class="form-control" name="valor_complejo_distante" id="id_editar_valor_complejo_distante" disabled>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="id_editar_fact_cluster_mov1" title="Movilidad cluster">MOV</label>
                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="fact_cluster_mov1" id="id_editar_fact_cluster_mov1">
            </div>
            <div class="col-12">
                <h5 class="text-info">DURANTE ARRIBO - LLEGADA</h5>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <label for="id_editar_situacion_envio">Situación</label>
                <select name="situacion_envio" required id="id_editar_situacion_envio" class="disable-estado-1 disable_inactiva disable_estado_7 disable_estado_2 form-control simple-select2">
                    <option></option>
                    <option value="CONFORME">CONFORME</option>
                    <option value="INCIDENCIA">INCIDENCIA</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <label for="id_editar_incidencia_envio">Incidencia</label>
                <select name="incidencia_envio" required id="id_editar_incidencia_envio" class="disable-estado-1 disable_inactiva disable_estado_7 disable_estado_2 form-control simple-select2">
                    <option></option>
                    <option value="DAÑO PARCIAL">DAÑO PARCIAL</option>
                    <option value="DAÑO TOTAL">DAÑO TOTAL</option>
                    <option value="PERDIDA PARCIAL">PERDIDA PARCIAL</option>
                    <option value="PERDIDA TOTAL">PERDIDA TOTAL</option>
                </select>
            </div>
            <div class="form-group col-12">
                <textarea class="disable-estado-1 form-control form-control-sm disable_estado_2 disable_inactiva disable_estado_7" name="text_disconforme" id="id_editar_text_disconforme" placeholder="Detallar la incidencia de la guía"></textarea>
            </div>
            <div class="col-12">
                <h5 class="text-info">DURANTE REPARTO</h5>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <label for="id_editar_situacion_durante_envio">Situación</label>
                <select name="situacion_durante_envio" required id="id_editar_situacion_durante_envio" class="disable-estado-1 disable_estado_8 disable_inactiva disable_estado_2 form-control simple-select2">
                    <option></option>
                    <option value="CONFORME">CONFORME</option>
                    <option value="INCIDENCIA">INCIDENCIA</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <label for="id_editar_incidencia_durante_envio">Incidencia</label>
                <select name="incidencia_durante_envio" required id="id_editar_incidencia_durante_envio" class="disable-estado-1 disable_estado_8 disable_inactiva disable_estado_2 form-control simple-select2">
                    <option></option>
                    <option value="DAÑO PARCIAL">DAÑO PARCIAL</option>
                    <option value="DAÑO TOTAL">DAÑO TOTAL</option>
                    <option value="PERDIDA PARCIAL">PERDIDA PARCIAL</option>
                    <option value="PERDIDA TOTAL">PERDIDA TOTAL</option>
                </select>
            </div>
            <div class="form-group col-12">
                <textarea class="disable-estado-1 form-control form-control-sm disable_estado_2 disable_inactiva disable_estado_8" name="text_durante_disconforme" id="id_editar_text_durante_disconforme" placeholder="Detallar la incidencia de la guía"></textarea>
            </div>
            <div class="col-12">
                <h5 class="text-info">ENTREGA CLUSTER</h5>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <label for="id_editar_estado_entregado">Status</label>
                <select id="id_editar_estado_entregado" class="disable-estado-1 form-control simple-select2 disable_estado_2 disable_inactiva disable_estado_8">
                    <option></option>
                    <option value="ENTREGADO">ENTREGADO</option>
                    <option value="NO ENTREGADO">NO ENTREGADO</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <label for="id_editar_nro_visitas_reparto">N° Visitas</label>
                <select name="nro_visitas_reparto" id="id_editar_nro_visitas_reparto" class="disable-estado-1 disable_estado_8 simple-select2 form-control disable_estado_2 disable_inactiva" required>
                    <option value="PRIMERA">PRIMERA</option>
                    <option value="SEGUNDA">SEGUNDA</option>
                    <option value="TERCERA">TERCERA</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <label for="id_editar_motivo_no_ent">Motivo No Ent</label>
                <select id="id_editar_motivo_no_ent" name="motivo_no_ent" class="disable-estado-1 disable_estado_8 form-control form-control-sm simple-select2 disable_inactiva disable_estado_2">
                    <option></option>
                    <option value="AUSENTE">AUSENTE</option>
                    <option value="DIR INCOMPLETA">DIR INCOMPLETA</option>
                    <option value="SE MUDO">SE MUDO</option>
                    <option value="DESCONOCIDO">DESCONOCIDO</option>
                    <option value="RECHAZADO">RECHAZADO</option>
                </select>
            </div>
            <div class="col-12">
                <ul id="visitas-entregas" class="ml-4 mb-0"></ul>
            </div>
            <div class="col-12">
                <h5 class="text-info">IMAGENES DE LA ENTREGA</h5>
            </div>
            <div class="col-12">
                <div class="d-flex justify-content-center flex-wrap" style="gap:10px;" id="list-img"></div>
            </div>
            <div class="form-group col-12 img-entregas"></div>
            <div class="col-12">
                <h5 class="text-info">DETALLES</h5>
            </div>
            <div class="form-group col-12 text-right">
                <button type="button" class="btn btn-primary btn-sm mr-2" title="Agregar detalle" id="add-new-detalle"><i class="fas fa-plus"></i></button>
            </div>
            <div class="form-group col-12">
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
                        <tbody id="tabla-detalle"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right">Totales</td>
                                <td>
                                    <input type="number" value="0" name="tot_cant" id="id_editar_tot_cant" class="form-control form-control-sm" min="0">
                                </td>
                                <td>
                                    <input type="number" name="tot_kg" value="0.00" id="id_editar_tot_kg" class="form-control form-control-sm clear-detalle-guia-decimal" min="0" step="0.01">
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <input type="number" name="tot_vol" id="id_editar_tot_vol" value="0.00" id="total-vol" class="form-control form-control-sm clear-detalle-guia-decimal" min="0" step="0.01">
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <input type="submit" hidden id="update-guia">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary ocultar-situacion" id="btn-actualizar-guias">Actualizar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>