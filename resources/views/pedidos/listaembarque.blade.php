@extends('general.index')
@section('head')
    <script src="{{asset('asset/embarque.js')}}"></script>
    <title>Embarcar manifiestos</title>
@endsection
@section('body')
<style>
    .rectangulo-filtro{
        padding: 1px 10px;
        border: 1px solid #d4d4d4;
        background: #ececec;
        border-radius: 20px;
    }
    .rectangulo-filtro i{
        color: rgb(192, 192, 192);
    }
    .rectangulo-filtro .icono-delete{
        cursor: pointer;
        padding: 2px;
    }
</style>
<section class="bg-white p-2">
    <form id="add-embarque" class="mb-4 form-row">
        <div class="my-4 col-12">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/shipment.png')}}" alt="Imagen de clientes">
            </div>
            <h3 class="text-primary text-center">Embarcar manifiestos</h3>
        </div>
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
            <label for="id_fch_emba">Fecha de Embarque <small class="text-danger">*</small></label>
            <input type="date" id="id_fch_emba" name="fch_emba" required class="form-control" value="{{$hoy}}" min="{{$diasAtras}}">
        </div>
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
            <label for="id_id_cond">Nombre del Driver <small class="text-danger">*</small></label>
            <select name="id_cond" id="id_id_cond" class="form-control simple-select2" required>
                <option></option>
                @foreach ($conductores as $c)
                    <option value="{{$c->id}}">{{$c->name}} {{$c->last_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
            <label for="id_id_deal">Nombre del Dealer 1 <small class="text-danger">*</small></label>
            <select name="id_deal" id="id_id_deal" class="form-control simple-select2" required>
                <option></option>
                @foreach ($dealers as $de)
                    <option value="{{$de->id}}">{{$de->name}} {{$de->last_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
            <label for="id_id_deal2">Nombre del Dealer 2</label>
            <select name="id_deal2" id="id_id_deal2" class="form-control simple-select2">
                <option></option>
                <option value="NINGUNO" selected>NINGUNO</option>
                @foreach ($dealers as $de)
                    <option value="{{$de->id}}">{{$de->name}} {{$de->last_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
            <label for="id_resp_emba">Responsable Embarque <small class="text-danger">*</small></label>
            <select name="resp_emba" id="id_resp_emba" class="form-control simple-select2" required>
                <option value="">Seleccione un responsable</option>
                <option value="Dealer 1" selected>Dealer 1</option>
                <option value="Dealer 2" disabled>Dealer 2</option>
            </select>
        </div>
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
            <label for="id_id_vehi">Placa del Vehículo <small class="text-danger">*</small></label>
            <select name="id_vehi" id="id_id_vehi" class="form-control simple-select2" required>
                <option></option>
                @foreach ($vehiculos as $v)
                    <option value="{{$v->id}}">{{$v->placa}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
            <label for="id_id_empr_tran">Empresas Transporte <small class="text-danger">*</small></label>
            <select name="id_empr_tran[]" id="id_id_empr_tran" class="form-control simple-select2" multiple required>
                <option></option>
                @foreach ($transportes as $t)
                    <option value="{{$t->id}}">{{$t->empresa}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="id_hr_fin_esti_emba">Fin Est Embarque <small class="text-danger">*</small></label>
            <input type="time" name="hr_fin_esti_emba" id="id_hr_fin_esti_emba" class="form-control" required>
        </div>
        <div class="form-group col-12">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="nosotros" class="custom-control-input" id="id_envio_nostros">
                <label class="custom-control-label" for="id_envio_nostros">Envio local</label>
            </div>
        </div>
        <div class="form-group col-12">
            <button type="submit" class="btn btn-primary">Siguiente</button>
            <button type="reset" class="btn btn-danger">Cancelar</button>
        </div>
    </form>
    
    <div class="form-group form-row">
        <div class="form-group col-12">
            <h4 class="text-primary"><i class="fas fa-caret-right"></i> Manifiestos para embarcar</h4>
        </div>
        
        <div class="form-group col-12 col-lg-9 col-xl-10">
            <div class="form-group d-flex flex-wrap" style="gap: 10px 20px;" id="detalle-provincia-destino"></div>
        </div>
        <div class="text-lg-right text-primary col-12 col-lg-3 col-xl-2">
            <strong>Total Manifiesto: </strong>
            <strong id="total_resultamos_mostrados"></strong>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="id_prov_dest">Ciudades base</label>
            <select name="prov_dest" id="id_prov_dest" class="form-control simple-select2">
                <option value="todos">TODOS</option>
                    @foreach ($ciudadesBase as $key => $ciudad)
                        <option data-position="{{$key}}" value="{{$ciudad->value}}" data-tipo="{{$ciudad->tipo_base}}">{{$ciudad->nombre}}</option>
                    @endforeach
            </select>
        </div>
        <div class="form-group col-12">
            <div class="d-flex" style="gap: 10px;" id="filtro-section"></div>
        </div>
    </div>
    <div class="d-flex mb-3" style="gap: 10px;">
            <div>
                <div class="cuadro table-success"></div>
                <span>Asignados</span>
            </div>
            <div>
                <div class="cuadro table-warning"></div>
                <span>Sin asignar</span>
            </div>
        </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover table-bordered" id="tabla-embarque">
            <thead>
                <tr>
                    <th>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="id_check_todo">
                            <label class="custom-control-label" for="id_check_todo"></label>
                        </div>
                    </th>
                    <th>N° Manif</th>
                    <th>Fch Manif</th>
                    <th>Trayecto</th>
                    <th>Envio</th>
                    <th>Prov Orig</th>
                    <th>Prov Dest</th>
                    <th>Ciudad Base</th>
                    <th>Cluster</th>
                    <th>Bulto</th>
                    <th>N° Guías</th>
                </tr>
            </thead>
            <tbody id="tabla-manifiestos"></tbody>
        </table>
    </div>
</section>
@include('modal.definirfletes')
@endsection