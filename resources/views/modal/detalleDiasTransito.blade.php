<div class="modal fade" id="modal-detalle-dias-transito" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titulo-modal-transito">Agregar Día Tránsito</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="modificar-dias-fletes" class="form-row">
                    <div class="form-group col-6">
                        <label for="id_departamento_origen">Departamento Origen</label>
                        <select name="departamento_origen" id="id_departamento_origen" class="simple-select2" required>
                            <option value="" hidden>Seleccione un departamento</option>
                            @foreach ($departamentos as $depar)
                                <option value="{{$depar->id_ubigeo}}" {{$depar->id_ubigeo == '3926' ? 'selected' : ''}}>{{$depar->nombre_ubigeo}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label for="id_departamento_destino">Departamento Destino</label>
                        <select name="departamento_destino" id="id_departamento_destino" class="simple-select2" required>
                            <option value="" hidden>Seleccione un departamento</option>
                            @foreach ($departamentos as $depar)
                                <option value="{{$depar->id_ubigeo}}">{{$depar->nombre_ubigeo}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label for="id_ciudad_base">Ciudad Base</label>
                        <input type="text" name="ciudad_base" id="id_ciudad_base" class="form-control form-control-sm" required>
                    </div>
                    <div class="form-group col-6">
                        <label for="id_ciudad_destino">Ciudad Destino</label>
                        <input type="text" name="ciudad_destino" id="id_ciudad_destino" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-12">
                        <h6 class="text-info">Distancias - Km</h6>
                    </div>
                    <div class="form-group col-6 col-md-4">
                        <label for="id_origen_base_km">De Origen a Base</label>
                        <input type="number" step="0.01" name="origen_base_km" id="id_origen_base_km" class="form-control form-control-sm calcular-km" required>
                    </div>
                    <div class="form-group col-6 col-md-4">
                        <label for="id_base_destino_km">De Base a Destino</label>
                        <input type="number" step="0.01" name="base_destino_km" id="id_base_destino_km" class="form-control form-control-sm calcular-km" required>
                    </div>
                    <div class="form-group col-12 col-md-4">
                        <label for="id_total_km_origen_destino">Total</label>
                        <input type="number" step="0.01" name="total_km_origen_destino" id="id_total_km_origen_destino" required class="form-control form-control-sm" readonly>
                    </div>
                    <div class="col-12">
                        <h6 class="text-info">Tiempo - Hr</h6>
                    </div>
                    <div class="form-group col-6 col-lg-4">
                        <label for="id_origen_base_hr_terrestre">De Origen a Base <i class="fas fa-truck"></i></label>
                        <input type="number" step="0.01" name="origen_base_hr_terrestre" id="id_origen_base_hr_terrestre" class="form-control form-control-sm calcular-hr" data-total="#id_total_hr_origen_destino">
                    </div>
                    <div class="form-group col-6 col-lg-4">
                        <label for="id_origen_base_hr_aereo">De Origen a Base <i class="fas fa-plane"></i></label>
                        <input type="number" step="0.01" name="origen_base_hr_aereo" value="0.00" id="id_origen_base_hr_aereo" class="form-control form-control-sm calcular-hr" data-total="#id_total_origen_base_hr_aereo">
                    </div>
                    <div class="form-group col-12 col-lg-4">
                        <label for="id_transito_destino_hr">De Base a Destino</label>
                        <input type="number" step="0.01" name="transito_destino_hr" id="id_transito_destino_hr" class="form-control form-control-sm calcular-hr" data-total="ambos">
                    </div>
                    <div class="form-group col-6 col-lg-4">
                        <label for="id_total_hr_origen_destino">Total <i class="fas fa-truck"></i></label>
                        <input type="number" step="0.01" name="total_hr_origen_destino" id="id_total_hr_origen_destino" class="form-control form-control-sm" readonly>
                    </div>
                    <div class="form-group col-6 col-lg-4">
                        <label for="id_total_origen_base_hr_aereo">Total <i class="fas fa-plane"></i></label>
                        <input type="number" step="0.01" name="total_origen_base_hr_aereo" id="id_total_origen_base_hr_aereo" class="form-control form-control-sm" readonly>
                    </div>
                    <input type="submit" hidden id="id_send_dias_transito">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="agregar-transito">Agregar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
