@extends('general.index')
@section('head')
    <script src="{{asset('asset/homedatatable.js')}}"></script>
    <link rel="stylesheet" href="{{asset('library/datatable/datatable-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/datatable/responsive-bootstrap.min.css')}}">
    <script src="{{asset('library/datatable/jq-datatable.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-bootstrap4.min.js')}}"></script>
    <script src="{{asset('library/datatable/datatable-responsive.min.js')}}"></script>
    <script src="{{asset('library/datatable/responsive-bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/devolverguias.js')}}"></script>
    <title>Retornar guias</title>
@endsection
@section('body')
<section class="">
    <div class="my-4">
        <div class="baner-modulo">
            <img src="{{asset('asset/img/intranet/embarque.png')}}" alt="Imagen de embarque">
        </div>
        <h3 class="text-primary text-center">Retorno de Cluster</h3>
    </div>
    <div class="p-2">
        <form class="mb-3" id="retornar-cargos">
            <fieldset class="bg-white px-3 border mb-2 form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">DETALLES</legend>
                <div class="col-12 col-md-6 col-lg-3 form-group">
                    <label for="id_fch_emba">Fecha de Embarque</label>
                    <input type="date" id="id_fch_emba" name="fch_emba" required class="form-control" value="{{$hoy}}" min="{{$hoy}}">
                </div>
                <div class="col-12 col-md-6 col-lg-3 form-group">
                    <label for="id_id_empr_tran">Empresas Transporte</label>
                    <select name="id_empr_tran" id="id_id_empr_tran" class="form-control simple-select2" required>
                        <option></option>
                        @foreach ($transportes as $t)
                            <option value="{{$t->id}}">{{$t->empresa}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_hr_fin_esti_emba">Fecha Llegada</label>
                    <input type="date" name="fecha_llegada" id="id_hr_fin_esti_emba" class="form-control" required>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_hr_fin_esti_emba">Hr Llegada</label>
                    <input type="time" name="hr_llegada" id="id_hr_fin_esti_emba" class="form-control" required>
                </div>
            </fieldset>
            <fieldset class="bg-white px-3 border mb-2 form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">BULTOS</legend>
                <div class="form-group col-12">
                    <div style="max-width: 150px; margin-left: auto;">
                        <input type="number" id="total_bultos" min="0" class="form-control" readonly>
                    </div>
                </div>
                <div class="form-group col-6 col-md-3 col-lg-2">
                    <label for="id_nro_cajas">Cajas</label>
                    <input type="number" name="nro_cajas" id="id_nro_cajas" class="form-control calc-total" min="0">
                </div>
                <div class="form-group col-6 col-md-3 col-lg-2">
                    <label for="id_nro_jabas">Jabas</label>
                    <input type="number" name="nro_jabas" id="id_nro_jabas" class="form-control calc-total" min="0">
                </div>
                <div class="form-group col-6 col-md-3 col-lg-2">
                    <label for="id_nro_cajones">Cajones</label>
                    <input type="number" name="nro_cajones" id="id_nro_cajones" class="form-control calc-total" min="0">
                </div>
                <div class="form-group col-6 col-md-3 col-lg-2">
                    <label for="id_nro_sobres">Sobres</label>
                    <input type="number" name="nro_sobres" id="id_nro_sobres" class="form-control calc-total" min="0">
                </div>
                <div class="form-group col-6 col-md-3 col-lg-2">
                    <label for="id_nro_paquetes">Paquetes</label>
                    <input type="number" name="nro_paquetes" id="id_nro_paquetes" class="form-control calc-total" min="0">
                </div>
                <div class="form-group col-6 col-md-3 col-lg-2">
                    <label for="id_num_otros">Otros</label>
                    <input type="number" name="num_otros" id="id_num_otros" class="form-control calc-total" min="0">
                </div>
                <div class="form-group col-12">
                    <label for="id_detalle_otro">Detallar otro</label>
                    <textarea name="detalle_otro" maxlength="255" id="id_detalle_otro" class="form-control"></textarea>
                </div>
            </fieldset>
            <div class="form-group">
                <button class="btn btn-success" type="submit">Agregar</button>
                <button class="btn btn-danger" type="reset">Cancelar</button>
            </div>
        </form>
    </div>
    <div class="bg-white p-2">
        <h4 class="text-primary">Guias a retornar</h4>
        <table class="table table-sm table-bordered" id="tabla-retorno-guia">
            <thead>
                <tr>
                    <th>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="id_check_todo">
                            <label class="custom-control-label" for="id_check_todo"></label>
                        </div>
                    </th>
                    <th>N° Guia</th>
                    <th>N° Doc Remi</th>
                    <th>Nom Remi</th>
                    <th>N° Doc Dest</th>
                    <th>Nom Dest</th>
                    <th>Trayecto</th>
                    <th>Prov Orig</th>
                    <th>Prov Dest</th>
                </tr>
            </thead>
            <tbody id="tabla-cuerpo-guias"></tbody>
        </table>
    </div>
</section>
@include('modal.detalleguia')
@include('modal.detallembarque')
@endsection