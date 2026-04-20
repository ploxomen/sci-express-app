<div class="modal fade" id="seleccionar-comprobantes" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccion de guías</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-filtro-seleccion" class="form-row mb-4 align-items-center">
                    <div class="form-group col-12 col-md-6 col-lg-3">
                        <label for="txt-fecha-inicio-guias">Fecha Inicio</label>
                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}" name="fecha_inicio"
                            id="txt-fecha-inicio-guias">
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-3">
                        <label for="txt-fecha-final-guias">Fecha Final</label>
                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}" name="fecha_fin"
                            id="txt-fecha-final-guias">
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-3">
                        <div class="custom-control custom-radio">
                            <input type="radio" value="por ciudad" checked id="customRadio1"
                                data-filter="#txt-ciudad-base" name="tipo_filtro" class="custom-control-input">
                            <label class="custom-control-label" for="customRadio1">Por ciudad base</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" value="por cluster" id="customRadio2" data-filter="#txt-clusters"
                                name="tipo_filtro" class="custom-control-input">
                            <label class="custom-control-label" for="customRadio2">Por cluster</label>
                        </div>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-2">
                        <label for="txt-ciudad-base">Ciudades Base</label>
                        <select name="ciudad_base" id="txt-ciudad-base" class="simple-select2">
                            @foreach ($ciudades as $ciudad)
                                <option value="{{ $ciudad->id_ciudad_base }}"> {{ $ciudad->nombre_ciudad }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-2" hidden>
                        <label for="txt-clusters">Clusters</label>
                        <select id="txt-clusters" name="cluster">
                            @foreach ($clusters as $clu)
                                <option value="{{ $clu->id_cluster }}" data-nro="{{$clu->nombreProvincia}}">{{ $clu->name }} {{ $clu->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-1">
                        <button class="btn btn-sm btn-primary" title="Buscar comprobantes" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <div class="table-responsive my-2">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                            id="seleccion-todos-comprobantes">
                                        <label class="custom-control-label" for="seleccion-todos-comprobantes"></label>
                                    </div>
                                </th>
                                <th>N° de Guía (SCI)</th>
                                <th>Fecha Emisión</th>
                                <th>N° comprobante (SUNAT)</th>
                            </tr>
                        </thead>
                        <tbody id="comprobantes-sunat">
                            <x-non-result-table />
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="seleccion-guias-procesar">Seleccionar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
