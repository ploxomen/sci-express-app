@extends('general.index')
@section('head')
    <script src="{{asset('asset/recojotrabajador.js')}}"></script>
    <title>Recojos pendientes</title>
@endsection
@section('body')
<style>
    .detalle-otros{
        white-space: pre-line;
        font-size: 13px;
        margin-bottom: 0 !important; 
    }
</style>
<section class="p-2">
   <div class="row">
       @if (!empty($recojos_pend))
            @foreach ($recojos_pend as $rd)
                <div class="form-group col-12 col-lg-6">
                    <div class="card">
                        <b class="card-header">N° Recojo - {{$rd->nro_recojo}}</b>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-12">
                                    <div class="col-12">
                                        <h6 class="text-primary"><i class="fas fa-caret-right"></i> Responsables</h6>
                                    </div>
                                    <div class="d-flex flex-wrap" style="gap: 0 10px;">
                                        @if(!empty($rd->nomDiler))
                                        <div>
                                            <b>Dealer:</b>
                                            <span>{{$rd->nomDiler}}</span>
                                        </div>
                                        @endif
                                        @if(!empty($rd->nomConductor))
                                        <div>
                                            <b>Conductor:</b>
                                            <span>{{$rd->nomConductor}}</span>
                                        </div>
                                            
                                        @endif
                                        @if(!empty($rd->nomCluster))
                                        <div>
                                            <b>Cluster:</b>
                                            <span>{{$rd->nomCluster}}</span>
                                        </div>
                                            
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group col-12">
                                    <b>Fecha Recojo:</b>
                                    <span>{{$rd->fecha_recojo_dato}}</span>
                                </div>
                            </div>
                            <div id="accordion_{{$rd->id}}">
                                @foreach ($rd->detalleSolicitud as $key => $re)
                                    <div class="card">
                                        <div class="card-header p-1" id="headingRecojo{{$re->id_recojo_solicitud}}">
                                            <h6 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseRecojo{{$re->id_recojo_solicitud}}" aria-expanded="true" aria-controls="collapseOne">
                                                <b>N° Solicitud - {{$re->nro_solicitud}}</b>
                                                </button>
                                            </h6>
                                        </div>
                                        <div id="collapseRecojo{{$re->id_recojo_solicitud}}" class="collapse {{$key === 0 ? 'show' : ''}}" aria-labelledby="headingRecojo{{$re->id_recojo_solicitud}}" data-parent="#accordion_{{$rd->id}}">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="d-flex text-info" style="gap: 0 5px;">
                                                            <h6 class="text-primary">{{$re->tipoClienteFinal}}:</h6>
                                                            <h6 class="text-secondary">{{$re->clienteFinal}}</h6>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex text-info justify-content-between">
                                                            <h6 class="mb-0">Bultos</h6>
                                                            <h6>{{$re->total_bulto}}</h6>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-6 col-md-3">
                                                        <b>Sobres:</b>
                                                        <span>{{$re->total_sobres}}</span>
                                                    </div>
                                                    <div class="form-group col-6 col-md-3">
                                                        <b>Paquetes:</b>
                                                        <span>{{$re->total_paquetes}}</span>
                                                    </div>
                                                    <div class="form-group col-6 col-md-3">
                                                        <b>Cajas:</b>
                                                        <span>{{$re->total_cajas}}</span>
                                                    </div>
                                                    <div class="form-group col-6 col-md-3">
                                                        <b>Otros:</b>
                                                        <span>{{$re->total_otros}}</span>
                                                    </div>
                                                    <div class="form-group col-12">
                                                        <b>Detalle Otros:</b>
                                                        <p class="detalle-otros">{{$re->detalle_otros}}</p>
                                                    </div>
                                                    <div class="col-12">
                                                        <h6 class="text-info mb-0">Trayecto</h6>
                                                    </div>
                                                    <div class="form-group col-12 col-md-6 col-xl-4">
                                                        <b>Departamento:</b>
                                                        <span>{{$re->depart_cli_recojo}}</span>
                                                    </div>
                                                    <div class="form-group col-12 col-md-6 col-xl-4">
                                                        <b>Provincia:</b>
                                                        <span>{{$re->ciudad_cli_recojo}}</span>
                                                    </div>
                                                    <div class="form-group col-12 col-md-6 col-xl-4">
                                                        <b>Distrito:</b>
                                                        <span>{{$re->distrito_cli_recojo}}</span>
                                                    </div>
                                                    <div class="form-group col-12">
                                                        <b>Dirección:</b>
                                                        <span>{{$re->direccion_recojo}}</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <h6 class="text-info mb-0">Datos Recojo</h6>
                                                    </div>
                                                    <div class="form-group col-6 col-xl-4">
                                                        <b>Fecha Prog.:</b>
                                                        <span>{{$re->format_dia_recojo}}</span>
                                                    </div>
                                                    <div class="form-group col-6 col-xl-4">
                                                        <b>Hr Reco Est:</b>
                                                        <span>{{$re->format_hr_recojo}}</span>
                                                    </div>
                                                     <div class="form-group col-6 col-xl-4">
                                                        <b>Fecha Reco.:</b>
                                                        <span>{{$re->format_fecha_recojido}}</span>
                                                    </div>
                                                    <div class="form-group col-6 col-xl-4">
                                                        <b>Hr Llegada:</b>
                                                        <span>{{$re->format_hr_llegada}}</span>
                                                    </div>
                                                    <div class="form-group col-6 col-xl-4">
                                                        <b>Hr Despacho:</b>
                                                        <span>{{$re->format_hr_despacho}}</span>
                                                    </div>
                                                    <div class="form-group col-6 col-xl-4">
                                                        <b>Hr Salida:</b>
                                                        <span>{{$re->format_hr_salida}}</span>
                                                    </div>
                                                    @if ($re->format_hr_llegada != 'Sin definir' && $re->format_hr_despacho != 'Sin definir')
                                                        <div class="form-group col-12">
                                                            <label><b>Doc Recojo</b></label><br>
                                                            <div class="custom-control custom-radio d-inline mr-3">
                                                                <input required type="radio"  name="doc_reco" class="custom-control-input select-doc" id="id_una_guia_{{$re->id_recojo_solicitud}}" value="UNA GUIA">
                                                                <label class="custom-control-label" for="id_una_guia_{{$re->id_recojo_solicitud}}">UNA GUIA</label>
                                                            </div>
                                                            <div class="custom-control custom-radio d-inline">
                                                                <input required type="radio" name="doc_reco" class="custom-control-input select-doc" id="id_varias_guias_{{$re->id_recojo_solicitud}}" value="VARIAS GUIAS">
                                                                <label class="custom-control-label" for="id_varias_guias_{{$re->id_recojo_solicitud}}">VARIAS GUIAS</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-12 col-lg-8">
                                                            <label for="id_nro_guias_conso_{{$re->id_recojo_solicitud}}">N° Guías Cons</label>
                                                            <select multiple class="form-control cb-mult-nuevo" required id="id_nro_guias_conso_{{$re->id_recojo_solicitud}}"></select>
                                                        </div>
                                                        <div class="form-group col-12 col-lg-4">
                                                            <label for="id_cant_bulto_{{$re->id_recojo_solicitud}}">Cant Bultos</label>
                                                            <input required type="number" class="form-control" value="{{$re->total_bulto}}" step="0.01" id="id_cant_bulto_{{$re->id_recojo_solicitud}}">
                                                        </div>
                                                    @endif
                                                    <div class="form-grouo col-12 text-center">
                                                        <button type="button" class="btn btn-danger btn-sm change-recojo" data-recojo="{{$re->id_recojo_solicitud}}" data-solicitud="{{$re->nro_solicitud}}">
                                                            @if ($re->format_hr_llegada == 'Sin definir')
                                                            Marcar Hr Llegada
                                                            @else
                                                                @if ($re->format_hr_despacho == 'Sin definir')
                                                                    Marcar Hr Despacho
                                                                @else
                                                                    @if ($re->format_hr_salida == 'Sin definir')
                                                                    Marcar Hr Salida
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        </button>
                                                    </div>
                                                </div>                                                
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                        </div>
                    </div>
                </div>
            @endforeach
       @else
           <div class="form-group col-12 bg-white p-2">
               <h3 class="text-primary text-center">No se encontraron recojos pendientes</h3>
           </div>
       @endif
    </div>
</section>
@endsection