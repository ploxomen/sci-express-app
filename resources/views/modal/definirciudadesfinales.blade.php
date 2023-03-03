<div class="modal fade" id="modal-difinir-cluster" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Definir ciudades de destino</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group text-right">
              <button class="btn btn-primary btn-sm" type="button" id="btn-add-ciudad" title="Agregar más ciudades de destino"><i class="fas fa-plus"></i></button>
            </div>
            <div class="form-group">
                <b>
                    <span class="text-info">Distrito</span> / 
                    <span class="text-info">Ciudad</span> / 
                    <span class="text-info">Zona Complejo Distante</span> / 
                    <span class="text-info">Costo FAU</span> /
                    <span class="text-info" title="Movilidad a cobrar">Mov 1</span>
                </b>
            </div>
            <ol id="lista-destino-final" class="pl-3">
              <li data-type="new">
                  <div class="form-row">
                      <div class="form-group col-6 col-lg-3">
                          <select class="form-control cb-ubigeo" required data-ciudades="#id_ciudad_new_destino_1" data-idciudad="1" id="id_distrito_new_clu_1"></select>
                      </div>
                      <div class="form-group col-6 col-lg-2">
                          <select class="form-control cb-select2-tags" id="id_ciudad_new_destino_1"></select>
                      </div>
                      <div class="form-group col-6 col-sm-6 col-lg-2">
                          <select required class="form-control simple-select2" id="opcion-alejado-new-destino-1" data-costo="#costo-new-alejado-1"></select>
                      </div>
                      <div class="form-group col-6 col-sm-4 col-lg-2">
                          <input type="number" placeholder="FAU" id="costo-new-alejado-1" disabled required step="0.01" min="0" class="form-control form-control-sm txtFau">
                      </div>
                        <div class="form-group col-6 col-sm-4 col-lg-2">
                          <input type="number" placeholder="MOV" id="costo-new-mov-1" step="0.01" min="0" class="form-control form-control-sm txtMov1">
                      </div>
                      <div class="form-group col-12 col-sm-2 col-lg-1 text-right">
                          <input type="radio" checked name="destino_final" class="selection-base">
                          <button type="button" class="btn btn-danger btn-sm btn-delete-destino"><i class="far fa-trash-alt"></i></button>
                      </div>
                  </div> 
              </li>
            </ol>
          </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="agregar-cluster">Agregar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>