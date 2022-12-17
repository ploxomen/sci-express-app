<div class="modal fade" id="modal-usuario-editar" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="frm-edid-user" class="form-row">
                <div class="form-group col-12 col-md-4 col-lg-4">
                    <label for="type_document">Tipo de documento:</label>
                    <select name="type_document" id="modal_type_document" class="form-control simple-select2 cb-tipo-doc" data-input="modal_n_document">
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-5 col-lg-4">
                    <label for="n_document">N° de documento:</label>
                    <input type="text" name="n_document" id="modal_n_document" class="form-control">
                </div>
                <div class="form-group col-12 col-md-3 col-lg-4">
                    <label for="modal_id_area">Área: <small class="text-danger">*</small></label>
                    <select name="id_area" class="form-control" id="modal_id_area" required>
                        <option></option>
                        @foreach ($areas as $area)
                            <option value="{{$area->id}}">{{$area->area}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="name">Nombres: <small class="text-danger">*</small></label>
                    <input type="text" name="name" id="modal_name" class="form-control" required>
                </div>
                <div class="form-group col-12 col-md-6">
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
                
                <div class="col-12 change_centro_costo">
                    <h5 class="text-secondary">Centro de Costo</h5>
                </div>
                <div class="form-group col-12 change_centro_costo">
                    <span class="text-primary">Asigne los clientes para el usuario con su debido centro de costo</span>
                </div>
                <div class="form-group text-center col-4 change_centro_costo">
                    <span class="text-info">Cliente</span>
                </div>
                <div class="form-group text-center col-3 change_centro_costo">
                    <span class="text-info">Centro de costo</span>
                </div>
                <div class="form-group text-center col-4 change_centro_costo">
                    <span class="text-info">N° de Cuenta</span>
                </div>
                <div class="form-group text-right col-1 change_centro_costo">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="agregar-nuevo-cliente" title="Agregar clientes"><i class="fas fa-plus"></i></button>
                </div>
                <div class="for-group col-12 change_centro_costo">
                    <ol id="lista-contenedora-clientes" class="ml-3">
                        <li>
                            <div class="form-row">
                                <div class="form-group col-12 col-md-4">
                                    <select name="id_documento_cli[]" id="documento_cli_1" class="simple-select2-cliente">
                                        <option></option>
                                        @foreach ($clientes as $k => $cliente)
                                            <option data-nro="{{$cliente->nro_doc}}" value="{{$cliente->id}}">{{$cliente->nom_cli}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-5 col-md-3">
                                    <select name="centro_costo[]" id="cc_cli_1" class="simple-select2"></select>
                                </div>
                                <div class="form-group col-5 col-md-4">
                                    <select name="centro_cuenta[]" id="cuenta_cli_1" multiple class="cb-mult-nuevo"></select>
                                </div>
                                <div class="form-group col-2 col-md-1 text-right">
                                    <button class="btn btn-sm btn-outline-danger" type="button" title="Eliminar cliente"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                        </li>
                    </ol>
                </div>
                <button type="submit" id="btn-send-edid-user" hidden></button>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="actualizar-usuario">Modificar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
</div>