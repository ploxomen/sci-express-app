<div class="modal fade" id="modal-usuario-editar" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="frm-edid-user">
            <div class="px-3 border mb-2 form-row">
                <div class="form-group col-12 col-md-6">
                    <label for="type_document">Tipo de documento: <small class="text-danger">*</small> </label>
                    <select name="type_document" id="modal_type_document" class="form-control" required>
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="n_document">N° de documento: <small class="text-danger">*</small></label>
                    <input type="text" name="n_document" id="modal_n_document" class="form-control" required>
                </div>
                <div class="form-group col-12">
                    <label for="name">Nombres: <small class="text-danger">*</small></label>
                    <input type="text" name="name" id="modal_name" class="form-control" required>
                </div>
                <div class="form-group col-12">
                    <label for="last_name">Apellidos: <small class="text-danger">*</small></label>
                    <input type="text" name="last_name" id="modal_last_name" class="form-control" required>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="email">Correo: <small class="text-danger">*</small></label>
                    <input type="email" class="form-control" name="email" id="modal_email" required>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="phone">Celular:</label> 
                    <input type="tel" maxlength="9" minlength="9" class="form-control" name="phone" id="modal_phone">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="modal_sexo">Sexo:</label> 
                    <select name="sexo" id="modal_sexo" class="form-control">
                        <option value="" hidden>Selecione una opción</option>
                        <option value="H">Hombre</option>
                        <option value="M">Mujer</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="modal_nacimiento">Fecha Nacimiento:</label> 
                    <input type="date" class="form-control" name="nacimiento" id="modal_nacimiento">
                </div>
                <div class="form-group col-12">
                    <label for="modal_direction">Direccion:</label>
                    <textarea name="direction" class="form-control" id="modal_direction" cols="30" rows="2"></textarea>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="modal_id_area">Área: <small class="text-danger">*</small></label>
                    <select name="id_area" class="form-control" id="modal_id_area" required>
                        <option></option>
                        @foreach ($areas as $area)
                            <option value="{{$area->id}}">{{$area->area}}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" id="btn-send-edid-user" hidden></button>
            </div>
        </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="actualizar-usuario">Modificar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
</div>