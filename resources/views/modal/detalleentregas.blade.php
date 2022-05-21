<div class="modal fade" id="modal-detalle-entregas" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Campos emergentes</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="frm-update-reparto">
                <input type="hidden" name="id_cluster" id="id_modal_id_cluster">
                <input type="hidden" name="id_guia" id="id_modal_id_guia">
                @php
                    $disabled = isset($clusters) ? '' : 'disabled';
                @endphp
                <div class="form-row">
                    <div class="form-group col-12 col-md-6">
                        <label for="id_modal_fecha_reparto">Fecha de reparto <small class="text-danger">*</small></label>
                        <input type="date" name="fecha_reparto" id="id_modal_fecha_reparto" class="form-control" required {{$disabled}} >
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="id_modal_fecha_entrega">Fecha de entrega</label>
                        <input type="date" name="fecha_hr_entregado" id="id_modal_fecha_entrega" class="form-control" {{$disabled}} >
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="id_modal_situacion_durante_envio">Situación Repeparto</label>
                        <select id="id_modal_situacion_durante_envio" {{$disabled}} name="situacion_durante_envio" class="form-control simple-select2">
                            <option></option>
                            <option value="CONFORME">CONFORME</option>
                            <option value="INCIDENCIA">INCIDENCIA</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="id_modal_incidencia_durante_envio">Incidencia Rep</label>
                        <select id="id_modal_incidencia_durante_envio" {{$disabled}} name="incidencia_durante_envio" class="form-control simple-select2">
                            <option></option>
                            <option value="DAÑO PARCIAL">DAÑO PARCIAL</option>
                            <option value="DAÑO TOTAL">DAÑO TOTAL</option>
                            <option value="PERDIDA PARCIAL">PERDIDA PARCIAL</option>
                            <option value="PERDIDA TOTAL">PERDIDA TOTAL</option>
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <textarea class="form-control" id="id_modal_text_durante_disconforme" {{$disabled}} name="text_durante_disconforme" placeholder="Detallar la incidencia" maxlength="500"></textarea>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="id_modal_estado_entregado">Status Entrega</label>
                        <select id="id_modal_estado_entregado" name="estado_entregado" {{$disabled}} class="form-control simple-select2">
                            <option></option>
                            <option value="ENTREGADO">ENTREGADO</option>
                            <option value="NO ENTREGADO">NO ENTREGADO</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="id_modal_nro_visitas_reparto">N° Visitas</label>
                        <select id="id_modal_nro_visitas_reparto" name="nro_visitas_reparto" {{$disabled}} class="form-control simple-select2">
                            <option></option>
                            <option value="PRIMERA">PRIMERA</option>
                            <option value="SEGUNDA">SEGUNDA</option>
                            <option value="TERCERA">TERCERA</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="id_modal_motivo_no_ent">Motivo No Entregado</label>
                        <select id="id_modal_motivo_no_ent" name="motivo_no_ent" {{$disabled}} class="form-control simple-select2">
                            <option></option>
                            <option value="AUSENTE">AUSENTE</option>
                            <option value="DIR INCOMPLETA">DIR INCOMPLETA</option>
                            <option value="SE MUDO">SE MUDO</option>
                            <option value="DESCONOCIDO">DESCONOCIDO</option>
                            <option value="RECHAZADO">RECHAZADO</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="id_modal_situ">Situación Guía</label>
                        <select id="id_modal_situ" name="situ" {{$disabled}} class="form-control simple-select2">
                            <option></option>
                            <option value="7">REPARTO</option>
                            <option value="8">ENTREG Y CONF</option>
                            <option value="14">ENTREG NO CONCRETADA</option>
                        </select>
                    </div>
                </div>
                <button type="submit" hidden id="modificar-entregas-sectorista"></button>
            </form>
        </div>
        <div class="modal-footer">
            @isset($clusters)
                <button type="button" class="btn btn-primary" id="modificar-detalle-entrega">Actualizar</button>
            @endisset
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Atras</button>
        </div>
      </div>
    </div>
  </div>