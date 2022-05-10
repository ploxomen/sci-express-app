<div class="modal fade" id="modal-difinir-fletes" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Definir Fletes</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="frm-lista-fletes">
                <div class="row">
                  <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_tota_bult">Bulto Total</label>
                    <input type="number" name="tota_bult" id="id_tota_bult" min="1" class="form-control" required>
                  </div>
                  <div class="form-group col-12 col-md-6 col-lg-3">
                      <label for="id_flet_esti">Flete Total</label>
                      <input type="number" name="flet_esti" id="id_flet_esti" min="1" class="form-control" step="0.01" required>
                  </div>
                </div>
                <ol id="lista-ciudades-base" class="ml-3"></ol>
                <button type="submit" hidden id="enviar-fletes"></button>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="siguiente-fletes">Procesar embarque</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Atras</button>
        </div>
      </div>
    </div>
  </div>