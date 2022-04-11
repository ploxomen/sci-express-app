<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Definir ciudades de destino</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <span class="text-info mb-2">Seleccione la ciudad base</span>
                <button class="btn btn-primary btn-sm" title="Agregar más ciudades base"><i class="fas fa-plus"></i></button>
            </div>
            <div class="form-group">
                <ol id="lista-destino-final">
                    <li>
                        <div class="row">
                            <div class="form-group col-12 col-md-6">
                                <b>Distritos</b>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <b>Ciudades</b>
                            </div>
                        </div> 
                       <div class="row">
                            <div class="form-group col-12 col-md-6">
                                <select class="form-control cb-ubigeo" id="id_distrito_clu_1"></select>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <select class="form-control cb-select2-tags" id="id_ciudad_destino_1"></select>
                            </div>
                        </div> 
                    </li>
                </ol>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>