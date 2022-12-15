@extends('general.index')
@section('head')
   
    <script src="{{asset('asset/recojos.js')}}"></script>
    <title>Recojos</title>
@endsection
@section('body')
<section class="p-2">
    <div class="my-4">
        <div class="baner-modulo">
            <img src="{{asset('asset/img/intranet/pickup.png')}}" alt="Imagen de clientes">
        </div>
        <h3 class="text-primary text-center">Recojos</h3>
    </div>
    <div class="mb-4 bg-white p-3">
        <div class="row">
            <div class="col-12 col-md-6 col-lg-8">
                <h4 class="text-primary">Solicitud de recojos</h4>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <form id="frm_filtros_soli" class="form-row">
                    <div class="form-group col-6">
                        <label for="filtro_fecha_recojo">Fecha Recojo</label>
                        <input type="date" value="{{date('Y-m-d')}}" class="form-control form-control-sm" id="filtro_fecha_recojo">
                    </div>
                    <div class="form-group col-6">
                        <label for="filtro_id_trayecto">Trayectos</label>
                        <select name="trayecto" required id="filtro_id_trayecto" class="form-control form-control-sm">
                            <option value="0">Todos</option>
                            @foreach ($trayectos as $trayecto)
                                <option value="{{$trayecto->id}}">{{$trayecto->tipo}}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="form-group">
            <div class="for-group">
                <h6 class="text-primary d-inline-block">Total Solicitud por programar:</h6>
                <h6 class="d-inline-block" id="total_solicitudes_mostradas_programar"></h6>
            </div>
            <div class="form-group">
                <h6 class="text-primary d-inline-block">N° de Solicitudes mostradas:</h6>
                <h6 class="d-inline-block" id="total_solicitudes_mostradas"></h6>
            </div>
        </div>
        <div class="form-group">
            <h6 class="text-primary">Solicitudes agrupadas por Fch. Recojo:</h6>
            <div class="d-flex flex-wrap" style="gap: 10px;" id="id_fechas_recojos_solicitud"></div>
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
            <div>
                <div class="cuadro table-info"></div>
                <span>Por reprogramar</span>
            </div>
        </div>
        <div class="form-group table-responsive">
            <table class="table table-bordered table-striped table-sm" id="tabla-solicitud">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="seleccionar_todo"></th>
                        <th>N° Soli</th>
                        <th>Fch. Hr. <br>Recojo</th>
                        <th>N° Doc Cli</th>
                        <th>Nomb Cliente</th>
                        <th>Nomb Soli</th>
                        <th>Trayecto</th>
                        <th>Departamento</th>
                        <th>Provincia</th>
                        <th>Distrito</th>
                        <th>Dirección</th>
                        <th>Bulto</th>
                    </tr>
                </thead>
                <tbody id="tabla-cuerpo-soli"></tbody>
            </table>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-success btn-sm" id="siguiente_programar"><i class="fas fa-hand-point-right"></i> Siguiente</button>
        </div>
    </div>
    {{-- <form id="frm_recojo" class="mb-4" >
        
        <div class="row">
            <div class="col-12 col-lg-6">
                <fieldset class="bg-white px-3 border mb-4 form-row">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">PROGRAMACIÓN</legend>
                    <div class="form-group col-12 col-md-6">
                        <label for="id_dia_recojo">Fecha de recojo</label>
                        <input type="date" required class="form-control" id="id_dia_recojo" name="dia_recojo" value="{{date('Y-m-d')}}">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="id_vehiculo">Placa de vehículo</label>
                        <select required class="form-control simple-select2" id="id_id_vehiculo" name="id_vehiculo">
                            <option></option>
                            @foreach ($vehiculos as $v)
                                <option value="{{$v->id}}">{{$v->placa}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label for="id_id_cluster">Cluster Disponibles</label>
                        <select disabled required class="form-control simple-select2" id="id_id_cluster" name="id_cluster">
                            <option></option>
                            @foreach ($vehiculos as $v)
                                <option value="{{$v->id}}">{{$v->placa}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-6">
                        <label for="id_conductor">Nombre driver</label>
                         <select disabled required class="form-control simple-select2" id="id_id_conductor" name="id_conductor">
                            <option></option>
                            @foreach ($conductores as $c)
                                <option {{$c->status != 1 ? 'disabled' : ''}} value="{{$c->id}}">{{$c->name}} {{$c->last_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-6">
                        <label for="id_nom_dealer">Nombre dealer</label>
                        <select disabled required class="form-control simple-select2" id="id_id_dealer" name="id_dealer">
                            <option></option>
                            @foreach ($dealers as $de)
                                <option {{$de->status != 1 ? 'disabled' : ''}} value="{{$de->id}}">{{$de->name}} {{$de->last_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </fieldset>
            </div>
            <div class="mb-4 col-12 col-lg-6">
                <fieldset class="bg-white px-3 border mb-4 form-row">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">GESTIÓN</legend>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="id_hr_llegada">Hr Llegada</label>
                        <input type="time" class="form-control" id="id_hr_llegada" name="hr_llegada">
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="id_hr_despacho">Hr Despacho</label>
                        <input type="time" class="form-control" id="id_hr_despacho" name="hr_despacho">
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="id_hr_salida">Hr Salida</label>
                        <input type="time" class="form-control" id="id_hr_salida" name="hr_salida">
                    </div>
                    <div class="form-group col-12 form-row">
                        <div class="form-group col-12 col-md-4">
                            <span>Doc Recojo</span>
                        </div>
                        <div class="form-group col-12 col-md-8">
                            <div class="custom-control custom-radio d-inline mr-3">
                                <input type="radio" name="doc_recojo" class="custom-control-input" id="id_una_guia" value="UNA GUIA">
                                <label class="custom-control-label" for="id_una_guia">UNA GUIA</label>
                            </div>
                            <div class="custom-control custom-radio d-inline">
                                <input type="radio" name="doc_recojo" class="custom-control-input" id="id_varias_guias" value="VARIAS GUIAS">
                                <label class="custom-control-label" for="id_varias_guias">VARIAS GUIAS</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="id_nro_guias_conso">N° Guía Consolidada</label>
                        <input type="text" class="form-control" id="id_nro_guias_conso" name="nro_guias_conso">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="id_cant_bulto">Cant Bultos</label>
                        <input type="text" class="form-control" id="id_cant_bulto" name="cant_bulto">
                    </div>
                </fieldset>
            </div>
            <div class="form-group col-12">
                <button hidden class="btn btn-primary" id="btn-change-estado" type="submit"></button>
                <button hidden class="btn btn-danger" id="btn-cancelar" type="reset">Cancelar</button>
            </div>
        </div>
    </form> --}}
        
</section>
@include('modal.programarRecojo')   
@endsection