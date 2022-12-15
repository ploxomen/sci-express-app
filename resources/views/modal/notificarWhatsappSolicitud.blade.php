<div class="modal fade" id="modal-notificar-whatsapp" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Aprobación Solicitud</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="modificar-fletes" class="form-row">
                <div class="col-12 form-group">
                    <h6 class="text-primary">Lista de Solicitudes para notificar</h6>
                </div>
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th rowspan="2">
                                        <input type="checkbox" title="Selecionar todo para notificar" id="seleccion-todo-notificar">
                                    </th>
                                    <th rowspan="2">N° Solicitud</th>
                                    <th colspan="2">Recojo</th>
                                    <th rowspan="2">Acción</th>
                                </tr>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                </tr>
                            </thead>
                            <tbody id="llenar-solicitud">
                                @if ($confirmacion->count() > 0)
                                    @foreach ($confirmacion as $solicitud)
                                        <tr id="solicitud-notificacion-{{$solicitud->id}}">
                                            <td>
                                                <input type="checkbox" name="solicitud[]" class="check-notificar" value="{{$solicitud->id}}">
                                            </td>
                                            <td>
                                                {{str_pad($solicitud->id,5,"0",STR_PAD_LEFT)}}
                                            </td>
                                            <td>
                                                <input type="date" class="form-control form-control-sm" value="{{$solicitud->dia_recojo}}">
                                            </td>
                                            <td>
                                                <input type="time" class="form-control form-control-sm" value="{{$solicitud->hr_recojo}}">
                                            </td>
                                            <td>
                                                @if (!empty($solicitud->phone))
                                                    <button data-name="{{$solicitud->name}}" data-lastname="{{$solicitud->last_name}}" type="button" class="btn btn-sm btn-light notificar-whatsapp" data-phone="{{$solicitud->phone}}"><i class="fab fa-whatsapp text-success"></i></button>
                                                @else
                                                    <i class="fas fa-times text-danger"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                        <tr>
                                            <td class="text-center" colspan="5">No se encontraron resultados</td>
                                        </tr>
                                @endif
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="guardar-notificaciones">Notificar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>