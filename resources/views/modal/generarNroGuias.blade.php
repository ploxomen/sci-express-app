<div class="modal fade" id="generar-nro-guias" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Generar Guías/OS</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group text-right">
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarNuevaFilaGuia" title="Agregar una fila nueva al final de la tabla">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <form id="frmPreGuiaPrevius" class="table-responsive">
                <table class="table table-sm table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>N° GUÍA/OS</th>
                            <th>TRAYECTO</th>
                            <th>VIA <br> TRASLADO</th>
                            <th>DEPARTAMENTO</th>
                            <th>PROVINCIA</th>
                            <th>DISTRITO</th>
                        </tr>
                    </thead>
                    <tbody id="tablaNuevaGuia">
                        <tr>
                            <td>1</td>
                            <td style="min-width: 120px; max-width: 120px;">
                                <input type="text" name="nroGuia[]" class="form-control form-control-sm" data-input="nroguia" pattern="[0-9]{3}[-][0-9]{7}" minlength="11" maxlength="11">
                            </td>
                            <td style="min-width: 130px; max-width: 130px;">
                                <select id="idTrayectoEnvio1" name="trayecto[]" class="simple-select2" required data-tipo="trayecto">
                                    @foreach ($trayecto as $tra)
                                        <option value="{{$tra->id}}">{{$tra->tipo}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="min-width: 130px; max-width: 130px;">
                                <select id="idViaTraslado1" name="traslado[]" class="simple-select2" required data-tipo="traslado">
                                    @foreach ($via_envios as $via)
                                        <option value="{{$via->id}}">{{$via->envio}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="min-width: 150px; max-width: 150px;">
                                <select class="form-control cb-ubigeo" name="departamento[]" required id="idDestiDepart1" data-consulta="provincia" data-info="idDestiProv1" data-info-distrito="idDestiDist1">
                                    <option value="" hidden>Seleccione un departamento</option>
                                    @foreach ($departamentos as $depar)
                                        <option value="{{$depar->id_ubigeo}}" {{$depar->id_ubigeo == 3926 ? 'selected' : ''}}>{{$depar->nombre_ubigeo}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="min-width: 150px; max-width: 150px;">
                                <select class="form-control cb-ubigeo" name="provincia[]" required id="idDestiProv1" data-consulta="distrito" data-info="idDestiDist1">
                                    <option value=""></option>
                                    @foreach ($provincias as $prov)
                                        <option value="{{$prov->id_ubigeo}}">{{$prov->nombre_ubigeo}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="min-width: 150px; max-width: 150px;">
                                <select class="form-control cb-ubigeo" name="distrito[]" required id="idDestiDist1">
                                </select>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit" hidden id="btnFormSendPrev"></button>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="btnSiguientePreGuia">Siguiente</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Atras</button>
        </div>
      </div>
    </div>
  </div>