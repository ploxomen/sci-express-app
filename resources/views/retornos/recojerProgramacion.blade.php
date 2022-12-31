@extends('general.index')
@section('head')
    <script src="{{asset('asset/misrecojosretornos.js')}}"></script>
    <title>Recoger retornos de clusters</title>
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
       @if (!empty($programaciones->count()))
            @foreach ($programaciones as $rd)
                @php
                    $total = count($rd->detalles);
                    $semiTotal = 0;
                @endphp
                <div class="form-group col-12 col-lg-6">
                    <div class="card">
                        <b class="card-header">N° Recojo - {{$rd->nroProgramado}}</b>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-12">
                                    <h6 class="text-primary"><i class="fas fa-caret-right"></i> Responsables</h6>
                                    
                                    <div class="d-flex flex-wrap" style="gap: 0 10px;">
                                        @if(!empty($rd->dealers))
                                        <div>
                                            <b>Dealer:</b>
                                            <span>{{$rd->dealers}}</span>
                                        </div>
                                        @endif
                                        @if(!empty($rd->conductores))
                                        <div>
                                            <b>Conductor:</b>
                                            <span>{{$rd->conductores}}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group col-6">
                                    <b>Fecha Recojo:</b>
                                    <span>{{$rd->fechaProgramada}}</span>
                                </div>
                                <div class="form-group col-6">
                                    <b>Vehiculo:</b>
                                    <span>{{$rd->vehiculos}}</span>
                                </div>
                            </div>
                            <div id="accordion_{{$rd->id}}" class="form-group">
                                @foreach ($rd->detalles as $key => $re)
                                    @php
                                        if($re->estado >= 3){
                                            $semiTotal++;
                                        }
                                    @endphp
                                    <div class="card">
                                        <div class="card-header p-1" id="headingRecojo{{$re->id}}">
                                            <div class="mb-0 d-flex justify-content-between align-items-center">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseRecojo{{$re->id}}" aria-expanded="true" aria-controls="collapseOne">
                                                    <b>{{$re->empresa}}</b>
                                                </button>
                                                <b>
                                                    {{$re->total_bultos}}
                                                </b>
                                            </div>
                                        </div>
                                        <div id="collapseRecojo{{$re->id}}" class="collapse {{$key === 0 ? 'show' : ''}}" aria-labelledby="headingRecojo{{$re->id}}" data-parent="#accordion_{{$rd->id}}">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <b>Empresa Transporte: </b>
                                                        <span>{{$re->empresa}}</span>
                                                    </div>
                                                    <div class="form-group col-12 col-md-7">
                                                        <b>Cluster: </b>
                                                        <span>{{$re->nombreCluster}}</span>
                                                    </div>
                                                    <div class="form-group col-12 col-md-5">
                                                        <b>FechaHr Llegada: </b>
                                                        <span>{{$re->fechaHrLlegada}}</span>
                                                    </div>
                                                    <div class="form-group col-4 col-md-3">
                                                        <b>Sobres:</b>
                                                        <span>{{empty($re->nro_sobres) ? 0 : $re->nro_sobres}}</span>
                                                    </div>
                                                    <div class="form-group col-4 col-md-3">
                                                        <b>Paquetes:</b>
                                                        <span>{{empty($re->nro_paquetes) ? 0 : $re->nro_paquetes}}</span>
                                                    </div>
                                                    <div class="form-group col-4 col-md-3">
                                                        <b>Cajas:</b>
                                                        <span>{{empty($re->nro_cajas) ? 0 : $re->nro_cajas}}</span>
                                                    </div>
                                                    <div class="form-group col-4 col-md-3">
                                                        <b>Jabas:</b>
                                                        <span>{{empty($re->nro_jabas) ? 0 : $re->nro_jabas}}</span>
                                                    </div>
                                                    <div class="form-group col-4 col-md-3">
                                                        <b>Cajones:</b>
                                                        <span>{{empty($re->nro_cajones) ? 0 : $re->nro_cajones}}</span>
                                                    </div>
                                                    <div class="form-group col-4 col-md-3">
                                                        <b>Otros:</b>
                                                        <span>{{empty($re->num_otros) ? 0 : $re->num_otros}}</span>
                                                    </div>
                                                    <div class="form-group col-12">
                                                        <b>Detalle Otros:</b>
                                                        <p class="detalle-otros">{{empty($re->detalle_otro) ? "-" : $re->detalle_otro}}</p>
                                                    </div>
                                                    <div class="form-group col-12">
                                                        <b>Tipo Comprobantes: </b>
                                                        <span>{{empty($re->tipo_comprobantes) ? "-" : $re->tipo_comprobantes}}</span>
                                                    </div>
                                                    <div class="form-group col-12">
                                                        <b>N° Comprobantes: </b>
                                                        <span>{{empty($re->nro_guias) ? "-" : $re->nro_guias}}</span>
                                                    </div>
                                                    <div class="form-group col-12">
                                                        <b>Importe Embarque: </b>
                                                        <span>{{empty($re->importe_embarque) ? "S/ 0.00" : "S/ " . $re->importe_embarque}}</span>
                                                    </div>
                                                    <div class="form-group col-12 text-center">
                                                    @if ($re->estado === 2)
                                                        <button class="btn btn-sm btn-outline-success btn-recoger" data-cluster="{{$re->nombreCluster}}" data-fecha="{{$re->fechaHrLlegada}}" data-recogo="{{$rd->id}}" data-retorno="{{$re->id}}">
                                                            Recogido
                                                        </button>
                                                    @else
                                                        <b>Fecha Hr. Recogido: </b>
                                                        <span>{{empty($re->fechaHrRecogido) ? "-" : $re->fechaHrRecogido}}</span>
                                                    @endif
                                                    </div>
                                                </div>                                                
                                            </div>
                                        </div>
                                    </div>
                                        
                                @endforeach
                            </div>
                            @if ($total === $semiTotal)
                                <div class="form-group text-center">
                                    <button class="btn btn-sm btn-outline-primary btn-finalizar" data-recogo="{{$rd->id}}">
                                        Finalizar Recojo
                                    </button>
                                </div>
                                
                            @endif
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