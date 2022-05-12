<div class="modal fade" id="modal-usuario-password" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Restablecer contraseña</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="nuevo_password">Nueva contraseña</label>
                <input type="text" class="form-control text-minuscula" id="nuevo_password" value="sistemaenvio{{$year}}" data-default="sistemaenvio{{$year}}">
            </div>
            <div class="form-group">
                <small class="text-info">Esta contraseña será temporal hasta que el usuario ingrese y lo cambie.</small>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="cambiar-password">Restablecer</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>