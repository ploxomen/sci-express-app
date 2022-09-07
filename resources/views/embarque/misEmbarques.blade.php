@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nanogallery2/3.0.5/css/nanogallery2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nanogallery2/3.0.5/jquery.nanogallery2.min.js"></script>
    <script src="{{asset('asset/misEmbarques.js')}}"></script>
    <title>Mis Embarques</title>
@endsection
@section('body')
<section class="bg-white p-2">
    <form class="mb-4 form-row" id="frm-aplicar-filtros">
        <div class="my-4 col-12">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/cajas.png')}}" alt="Imagen de varias cajas">
            </div>
            <h3 class="text-primary text-center">Mis Embarques</h3>
        </div>
        <div class="form-group col-6 col-lg-3 col-xl-2">
            <label for="fecha_inicio">Fecha Inicio</label>
            <input type="date" class="form-control" id="fecha_inicio" value="{{$fechaFiltros['finFiltro']}}" required>
        </div>
        <div class="form-group col-6 col-lg-3 col-xl-2">
            <label for="id_fecha_fin">Fecha Fin</label>
            <input type="date" class="form-control" id="id_fecha_fin" value="{{$fechaFiltros['inicioFiltro']}}" required>
        </div>
        <div class="form-group col-6 col-lg-3 col-xl-2">
            <label for="estado_guias">Situación Embarque</label>
            <select class="form-control cb-filtros simple-select2" id="estado_embarque">
                <option value="todos" selected>TODOS</option>
                <option value="0">PROGRAMADO</option>
                <option value="1">EMBARCADO</option>
                <option value="2">PROC. INFORME</option>
                <option value="3">INFORMADO</option>
                <option value="4">TRANSITO</option>
                <option value="5">ARRIVO</option>
            </select>
        </div>
        <div class="form-group col-6 col-lg-3 col-xl-2">
            <label for="id_tipo">Driver</label>
            <select class="form-control cb-filtros simple-select2" id="id_driver">
                <option value="todos" selected>TODOS</option>
                @foreach ($conductores as $dri)
                    <option value="{{$dri->id}}">{{$dri->name}} {{$dri->last_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-6 col-lg-3 col-xl-2">
            <label for="id_tipo">Dealer</label>
            <select class="form-control cb-filtros simple-select2" id="id_dealer">
                <option value="todos" selected>TODOS</option>
                @foreach ($dealers as $dea)
                    <option value="{{$dea->id}}">{{$dea->name}} {{$dea->last_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-6 col-lg-3 col-xl-2">
            <label for="id_tipo">Vehículo</label>
            <select class="form-control cb-filtros simple-select2" id="id_vehiculo">
                <option value="todos" selected>TODOS</option>
                @foreach ($vehiculos as $ve)
                    <option value="{{$ve->id}}">{{$ve->placa}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12">
            <button type="button" class="btn btn-sm btn-success" id="btn-actualizar-guias" title="Actualizar embarques de informado a transito"><i class="fas fa-sync-alt"></i></button>
            <button type="submit" class="btn btn-sm btn-primary" title="Aplicar los filtros"><i class="fas fa-search"></i></button>
        </div>
    </form>
    <div class="form-group">
        <table class="table table-sm table-bordered" id="tabla-embarques">
            <thead>
                <tr>
                    <th>N° Embarque</th>
                    <th>Fch. Emisión</th>
                    <th>Situación</th>
                    <th>Driver</th>
                    <th>Manifiestos Search</th>
                    <th>Dealer</th>
                    <th>Dealer 2</th>
                    <th>Placa <br>Vehículo</th>
                    <th>H. Inicio</th>
                    <th>H. Fin</th>
                    <th>N° Manifiestos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
    </div>
</section>
@include('modal.editarEmbarque')
@include('modal.mostrarManifiesto')
@include('modal.editarFletes')
@include('modal.informeembarque')
@endsection