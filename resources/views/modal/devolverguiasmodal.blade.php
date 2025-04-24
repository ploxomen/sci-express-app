<div class="modal fade" id="guardar-detalles-nuevo" data-backdrop="static" data-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Embarcar guías</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="retornar-cargos" class="row">
                    <div class="col-12">
                        <h6 class="text-primary"><i class="fas fa-caret-right"></i> Detalles</h6>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 form-group">
                        <label for="id_fch_emba">Fecha de Embarque</label>
                        <input type="date" id="id_fch_emba" name="fch_emba" required class="form-control"
                            value="{{ $hoy }}">
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 form-group">
                        <label for="id_id_empr_tran">Empresa Transporte</label>
                        <select name="id_empr_tran" id="id_id_empr_tran" class="form-control simple-select2" required>
                            <option></option>
                            @foreach ($transportes as $t)
                                <option value="{{ $t->id }}">{{ $t->empresa }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 form-group">
                        <label for="id_tipo_comprobantes">Comprobantes</label>
                        <select id="id_tipo_comprobantes" name="tipo_comprobantes" class="simple-select2" multiple required>
                            <option></option>
                            <option value="GUIA TRANSPORTISTA">GUIA TRANSPORTISTA</option>
                            <option value="FACTURA">FACTURA</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3 form-group">
                        <label for="id_nro_guias">N° Comprobantes</label>
                        <select id="id_nro_guias" class="form-control cb-mult-nuevo" multiple required>
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-xl-3">
                        <label for="id_importe_embarque">Importe Factura</label>
                        <input type="number" name="importe_embarque" step="0.01" class="form-control" required>
                    </div>
                    <div class="form-group col-12 col-md-6 col-xl-3">
                        <label for="id_hr_fin_esti_emba">Fecha Llegada</label>
                        <input type="date" name="fecha_llegada" id="id_hr_fin_esti_emba" class="form-control"
                            required>
                    </div>
                    <div class="form-group col-12 col-md-6 col-xl-3">
                        <label for="id_hr_fin_esti_emba">Hr Llegada</label>
                        <input type="time" name="hr_llegada" id="id_hr_fin_esti_emba" class="form-control" required>
                    </div>
                    <div class="col-12 form-group">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="text-primary"><i class="fas fa-caret-right"></i> Bultos</h6>
                            <div style="max-width: 150px; margin-left: auto;">
                                <input type="number" id="total_bultos" name="total_bultos" min="0" required class="form-control"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-6 col-md-3 col-lg-2">
                        <label for="id_nro_cajas">Cajas</label>
                        <input type="number" name="nro_cajas" id="id_nro_cajas" class="form-control calc-total"
                            min="0">
                    </div>
                    <div class="form-group col-6 col-md-3 col-lg-2">
                        <label for="id_nro_jabas">Jabas</label>
                        <input type="number" name="nro_jabas" id="id_nro_jabas" class="form-control calc-total"
                            min="0">
                    </div>
                    <div class="form-group col-6 col-md-3 col-lg-2">
                        <label for="id_nro_cajones">Cajones</label>
                        <input type="number" name="nro_cajones" id="id_nro_cajones" class="form-control calc-total"
                            min="0">
                    </div>
                    <div class="form-group col-6 col-md-3 col-lg-2">
                        <label for="id_nro_sobres">Sobres</label>
                        <input type="number" name="nro_sobres" id="id_nro_sobres" class="form-control calc-total"
                            min="0">
                    </div>
                    <div class="form-group col-6 col-md-3 col-lg-2">
                        <label for="id_nro_paquetes">Paquetes</label>
                        <input type="number" name="nro_paquetes" id="id_nro_paquetes"
                            class="form-control calc-total" min="0">
                    </div>
                    <div class="form-group col-6 col-md-3 col-lg-2">
                        <label for="id_num_otros">Otros</label>
                        <input type="number" name="num_otros" id="id_num_otros" class="form-control calc-total"
                            min="0">
                    </div>
                    <div class="form-group col-12">
                        <label for="id_detalle_otro">Detallar otro</label>
                        <textarea name="detalle_otro" maxlength="255" id="id_detalle_otro" class="form-control"></textarea>
                    </div>
                    <div class="col-12">
                        <h6 class="text-primary"><i class="fas fa-caret-right"></i> Imágenes</h6>
                    </div>
                    <div class="form-group col-12">
                        <div class="d-flex justify-content-center flex-wrap gap-1 flex-row-reverse" id="list-img">
                            <input type="file" hidden id="input-img-new" accept="image/*">
                            <button type="button" class="new-img" id="btn-new-img"><i
                                    class="far fa-image"></i></button>
                        </div>
                    </div>
                    <button type="submit" hidden id="btn-terminar-embarque"></button>
                </form>
                <div class="d-flex mb-3" style="gap: 10px;">
                    <div>
                        <div class="cuadro table-primary"></div>
                        <span>Guardados</span>
                    </div>
                    <div>
                        <div class="cuadro table-warning"></div>
                        <span>Nuevos</span>
                    </div>
                </div>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>N° guía</th>
                            <th>Nro Doc Remi</th>
                            <th>Nom Cli Remi</th>
                            <th>Nro Doc Dest</th>
                            <th>Nom Remi Dest</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-guias-modal"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-guardar-guias-embarcar">Embarcar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
