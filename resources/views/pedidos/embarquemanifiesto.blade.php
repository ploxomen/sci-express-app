@extends('general.index')
@section('head')
    <script src="{{asset('asset/marcarembarque.js')}}"></script>
    <title>Embarques pendientes</title>
@endsection
@section('body')
    <style>
        .form-row span,
        .form-row b{
            font-size: 14px;
        }
    </style>
    <section class="p-2">
        @if (empty($embarques))
            <h3 class="text-center text-primary">No se te asigno manifiestos para embarcar</h3>
        @else
            <div class="row">
            @foreach ($embarques as $emb)
                <div class="form-group col-12 col-lg-6 col-xl-4">
                    <div class="card {{$emb->nosotros === 1 ? 'card border-danger' : ''}}">
                        <div class="card-header text-right">
                            <b>N° embarque - {{$emb->nro_embarque}}</b>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-12">
                                    <b>Fecha Embarque: </b>
                                    <span>{{$emb->format_embarque}}</span>
                                </div>
                                <div class="col-12">
                                    <h6 class="text-primary"><i class="fas fa-caret-right"></i> Responsables</h6>
                                </div>
                                <div class="form-group col-12 col-lg-6 col-xl-4">
                                    <b class="d-block">Conductor</b>
                                    <span>{{$emb->nom_cond}}</span>
                                </div>
                                <div class="form-group col-12 col-lg-6 col-xl-4">
                                    <b class="d-block">Dealer 1</b>
                                    <span>{{$emb->nom_deal}}</span>
                                </div>
                                <div class="form-group col-12 col-lg-6 col-xl-4">
                                    <b class="d-block">Dealer 2</b>
                                    <span>{{$emb->nom_deal2}}</span>
                                </div>
                                <div class="col-12">
                                    <h6 class="text-primary"><i class="fas fa-caret-right"></i> Transporte</h6>
                                </div>
                                <div class="form-group form-group col-12 col-md-6">
                                    <b class="d-bloc">Empresas Disponibles</b>
                                    <ul class="ml-3 mb-0">
                                        @foreach ($emb->transportes as $trans)
                                            <li><span>{{$trans->empresa}}</span></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <b class="d-block">Vehículo Embarque</b>
                                    <span>{{$emb->placa}}</span>
                                </div>
                                <div class="col-12">
                                    <h6 class="text-primary"><i class="fas fa-caret-right"></i> Información</h6>
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-8">
                                    <b>Flete Estimado:</b>
                                    <span>S/ {{$emb->flet_esti}}</span>
                                </div>
                                 <div class="form-group col-12 col-md-6 col-lg-4">
                                    <b>Tot. Bultos:</b>
                                    <span>{{$emb->total_bultos_paquete}}</span>
                                </div>
                                <div class="col-12">
                                    <h6 class="text-primary"><i class="fas fa-caret-right"></i> Hr Embarque</h6>
                                </div>
                                <div class="form-group col-12 col-md-6 col-xl-4">
                                    <b class="d-block">Hr Ini</b>
                                    <span>{{$emb->hr_inicio_embarque}}</span>
                                </div>
                                <div class="form-group col-12 col-md-6 col-xl-4">
                                    <b class="d-block">Hr Est Fin</b>
                                    <span>{{$emb->hr_estimada}}</span>
                                </div>
                                <div class="form-group col-12 col-md-6 col-xl-4">
                                    <b class="d-block">Hr Fin</b>
                                    <span>{{$emb->hr_fin_embarque}}</span>
                                </div>
                                <div class="col-12">
                                    <h6 class="text-primary"><i class="fas fa-caret-right"></i> Manifiestos</h6>
                                </div>
                                <div class="form-group col-12">
                                    <div id="accordion_{{$emb->id}}">
                                    @foreach ($emb->manfiestos as $k=>$m)
                                        <div class="card">
                                            <div class="card-header p-0" id="headingOne">
                                                <h5 class="mb-0">
                                                    <button class="btn btn-link d-block w-100" data-toggle="collapse" data-target="#colapset_mani_{{$m->id}}" aria-expanded="true">
                                                        <div class="d-flex justify-content-between">
                                                            <span>
                                                                @if ($m->via_envio == 'TERRESTRE')
                                                                    <i class="fas fa-truck"></i>
                                                                @else
                                                                    <i class="fas fa-plane"></i>
                                                                @endif 
                                                                <b>Ciudad: {{$m->nombreciudad}}</b>
                                                            </span>
                                                            <b title="Bultos para la ciudad {{$m->nombreciudad}}">{{$m->subtotalbulto}}</b>
                                                        </div>
                                                    </button>
                                                </h5>
                                            </div>
                                            <div id="colapset_mani_{{$m->id}}" class="collapse {{$k === 0 ? 'show' : ''}}" data-parent="#accordion_{{$emb->id}}">
                                                <div class="card-body">
                                                    <h6 class="text-info">Manifiestos</h6>
                                                    <div class="d-flex flex-wrap" style="gap: 8px;">
                                                        @foreach ($m->listaManifiestos as $ma)
                                                            <span>
                                                                N° {{$ma}}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                    @if (!empty($emb->hr_ini_emba) && empty($emb->fin_emba))
                                                        <div>
                                                            <label for="transpotes_select_{{$emb->id}}">Empresa de Transporte</label>
                                                            <select class="form-control form-control-sm" id="transpotes_select_{{$emb->id}}">
                                                                <option value="" hidden>Seleccione la empresa de trasnporte</option>
                                                                @foreach ($emb->transportes as $trans)
                                                                    <option value="{{$trans->id}}">{{$trans->empresa}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                                @if ($emb->nosotros === 1)
                                    <div class="form-group col-12">
                                        <strong class="text-danger">Envio local</strong>
                                    </div>
                                @endif
                                
                                <div class="col-12 text-center">
                                    <button class="btn btn-primary marcar-embarque" data-embarque="{{$emb->id}}">Marcar Hr {{empty($emb->hr_ini_emba) ? 'Inicio' : 'Fin'}}</button>
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