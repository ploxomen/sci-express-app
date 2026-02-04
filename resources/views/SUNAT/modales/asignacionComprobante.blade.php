<div class="modal fade" id="comprobante-asignacion" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asignación de comprobante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm-asignacion-comprobante" class="form-row">
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="nro_guia_sci">N° Guia - SCI</label>
                        <select name="id_guia" id="nro_guia_sci" required class="simple-select2"></select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="tipo_comprobantes_asignacion">Tipos</label>
                        <select class="form-control cb-filtros" name="tipo_comprobante_sunat" id="tipo_comprobantes_asignacion" required>
                            <option value="09">GUIA REMISION REMITENTE</option>
                            <option value="31">GUIA REMISION TRANSPORTISTA</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="nro_comprobante_sunat">N° comprobante SUNAT</label>
                        <input type="text" class="form-control form-control-sm" placeholder="0000-000000" id="nro_comprobante_sunat" required name="nro_comprobante_sunat">
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <button class="btn btn-sm btn-info" type="button" title="Ver GUIA SCI" id="consulta-guia-sci">
                            <i class="fas fa-eye"></i>
                            GUIA SCI
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="frm-asignacion-comprobante" class="btn btn-primary">Asociar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
