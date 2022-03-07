@extends('general.index')
@section('head')
    <title>Entregas</title>
@endsection
@section('body')
<style>
     .mw-100{
        max-width: 100px !important;
    }
    .gap-10{
        gap: 10px;
    }
</style>
<section class="">
    <div class="my-4">
        <div class="baner-modulo">
            <img src="{{asset('asset/img/intranet/delivery.png')}}" alt="Imagen de clientes">
        </div>
        <h3 class="text-primary text-center">Entregas</h3>
    </div>
    <form action="" class="bg-white p-2 mb-3">
        <div class="form-row">
            <div class="form-group col-12 co-md-6 col-lg-3">
                <label for="">Fecha de Confirm</label>
                <input type="date" class="form-control">
            </div>
            <div class="form-group col-12 co-md-6 col-lg-3">
                <label for="">Fecha de Reparto</label>
                <input type="date" class="form-control">
            </div>
            <div class="form-group col-12 co-md-6 col-lg-3">
                <label for="">Situación Guía</label>
                <select name="" id="" class="form-control"></select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-3">
                <label for="">Provincia</label>
                <select name="" id="" class="form-control"></select>
            </div>
        </div>
    </form>
    <div class="bg-white p-2 mb-3">
        <div class="form-group d-flex justify-content-between flex-wrap">
            <h3 class="text-primary">De Gías</h3>
            <div class="d-flex align-items-center">
                <label for="" class="mr-3">A confirmar</label>
                <input type="text" readonly class="form-control mw-100" value="1">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nro_Guia</th>
                        <th>Nro_Guia_SO</th>
                        <th>Sitac_Manif</th>
                        <th>Prov_De</th>
                        <th>Nomb_Cluster</th>
                        <th>Nomb</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <form action="" class="bg-white form-group p-2">
        <fieldset class="px-3 border mb-4 row">
            <legend class="d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">KPI'S</legend>
            <div class="form-group col-12 col-md-6">
                <label for="">Confirmadas</label>
                <div class="d-flex gap-10">
                    <input type="text" class="form-control w-50" disabled>
                    <input type="text" class="form-control w-50" disabled>
                </div>
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="">Incidencias</label>
                <div class="d-flex gap-10">
                    <input type="text" class="form-control w-50" disabled>
                    <input type="text" class="form-control w-50" disabled>
                </div>
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="">Efectuadas</label>
                <div class="d-flex gap-10">
                    <input type="text" class="form-control w-50" disabled>
                    <input type="text" class="form-control w-50" disabled>
                </div>
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="">A tiempo</label>
                <div class="d-flex gap-10">
                    <input type="text" class="form-control w-50" disabled>
                    <input type="text" class="form-control w-50" disabled>
                </div>
            </div>
        </fieldset>
    </form>
       
</section>
@endsection