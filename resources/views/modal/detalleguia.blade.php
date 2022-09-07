<div class="modal fade" id="detalle-guias-llegada" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <span class="modal-title" id="staticBackdropLabel">Guía N° <span id="id_nro_guia"></span></span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-row form-info">
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <b class="text-secondary d-block">Fecha Emisión</b>
                <span id="id_fecha_emision"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <b class="text-secondary d-block">Fecha Reparto</b>
                <span id="id_fecha_reparto"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <b class="text-secondary d-block">Ordenes de Compra</b>
                <span id="id_orde_comp"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <b class="text-secondary d-block">Fecha Venc OC</b>
                <span id="id_fech_venc_orde_comp"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">REMITENTE</h5>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Nombres</b>
                <span id="id_nom_cli_remitente"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">DESTINATARIO</h5>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Nombres</b>
                <span id="id_nom_cli_destinatario"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary d-block">Departamento</b>
                <span id="id_dest_depa"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary d-block">Provincia</b>
                <span id="id_dest_prov"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary d-block">Distrito</b>
                <span id="id_dest_dist"></span>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Dirección</b>
                <span id="id_dest_dire"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">DETALLES</h5>
            </div>
            <div class="form-group col-12">
                <div id="detalle_guias"></div>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 ocultar-situacion">
                <label for="id_situacion_envio">Situación Env</label>
                <select name="situacion_envio" required id="id_situacion_envio" class="form-control simple-select2">
                    <option></option>
                    <option value="CONFORME">CONFORME</option>
                    <option value="INCIDENCIA">INCIDENCIA</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 ocultar-situacion">
                <label for="id_incidencia_envio">Incidencia Env</label>
                <select name="incidencia_envio" required id="id_incidencia_envio" class="form-control simple-select2" disabled>
                    <option></option>
                    <option value="DAÑO PARCIAL">DAÑO PARCIAL</option>
                    <option value="DAÑO TOTAL">DAÑO TOTAL</option>
                    <option value="PERDIDA PARCIAL">PERDIDA PARCIAL</option>
                    <option value="PERDIDA TOTAL">PERDIDA TOTAL</option>
                </select>
            </div>
            <div class="form-group col-12 ocultar-situacion">
                <textarea class="form-control" id="text-inconforme" placeholder="Detallar la inconformidad de la guía" disabled></textarea>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary ocultar-situacion" id="btn-guardar-guias">Guardar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>