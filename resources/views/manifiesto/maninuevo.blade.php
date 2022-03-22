@extends('general.index')
@section('head')
    <script src="{{asset('asset/manicrear.js')}}"></script>
    <title>Nuevo manifiesto</title>
@endsection
@section('body')
    <form id="frm_manifiesto" class="bg-white p-3 mb-4">
        <div class="form-row">
            <div class="my-4 col-12">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/manifiesto.png')}}" alt="Imagen de clientes">
            </div>
            <h3 class="text-primary text-center">Nuevo manifiesto</h3>
        </div>
        
        <div class="form-group col-12 col-md-6 col-lg-4">
            <label for="id_trayecto">Trayecto</label>
            <select name="trayecto" id="id_trayecto" class="form-control" required>
                @foreach ($trayectos as $trayecto)
                    <option value="{{$trayecto->id}}">{{$trayecto->tipo}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-4">
            <label for="id_prov_orig">Provincia Origen</label>
            <select name="prov_orig" id="id_prov_orig" class="form-control cb-prov" required>
                <option></option>
                @foreach ($departamentoProv as $depart)
                    <optgroup label="{{$depart['nom_ubigeo_dep']}}">
                        @foreach ($depart['provincias'] as $prov)
                            <option value="{{$prov->id_ubigeo}}">{{$prov->nombre_ubigeo}}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-4">
            <label for="id_prov_dest">Provincia Destino</label>
            <select name="prov_dest" id="id_prov_dest" class="form-control cb-prov" required>
                <option></option>
                @foreach ($departamentoProv as $depart)
                    <optgroup label="{{$depart['nom_ubigeo_dep']}}">
                        @foreach ($depart['provincias'] as $prov)
                            <option value="{{$prov->id_ubigeo}}">{{$prov->nombre_ubigeo}}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input change-doc" checked id="id_view_all_prov">
                <label class="custom-control-label" for="id_view_all_prov">Mostrar todas las guías pendientes</label>
            </div>
        </div>
        <div class="form-group col-12">
            <button type="button" id="searc_guias" class="btn btn-primary" title="Buscar guías"><i class="fas fa-search"></i></button>
            <button type="submit" class="btn btn-success" title="Generar manifiesto"><i class="fas fa-save"></i></button>
        </div>
        </div>
    </form>
    <section class="p-3 bg-white">
        <h4 class="text-primary"><i class="far fa-newspaper"></i> Guías pendientes</h4>
        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="id_check_todo">
                                <label class="custom-control-label" for="id_check_todo"></label>
                            </div>
                        </th>
                        <th>N°</th>
                        <th>Fch Emisión</th>
                        <th>Prov Orig</th>
                        <th>Prov Dest</th>
                        <th>N° Ord Comp</th>
                        <th>Venc Ord Comp</th>
                        <th>Nomb Remitente</th>
                        <th>Nomb Destinatario</th>
                        <th>Tot B</th>
                        <th>Cant Comp</th>
                    </tr>
                </thead>
                <tbody id="table-guias"></tbody>
            </table>
        </div>
    </section>
@include('modal.nuevomanifiesto')
@endsection