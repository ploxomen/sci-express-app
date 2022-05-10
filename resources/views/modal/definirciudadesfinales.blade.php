<div class="modal fade" id="modal-difinir-cluster" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Definir ciudades de destino</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group d-flex justify-content-between flex-wrap">
                <span class="text-info mb-2">Seleccione una ciudad base</span>
                <button class="btn btn-primary btn-sm" id="btn-add-ciudad" title="Agregar más ciudades de destino"><i class="fas fa-plus"></i></button>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="form-group col-12 col-md-6 text-center">
                        <b>Distritos</b>
                    </div>
                    <div class="form-group col-12 col-md-6 text-center">
                        <b>Ciudades</b>
                    </div>
                </div> 
                <ol id="lista-destino-final" class="pl-2">
                    <li>
                       <div class="form-row">
                            <div class="form-group col-12 col-md-5">
                                <select class="form-control cb-ubigeo" data-ciudades="#id_ciudad_destino_1" id="id_distrito_clu_1"></select>
                            </div>
                            <div class="form-group col-12 col-md-5">
                                <select class="form-control cb-select2-tags" id="id_ciudad_destino_1"></select>
                            </div>
                            <div class="form-group col-12 col-md-2 text-right">
                              <input type="radio" name="destino_final" class="selection-base">
                              <button class="btn btn-danger btn-sm btn-delete-destino"><i class="far fa-trash-alt"></i></button>
                            </div>
                        </div> 
                    </li>
                </ol>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="agregar-cluster">Agregar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>