<div class="modal fade" id="embarque-editar" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Embarque N° <span id="informe_nro_embarque"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-row" id="update-embarque-form">
            <div class="form-group col-6">
                <label for="informe_fecha_embarque_input">Fecha Embarque</label>
                <input type="date" name="fch_emba" id="informe_fecha_embarque_input" class="form-control form-control-sm" required>
            </div>
            <div class="form-group col-6">
                <label for="informe_id_cond">Driver</label>
                <select class="form-control form-control-sm simple-select2 reset-update" name="id_cond" id="informe_id_cond" required>
                    @foreach ($conductores as $dri)
                        <option value="{{$dri->id}}">{{$dri->name}} {{$dri->last_name}}</option>
                    @endforeach
                </select>
            </div>
           <div class="form-group col-6">
                <label for="informe_id_deal">Dealer 1</label>
                <select class="form-control form-control-sm simple-select2 reset-update" name="id_deal" id="informe_id_deal" required>
                    @foreach ($dealers as $dea)
                        <option value="{{$dea->id}}">{{$dea->name}} {{$dea->last_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-6">
                <label for="informe_id_deal2">Dealer 2</label>
                <select class="form-control form-control-sm simple-select2 reset-update" name="id_deal2" id="informe_id_deal2">
                    <option value="NINGUNO">NINGUNO</option>
                    @foreach ($dealers as $dea)
                        <option value="{{$dea->id}}">{{$dea->name}} {{$dea->last_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-6">
                <label for="informe_resp_emba">Responsable Informe</label>
                <select class="form-control form-control-sm simple-select2 reset-update" name="resp_emba" id="informe_resp_emba" required>
                    <option value="Dealer 1" selected>Dealer 1</option>
                    <option value="Dealer 2" disabled>Dealer 2</option>
                </select>
            </div>
            <div class="form-group col-6">
                <label for="informe_id_vehi">Vehículo</label>
                <select class="form-control form-control-sm simple-select2 reset-update" name="id_vehi" id="informe_id_vehi" required>
                    <option></option>
                    @foreach ($vehiculos as $v)
                        <option value="{{$v->id}}">{{$v->placa}}</option>
                    @endforeach
                </select>
            </div>
            {{-- <div class="form-group col-6 col-lg-4">
                <label for="informe_completado">Situación</label>
                <select class="form-control form-control-sm simple-select2 reset-update" name="completado" id="informe_completado" required>
                    
                </select>
            </div> --}}
            <div class="form-group col-12">
                <label for="informe_empresas">Empresa de Transporte</label>
                <select name="id_empr_tran[]" id="informe_empresas" class="simple-select2 reset-update" multiple required>
                    <option></option>
                    @foreach ($transportes as $t)
                        <option value="{{$t->id}}">{{$t->empresa}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-6 col-md-4">
                <label for="informe_hr_ini_emba">Hora Inicio</label>
                <input type="time" class="form-control form-control-sm" name="hr_ini_emba" id="informe_hr_ini_emba">
            </div>
            <div class="form-group col-6 col-md-4">
                <label for="informe_fin_emba">Hora Fin</label>
                <input type="time" class="form-control form-control-sm" name="fin_emba" id="informe_fin_emba">
            </div>
            <div class="form-group col-6 col-md-4">
                <label for="informe_hr_fin_esti_emba">Hora Fin Est</label>
                <input type="time" class="form-control form-control-sm" name="hr_fin_esti_emba" id="informe_hr_fin_esti_emba" required>
            </div>
            <div class="form-group col-12">
                <h6 class="text-primary">Manifiestos</h6>
            </div>
            <div class="col-12">
                <ol id="lista-manifiestos" class="ml-3"></ol>
            </div>
            <div class="form-group col-12">
                <h6 class="text-primary">Insertar Manifiestos</h6>
            </div>
            <div class="col-12">
                <ol id="lista-manifiestos-pendientes" class="ml-3"></ol>
                <button type="submit" hidden id="update-embarque"></button>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="actualizar-embarque">Actualizar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
