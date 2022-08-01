<div class="modal fade" id="modal-detalle-consultas" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalle de envío</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="p-2">
                <div class="border-guia p-2 mb-4 ocultar-box">
                    <h6 class="text-center text-primary"><b>DESTINATARIO</b></h6>
                    <div class="mb-1">
                        <b>Nombre: </b>
                        <span id="info_nombre_destinatario"></span>
                    </div>
                    <div>
                        <b>Dirección: </b>
                        <span id="info_direccion_destinatario"></span>
                    </div>
                    <div class="mb-1">
                        <span id="info_departamento_destinatario"></span> - <span id="info_provincia_destinatario"></span> - <span id="info_distrito_destinatario"></span>
                    </div>
                </div>
                <div class="border-guia p-2 ocultar-box">
                    <h6 class="text-center text-primary"><b>ESTADO - RASTREO</b></h6>
                    <div class="row">
                        <div class="form-group col-12 col-sm-6">
                            <b>Registrada el: <span id="info_fecha_rastreo_1" class="text-proceso"></span></b>
                        </div>
                        <div class="form-group col-12 col-sm-6">
                            <b>En Tránsito el: <span id="info_fecha_rastreo_5" class="text-proceso"></span></b>
                        </div>
                        <div class="form-group col-12 col-sm-6">
                            <b>En reparto el: <span id="info_fecha_rastreo_7" class="text-proceso"></span></b>
                        </div>
                        <div class="form-group col-12 col-sm-6">
                            <b>Entregado el: <span id="info_fecha_rastreo_8" class="text-proceso"></span></b>
                        </div>
                        <div class="form-group col-12 text-center">
                            <b>Cargo entregado: <span id="info_fecha_rastreo_13" class="text-proceso"></span></b>
                        </div>
                        <div class="form-group col-12">
                            <div class="bg-estado text-center">
                                <span>ESTADO ACTUAL: </span>
                                <span id="info_estado_actual"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Atras</button>
        </div>
      </div>
    </div>
  </div>