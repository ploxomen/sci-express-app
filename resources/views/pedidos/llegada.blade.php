@extends('general.index')
@section('head')
    <script src="{{asset('asset/llegada.js')}}"></script>
    <title>Llegada</title>
@endsection
@section('body')
<style>
    .mw-100{
        max-width: 100px !important;
    }
    table tbody tr td{
        vertical-align: middle !important;
    }
</style>
<section class="">
    <div class="my-4">
        <div class="baner-modulo">
            <img src="{{asset('asset/img/intranet/arrived.png')}}" alt="Imagen de clientes">
        </div>
        <h3 class="text-primary text-center">Embarques por arribar</h3>
    </div>
    <div class="bg-white p-2 mb-3">
        <div class="form-group d-flex justify-content-between flex-wrap">
            <h5 class="text-primary">De Embarques</h5>
            <div class="d-flex align-items-center">
                <label for="" class="mr-3">A confirmar</label>
                <input type="text" readonly class="form-control mw-100" value="0" id="valor-embarques">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Empresa</th>
                        <th>Fecha Llegada</th>
                        <th>Hr Llegada</th>
                        <th>Total Bulto</th>
                        <th>Total Manif</th>
                        <th>Acciónes</th>
                    </tr>
                </thead>
                <tbody id="tabla-embarques"></tbody>
            </table>
        </div>
    </div>
    <div class="bg-white p-2 mb-3">
        <div class="form-group d-flex justify-content-between flex-wrap">
            <h5 class="text-primary">De Manifiestos y guias</h5>
            <div class="d-flex align-items-center">
                <label for="" class="mr-3"># Guías a confirmar</label>
                <input type="text" readonly class="form-control mw-100" value="0" id="valor-manifiesto">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Fecha Manif</th>
                        <th>Prov Origen</th>
                        <th>Prov Destino</th>
                        <th>Total Guías</th>
                        <th>Estado</th>
                        <th>N° Guía</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tabla-mani">
                    <tr>
                        <td colspan="8" class="text-center">Seleccione un embarque</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
@include('modal.detalleguia')
@endsection