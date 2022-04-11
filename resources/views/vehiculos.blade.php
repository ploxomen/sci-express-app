@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/vehiculos.js')}}"></script>
    <title>Vehículos de transporte</title>
@endsection
@section('body')
    <div class="my-4">
        <div class="baner-modulo">
            <img src="{{asset('asset/img/intranet/carro.png')}}" alt="Imagen de un carro">
        </div>
        <h3 class="text-primary text-center">Vehículos de transporte</h3>
    </div>
    <section class="bg-white p-2">
        <div class="form-group">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-nueva-empresa">Nuevo</button>
        </div>
        <table id="tabla-vehiculos" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Placa</th>
                    <th>Año</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="lista-vehiculos"></tbody>
        </table>
    </section>
    <div class="modal fade" id="modal-nueva-empresa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar un nuevo vehículo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_transporte" class="form-row">
                    <input type="hidden" id="empresa_id">
                    <div class="form-group col-12 col-lg-6">
                        <label for="id_placa">Placa</label>
                        <input type="text" required class="form-control" id="id_placa" name="placa">
                    </div>
                    <div class="form-group col-12 col-lg-6">
                        <label for="id_year">Año</label>
                        <input type="text" required class="form-control" id="id_year" name="year">
                    </div>
                    <div class="form-group col-12 col-lg-6">
                        <label for="id_marca">Marca</label>
                        <input type="text" required class="form-control" id="id_marca" name="marca">
                    </div>
                    <div class="form-group col-12 col-lg-6">
                        <label for="id_direccion">Modelo</label>
                        <textarea required class="form-control" id="id_direccion" name="direccion"></textarea>
                    </div>
                    <div class="form-group col-12">
                        <button type="submit" hidden id="btn-send-frm"></button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btn-save">Guardar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
            </div>
            </div>
        </div>
    </div>
@endsection