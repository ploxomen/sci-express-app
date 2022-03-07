@extends('general.index')
@section('head')
    <script src="{{asset('asset/recojos.js')}}"></script>
    <title>Recojos</title>
@endsection
@section('body')
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
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="id_estado_soli">Estado</label>
                        <select name="estado_soli" id="id_estado_soli" class="form-control">
                            @foreach ($estados_solicitud as $estado)    
                                <option value="{{$estado->id}}">{{$estado->estado}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="id_fech_recojo">Fecha de recojo</label>
                        <input type="date" class="form-control" id="id_fech_recojo" name="fech_recojo">
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="id_placa_vehiculo">Placa de vehículo</label>
                        <input type="text" class="form-control" id="id_placa_vehiculo" name="placa_vehiculo">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="id_nom_driver">Nombre driver</label>
                        <input type="text" class="form-control" id="id_nom_driver" name="nom_driver">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="id_nom_dealer">Nombre dealer</label>
                        <input type="text" class="form-control" id="id_nom_dealer" name="nom_dealer">
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
                        <label for="id_cant_bultos">Cant Bultos</label>
                        <input type="text" class="form-control" id="id_cant_bultos" name="cant_bultos">
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
            <form id="frm_filtros_soli" class="d-flex" style="width: 100%; max-width: 600px; gap: 10px;">
                <div class="form-group" style="width: 100%; max-width: 200px;">
                    <label for="id_estado_cli">Estado</label>
                    <select name="estado" id="id_estado_cli" class="form-control form-control-sm">
                        <option value="TODOS">TODOS</option>
                        @foreach ($estados_solicitud as $estado)    
                            <option value="{{$estado->id}}" {{$estado->estado == 'PENDIENTE' ? 'selected' : ''}}>{{$estado->estado}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="width: 100%; max-width: 400px;">
                    <label for="id_clientes">Clientes</label>
                    <select name="nro_doc" id="id_clientes" class="form-control">
                        <option value="TODOS">TODOS</option>
                        @foreach ($clientes as $k => $cliente)
                            <option value="{{$cliente->nro_doc}}">{{$cliente->nom_cli}}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" style="font-size: 13px;">
                <thead>
                    <tr>
                        <th>Nro_Soli</th>
                        <th>Fch_Soli</th>
                        <th>Hr_Reco</th>
                        <th>Nomb_Cliente</th>
                        <th>Direcc_Recojo</th>
                        <th>Status_Solic</th>
                        <th>Cant_Bulto</th>
                        <th>Nomb_Solic</th>
                        <th>Trayecto_Envio</th>
                        <th>Día_Recojo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla_solicitud">
                </tbody>
            </table>
        </div>
    </div>
        
</section>
@endsection