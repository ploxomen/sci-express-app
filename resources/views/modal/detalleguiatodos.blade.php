<div class="modal fade" id="detalle-guias-todo" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Guía N° <span id="id_nro_guia"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-row form-info">
            <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="text-secondary d-block">Tipo</b>
                <span id="id_guia_tipo"></span>
            </div>
            <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="text-secondary d-block">Estado</b>
                <span id="id_estado_guia"></span>
            </div>
            <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="d-block text-secondary">Responsable Pago</b>
                <span id="id_resp_pago"></span>
            </div>
            <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="text-secondary d-block">Fecha Emisión</b>
                <span id="id_fecha_emision"></span>
            </div>
            <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="text-secondary d-block">Comprobantes</b>
                <span id="id_comp_clie"></span>
            </div>
            <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="text-secondary d-block">N° Comprobante</b>
                <span id="id_nro_comp_cli"></span>
            </div>
             <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="text-secondary d-block">Cant Comp</b>
                <span id="id_cant_comp"></span>
            </div>
            <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="text-secondary d-block">Orden Compra</b>
                <span id="id_orde_comp"></span>
            </div>
            <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="text-secondary d-block">Vto OC</b>
                <span id="id_fecha_venc_ord_comp"></span>
            </div>
            <div class="form-group form-group col-12">
                <h5 class="text-info">Traslado</h5>
            </div>
            <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="text-secondary d-block">Fecha Traslado</b>
                <span id="id_fecha_traslado"></span>
            </div>
            <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="text-secondary d-block">Trayecto</b>
                <span id="id_tipo_trayecto"></span>
            </div>
            <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="text-secondary d-block">Vía Envio</b>
                <span id="id_via_envio"></span>
            </div>
            <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="text-secondary d-block">Vía Traslado</b>
                <span id="id_via_traslado"></span>
            </div>
            <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="text-secondary d-block">Día Transito</b>
                <span id="id_dias_tran"></span>
            </div>
            <div class="form-group col-12 col-lg-6 col-xl-4">
                <b class="text-secondary d-block">Costo Envio</b>
                <span id="id_cst_mest_env"></span>
            </div>
            <div class="form-group form-group col-12">
                <h5 class="text-info">Remitente</h5>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Nombres</b>
                <span id="id_nom_cli_remitente"></span>
            </div>
            <div class="form-group col-12 col-md-6">
                <b class="text-secondary d-block">Tipo Documento</b>
                <span id="id_remi_tipo_docu"></span>
            </div>
            <div class="form-group col-12 col-md-6">
                <b class="text-secondary d-block">N° Documento</b>
                <span id="id_remi_nro_docu"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary d-block">Departamento</b>
                <span id="id_remi_depa"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary d-block">Provincia</b>
                <span id="id_remi_prov"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary d-block">Distrito</b>
                <span id="id_remi_dist"></span>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Dirección</b>
                <span id="id_remi_dire"></span>
            </div>
            <div class="form-group form-group col-12">
                <h5 class="text-info">DESTINATARIO</h5>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Nombres</b>
                <span id="id_nom_cli_destinatario"></span>
            </div>
            <div class="form-group col-12 col-md-6">
                <b class="text-secondary d-block">Tipo Documento</b>
                <span id="id_dest_tipo_doc"></span>
            </div>
            <div class="form-group col-12 col-md-6">
                <b class="text-secondary d-block">N° Documento</b>
                <span id="id_dest_nro_docu"></span>
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

            <div class="form-group col-12">
                <h5 class="text-info">DETALLES</h5>
            </div>
            <div class="form-group col-12 col-lg-4">
                <b class="text-secondary d-block">Total Cantidad</b>
                <span id="id_tot_cant"></span>
            </div>
            <div class="form-group col-12 col-lg-4">
                <b class="text-secondary d-block">Total Kg</b>
                <span id="id_tot_kg"></span>
            </div>
            <div class="form-group col-12 col-lg-4">
                <b class="text-secondary d-block">Total Volumen</b>
                <span id="id_tot_vol"></span>
            </div>
            <div class="form-group col-12">
                <div id="detalle_guias"></div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>