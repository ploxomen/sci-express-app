<div class="modal fade" id="modal-editar-cluster" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar cluster</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
                <form id="edid_cluster" class="py-3 bg-white form-row">
                    <input type="hidden" id="id_cluster_id" name="cluster_id" required>
                    <div class="col-12 form-group col-md-6 col-lg-4">
                        <label for="id_n_document">N° D.N.I <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="id_n_document" required name="n_document" minlength="8" maxlength="8">
                    </div>
                    <div class="col-12 form-group col-md-6 col-lg-4">
                        <label for="id_name">Nombres <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="id_name" required name="name" maxlength="255">
                    </div>
                    <div class="col-12 form-group col-md-6 col-lg-4">
                        <label for="id_last_name">Apellidos <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="id_last_name" required name="last_name" maxlength="255">
                    </div>
                    <div class="col-12 form-group col-md-6 col-lg-3 col-xl-3">
                        <label for="id_ruc_clu">N° R.U.C</label>
                        <input type="text" class="form-control" id="id_ruc_clu" name="ruc_clu" minlength="11" maxlength="11">
                    </div>
                    <div class="col-12 form-group col-md-6 col-lg-7 col-xl-6">
                        <label for="id_razon_clu">Razón Social</label>
                        <input type="text" class="form-control" id="id_razon_clu" name="razon_clu">
                    </div>
                    <div class="col-12 form-group col-md-6 col-lg-2 col-xl-3">
                        <label for="id_phone">Celular principal</label>
                        <input type="tel" class="form-control" id="id_phone" name="phone" minlength="9" maxlength="9">
                    </div>
                    <div class="col-12 form-group col-md-6 col-lg-4">
                        <label for="id_inst_fina_clu">Inst Financiera</label>
                        <select id="id_inst_fina_clu" class="form-control simple-select2" name="inst_fina_clu">
                            <option></option>
                            <option value="BANCO DE CRÉDITO DEL PERÚ">BANCO DE CRÉDITO DEL PERÚ</option>
                            <option value="BANCO DE LA NACIÓN">BANCO DE LA NACIÓN</option>
                            <option value="BANCO INTERAMERICADO DE FINANZAS (BanBif)">BANCO INTERAMERICADO DE FINANZAS (BanBif)</option>
                            <option value="BANCO PICHINCHA">BANCO PICHINCHA</option>
                            <option value="BBVA">BBVA</option>
                            <option value="INTERBANK">INTERBANK</option>
                            <option value="MI BANCO">MI BANCO</option>
                            <option value="SCOTIABANK">SCOTIABANK</option>
                        </select>
                    </div>
                    <div class="col-12 form-group col-md-6 col-lg-8">
                        <label for="id_nro_cuenta_clu">N° Cuenta</label>
                        <input type="text" class="form-control" name="nro_cuenta_clu" id="id_nro_cuenta_clu">
                    </div>
                    <div class="col-12 form-group">
                        <label for="id_nro_cuenta_clu">Consignados</label>
                        <select class="form-control cb-select2-tags" name="consignados[]" id="id_consignados" multiple></select>
                    </div>
                    <div class="col-12 form-group col-md-6">
                        <label for="id_departamento_clu">Departamento <small class="text-danger">*</small></label>
                        <select id="id_departamento_clu" name="departamento_clu" required class="form-control cb-ubigeo" data-consulta="provincia" data-info="id_provincia_clu" data-info-distrito="id_distrito_clu">
                            <option value="" hidden>Seleccione un departamento</option>
                            @foreach ($departamentos as $depar)
                                <option value="{{$depar->id_ubigeo}}">{{$depar->nombre_ubigeo}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 form-group col-md-6">
                        <label for="">Provincia <small class="text-danger">*</small></label>
                        <select class="form-control cb-ubigeo" id="id_provincia_clu" data-combo="lista_provincia_clu" name="provincia_clu" data-consulta="distrito"  data-info="id_distrito_clu" required></select>
                    </div>
                    <div class="form-group col-12">
                        <h5 class="text-primary">Ciudades de destino</h5>
                        <div class="form-group text-right">
                            <button class="btn btn-primary btn-sm" type="button" id="btn-add-ciudad" title="Agregar más ciudades de destino"><i class="fas fa-plus"></i></button>
                        </div>
                        <div class="row">
                            <div class="form-group col-12 col-md-6 text-center">
                                <b>Distritos</b>
                            </div>
                            <div class="form-group col-12 col-md-6 text-center">
                                <b>Ciudades</b>
                            </div>
                        </div> 
                        <ol id="lista-destino-final" class="pl-3"></ol>
                    </div>
                    <div class="form-group col-12">
                        <small class="text-danger">Recuerda, para guardar todos los cambios presiona el botón "Actualizar"</small>
                    </div>
                    <div class="col-12 form-group">
                        <button type="submit" id="update-cluster" hidden></button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-actualizar-cluster" data-click="update-cluster">Actualizar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
      </div>
    </div>
  </div>