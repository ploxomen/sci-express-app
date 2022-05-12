@extends('general.index')
@section('head')
    <script src="{{asset('asset/recojotrabajador.js')}}"></script>
    <title>Recojos pendientes</title>
@endsection
@section('body')
<section class="p-2">
   <div class="row">
       @if (!empty($recojos_pend))
            @foreach ($recojos_pend as $re)
                <div class="form-group col-12 col-lg-6">
                    <div class="card">
                        <h5 class="card-header">N° Solicitud - {{str_pad($re->id, 5, "0", STR_PAD_LEFT)}}</h5>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-12">
                                    <b>Estado:</b>
                                    <span>{{$re->estado}}</span>
                                </div>
                                <div class="form-group col-12">
                                    <h5 class="text-info">Cliente</h5>
                                </div>
                                <div class="form-group col-12">
                                    <b>Cliente:</b>
                                    <span>{{$re->format_id_cliente}}</span>
                                </div>
                                <div class="form-group col-6">
                                    <b>Tipo Doc:</b>
                                    <span>{{$re->format_tipo_doc_cli}}</span>
                                </div>
                                <div class="form-group col-6">
                                    <b>N° Doc:</b>
                                    <span>{{$re->nro_doc_cli}}</span>
                                </div>
                                <div class="form-group col-12">
                                    <h5 class="text-info">Solicitante</h5>
                                </div>
                                <div class="form-group col-12">
                                    <b>Solicitante:</b>
                                    <span>{{$re->nom_soli}}</span>
                                </div>
                                <div class="form-group col-6">
                                    <b>Tipo Doc:</b>
                                    <span>{{$re->format_tipo_doc_soli}}</span>
                                </div>
                                <div class="form-group col-6">
                                    <b>N° Doc:</b>
                                    <span>{{$re->nro_doc_soli}}</span>
                                </div>
                                <div class="form-group col-12">
                                    <h5 class="text-info">Trayecto</h5>
                                </div>
                                <div class="form-group col-12 col-md-6 col-xl-4">
                                    <b>Departamento:</b>
                                    <span>{{$re->depar_cli}}</span>
                                </div>
                                <div class="form-group col-12 col-md-6 col-xl-4">
                                    <b>Provincia:</b>
                                    <span>{{$re->ciudad_cli}}</span>
                                </div>
                                <div class="form-group col-12 col-md-6 col-xl-4">
                                    <b>Distrito:</b>
                                    <span>{{$re->distrito_cli}}</span>
                                </div>
                                <div class="form-group col-12">
                                    <b>Dirección:</b>
                                    <span>{{$re->direccion_cli}}</span>
                                </div>
                                <div class="form-group col-12">
                                    <h5 class="text-info">Datos Recojo</h5>
                                </div>
                                <div class="form-group col-12 col-md-6 col-xl-4">
                                    <b>Fecha:</b>
                                    <span>{{$re->format_dia_recojo}}</span>
                                </div>
                                <div class="form-group col-12 col-md-6 col-xl-4">
                                    <b>Hr Reco Est:</b>
                                    <span>{{$re->format_hr_recojo}}</span>
                                </div>
                                <div class="form-group col-12 col-md-6 col-xl-4">
                                    <b>Hr Llegada:</b>
                                    <span>{{$re->format_hr_llegada}}</span>
                                </div>
                                <div class="form-group col-12 col-md-6 col-xl-4">
                                    <b>Hr Despacho:</b>
                                    <span>{{$re->format_hr_despacho}}</span>
                                </div>
                                <div class="form-group col-12 col-md-6 col-xl-4">
                                    <b>Hr Salida:</b>
                                    <span>{{$re->format_hr_salida}}</span>
                                </div>
                                @if ($re->format_hr_llegada != 'Sin definir' && $re->format_hr_despacho != 'Sin definir')
                                    <div class="form-group col-12">
                                        <label><b>Doc Recojo</b></label><br>
                                        <div class="custom-control custom-radio d-inline mr-3">
                                            <input required type="radio"  name="doc_reco" class="custom-control-input select-doc" id="id_una_guia_{{$re->id_recojo}}" value="UNA GUIA">
                                            <label class="custom-control-label" for="id_una_guia_{{$re->id_recojo}}">UNA GUIA</label>
                                        </div>
                                        <div class="custom-control custom-radio d-inline">
                                            <input required type="radio" name="doc_reco" class="custom-control-input select-doc" id="id_varias_guias_{{$re->id_recojo}}" value="VARIAS GUIAS">
                                            <label class="custom-control-label" for="id_varias_guias_{{$re->id_recojo}}">VARIAS GUIAS</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="id_nro_guias_conso_{{$re->id_recojo}}">N° Guías Cons</label>
                                        <select multiple class="form-control cb-mult-nuevo" required id="id_nro_guias_conso_{{$re->id_recojo}}"></select>
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="id_cant_bulto_{{$re->id_recojo}}">Cant Bultos</label>
                                        <input required type="number" class="form-control" value="{{$re->cant_bulto}}" step="0.01" id="id_cant_bulto_{{$re->id_recojo}}">
                                    </div>
                                @endif
                                <div class="form-grouo col-12 text-center">
                                    <button type="button" class="btn btn-danger change-recojo" data-recojo="{{$re->id_recojo}}">
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
       @else
           <div class="form-group col-12 bg-white p-2">
               <h3 class="text-primary text-center">No se encontraron recojos pendientes</h3>
           </div>
       @endif
    </div>
</section>
@endsection