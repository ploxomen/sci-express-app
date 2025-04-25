<div class="modal fade" id="denegar-guia" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Denegar guía</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="formulario-denegar">
                <ul id="contenido-desaprobar" class="form-group"></ul>
                <div class="form-group">
                  <label for="detalle-denegar">Detalle</label>
                  <textarea name="detalle" class="form-control form-control-sm" id="detalle-denega"></textarea>
                </div>
                <input type="submit" id="txt-enviar" hidden>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="btn-denegar-modal">Denegar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>