<div class="modal fade" id="editar-cluster" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
                    <div class="col-12 form-group col-md-6 col-lg-3 col-xl-4">
                        <label for="id_ruc_clu">N° R.U.C</label>
                        <input type="text" class="form-control" id="id_ruc_clu" name="ruc_clu" minlength="11" maxlength="11">
                    </div>
                    <div class="col-12 form-group col-md-6 col-lg-9 col-xl-8">
                        <label for="id_razon_clu">Razón Social</label>
                        <input type="text" class="form-control" id="id_razon_clu" name="razon_clu">
                    </div>
                    <div class="col-12 form-group col-md-6">
                        <label for="id_email">Correo <small class="text-danger">*</small></label>
                        <input type="email" class="form-control" id="id_email" name="email" required>
                        <small class="text-danger">Recuerda que el correo será el usuario del cluster</small>
                    </div>
                    <div class="col-12 form-group col-md-6">
                        <label for="id_phone">Contacto principal</label>
                        <input type="tel" class="form-control" id="id_phone" name="phone" minlength="9" maxlength="9">
                        <small class="text-info">Es el número de celular personal del cluster</small>
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
                    <div class="col-12 form-group col-md-6 col-lg-4">
                        <label for="id_departamento_clu">Departamento <small class="text-danger">*</small></label>
                        <select id="id_departamento_clu" name="departamento_clu" required class="form-control cb-ubigeo" data-consulta="provincia" data-info="id_provincia_clu" data-info-distrito="id_distrito_clu">
                            <option value="" hidden>Seleccione un departamento</option>
                            @foreach ($departamentos as $depar)
                                <option value="{{$depar->id_ubigeo}}">{{$depar->nombre_ubigeo}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 form-group col-md-6 col-lg-4">
                        <label for="">Provincia <small class="text-danger">*</small></label>
                        <select class="form-control cb-ubigeo" id="id_provincia_clu" data-combo="lista_provincia_clu" name="provincia_clu" data-consulta="distrito"  data-info="id_distrito_clu" required></select>
                    </div>
                    <div class="col-12 form-group col-md-6 col-lg-4">
                        <label for="id_distrito_clu">Distrito <small class="text-danger">*</small></label>
                        <select class="form-control cb-ubigeo" id="id_distrito_clu" data-combo="lista_distrito_clu" name="distrito_clu" required></select>
                    </div>
                    <div class="col-12 form-group">
                        <label for="id_direccion_clu">Dirección <small class="text-danger">*</small></label>
                        <textarea class="form-control" name="direccion_clu" id="id_direccion_clu" required></textarea>
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