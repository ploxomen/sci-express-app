<div class="modal fade" id="programar-recojo" data-backdrop="static" data-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Programar Recojo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body">
            <form class="row" id="form-programar-recojo">
                <div class="form-group col-12">
                    <span class="text-info">
                        <b>N° Solicitudes seleccionadas:</b>
                        <b id="modal_nro_solcicitudes"></b>
                    </span>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="dia_recojo">Fecha de recojo</label>
                    <input type="date" class="form-control" id="id_dia_recojo" name="dia_recojo" disabled>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="id_id_vehiculo">Placa de vehículo</label>
                    <select required class="form-control simple-select2" id="id_id_vehiculo" name="id_vehiculo">
                        <option></option>
                        @foreach ($vehiculos as $v)
                            <option value="{{$v->id}}">{{$v->placa}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12" hidden>
                    <label for="id_id_cluster">Cluster Disponibles</label>
                    <select disabled required class="form-control simple-select2" id="id_id_cluster" name="id_cluster">
                        <option></option>
                        @foreach ($clusters as $v)
                            @if ($v->status === 1)
                                <option value="{{$v->id_cluster}}" data-provincia="{{$v->provincia_clu}}">{{$v->name}} {{$v->last_name}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12" hidden id="text-info-danger-cluster">
                    <small class="text-danger">No se encontró a ningún cluster para las provincias seleccionadas</small>
                </div>
                <div class="form-group col-12 col-lg-6" hidden>
                    <label for="id_id_conductor">Nombre driver</label>
                        <select disabled required class="form-control simple-select2" id="id_id_conductor" name="id_conductor">
                        <option></option>
                        @foreach ($conductores as $c)
                            
                            <option {{$c->status != 1 ? 'disabled' : ''}} value="{{$c->id}}">{{$c->name}} {{$c->last_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6" hidden>
                    <label for="id_id_dealer">Nombre dealer</label>
                    <select disabled required class="form-control simple-select2" id="id_id_dealer" name="id_dealer">
                        <option></option>
                        @foreach ($dealers as $de)
                            <option {{$de->status != 1 ? 'disabled' : ''}} value="{{$de->id}}">{{$de->name}} {{$de->last_name}}</option>
                        @endforeach
                    </select>
                </div>
                <input type="submit" hidden id="programar-recojo-modal">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="btn-programar-recojo">Programar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
    </div>
  </div>
</div>
