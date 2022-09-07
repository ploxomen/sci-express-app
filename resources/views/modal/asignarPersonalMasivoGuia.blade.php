<div class="modal fade" id="modal-asignar-masivo" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="titulo-modal-flete">Asignación masiva</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="modificar-fletes" class="form-row">
                <div class="form-group col-12">
                    <label for="id_asignacion_user">Usuarios</label>
                    <select name="asignacion_user" id="id_asignacion_user" class="simple-select2">
                        @foreach ($usuarios as $user)
                            @if ($user->status === 1)
                                <option value="{{$user->id}}">{{$user->name}} {{$user->last_name}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-12 form-group">
                    <h6 class="text-primary">Lista de Guías/OS por asignar</h6>
                </div>
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <th>
                                    <input type="checkbox" title="Selecionar todo" id="seleccion-todo-asignar">
                                </th>
                                <th>N° Guía/OS</th>
                                <th>Fecha Emi</th>
                            </thead>
                            <tbody id="llenar-guias-por-asignar"></tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="asignar-masivo-guias">Asignar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>