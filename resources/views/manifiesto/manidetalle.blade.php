@extends('general.index')
@section('head')
    <title>Manifiesto</title>
@endsection
@section('body')
<section class="bg-white p-2">
    <form action="" class="mb-4 form-row">
        <div class="my-4 col-12">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/manifiesto.png')}}" alt="Imagen de clientes">
            </div>
            <h3 class="text-primary text-center">Manifiestos</h3>
        </div>
        <div class="form-group col-12 col-md-6">
            <label for="id_responsable_pago">Fch Manif</label>
            <input type="date" class="form-control">
        </div>
        <div class="form-group col-12 col-md-6">
            <label for="id_status">Trayecto</label>
            <select name="" id="" class="form-control">
                <option value="">LIMA - PROVINCIIA</option>
                <option value="">PROVINCIA - LIMA</option>
                <option value="">LIMA - LIMA</option>
                <option value="">PROVINCIA - PROVINCIA</option>
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="id_status">Provincia Origen</label>
            <select name="" id="" class="form-control">
                <option value="">LIMA</option>
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="id_status">Provincia Destino</label>
            <select name="" id="" class="form-control">
                <option value="">LIMA</option>
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="id_status">Status Guia</label>
            <select name="" id="" class="form-control">
                <option value="ACTIVA" selected>ACTIVA</option>
                <option value="ANULADA">ANULADA</option>
                <option value="INACTIVA">INACTIVA</option>
            </select>
        </div>
        <div class="form-group col-12 col-md-6 col-lg-3">
            <label for="id_status">Situación Guia</label>
            <select name="situacion_g" class="form-control">
                <option value="">REGISTRADA</option>
                <option value="">MANIFESTADA</option>
                <option value="">PROC EMBARQUE</option>
                <option value="">EMBARCADA</option>
                <option value="">TRANSITO</option>
                <option value="">ARRIVO</option>
                <option value="">REPARTO</option>
                <option value="">ENTREG Y CONF</option>
                <option value="">RETORNO CARGO</option>
                <option value="">CARGO EN CD</option>
                <option value="">DEVOLUCIÓN</option>
                <option value="">CARGO EN CUENTA</option>
            </select>
        </div>

        <div class="form-group col-12 col-md-6">
            <label for="id_situacion_g">Situación de Manifiesto</label>
            <select name="situacion_g" class="form-control">
                <option value="">REGISTRADA</option>
                <option value="">MANIFESTADA</option>
                <option value="">PROC EMBARQUE</option>
                <option value="">EMBARCADA</option>
                <option value="">TRANSITO</option>
                <option value="">REPARTO</option>
                <option value="">ENTREG Y CONF</option>
                <option value="">CLUSTER PROV</option>
                <option value="">RETORNO CARGO</option>
                <option value="">CARGO EN CD</option>
                <option value="">DEVOLUCIÓN</option>
                <option value="">CARGO EN CUENTA</option>
            </select>
        </div>
        <div class="form-group col-12 col-md-6">
            <label for="id_situacion_g">Nombre de Cluster</label>
            <select class="form-control">
                <option value="">JEAN PIER CARRASCO TAMARIZ</option>
                
            </select>
        </div>
        <fieldset class="px-3 border col-12 col-lg-6 col-xl-7 mb-4 form-row">
            <legend class="d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">EMPRESA</legend>
            <div class="form-group col-12 col-lg-6">
                <label for="">Empresa</label>
                <select name="" id="" class="form-control">
                    @foreach ($tipo_documentos as $tipo_docu)
                        <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="">Nro</label>
                <input type="text" class="form-control">
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="">Comprobantes</label>
                <select name="" id="" class="form-control">
                    <option value="">NN</option>
                </select>
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="">Importe Factura</label>
                <input type="number" min="0" step="0.01" class="form-control">
            </div>
        </fieldset>
        <fieldset class="px-3 border col-12 col-lg-6 col-xl-5 mb-4 form-row">
            <legend class="d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">FECHAS</legend>
            <div class="form-group col-12 col-lg-6">
                <label for="">Fch Emb</label>
                <input type="date" class="form-control">
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="">Fch Llegada</label>
                <input type="date" class="form-control">
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="">Hr Llegada</label>
                <input type="time" class="form-control">
            </div>
        </fieldset>
        <div class="col-12 col-lg-9 form-group p-4">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nro_Guia_S</th>
                            <th>Tipo_Envio_01</th>
                            <th>Descrip_01</th>
                            <th>Cant_01</th>
                            <th>Kg_01</th>
                            <th>Largo_01</th>
                            <th>Ancho_01</th>
                            <th>Alto_01</th>
                            <th>Vol_01</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <fieldset class="border col-12 col-lg-3 form-row form-group">
            <legend class="d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">BULTOS</legend>
            <div class="col-12 form-group">
                <label for="">Total</label>
                <input type="number" min="0" class="form-control">
            </div>
            <div class="col-12 col-md-6 form-group">
                <label for="">Cajas</label>
                <input type="number" min="0" class="form-control">
            </div>
            <div class="col-12 col-md-6 form-group">
                <label for="">Jabas</label>
                <input type="number" min="0" class="form-control">
            </div>
            <div class="col-12 col-md-6 form-group">
                <label for="">Cajones</label>
                <input type="number" min="0" class="form-control">
            </div>
            <div class="col-12 col-md-6 form-group">
                <label for="">Sobres</label>
                <input type="number" min="0" class="form-control">
            </div>
            <div class="col-12 col-md-6 form-group">
                <label for="">Paquetes</label>
                <input type="number" min="0" class="form-control">
            </div>
            <div class="col-12 col-md-6 form-group">
                <label for="">Otros</label>
                <input type="number" min="0" class="form-control">
            </div>
            <div class="col-12 form-group">
                <label for="">Detalle Otro</label>
                <input type="text" class="form-control">
            </div>
        </fieldset>
        <div class="col-12 col-md-6 col-lg-3 form-group">
            <label for="">Contribución Marginal</label>
            <input type="text" class="form-control">
        </div>
        <div class="col-12 col-md-6 col-lg-3 form-group">
            <label for="">Flete Estimado</label>
            <input type="number" class="form-control" step="0.01" min="0">
        </div>
    </form>
       
</section>
@endsection