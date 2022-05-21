<div class="modal fade" id="detalle-guias-llegada-cluster" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <span class="modal-title" id="staticBackdropLabel">Guía N° <span id="id_modal2_nro_guia"></span></span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-row form-info">
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <b class="text-secondary d-block">Fecha Emisión:</b>
                <span id="id_modal2_fecha_emision"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <b class="text-secondary d-block">Fecha Reparto:</b>
                <span id="id_modal2_fecha_reparto"></span>
            </div>
             <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <b class="text-secondary d-block">Ordenes de Compra</b>
                <span id="id_modal2_orde_comp"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                <b class="text-secondary d-block">Fecha Venc OC</b>
                <span id="id_modal2_fech_venc_orde_comp"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">REMITENTE</h5>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Nombres</b>
                <span id="id_modal2_nom_cli_remitente"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">DESTINATARIO</h5>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Nombres</b>
                <span id="id_modal2_nom_cli_destinatario"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary d-block">Departamento</b>
                <span id="id_modal2_dest_depa"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary d-block">Provincia</b>
                <span id="id_modal2_dest_prov"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary d-block">Distrito</b>
                <span id="id_modal2_dest_dist"></span>
            </div>
            <div class="form-group col-12">
                <b class="text-secondary d-block">Dirección</b>
                <span id="id_modal2_dest_dire"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">ENVIO</h5>
            </div>
            <div class="form-group col-12 col-md-6">
                <b class="text-secondary d-block">Situación</b>
                <span id="id_modal2_situacion_envio"></span>
            </div>
            <div class="form-group col-12 col-md-6">
                <b class="text-secondary d-block">Incidencia</b>
                <span id="id_modal2_incidencia_envio"></span>
            </div>
            <div class="form-group col-12 col-md-6">
                <b class="text-secondary d-block">Detalle Incidencia</b>
                <span id="id_modal2_text_disconforme"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">DURANTE REPARTO</h5>
            </div>
            <div class="form-group col-12 col-md-6">
                <b class="text-secondary d-block">Situación</b>
                <span id="id_modal2_situacion_durante_envio"></span>
            </div>
            <div class="form-group col-12 col-md-6">
                <b class="text-secondary d-block">Incidencia</b>
                <span id="id_modal2_incidencia_durante_envio"></span>
            </div>
            <div class="form-group col-12 col-md-6">
                <b class="text-secondary d-block">Detalle Incidencia</b>
                <span id="id_modal2_text_durante_disconforme"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">ENTREGA</h5>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary d-block">Status</b>
                <span id="id_modal2_estado_entregado"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary d-block">N° visitas</b>
                <span id="id_modal2_nro_visitas_reparto"></span>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <b class="text-secondary d-block">Motivo no Ent</b>
                <span id="id_modal2_motivo_no_ent"></span>
            </div>
            <div class="col-12">
                <h5 class="text-info">DETALLES</h5>
            </div>
            {{-- <div class="form-group col-12 col-lg-4">
                <b class="text-secondary d-block">Total Cantidad</b>
                <span id="id_modal2_tot_cant"></span>
            </div>
            <div class="form-group col-12 col-lg-4">
                <b class="text-secondary d-block">Total Kg</b>
                <span id="id_modal2_tot_kg"></span>
            </div>
            <div class="form-group col-12 col-lg-4">
                <b class="text-secondary d-block">Total Volumen</b>
                <span id="id_modal2_tot_vol"></span>
            </div> --}}
            <div class="form-group col-12">
                <div id="id_modal2_detalle_guias"></div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>