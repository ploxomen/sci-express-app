<div class="modal fade" id="modal-usuario-cliente" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Asignar Centro de Costo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <span class="text-primary">Asigne los clientes para el usuario con su debido centro de costo</span>
        </div>
        <div class="form-group form-row">
            <div class="form-group text-center col-4">
                <span class="text-info">Cliente</span>
            </div>
            <div class="form-group text-center col-3">
              <span class="text-info">Centro de costo</span>
            </div>
            <div class="form-group text-center col-4">
              <span class="text-info">N° de Cuenta</span>
            </div>
            <div class="form-group text-right col-1">
              <button class="btn btn-sm btn-outline-primary" id="agregar-nuevo-cliente" title="Agregar clientes"><i class="fas fa-plus"></i></button>
            </div>
        </div>
        <form class="form-group" id="form-usuario-cliente">
            <ol id="lista-contenedora-clientes" class="ml-3">
                <li>
                    <div class="form-row">
                        <div class="form-group col-12 col-md-4">
                            <select name="id_documento_cli[]" id="documento_cli_1" required class="simple-select2-cliente">
                                <option></option>
                                @foreach ($clientes as $k => $cliente)
                                    <option data-nro="{{$cliente->nro_doc}}" value="{{$cliente->id}}">{{$cliente->nom_cli}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-5 col-md-3">
                            <select name="centro_costo[]" id="cc_cli_1" required class="simple-select2"></select>
                        </div>
                        <div class="form-group col-5 col-md-4">
                            <select id="c_cli_1" multiple class="cb-mult-nuevo"></select>
                        </div>
                        <div class="form-group col-2 col-md-1 text-right">
                            <button class="btn btn-sm btn-outline-danger" type="button" title="Eliminar cliente"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                </li>
            </ol>
            <button type="submit" hidden id="enviar-usuario-cliente"></button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="agregar-usuario-cliente">Agregar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Regresar</button>
      </div>
    </div>
  </div>
</div>