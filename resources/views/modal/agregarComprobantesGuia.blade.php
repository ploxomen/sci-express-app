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
                                    @foreach ($comprobantes as $comprobante)
                                        <option value="{{ $comprobante["comprobante"] }}" {{ $comprobante["comprobante"] === "FACTURA ELECTRÓNICA" ?? 'selected'}} data-series="{{ implode(";",$comprobante["series"]) }}">{{ $comprobante["comprobante"] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-2">
                                <label for="comprobante-serie-0">Serie</label>
                                <select class="cb-select2-tags" data-type="serie">
                                    @foreach ($comprobantes[0]["series"] as $serie)
                                        <option value="{{ $serie }}">{{$serie}}</option>
                                    @endforeach
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
