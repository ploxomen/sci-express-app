 <div class="modal fade" id="modal-nueva-vehiculo" tabindex="-1" role="dialog" aria-labelledby="ModalVehiculo" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Vehículos de transporte</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="frm_vehiculo" class="form-row">
                <input type="hidden" id="vehiculo_id">
                <div class="form-group col-12 col-lg-6">
                    <label for="id_placa">Placa</label>
                    <input type="text" required class="form-control" id="id_placa" name="placa">
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_year">Año</label>
                    <input type="text" required class="form-control" id="id_year" name="year">
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_marca">Marca</label>
                    <input type="text" required class="form-control" id="id_marca" name="marca">
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_modelo">Modelo</label>
                    <input type="text" required class="form-control" id="id_modelo" name="modelo"></textarea>
                </div>
                <div class="form-group col-12">
                    <button type="submit" hidden id="btn-send-frm"></button>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="btn-save">Guardar</button>
            <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
        </div>
        </div>
    </div>
</div>