@extends('general.index')
@section('head')
    <script src="{{asset('asset/editarperfil.js')}}"></script>
    <title>Mis perfil</title>
@endsection
@section('body')
<div class="my-4">
    <div class="baner-modulo">
        <img src="{{asset('asset/img/intranet/grupo.png')}}" alt="Imagen de usuario">
    </div>
    <h3 class="text-primary text-center">Mi perfil</h3>
</div>
<section class="p-1">
    <div class="bg-white container p-3">
        <div class="form-group">
            <span class="text-info">Los campos que están deshabilitados no pueden ser modificados, solo lo puede hacer el usuario con el rol "Manager"</span>
        </div>
        <form id="frm_editar_usuario" class="form-row">
            <div class="form-group col-12 col-lg-6">
                <label for="id_type_document">Tipo Documento</label>
                <select id="id_type_document" class="form-control" disabled>
                    @foreach ($tipo_documentos as $tipo_docu)
                        <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="id_n_document">N° Documento</label>
                <input type="text" id="id_n_document" class="form-control" disabled>
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="id_name">Nombres</label>
                <input type="text" id="id_name" class="form-control" disabled>
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="id_last_name">Apellidos</label>
                <input type="text" id="id_last_name" class="form-control" disabled>
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="id_email">Correo</label>
                <input type="email" id="id_email" class="form-control" disabled>
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="id_phone">Celular</label>
                <input type="text" name="phone" id="id_phone" class="form-control">
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="id_sexo">Sexo</label>
                <select name="sexo" id="id_sexo" class="form-control">
                    <option value="" hidden>Seleccione una opción</option>
                    <option value="H">Hombre</option>
                    <option value="M">Mujer</option>
                </select>
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="id_nacimiento">Fecha Nacimiento</label>
                <input type="date" name="nacimiento" id="id_nacimiento" class="form-control">
            </div>
            <div class="form-group col-12">
                <label for="id_direction">Dirección</label>
                <input type="text" id="id_direction" name="direction" class="form-control">
            </div>
            <div class="form-group col-12">
                <button class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</section>
@endsection