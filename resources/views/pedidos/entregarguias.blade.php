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
    @if (empty($guias))
    <div class="form-group">
        <h5 class="text-center text-primary">No se encontraron pedidos para entregar</h5>
    </div>
    @else
    <div class="accordion" id="accordion-guias">
        @foreach ($guias as $k => $g)
            <div class="card">
                <div class="card-header" id="headingOne{{$g->id}}">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse{{$g->id}}" aria-expanded="true" aria-controls="collapse{{$g->id}}">
                        Guía N° {{$g->nro_guia}}
                        </button>
                    </h2>
                </div>
                <div id="collapse{{$g->id}}" class="collapse {{$k === 0 ? 'show' : ''}}" aria-labelledby="headingOne{{$g->id}}" data-parent="#accordion-guias">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-6">
                                <b>Fecha Emitida: </b>
                                <span>{{$g->format_fecha}}</span>
                            </div>
                            <div class="form-group col-6">
                                <b>Fecha Reparto: </b>
                                <span>{{$g->fecha_reparto}}</span>
                            </div>
                            <div class="col-12">
                                <h6 class="text-primary"><i class="fas fa-caret-right"></i> Remitente</h6>
                            </div>
                            <div class="form-group col-12">
                                <b>Nombres: </b>
                                <span>{{$g->nom_prov}}</span>
                            </div>
                            <div class="col-12">
                                <h6 class="text-primary"><i class="fas fa-caret-right"></i> Destinatario</h6>
                            </div>
                            <div class="form-group col-12">
                                <b>Nombres: </b>
                                <span>{{$g->nom_dest}}</span>
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <b>Departamento: </b>
                                <span>{{$g->dest_depa}}</span>
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <b>Provincia: </b>
                                <span>{{$g->dest_prov}}</span>
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <b>Distrito: </b>
                                <span>{{$g->dest_dist}}</span>
                            </div>
                            <div class="form-group col-12">
                                <b>Dirección: </b>
                                <span>{{$g->dest_dire}}</span>
                            </div>
                            <div class="col-12">
                                <h6 class="text-primary"><i class="fas fa-caret-right"></i> Incidencias durante reparto</h6>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="id_situacion_reparto_{{$g->id}}">Situación Rep</label>
                                <select id="id_situacion_reparto_{{$g->id}}" class="form-control simple-select2 situacion-reparto" data-status="#id_estado_entregado_{{$g->id}}" data-detalle="#id_text_disconforme_reparto_{{$g->id}}" data-incidencia="#id_incidencia_reparto_{{$g->id}}">
                                    <option></option>
                                    <option value="CONFORME">CONFORME</option>
                                    <option value="INCIDENCIA">INCIDENCIA</option>
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="id_incidencia_reparto_{{$g->id}}">Incidencia Rep</label>
                                <select id="id_incidencia_reparto_{{$g->id}}" data-estado="#id_estado_entregado_{{$g->id}}" class="form-control simple-select2 situacion-incidencia" disabled>
                                    <option></option>
                                    <option value="DAÑO PARCIAL">DAÑO PARCIAL</option>
                                    <option value="DAÑO TOTAL">DAÑO TOTAL</option>
                                    <option value="PERDIDA PARCIAL">PERDIDA PARCIAL</option>
                                    <option value="PERDIDA TOTAL">PERDIDA TOTAL</option>
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <textarea class="form-control" disabled id="id_text_disconforme_reparto_{{$g->id}}" placeholder="Detallar la incidencia" maxlength="500"></textarea>
                            </div>
                            <div class="col-12">
                                <h6 class="text-primary"><i class="fas fa-caret-right"></i> Estados de entrega</h6>
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="id_estado_entregado_{{$g->id}}">Status Ent</label>
                                <select id="id_estado_entregado_{{$g->id}}" class="form-control simple-select2 status-entrega" data-noentregado="#id_motivo_no_ent_{{$g->id}}" data-visitas="#id_nro_visitas_reparto_{{$g->id}}">
                                    <option></option>
                                    <option value="ENTREGADO">ENTREGADO</option>
                                    <option value="NO ENTREGADO">NO ENTREGADO</option>
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="id_nro_visitas_reparto_{{$g->id}}">N° Visitas</label>
                                <select id="id_nro_visitas_reparto_{{$g->id}}" class="form-control simple-select2">
                                    <option></option>
                                    <option value="PRIMERA">PRIMERA</option>
                                    <option value="SEGUNDA">SEGUNDA</option>
                                    <option value="TERCERA">TERCERA</option>
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="id_motivo_no_ent_{{$g->id}}">Motivo No Ent</label>
                                <select id="id_motivo_no_ent_{{$g->id}}" class="form-control simple-select2">
                                    <option></option>
                                    <option value="AUSENTE">AUSENTE</option>
                                    <option value="DIR INCOMPLETA">DIR INCOMPLETA</option>
                                    <option value="SE MUDO">SE MUDO</option>
                                    <option value="DESCONOCIDO">DESCONOCIDO</option>
                                    <option value="RECHAZADO">RECHAZADO</option>
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <form id="form-guias-add-{{$g->id}}" hidden enctype="multipart/form-data">
                                    <input type="file" hidden name="img-guias[]" id="img-guias-send-{{$g->id}}" multiple>
                                </form>
                                <input type="file" data-transfer="img-guias-send-{{$g->id}}" hidden id="img-guia-{{$g->id}}" class="imgs-guias" multiple accept="image/*">
                                <button class="btn btn-sm btn-primary btn-add-img" data-input="#img-guia-{{$g->id}}" title="Agregar imágenes"><i class="fas fa-plus"></i> <i class="fas fa-camera"></i></button>
                            </div>
                            <div class="form-group col-12 text-center">
                                <button class="btn btn-sm btn-danger btn-save-entrega" data-nroguia="{{$g->nro_guia}}" data-motivo-noentregado="id_motivo_no_ent_{{$g->id}}" data-nro-visitas="id_nro_visitas_reparto_{{$g->id}}" data-situacion-detalle="id_text_disconforme_reparto_{{$g->id}}" data-situacion-incidencia="id_incidencia_reparto_{{$g->id}}" data-estado-entrega="id_estado_entregado_{{$g->id}}" data-situacion-reparto="id_situacion_reparto_{{$g->id}}" data-guia="{{$g->id}}" data-files="img-guias-send-{{$g->id}}" data-formulario="form-guias-add-{{$g->id}}">Entregar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif
</section>
@endsection
