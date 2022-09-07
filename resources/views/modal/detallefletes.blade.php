<div class="modal fade" id="modal-detalle-misfletes" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="titulo-modal-flete">Agregar Flete</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="modificar-fletes" class="form-row">
                <div class="form-group col-12"><label for="id_text_ciudad_base">Ciudad Base</label><input type="text" name="ciudad_base" required id="id_text_ciudad_base" class="form-control"></div>
                <div class="form-group col-12 col-md-4"><label for="id_text_sobres">Sobres</label><input type="number" step="0.01" name="sobres" id="id_text_sobres" class="form-control"></div>
                <div class="form-group col-12 col-md-4"><label for="id_text_cajas">Cajas</label><input type="number" step="0.01" name="cajas" id="id_text_cajas" class="form-control"></div>
                <div class="form-group col-12 col-md-4"><label for="id_text_cajones">Cajones</label><input type="number" step="0.01" name="cajones" id="id_text_cajones" class="form-control"></div>
                <div class="form-group col-12 col-md-4"><label for="id_text_paquetes">Paquetes</label><input type="number" step="0.01" name="paquetes" id="id_text_paquetes" class="form-control"></div>
                <div class="form-group col-12 col-md-4"><label for="id_text_jabas">Jabas</label><input type="number" step="0.01" name="jabas" id="id_text_jabas" class="form-control"></div>
                <div class="form-group col-12 col-md-4"><label for="id_text_parihuelas">Otros</label><input type="number" step="0.01" name="parihuelas" id="id_text_parihuelas" class="form-control"></div>
                <input type="submit" hidden id="send-fletes">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="agregar-fletes">Agregar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>