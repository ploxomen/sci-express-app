<div class="modal fade" id="modal-solicitud-actualizar" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modificar Solicitud</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="formulario-editar-solicitud" class="form-row">
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="solicitud_modificar_nro_solicitud">N° solicitud</label>
                    <input type="text" required name="nro_solicitud" class="form-control form-control-sm" maxlength="5" accept="[0-9]{5}" id="solicitud_modificar_nro_solicitud">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="solicitud_modificar_fecha_soli">Fecha de solicitud</label>
                    <input type="date" name="fecha_soli" required class="form-control form-control-sm" id="solicitud_modificar_fecha_soli">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="solicitud_modificar_trayecto">Trayecto de envio</label>
                    <select name="trayecto" required id="solicitud_modificar_trayecto" class="form-control form-control-sm disabled-estatus-2">
                        @foreach ($trayectos as $trayecto)
                            <option value="{{$trayecto->id}}">{{$trayecto->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="solicitud_modificar_responsable">Responsable</label>
                    <select name="responsable" required id="solicitud_modificar_responsable" class="form-control form-control-sm simple-select2">
                        <option value="CLIENTE">CLIENTE</option>
                        <option value="PROVEEDOR">PROVEEDOR</option>
                        <option value="INVITADO">INVITADO</option>
                    </select>
                </div>
                <div class="col-12">
                    <h5 class="text-primary mb-1">Cliente</h5>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="solicitud_modificar_id_cliente">Nombre</label>
                    <select name="id_cliente" id="solicitud_modificar_id_cliente" required class="form-control form-control-sm simple-select2 datos-cliente disabled-estatus-2">
                        <option></option>
                        @foreach ($clientes as $k => $cliente)
                            <option data-nro="{{$cliente->nro_doc}}" value="{{$cliente->id}}">{{$cliente->nom_cli}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-6 col-lg-3">
                    <label for="solicitud_modificar_doc_iden_cli">Tipo Documento</label>
                    <select name="doc_iden_cli" required id="solicitud_modificar_doc_iden_cli" class="form-control form-control-sm text-clear simple-select2 cb-tipo-doc datos-cliente" data-cliente="doc_ident" data-input="solicitud_modificar_nro_doc_cli">
                        <option value="" hidden>Seleccione una opción</option>
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-6 col-lg-3">
                    <label for="solicitud_modificar_nro_doc_cli">N° Documento</label>
                    <input type="text" required class="form-control form-control-sm text-clear datos-cliente" id="solicitud_modificar_nro_doc_cli" name="nro_doc_cli" data-cliente="nro_doc">
                </div>
                <div class="col-12">
                    <h5 class="text-primary mb-1">Proveedor</h5>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="solicitud_modificar_id_soli">Nombre</label>
                    <select id="solicitud_modificar_id_soli" name="id_soli" class="form-control form-control-sm limitar-proveedor datos-proveedor disabled-estatus-2"></select>
                </div>
                <div class="form-group col-6 col-lg-3">
                    <label for="solicitud_modificar_doc_iden_soli">Tipo Documento</label>
                    <select name="doc_iden_soli" id="solicitud_modificar_doc_iden_soli" class="limitar-proveedor form-control form-control-sm text-clear simple-select2 cb-tipo-doc datos-proveedor" data-input="solicitud_modificar_nro_doc_soli">
                        <option value="" hidden>Seleccione una opción</option>
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-6 col-lg-3">
                    <label for="solicitud_modificar_nro_doc_soli">N° Documento</label>
                    <input type="text" class="limitar-proveedor form-control form-control-sm text-clear datos-proveedor" id="solicitud_modificar_nro_doc_soli" name="nro_doc_soli">
                </div>
                <div class="col-12">
                    <h5 class="text-primary mb-1">Invitado</h5>
                </div>
                 <div class="form-group col-12 col-lg-6">
                    <label for="solicitud_modificar_id_invitado">Nombre</label>
                    <select name="id_invitado" class="simple-select2 datos-invitado" id="solicitud_modificar_id_invitado"></select>
                </div>
                <div class="form-group col-6 col-lg-3">
                    <label for="solicitud_modificar_centro_costo_invitado">Centro Costo</label>
                    <select name="centro_costo_invitado" class="simple-select2 datos-invitado" id="solicitud_modificar_centro_costo_invitado"></select>
                </div>
                <div class="form-group col-6 col-lg-3">
                    <label for="solicitud_modificar_cuenta_invitado">Cuenta</label>
                    <select name="cuenta_invitado" class="cb-select2-tags datos-invitado" id="solicitud_modificar_cuenta_invitado"></select>
                </div>
                <div class="col-12">
                    <h5 class="text-primary mb-1">Recojo</h5>
                </div>
                 <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="solicitud_modificar_dia_recojo">Día del recojo</label>
                    <input type="date" required class="form-control form-control-sm disabled-estatus-2" name="dia_recojo" id="solicitud_modificar_dia_recojo">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="solicitud_modificar_hr_recojo_estimado">Hr Recojo estimado</label>
                    <input type="time" required class="form-control form-control-sm" name="hr_recojo_estimado" id="solicitud_modificar_hr_recojo_estimado">        
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="solicitud_modificar_total_bulto">Cant de bulto</label>
                    <input type="number" class="form-control form-control-sm" name="total_bulto" id="solicitud_modificar_total_bulto">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="solicitud_modificar_depart_cli_recojo">Departamento</label>
                    <select name="depart_cli_recojo" data-cliente="dpto_so" required id="solicitud_modificar_depart_cli_recojo" class="form-control form-control-sm cb-ubigeo disabled-estatus-2" data-proveedor="depa_prov" data-consulta="provincia" data-info="solicitud_modificar_ciudad_cli_recojo" data-info-distrito="solicitud_modificar_distrito_cli_recojo">
                        <option></option>
                        @foreach ($dep as $d)
                            <option value="{{$d->id_ubigeo}}">{{$d->nombre_ubigeo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="solicitud_modificar_ciudad_cli_recojo" data-replace="id_ciudad_cli">Provincia</label>
                    <select name="ciudad_cli_recojo" data-replace="id_ciudad_cli" required id="solicitud_modificar_ciudad_cli_recojo" class="form-control form-control-sm cb-ubigeo disabled-estatus-2" data-cliente="prov_so" data-proveedor="prov_prov" data-consulta="distrito" data-info="solicitud_modificar_distrito_cli_recojo">
                        <option></option>
                    </select>            
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="solicitud_modificar_distrito_cli_recojo" data-replace="id_distrito_cli">Distrito</label>
                    <select name="distrito_cli_recojo" data-replace="id_distrito_cli" required id="solicitud_modificar_distrito_cli_recojo" class="form-control form-control-sm cb-ubigeo disabled-estatus-2" data-proveedor="dist_prov" data-cliente="dist_so">
                        <option></option>
                    </select>            
                </div>
                <div class="form-group col-12">
                    <label for="solicitud_modificar_direccion_recojo">Dirección</label>
                    <input type="text" data-cliente="dir_sede_so" data-proveedor="dire_prov" name="direccion_recojo" maxlength="255" class="form-control form-control-sm form-control form-control-sm-sm" id="solicitud_modificar_direccion_recojo">
                </div>
                <div class="col-11">
                    <h5 class="text-primary mb-1">Datos envio</h5>
                </div>
                <div class="col-1 form-group text-right">
                    <button type="button" class="btn btn-sm btn-primary mr-2" id="agregar-datos-recojo" title="Agregar dirección"><i class="fas fa-plus"></i></button>
                </div>
                <ol class="pl-4 col-12 mb-0" id="lista-detalles-bultos">
                    <li class="mb-3">
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="id_depar_cli_1" data-replace="id_depar_cli">Departamento</label>
                                <select data-name="depar_cli" data-replace="id_depar_cli" required id="id_depar_cli_1" class="form-control form-control-sm cb-ubigeo" data-proveedor="depa_prov" disabled data-consulta="provincia" data-info="id_ciudad_cli_1" data-info-distrito="id_distrito_cli_1">
                                    <option></option>
                                    @foreach ($dep as $d)
                                        <option value="{{$d->id_ubigeo}}">{{$d->nombre_ubigeo}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="id_ciudad_cli_1" data-replace="id_ciudad_cli">Provincia</label>
                                <select data-name="ciudad_cli" disabled data-replace="id_ciudad_cli" required id="id_ciudad_cli_1" class="form-control form-control-sm cb-ubigeo" data-proveedor="prov_prov" data-consulta="distrito" data-info="id_distrito_cli_1">
                                    <option></option>
                                </select>            
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="id_distrito_cli_1" data-replace="id_distrito_cli">Distrito</label>
                                <select data-name="distrito_cli" disabled data-replace="id_distrito_cli" required id="id_distrito_cli_1" class="form-control form-control-sm cb-ubigeo" data-consulta="distrito2" data-proveedor="dist_prov">
                                    <option></option>
                                </select>            
                            </div>
                            <div class="col-12"></div>
                            <div class="form-group col-12 col-md-4 col-lg-3">
                                <label for="id_sobres_1" disabled data-replace="id_sobres">Sobres</label>
                                <input type="number" data-replace="id_sobres" data-name="sobres" id="id_sobres_1" class="form-control form-control-sm form-control form-control-sm-sm calcular-bultos" data-consulta="sobre">
                            </div>
                            <div class="form-group col-12 col-md-4 col-lg-3">
                                <label for="id_cajas_1" data-replace="id_cajas">Cajas</label>
                                <input type="number" disabled data-replace="id_cajas" data-name="cajas" id="id_cajas_1" class="form-control form-control-sm form-control form-control-sm-sm calcular-bultos" data-consulta="caja">
                            </div>
                            <div class="form-group col-12 col-md-4 col-lg-3">
                                <label for="id_paquetes_1" data-replace="id_paquetes">Paquetes</label>
                                <input type="number" disabled data-replace="id_paquetes" data-name="paquetes" id="id_paquetes_1" class="form-control form-control-sm form-control form-control-sm-sm calcular-bultos" data-consulta="paquete">
                            </div>
                            <div class="form-group col-12 col-md-4 col-lg-3">
                                <label for="id_otros_1" data-replace="id_otros">Otros</label>
                                <input type="number" disabled data-replace="id_otros" data-name="otros" id="id_otros_1" class="form-control form-control-sm form-control form-control-sm-sm calcular-bultos" data-consulta="otro">
                            </div>
                            <div class="form-group col-12">
                                <label for="id_detalle_otros_1" data-replace="id_detalle_otros">Detalle Otros</label>
                                <textarea data-name="detalle_otros" data-replace="id_detalle_otros" id="id_detalle_otros_1" disabled class="form-control form-control-sm form-control form-control-sm-sm" data-consulta="detalle"></textarea>
                            </div>
                            <div class="form-group col-12 text-right">
                                <button type="button" class="btn btn-sm btn-danger delete-detalle-solicitud"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                    </li>
                </ol>
                <input type="submit" hidden id="btn-actualizar-solicitud">
                <div class="form-group col-12">
                    <small class="text-info">Debe precionar el botón modificar para que los cambios se guarden</small>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="actualiza-solicitud">Modificar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>