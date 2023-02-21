<div class="modal fade" id="modal-copiar-cliente" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Importar Configuración Cliente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="from-copiar-cliente" class="form-row">
                <div class="form-group col-12">
                    <label for="lista-clientes-copiar">Cliente</label>
                    <select id="lista-clientes-copiar" required name="cliente2">
                        <option></option>
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}" data-nro="{{$cliente->nro_doc}}">{{$cliente->nom_cli}}</option>
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