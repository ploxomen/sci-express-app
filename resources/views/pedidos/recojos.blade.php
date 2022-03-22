@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>    
    <script src="{{asset('asset/recojos.js')}}"></script>
    <title>Recojos</title>
@endsection
@section('body')
<style>
    .cuadro{
        width: 15px;
        height: 15px;
        display: inline-block;
    }
</style>
<section class="p-2">
    <form id="frm_recojo" class="mb-4" >
        <div class="my-4">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/pickup.png')}}" alt="Imagen de clientes">
            </div>
            <h3 class="text-primary text-center">Recojos</h3>
        </div>
        <div class="row">
            <div class="mb-4 col-12 col-lg-6">
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
                    <div class="form-group col-12" hidden>
                        <label for="id_id_cluster">Cluster Disponibles</label>
                        <select disabled required class="form-control simple-select2" id="id_id_cluster" name="id_cluster">
                            <option></option>
                            @foreach ($vehiculos as $v)
                                <option value="{{$v->id}}">{{$v->placa}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-6" hidden>
                        <label for="id_conductor">Nombre driver</label>
                         <select disabled required class="form-control simple-select2" id="id_id_conductor" name="id_conductor">
                            <option></option>
                            @foreach ($conductores as $c)
                                <option value="{{$c->id}}">{{$c->name}} {{$c->last_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-6" hidden>
                        <label for="id_nom_dealer">Nombre dealer</label>
                        <select disabled required class="form-control simple-select2" id="id_id_dealer" name="id_dealer">
                            <option></option>
                            @foreach ($dealers as $de)
                                <option value="{{$de->id}}">{{$de->name}} {{$de->last_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </fieldset>
            </div>
            <div class="form-group col-12 col-lg-6">
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
    </form>
    <div class="form-group bg-white p-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
            <h4 class="form-group text-primary">Solicitud de recojos</h4>
            <form id="frm_filtros_soli" class="d-flex justify-content-end" style="width: 100%; max-width: 600px; gap: 10px;">
                <div class="form-group" style="width: 100%; max-width: 200px;">
                    <label for="id_estado_cli">Estado</label>
                    <select name="estado" id="id_estado_cli" class="form-control form-control-sm">
                        <option value="TODOS">TODOS</option>
                        @foreach ($estados_solicitud as $estado)    
                            <option value="{{$estado->id}}" {{$estado->estado == 'PENDIENTE' ? 'selected' : ''}}>{{$estado->estado}}</option>
                        @endforeach
                    </select>
                </div>
            </form>
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
        <table class="table table-bordered table-striped table-sm" id="tabla-solicitud">
            <thead>
                <tr>
                    <th>N° Soli</th>
                    <th>Fecha Soli</th>
                    <th>Dia Reco</th>
                    <th>Hr Reco</th>
                    <th>Nomb Soli</th>
                    <th>N° Doc Cli</th>
                    <th>Nomb Cliente</th>
                    <th>Trayecto</th>
                    <th>Direc Reco</th>
                    <th>Bulto Soli</th>
                    <th>Estado Soli</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-cuerpo-soli"></tbody>
        </table>
    </div>
        
</section>
@endsection