@extends('general.index')
@section('head')
    <title>Lista de embarque</title>
@endsection
@section('body')
<section class="bg-white p-2">
    <form action="" class="mb-4 form-row">
        <div class="my-4 col-12">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/shipment.png')}}" alt="Imagen de clientes">
            </div>
            <h3 class="text-primary text-center">Lista de embarque</h3>
        </div>
        <div class="col-12 col-md-6 form-group">
            <label for="">Fecha de Embarque</label>
            <input type="date" class="form-control">
        </div>
        <div class="col-12 col-md-6 form-group">
            <label for="">Situación Manifiesto</label>
            <select name="" id="" class="form-control">
                <option value="">GENERADO</option>
                <option value="">PROC EMBARQUE</option>
                <option value="">EMBARCADO</option>
                <option value="">INFORMADO</option>
                <option value="">TRANSITO</option>
                <option value="">ARRIVO</option>
            </select>
        </div>
        <div class="col-12 col-md-6 form-group">
            <label for="">Nombre del Driver</label>
            <input type="text" class="form-control">
        </div>
        <div class="col-12 col-md-6 form-group">
            <label for="">Nombre del Dealer 01</label>
            <input type="text" class="form-control">
        </div>
        <div class="col-12 col-md-6 col-lg-4 form-group">
            <label for="">Nombre del Dealer 02</label>
            <input type="text" class="form-control">
        </div>
        <div class="col-12 col-md-6 col-lg-4 form-group">
            <label for="">Responsable Embarque</label>
            <select name="" id="" class="form-control">
                <option value="">NN</option>
            </select>
        </div>
        <div class="col-12 col-md-6 col-lg-4 form-group">
            <label for="">Placa del Vehículo</label>
            <input type="text" class="form-control">
        </div>
        
        <div class="form-group col-12">
            <div class="form-group row align-items-center">
                <div class="form-group col-12 col-md-6 col-lg-4 ">
                    <h5 class="text-primary">Embarcar Manifiestos</h5>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="">Bultos</label>
                    <input type="text" class="form-control" readonly>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="">Flete estimado</label>
                    <input type="text" class="form-control" readonly>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nro_manif</th>
                            <th>Fch_manif</th>
                            <th>Prov_desti</th>
                            <th>Nomb_cluster</th>
                            <th>Situa_mani</th>
                            <th>Nro_Lis_Emp</th>
                            <th>Emp_transp</th>
                            <th>Blt_01</th>
                            <th>Blt_02</th>
                            <th>Blt_03</th>
                            <th>Blt_04</th>
                            <th>Blt_05</th>
                            <th>Blt_06</th>
                            <th>Tt_Blt</th>
                            <th>Desc_OBlt</th>
                            <th>Tt_Cs</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="form-group col-12 p-3">
            <fieldset class="border form-row">
                <legend class="d-inline-block w-auto px-2 border ml-2 shadow-sm legend-add text-info">PRODUCTIVIDAD</legend>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="">Inicio de Emb</label>
                    <input type="time" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="">Fin Emb Estim</label>
                    <input type="time" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="">Fim Embarque</label>
                    <input type="time" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="">KPI</label>
                    <input type="text" readonly class="form-control">
                </div>
            </fieldset>
        </div>
    </form>
       
</section>
@endsection