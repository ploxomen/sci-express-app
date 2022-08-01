<div class="modal fade" id="detalle-guias-todo" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Tipo: </b>
                <span id="id_guia_tipo"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Estado: </b>
                <span id="id_estado_guia"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Resp Pago: </b>
                <span id="id_resp_pago"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Fecha Emisión: </b>
                <span id="id_fecha_emision"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Comprobantes: </b>
                <span id="id_comp_clie"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">N° Comprobante: </b>
                <span id="id_nro_comp_cli"></span>
            </div>
             <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Cant Comp: </b>
                <span id="id_cant_comp"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Orden Compra: </b>
                <span id="id_orde_comp"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Vto OC: </b>
                <span id="id_fecha_venc_ord_comp"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">TRASLADO</h5>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Fecha Traslado: </b>
                <span id="id_fecha_traslado"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Trayecto: </b>
                <span id="id_tipo_trayecto"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Vía Envio: </b>
                <span id="id_via_envio"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary d-block">Vía Traslado (Facturación): </b>
                <span id="id_via_traslado"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Día Transito: </b>
                <span id="id_dias_tran"></span>
            </div>
            <div class="form-group col-md-12 col-lg-4">
                <b class="text-secondary">Fecha Reparto: </b>
                <span id="id_fecha_reparto"></span>
            </div>
             <div class="form-group col-12">
                <b class="text-secondary">Fecha Entregado: </b>
                <span id="id_fecha_hr_entregado"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">REMITENTE</h5>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary">Nombres: </b>
                <span id="id_nom_cli_remitente"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-7">
                <b class="text-secondary">Tipo Documento: </b>
                <span id="id_remi_tipo_docu"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-5">
                <b class="text-secondary">N° Documento: </b>
                <span id="id_remi_nro_docu"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Departamento: </b>
                <span id="id_remi_depa"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Provincia: </b>
                <span id="id_remi_prov"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Distrito:</b>
                <span id="id_remi_dist"></span>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Dirección</b>
                <span id="id_remi_dire"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">DESTINATARIO</h5>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Nombres</b>
                <span id="id_nom_cli_destinatario"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-7">
                <b class="text-secondary">Tipo Documento: </b>
                <span id="id_dest_tipo_doc"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-5">
                <b class="text-secondary">N° Documento: </b>
                <span id="id_dest_nro_docu"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Departamento: </b>
                <span id="id_dest_depa"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Provincia: </b>
                <span id="id_dest_prov"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Distrito: </b>
                <span id="id_dest_dist"></span>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Dirección</b>
                <span id="id_dest_dire"></span>
            </div>
           <div class="col-12">
                <h5 class="text-info">ENVIO CLUSTER</h5>
            </div>
            <div class="form-group col-12 col-md-6">
                <b class="text-secondary">Situación:</b>
                <span id="id_situacion_envio"></span>
            </div>
            <div class="form-group col-12 col-md-6">
                <b class="text-secondary">Incidencia:</b>
                <span id="id_incidencia_envio"></span>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Detalle Incidencia</b>
                <span id="id_text_disconforme"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">DURANTE REPARTO</h5>
            </div>
            <div class="form-group col-12 col-md-6">
                <b class="text-secondary">Situación:</b>
                <span id="id_situacion_durante_envio"></span>
            </div>
            <div class="form-group col-12 col-md-6">
                <b class="text-secondary">Incidencia:</b>
                <span id="id_incidencia_durante_envio"></span>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Detalle Incidencia</b>
                <span id="id_text_durante_disconforme"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">ENTREGA CLUSTER</h5>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">Status:</b>
                <span id="id_estado_entregado"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary">N° visitas:</b>
                <span id="id_nro_visitas_reparto"></span>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Motivo no Ent</b>
                <span id="id_motivo_no_ent"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">IMAGENES DE LA ENTREGA</h5>
            </div>
            <div class="col-12 form-group" id="galery-img"></div>
            <div class="form-group col-12">
                <h5 class="text-info">DETALLES</h5>
            </div>
            <div class="form-group col-12 col-md-4">
                <b class="text-secondary d-block">Total Cantidad</b>
                <span id="id_tot_cant"></span>
            </div>
            <div class="form-group col-12 col-md-4">
                <b class="text-secondary d-block">Total Kg</b>
                <span id="id_tot_kg"></span>
            </div>
            <div class="form-group col-12 col-md-4">
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