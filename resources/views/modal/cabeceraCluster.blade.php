<div class="modal fade" id="modal-cabecera-costo-cluster" data-backdrop="static" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titulo-modal-flete">Cabecera Costo Facturación Cluster</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form id="cabecera-facturacion-costo-cluster-form" class="form-row">
              <div class="form-group col-4">
                  <label for="id_cabecera_tipo_calculo">Tipo</label>
                  <select id="id_cabecera_tipo_calculo" class="simple-select2" name="tipo_calculo">
                      <option value="normal">Monto Normal</option>
                      <option value="base">Monto Base</option>
                      <option value="adicional">Monto adicional</option>
                  </select>
              </div>
              <div class="form-group col-4">
                  <label for="id_cabecera_kg_minimo">Kg. Mínimo</label>
                  <input type="number" required class="form-control" name="kg_minimo" id="id_cabecera_kg_minimo" min="0" step="0.01">
              </div>
              <div class="form-group col-4">
                  <label for="id_cabecera_kg_maximo">Kg. Máximo</label>
                  <input type="number" required class="form-control" name="kg_maximo" id="id_cabecera_kg_maximo" step="0.01">
              </div>
              <div class="form-group col-4" hidden>
                <label for="id_cabecera_kg_base">Kg. Base</label>
                <input type="number" disabled required class="form-control" name="kg_base" id="id_cabecera_kg_base" step="0.01">
            </div>
              <div class="form-group col-12">
                  <div class="custom-control custom-switch">
                      <input type="checkbox" name="active_infinito" class="custom-control-input" id="checkInfinito">
                      <label class="custom-control-label" for="checkInfinito">Kg. máximo infinito</label>
                  </div>
              </div>
              <input type="submit" id="btn-cabecera-fac-costo-cluster" hidden>
          </form>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="agregar-fact-costo-cluster">Agregar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>