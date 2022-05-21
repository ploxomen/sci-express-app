<div class="modal fade" id="detalle-solicitud" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <span class="modal-title" id="staticBackdropLabel">Detalle de la solicitud - <span id="nro_solicitud"></span></span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-row form-info">
            <div class="form-group col-12 col-md-6">
                <label for="id_text_nom_resp" class="text-secondary">Responsable</label>
                <span id="id_text_nom_resp"></span>
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="id_text_estado">Estado</label>
                <span id="id_text_estado"></span>
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="id_fecha_soli">Fecha Soli</label>
                <span id="id_text_format_fecha_soli"></span>
                <input hidden disabled name="fecha_soli" id="id_fecha_soli" type="date" class="form-control form-control-sm">
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="id_trayecto">Trayecto</label>
                <span id="id_text_format_trayecto"></span>
                <select hidden name="trayecto" id="id_trayecto">
                    <option></option>
                    @foreach ($trayectos as $trayecto)
                        <option value="{{$trayecto->id}}">{{$trayecto->tipo}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group form-group col-12">
                <h5 class="text-info">Datos del cliente</h5>
            </div>
            <div class="form-group col-12">
                <label for="id_cliente">Cliente</label>
                <span id="id_text_format_id_cliente"></span>
                <select hidden name="id_cliente" id="id_cliente">
                    <option></option>
                    @foreach ($clientes as $k => $cliente)
                        <option data-nro="{{$cliente->nro_doc}}" value="{{$cliente->id}}">{{$cliente->nom_cli}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6">
                <label for="id_doc_iden_cli">Tipo Doc</label>
                <span id="id_text_format_tipo_doc_cli"></span>
                <select hidden name="doc_iden_cli" id="id_doc_iden_cli">
                    <option value="" hidden>Seleccione una opción</option>
                    @foreach ($tipo_documentos as $tipo_docu)
                        <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="id_nro_doc_cli">N° Doc</label>
                <span id="id_text_nro_doc_cli"></span>
                <input hidden name="nro_doc_cli" id="id_nro_doc_cli" type="text" class="form-control form-control-sm">
            </div>
             <div class="form-group col-12">
                <h5 class="text-info">Datos del solicitante</h5>
            </div>
            <div class="form-group col-12">
                <label for="id_soli">Solicitante</label>
                <span id="id_text_nom_soli"></span>
                <select hidden name="id_soli" id="id_soli"></select>
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="id_doc_iden_soli">Tipo Doc</label>
                <span id="id_text_format_tipo_doc_soli"></span>
                <select hidden name="doc_iden_soli" id="id_doc_iden_soli">
                    <option value="" hidden>Seleccione una opción</option>
                    @foreach ($tipo_documentos as $tipo_docu)
                        <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="id_nro_doc_soli">N° Doc</label>
                <span id="id_text_nro_doc_soli"></span>
                <select hidden name="nro_doc_soli" id="id_nro_doc_soli"></select>
            </div>
            <div class="form-group col-12">
                <h5 class="text-info">Datos de recojo</h5>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <label for="id_depar_cli">Departamento</label>
                <span id="id_text_format_depar_cli"></span>
                <select hidden name="depar_cli" id="id_depar_cli">
                    @foreach ($dep as $d)
                        <option value="{{$d->id_ubigeo}}">{{$d->nombre_ubigeo}}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group col-12 col-md-6 col-lg-4">
                <label for="id_ciudad_cli">Provincia</label>
                <span id="id_text_format_ciudad_cli"></span>
                <select hidden name="ciudad_cli" id="id_ciudad_cli"></select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <label for="id_distrito_cli">Distrito</label>
                <span id="id_text_format_distrito_cli"></span>
                <select hidden name="distrito_cli" id="id_distrito_cli"></select>
            </div>
            <div class="form-group col-12">
                <label for="id_direccion_cli">Dirección</label>
                <span id="id_text_direccion_cli"></span>
                <input hidden name="direccion_cli" id="id_direccion_cli" type="text" class="form-control form-control-sm">
            </div>
             <div class="form-group col-12">
                <label for="id_id_cluster">Cluster</label>
                <span id="id_text_nomcluster"></span>
                <input hidden name="id_cluster" id="id_id_cluster" type="text" class="form-control form-control-sm">
            </div>
             <div class="form-group col-12 col-lg-6">
                <label for="id_id_conductor">Conductor</label>
                <span id="id_text_nom_conductor"></span>
                <input hidden name="direccion_cli" id="id_id_conductor" type="text" class="form-control form-control-sm">
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="id_id_dealer">Dealer</label>
                <span id="id_text_nom_dealer"></span>
                <input hidden name="direccion_cli" id="id_id_dealer" type="text" class="form-control form-control-sm">
            </div>
            <div class="form-group col-12">
                <label for="id_id_vehiculo">Placa Vehículo</label>
                <span id="id_text_placa_vehiculo"></span>
                <input hidden name="direccion_cli" id="id_id_vehiculo" type="text" class="form-control form-control-sm">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <label for="id_dia_recojo">Fecha Recojo</label>
                <span id="id_text_format_dia_recojo"></span>
                <input hidden name="dia_recojo" id="id_dia_recojo" type="date" class="form-control form-control-sm">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <label for="id_hr_recojo_estimado">Hr Recojo Est</label>
                <span id="id_text_format_hr_recojo"></span>
                <input hidden name="hr_recojo_estimado" id="id_hr_recojo_estimado" type="time" class="form-control form-control-sm">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <label for="id_hr_llegada">Hr Llegada</label>
                <span id="id_text_format_hr_llegada"></span>
                <input hidden name="hr_llegada" id="id_hr_llegada" type="time" class="form-control form-control-sm">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <label for="id_hr_despacho">Hr Despacho</label>
                <span id="id_text_format_hr_despacho"></span>
                <input hidden name="hr_despacho" id="id_hr_despacho" type="time" class="form-control form-control-sm">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <label for="id_hr_salida">Hr Salida</label>
                <span id="id_text_format_hr_salida"></span>
                <input hidden name="hr_salida" id="id_hr_salida" type="time" class="form-control form-control-sm">
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <label for="id_cant_bulto">Bulto</label>
                <span id="id_text_cant_bulto"></span>
                <input hidden name="cant_bulto" id="id_cant_bulto" type="number" class="form-control form-control-sm">
            </div>
             <div class="form-group col-6">
                <label for="id_doc_recojo">Doc Recojo</label>
                <span id="id_text_doc_recojo"></span>
                <input hidden name="doc_recojo" id="id_doc_recojo" type="number" class="form-control form-control-sm">
            </div>
             <div class="form-group col-6">
                <label for="id_nro_guias_conso">N° Guías Con</label>
                <span id="id_text_nro_guias_conso"></span>
                <input hidden name="nro_guias_conso" id="id_nro_guias_conso" type="number" class="form-control form-control-sm">
            </div>
        </div>
      </div>
      <div class="modal-footer">
        {{-- <button type="button" class="btn btn-primary">Guardar</button> --}}
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>