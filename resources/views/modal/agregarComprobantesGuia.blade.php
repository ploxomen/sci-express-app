<div class="modal fade" id="agregar-comprobantes" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar comprobantes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex mb-3 justify-content-between flex-wrap">
                    <small class="text-muted">Precione <b class="text-primary">guardar</b> para efectuar los cambios</small>
                    <button class="btn btn-sm btn-primary" title="Agregar comprobante" id="btn-agregar-comprobantes">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <ul id="lista-comprobantes">
                    <li class="mb-2">
                        <div class="form-row">
                            <div class="col-12 col-md-6">
                                <label for="comprobante-tipo-0">Tipo comprobante</label>
                                <select class="simple-select2" data-type="comprobante">
                                    <option value="FACTURA ELECTRÓNICA" selected>FACTURA ELECTRÓNICA</option>
                                    <option value="BOLETA ELECTRÓNICA">BOLETA ELECTRÓNICA</option>
                                    <option value="GUIA DE REMISIÓN (R) ELECTRÓNICA">GUIA DE REMISIÓN (R) ELECTRÓNICA
                                    </option>
                                    <option value="GUIA DE REMISIÓN (T) ELECTRONICA">GUIA DE REMISIÓN (T) ELECTRÓNICA
                                    </option>
                                    <option value="GUIA DE INTERNAMIENTO">GUIA DE INTERNAMIENTO</option>
                                    <option value="ORDEN DE COMPRA">ORDEN DE COMPRA</option>
                                    <option value="OTRO" class="delete-option">OTRO</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-2">
                                <label for="comprobante-serie-0">Serie</label>
                                <select class="cb-select2-tags" data-type="serie">
                                    <option value="F001" selected>F001</option>
                                    <option value="F008">F008</option>
                                    <option value="F009">F009</option>
                                    <option value="F009">F009</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <label for="comprobante-numero-0">Número</label>
                                <input type="text" class="form-control form-control-sm" data-type="numero">
                            </div>
                            <div class="col-12 col-md-1">
                                <button title="Eliminar comprobante" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="guardar-comprobantes">Guardar</button>
                <button type="button" class="btn btn-secondary" id="eliminar-comprobantes" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
