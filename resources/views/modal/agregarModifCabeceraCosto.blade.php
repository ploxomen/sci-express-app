<div class="modal fade" id="modal-cabecera-costo-cliente" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="titulo-modal-flete">Cabecera Costo Facturación Cliente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="cabecera-facturacion-costo-cliente-form" class="form-row">
                <div class="form-group col-4">
                    <label for="id_cabecera_minimo_costo">Kg. Mínimo</label>
                    <input type="number" required class="form-control" name="kg_minimo" id="id_cabecera_minimo_costo" min="0" step="0.01">
                </div>
                <div class="form-group col-4">
                    <label for="id_cabecera_maximo_costo">Kg. Máximo</label>
                    <input type="number" required class="form-control" name="kg_maximo" id="id_cabecera_maximo_costo" step="0.01">
                </div>
                <div class="form-group col-4">
                    <label for="id_via_envio_costo">Via Envio</label>
                    <select id="id_via_envio_costo" class="simple-select2" name="id_via_envio" required>
                        @foreach ($viaEnvio as $via)
                            <option value="{{$via->id}}">{{$via->envio}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="active_infinito" class="custom-control-input" id="checkInfinito">
                        <label class="custom-control-label" for="checkInfinito">Kg. máximo infinito</label>
                    </div>
                </div>
                <input type="submit" id="btn-cabecera-fac-costo-cliente" hidden>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="agregar-fact-costo-cliente">Agregar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>