<div class="modal fade" id="modal-copiar-cluster" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Importar Configuración Cluster</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="from-copiar-cliente" class="form-row">
                <div class="form-group col-12">
                    <label for="lista-cluster-copiar">Cluster</label>
                    <select id="lista-cluster-copiar" required name="cluster2">
                        <option></option>
                        @foreach ($clusters as $cluster)
                            <option value="{{$cluster->id_cluster}}" data-nro="{{$cluster->nomProv}}">{{$cluster->name.' ' . $cluster->last_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12">
                  <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="idInclTabAsig" name="cabecera" checked>
                      <label class="custom-control-label" for="idInclTabAsig">Incluir tabla asignación de costos</label>
                  </div>
              </div>
                <input type="submit" id="btnImportDataCliente" hidden>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="btnImportarDataCliente">Importar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>