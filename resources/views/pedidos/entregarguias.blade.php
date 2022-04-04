@extends('general.index')
@section('head')
<script src="{{asset('asset/entregapedidos.js')}}"></script>
<title>Guias para entregar</title>
@endsection
@section('body')
<style>
    .card b,
    .card span{
        font-size: 14px;
    }
    .img-guias{
        display: block;
        width: 100%;
        object-fit: contain;
        height: 250px;
    }
    .img-btn-delete{
        width: 30px;
        height: 30px;
        position: absolute;
        top: 0;
        right: 0;
    }
</style>
<section class="p-2">
    <div class="row">
        @if (empty($guias))
        <div class="form-group col-12">
            <h5 class="text-center text-primary">No se encontraron pedidos para entregar</h5>
        </div>
        @else
        @foreach ($guias as $g)
        <div class="form-group col-12 col-md-6 col-xl-4">
            <div class="card bg-white">
                <div class="card-header text-right">
                    <b>Guía N° - {{$g->nro_guia}}</b>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-12">
                            <b class="d-block">Fecha Emitida</b>
                            <span>{{$g->format_fecha}}</span>
                        </div>
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-caret-right"></i> Remitente</h6>
                        </div>
                        <div class="form-group col-12">
                            <b class="d-block">Nombres</b>
                            <span>{{$g->nom_prov}}</span>
                        </div>
                        <div class="form-group col-6">
                            <b class="d-block">Tipo Doc</b>
                            <span>{{$g->remi_tipo_doc}}</span>
                        </div>
                        <div class="form-group col-6">
                            <b class="d-block">N° Doc</b>
                            <span>{{$g->remi_nro_docu}}</span>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4">
                            <b class="d-block">Departamento</b>
                            <span>{{$g->remi_depa}}</span>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4">
                            <b class="d-block">Provincia</b>
                            <span>{{$g->remi_prov}}</span>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4">
                            <b class="d-block">Distrito</b>
                            <span>{{$g->remi_dist}}</span>
                        </div>
                        <div class="form-group col-12">
                            <b class="d-block">Dirección</b>
                            <span>{{$g->remi_dire}}</span>
                        </div>
                         <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-caret-right"></i> Destinatario</h6>
                        </div>
                        <div class="form-group col-12">
                            <b class="d-block">Nombres</b>
                            <span>{{$g->nom_dest}}</span>
                        </div>
                        <div class="form-group col-6">
                            <b class="d-block">Tipo Doc</b>
                            <span>{{$g->dest_tipo_docu}}</span>
                        </div>
                        <div class="form-group col-6">
                            <b class="d-block">N° Doc</b>
                            <span>{{$g->dest_nro_docu}}</span>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4">
                            <b class="d-block">Departamento</b>
                            <span>{{$g->dest_depa}}</span>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4">
                            <b class="d-block">Provincia</b>
                            <span>{{$g->dest_prov}}</span>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4">
                            <b class="d-block">Distrito</b>
                            <span>{{$g->dest_dist}}</span>
                        </div>
                        <div class="form-group col-12">
                            <b class="d-block">Dirección</b>
                            <span>{{$g->dest_dire}}</span>
                        </div>
                        <div class="form-group col-12">
                            <form id="form-guias-add-{{$g->id}}" hidden enctype="multipart/form-data">
                                <input type="file" hidden name="img-guias[]" id="img-guias-send-{{$g->id}}" multiple>
                            </form>
                            <input type="file" data-transfer="img-guias-send-{{$g->id}}" hidden id="img-guia-{{$g->id}}" class="imgs-guias" multiple accept="image/*">
                            <button class="btn btn-sm btn-primary btn-add-img" data-input="#img-guia-{{$g->id}}" title="Agregar imágenes"><i class="fas fa-plus"></i> <i class="fas fa-camera"></i></button>
                        </div>
                        <div class="form-group col-12 text-center">
                            <button class="btn btn-sm btn-danger btn-save-entrega" data-guia="{{$g->id}}" data-files="img-guias-send-{{$g->id}}" data-formulario="form-guias-add-{{$g->id}}">Entregar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
</section>
@endsection
