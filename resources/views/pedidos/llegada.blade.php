@extends('general.index')
@section('head')
    <title>Llegada</title>
@endsection
@section('body')
<style>
    .mw-100{
        max-width: 100px !important;
    }
</style>
<section class="">
    <div class="my-4">
        <div class="baner-modulo">
            <img src="{{asset('asset/img/intranet/arrived.png')}}" alt="Imagen de clientes">
        </div>
        <h3 class="text-primary text-center">Llegadas</h3>
    </div>
    <form action="" class="bg-white p-2 mb-3">
        <div class="form-row">
            <div class="form-group col-12 co-md-6 col-lg-4 col-xl-3">
                <label for="">Fecha de Llegada</label>
                <input type="date" class="form-control">
            </div>
            <div class="form-group col-12 co-md-6 col-lg-4 col-xl-3">
                <label for="">Situacion Manifiesto</label>
                <select name="" id="" class="form-control"></select>
            </div>
            <div class="form-group col-12 co-md-6 col-lg-4 col-xl-3">
                <label for="">Situación Guía</label>
                <select name="" id="" class="form-control"></select>
            </div>
        </div>
    </form>
    <div class="bg-white p-2 mb-3">
        <div class="form-group d-flex justify-content-between flex-wrap">
            <h3 class="text-primary">De Manifiestos</h3>
            <div class="d-flex align-items-center">
                <label for="" class="mr-3">A confirmar</label>
                <input type="text" readonly class="form-control mw-100" value="1">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nro_Manif</th>
                        <th>Fch_Manif</th>
                        <th>Sitac_Manif</th>
                        <th>Prov_De</th>
                        <th>Nomb_Cluster</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
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
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
       
</section>
@endsection