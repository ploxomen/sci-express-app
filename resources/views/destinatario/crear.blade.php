@extends('general.index')
@section('head')
    <script src="{{asset('asset/destinatarioForm.js')}}"></script>
    <title>{{ $agregar ? 'Agregar destinatario' : 'Editar destinatario' }}</title>
@endsection
@section('body')
<section class="p-2">
    <form id="form-destinatarios" class="row">
        @csrf
        @if (!$agregar)
            @method('PUT')
            <input type="hidden" name="destinatario" value="{{$destinatario->id}}" id="id_nro_doc_cli">
        @endif
        <div class="mb-4 col-12">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/addcliente.png')}}" alt="Imagen de clientes">
            </div>
            <h3 class="text-primary text-center">{{$agregar ? 'Agregar nuevo destinatario' : 'Modificar destinatario'}}</h3>
        </div>
        <div class="mb-4 col-12">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">DATOS DEL DESTINATARIO</legend>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_tipo_doc">Tipo documento</label>
                    <select name="tipo_doc" id="id_tipo_doc" class="form-control simple-select2 cb-tipo-doc" data-input="id_nro_doc" required>
                        <option></option>
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}" {{ isset($destinatario->tipo_doc) && $tipo_docu->id === $destinatario->tipo_doc ? 'selected' : '' }}>{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_nro_doc">N° documento</label>
                    <input type="text" name="nro_doc" id="id_nro_doc" class="form-control" required value="{{ isset($destinatario->nro_doc) ? $destinatario->nro_doc : ''}}">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-6">
                    <label for="id_nombres">Nombre</label>
                    <input type="text" name="nombres" id="id_nombres" class="form-control" required value="{{ isset($destinatario->nombres) ? $destinatario->nombres : ''}}">
                </div>
            </fieldset>
        </div>
        <div class="mb-4 col-12">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add text-info">UBICACION</legend>
                <div class="form-group col-12">
                    <label for="id_direccion">Dirección</label>
                    <input type="text" name="direccion" id="id_direccion" class="form-control" value="{{ isset($destinatario->direccion) ? $destinatario->direccion : ''}}">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_dpto_sa">Departamento</label>
                    <select name="departamento" id="id_departamento" class="form-control cb-ubigeo selected-two" data-info="id_provincia" data-consulta="provincia" data-info-distrito="id_distrito" data-info-segundo-distrito="id_ciudad_final">
                        <option value=""></option>
                        @foreach ($departamentos as $departamento)
                            <option value="{{$departamento->id_ubigeo}}" {{ isset($destinatario->departamento) && $departamento->id_ubigeo == $destinatario->departamento ? 'selected' : '' }}>{{$departamento->nombre_ubigeo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_provincia">Provincia</label>
                    <select name="provincia" id="id_provincia" data-info="id_distrito" data-consulta="distrito" class="form-control cb-ubigeo selected-two">
                        <option value=""></option>
                        @foreach ($provincias as $provincia)
                            <option value="{{$provincia->id_ubigeo}}" {{ isset($destinatario->provincia) && $provincia->id_ubigeo == $destinatario->provincia ? 'selected' : '' }}>{{$provincia->nombre_ubigeo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_distrito">Distrito</label>
                    <select name="distrito" id="id_distrito" class="form-control cb-ubigeo cb-caserios selected-two" data-ciudades="#id_ciudad_final">
                        <option value=""></option>
                        @foreach ($distritos as $distrito)
                            <option value="{{$distrito->id_ubigeo}}" {{ isset($destinatario->distrito) && $distrito->id_ubigeo == $destinatario->distrito ? 'selected' : '' }}>{{$distrito->nombre_ubigeo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_ciudad_final">Ciudad Final</label>
                    <select name="id_ciudad_final" id="id_ciudad_final" class="form-control simple-select2">
                        <option value=""></option>
                        @foreach ($caserios as $caserio)
                            <option value="{{$caserio->id}}" {{ isset($destinatario->id_ciudad_final) && $caserio->id == $destinatario->id_ciudad_final ? 'selected' : '' }}>{{$caserio->nombre_ciudad}}</option>
                        @endforeach
                    </select>
                </div>
            </fieldset>
        </div>
        <div class="form-group col-12">
            @if (!$agregar)
                <button type="submit" class="btn btn-primary">Modificar</button>
                <a href="{{route('destinatarioMostrar')}}" class="btn btn-secondary">Cancelar</a>
            @else
                <button type="submit" class="btn btn-primary">Agregar</button>
                <button type="reset" class="btn btn-secondary">Limpiar</button>
            @endif
            
        </div>
    </form>
</section>
@endsection