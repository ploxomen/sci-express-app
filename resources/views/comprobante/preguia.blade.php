@extends('general.index')
@section('head')
    <script src="{{asset('asset/preguias.js')}}"></script>
    <title>Pre guías</title>
@endsection
@section('body')
    <main class="container bg-white">
        <div class="my-3">
            <div class="baner-modulo form-group">
                <img src="{{asset('asset/img/intranet/bill.png')}}" alt="Imagen de guia">
            </div>
            <h3 class="text-primary text-center">PreGuía</h3>
        </div>
        <form id="formGenerarGuias" class="form-row">
            <div class="form-group col-12 col-md-6 col-lg-4">
                <label for="txtNumeroRuc">N° RUC</label>
                <div class="input-group">
                    <input type="text" class="form-control" required id="txtNumeroRuc" maxlength="11" minlength="11">
                    <div class="input-group-prepend">
                        <button type="button" class="btn btn-sm btn-success" id="btnBuscarRemitente" title="Buscar remitente con el número de RUC">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-8">
                <label for="txtRemitente">Remitente</label>
                <input type="text" class="form-control" required disabled id="txtRemitente">
            </div>
            <div class="form-group col-12">
                <label for="txtDireccion">Dirección</label>
                <input type="text" class="form-control" required disabled id="txtDireccion">
            </div>
            <div class="mb-4 col-6">
                    <label for="txtCantidadGuiasSCI">CANT. GUÍAS SCI</label>
                    <input type="number" class="form-control" required value="1" min="1" id="txtCantidadGuiasSCI">
            </div>
            <div class="mb-4 col-6">
                    <label for="nroGuiaInicioSCI">N° GUÍA INICIO SCI</label>
                    <input type="text" value="{{$nro_guia_result}}" pattern="[0-9]{3}[-][0-9]{7}" minlength="11" maxlength="11" required class="form-control" id="nroGuiaInicioSCI">
            </div>
            <div class="form-group col-12">
                <div class="form-row align-items-center justify-content-center">
                    <div class="form-group col-auto">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="guiasCheck" data-numero="{{$nro_guia_result}}" required name="tipoGuia" checked class="custom-control-input changeNroSCI" value="guia">
                            <label class="custom-control-label" for="guiasCheck">GUÍAS</label>
                        </div>
                    </div>
                    <div class="form-group col-auto">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="osCheck" data-numero="{{$nro_os}}" required name="tipoGuia" class="custom-control-input changeNroSCI" value="os">
                            <label class="custom-control-label" for="osCheck">OS</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center">
                <button type="submit" class="btn btn-primary">Generar</button>
            </div>
        </form>
    </main>
    @include('modal.generarNroGuias')
    @include('modal.mostrarPrevioPreGuia')
@endsection