@extends('general.index')
@section('head')
    <script src="{{asset('asset/facturacionCliente.js')}}"></script>
    <title>Configuracion Cliente</title>
@endsection
@section('body')
<style>
    .tabla-cliente thead{ 
        position: sticky;
        top: 0;
        z-index: 5;
        background-color: #ffffff;
    }
    .tabla-cliente{
        border-spacing: 0 !important;
        border-collapse: separate !important;
    }
    table .sticki{
        position: sticky;
        left: 0;
    }
    
</style>
<div class="my-4">
    <div class="baner-modulo">
        <img src="{{asset('asset/img/intranet/configuraciones.png')}}" alt="Imagen de configuracion">
    </div>
    <h3 class="text-primary text-center">Configuración Facturación Cliente</h3>
</div>
<section class="p-2 bg-white">
    <form class="my-4">
        <h6 class="text-center text-info">Clientes</h6>
        <div class="form-group d-flex m-auto justify-content-center" style="gap: 10px; max-width: 800px;">
            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" id="btn-anterior-cliente" title="Anterior cliente" data-tipo="atras" disabled><i class="fas fa-angle-left"></i></button>
            <select id="lista-clientes">
                @foreach ($clientes as $cliente)
                    <option value="{{$cliente->id}}" data-nro="{{$cliente->nro_doc}}">{{$cliente->nom_cli}}</option>
                @endforeach
            </select>
            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" id="btn-siguiente-cliente" title="Siguiente cliente" data-tipo="adelante"><i class="fas fa-angle-right"></i></button>
        </div>
    </form>
    <div class="d-flex mb-3 justify-content-between flex-wrap" style="gap: 10px;">
        <div class="d-flex align-items-center" style="gap: 10px;">
            <div>
                <div class="cuadro table-primary"></div>
                <span>Monto Normal</span>
            </div>
            <div>
                <div class="cuadro table-danger"></div>
                <span>Monto Base</span>
            </div>
            <div>
                <div class="cuadro table-success"></div>
                <span>Monto Adicional</span>
            </div>
            <div>
                <div class="cuadro table-warning"></div>
                <span>Ciudad FAU</span>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-end" style="gap: 10px;">
            <button type="button" class="btn btn-sm btn-outline-info" data-toggle="tooltip" id="btn-modal-fact-cabecera" title="Agregar cabecera">
                <i class="fas fa-plus-circle"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" id="btn-sincronizar" title="Sincronizar ciudades base">
                <i class="fas fa-sync-alt"></i>            
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="tooltip" id="btn-importar" title="Importar configuración a otro cliente">
                <i class="far fa-copy"></i>
            </button>
        </div>
    </div>
    <div class="table-responsive mb-4" style="max-height: 650px;overflow: auto;">
        <table class="table table-sm tabla-cliente table-bordered table-sm mb-4 table-hover" style="">
            <thead id="cabezara-facturacion-cliente">
            </thead>
            <tbody id="facturacion-ciudades"></tbody>
        </table>
    </div>
    <h5 class="text-center text-primary">Tabla Asignación Costos</h5>
    <div class="d-flex align-items-center justify-content-end mb-3" style="gap: 10px;">
        <button type="button" class="btn btn-sm btn-outline-info" data-toggle="tooltip" id="btn-modal-fact-cabecera-costo" title="Agregar cabecera">
            <i class="fas fa-plus-circle"></i>
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-sm mb-4 table-hover">
            <thead id="cabezara-facturacion-cliente-costo">
            </thead>
            <tbody id="facturacion-ciudades-costo"></tbody>
        </table>
    </div>
</section>
@include('modal.agregarCabeceraFacClien',['nombre' => "Cliente"])
@include('modal.agregarModifCabeceraCosto')
@include('modal.copiarConfigCliente')
@endsection