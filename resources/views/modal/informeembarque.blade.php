<div class="modal fade bd-example-modal-lg" id="modal-informe-embarque" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Informe de Embarque</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="text-primary border-bottom pb-2 d-flex justify-content-between">
                <h5>Bulto total</h5>
                <h5 id="informe_bulto_total"></h5>
            </div>
            <form class="form-row">
                <div class="form-group col-6 col-lg-3" hidden>
                    <label for="">Flete Total</label>
                    <input type="number" disabled class="form-control form-control-sm">
                </div>
                <div class="form-group col-6 col-lg-3" hidden>
                    <label for="">Importe Total</label>
                    <input type="number" disabled class="form-control form-control-sm" value="0.00" step="0.01">
                </div>
                <div class="col-12"></div>
                <div class="form-group col-4 col-lg-2">
                    <label for="informe_embarque_nro_cajas">Cajas</label>
                    <input type="number" disabled id="informe_embarque_nro_cajas" class="form-control form-control-sm" min="0">
                </div>
                <div class="form-group col-4 col-lg-2">
                    <label for="informe_embarque_nro_jabas">Jabas</label>
                    <input type="number" disabled id="informe_embarque_nro_jabas" class="form-control form-control-sm" min="0">
                </div>
                <div class="form-group col-4 col-lg-2">
                    <label for="informe_embarque_nro_cajones">Cajones</label>
                    <input type="number" disabled id="informe_embarque_nro_cajones" class="form-control form-control-sm" min="0">
                </div>
                <div class="form-group col-4 col-lg-2">
                    <label for="informe_embarque_nro_sobres">Sobres</label>
                    <input type="number" disabled id="informe_embarque_nro_sobres" class="form-control form-control-sm" min="0">
                </div>
                <div class="form-group col-4 col-lg-2">
                    <label for="informe_embarque_nro_paquetes">Paquetes</label>
                    <input type="number" disabled id="informe_embarque_nro_paquetes" class="form-control form-control-sm" min="0">
                </div>
                <div class="form-group col-4 col-lg-2">
                    <label for="informe_embarque_num_otros">Otros</label>
                    <input type="number" disabled id="informe_embarque_num_otros" class="form-control form-control-sm" min="0">
                </div>
            </form>
            <h5 class="text-primary border-bottom pb-2">Informe de embarques</h5>
            <form id="frm-informar-embarques">
                <ol class="ml-3" id="informes-embarques"></ol>
                <input type="submit" id="enviar-informe" hidden>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="actualizar-embarque-informe">Actualizar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
    </div>
  </div>
</div>