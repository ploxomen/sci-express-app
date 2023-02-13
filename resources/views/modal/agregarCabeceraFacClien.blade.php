<div class="modal fade" id="modal-cabecera-cliente" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="titulo-modal-flete">Cabecera Facturación Cliente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="cabecera-facturacion-cliente-form" class="form-row">
                <div class="form-group col-4">
                    <label for="id_cabecera_numero">Número</label>
                    <input type="number" required disabled class="form-control" name="numero" id="id_cabecera_numero" min="1">
                </div>
                <div class="form-group col-8">
                    <label for="lista-via-envio">Via Envio</label>
                    <select id="lista-via-envio" class="simple-select2" name="id_via_envio">
                        @foreach ($viaEnvio as $via)
                            <option value="{{$via->id}}">{{$via->envio}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-6">
                    <label for="lista-tipo-cabecera">Tipo</label>
                    <select id="lista-tipo-cabecera" class="simple-select2" name="tipo_calculo">
                        <option value="normal">Monto Normal</option>
                        <option value="base">Monto Base</option>
                        <option value="adicional">Monto adicional</option>
                    </select>
                </div>
                <div class="form-group col-6" hidden id="box-kg-base" data-default="#box-default">
                    <label for="id-kg-base">Kg Base</label>
                    <input name="peso_maximo" type="number" required disabled class="form-control" step="0.01" id="id-kg-base">
                </div>
                <div class="form-group col-6" id="box-default">
                    <label for="id-monto-default">Monto default</label>
                    <input name="monto" type="number" class="form-control" step="0.01" id="id-monto-default">
                </div>
                <div class="form-group col-12">
                    <label for="id_descripcion">Descripción</label>
                    <textarea name="descripcion" id="id_descripcion" cols="30" rows="5" class="form-control"></textarea>
                </div>
                <input type="submit" id="btn-cabecera-fac-cliente" hidden>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="agregar-fact-cliente">Agregar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>