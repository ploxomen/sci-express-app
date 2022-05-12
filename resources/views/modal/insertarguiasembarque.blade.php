<div class="modal fade" id="insertar-guias-embarque" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Agregar guías de embarque</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form id="frm-lista-guias-embarque">
              <ol id="lista-guias-embarque" class="ml-3"></ol>
              <button type="submit" hidden id="enviar-informe"></button>
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn-generar-informe">Generar informe</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>