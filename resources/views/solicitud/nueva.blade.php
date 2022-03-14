@extends('general.index')
@section('head')
    <script src="{{asset('asset/solicitud.js')}}"></script>
    <title>Solicitud</title>
@endsection
@section('body')
<section class="p-2">
    <form id="agregar-solicitud" class="mb-4 row">
        <div class="my-4 col-12">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/request.png')}}" alt="Imagen de clientes">
            </div>
            <h3 class="text-primary text-center">Solicitud</h3>
        </div>
        <div class="col-12 mb-4">
            <div class="bg-white p-3 form-row">
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="">N° solicitud</label>
                    <input type="text" readonly class="form-control text-right" value="{{str_pad($ultimo_id,5,'0',STR_PAD_LEFT)}}">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_fecha_soli">Fecha de solicitud</label>
                    <input type="date" class="form-control" id="id_fecha_soli" value="{{$hoy}}" name="fecha_soli" max="{{$hoy}}">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_trayecto">Trayecto de envio</label>
                    <select name="trayecto" id="id_trayecto" class="form-control">
                        @foreach ($trayectos as $trayecto)
                            <option value="{{$trayecto->id}}">{{$trayecto->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                    <label for="id_estado">Estado de solicitud</label>
                    <select name="estado" id="id_estado" class="form-control">
                       @foreach ($estados_solicitud as $estado)
                           <option value="{{$estado->id}}">{{$estado->estado}}</option>
                       @endforeach
                    </select>
                </div> --}}
            </div>
        </div>
        <div class="mb-4 col-12 col-lg-6">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">DATOS DEL CLIENTE</legend>
                <div class="form-group col-12">
                    <label for="id_documento_cli">Nombre</label>
                    <select id="id_documento_cli" class="form-control">
                        <option></option>
                        @foreach ($clientes as $k => $cliente)
                            <option data-nro="{{$cliente->nro_doc}}" value="{{$cliente->id}}">{{$cliente->nom_cli}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_doc_iden_cli">Doc de Identidad</label>
                    <select name="doc_iden_cli" id="id_doc_iden_cli" class="form-control text-clear" data-cliente="doc_ident">
                        <option value="" hidden>Seleccione una opción</option>
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_nro_doc_cli">N° Documento</label>
                    <input type="text" class="form-control text-clear" id="id_nro_doc_cli" name="nro_doc_cli" data-cliente="nro_doc">
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12 col-lg-6">
            <fieldset class="px-3 bg-white border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">DATOS DEL SOLICITANTE</legend>
                <div class="form-group col-12">
                    <label for="id_nom_soli">Nombre</label>
                    <select id="id_nom_soli" name="id_soli" class="form-control">
                        <option value=""></option>
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_doc_iden_soli">Doc de Identidad</label>
                    <select name="doc_iden_soli" id="id_doc_iden_soli" class="form-control text-clear">
                        <option value="" hidden>Seleccione una opción</option>
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_nro_doc_soli">N° Documento</label>
                    <input type="text" class="form-control text-clear" id="id_nro_doc_soli" name="nro_doc_soli">
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">DATOS ADICIONALES DEL RECOJO</legend>
                <div class="form-group col-12">
                    <label for="id_direccion_cli">Dirección</label>
                    <input type="text" class="form-control text-clear" id="id_direccion_cli" name="direccion_cli" data-proveedor="dire_prov" data-cliente="dir_sede_so">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_depar_cli">Departamento</label>
                    <select name="depar_cli" id="id_depar_cli" class="form-control cb-ubigeo" data-cliente="dpto_so" data-proveedor="depa_prov" data-consulta="provincia" data-info="id_ciudad_cli" data-info-distrito="id_distrito_cli">
                        @foreach ($dep as $d)
                            <option value="{{$d->id_ubigeo}}">{{$d->nombre_ubigeo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_ciudad_cli">Provincia</label>
                    <select name="ciudad_cli" id="id_ciudad_cli" class="form-control cb-ubigeo" data-cliente="prov_so" data-proveedor="prov_prov" data-consulta="distrito" data-info="id_distrito_cli">
                        <option></option>
                    </select>            
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_distrito_cli">Distrito</label>
                    <select name="distrito_cli" id="id_distrito_cli" class="form-control cb-ubigeo" data-proveedor="dist_prov" data-cliente="dist_so">
                        <option></option>
                    </select>            
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_dia_recojo">Día del recojo</label>
                    <input type="date" class="form-control" value="{{$hoy}}" min="{{$hoy}}" name="dia_recojo" id="id_dia_recojo">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_hr_recojo_estimado">Hr Recojo estimado</label>
                    <input type="time" class="form-control" name="hr_recojo_estimado" id="id_hr_recojo_estimado">        
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_cant_bulto">Cant de bulto</label>
                    <input type="number" class="form-control" name="cant_bulto" id="id_cant_bulto">         
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12">
            <button type="submit" class="btn btn-primary">Agregar</button>
        </div>
    </form>
        
</section>
@endsection