<div class="modal fade" id="modal-usuario-roles" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Roles</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <h5 class="text-center text-primary">Lista de roles</h5>
            <h6 class="text-info form-group">Seleccione y pulse el botón actualizar para guardar los cambios</h6>
            <ul class="list-two-columns">
              @foreach ($roles as $rol)
              <li class="form-group">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input checkbox-modal-rol" id="usuario_rol_{{$rol->id}}" value="{{$rol->id}}">
                  <label class="custom-control-label" for="usuario_rol_{{$rol->id}}">{{$rol->rol}}</label>
                </div>
              </li>
              @endforeach
            </ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="btn-actualizar-rol">Actualizar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
</div>