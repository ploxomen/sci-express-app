@extends('general.index')
@section('head')
    <title>Agregar usuarios</title>
    <script src="{{asset('asset/auth/crear_usuario.js')}}"></script>
@endsection
@section('body')
    <div class="my-4">
        <div class="baner-modulo">
            <img src="{{asset('asset/img/intranet/add-user.png')}}" alt="Imagen de clientes">
        </div>
        <h3 class="text-primary text-center">Agregar usuarios</h3>
    </div>
    <section class="p-2">
        <form id="frm-add-user">
            <fieldset class="px-3 bg-white border mb-2">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Datos personales</legend>
                <div class="form-group col-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="id_trabajador_cliente">
                        <label class="custom-control-label" for="id_trabajador_cliente">Usuario trabajador del Cliente</label>
                    </div>
                </div>
                <div class="form-row">
                <div class="form-group col-12 col-md-6">
                    <label for="type_document">Tipo de documento:</label>
                    <select name="type_document" id="type_document" class="form-control simple-select2 cb-tipo-doc" data-input="n_document">
                        <option></option>
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="n_document">N° de documento:</label>
                    <input type="text" name="n_document" id="n_document" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="name">Nombres: <small class="text-danger">*</small></label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="last_name">Apellidos: <small class="text-danger">*</small></label>
                    <input type="text" name="last_name" id="last_name" class="form-control" required>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-4">
                    <label for="email">Correo: <small class="text-danger">*</small></label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-3">
                    <label for="phone">Celular:</label> 
                    <input type="tel" maxlength="9" minlength="9" class="form-control" name="phone" id="phone">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                    <label for="id_sexo">Sexo:</label> 
                    <select name="sexo" id="id_sexo" class="form-control">
                        <option value="" hidden>Selecione una opción</option>
                        <option value="H">Hombre</option>
                        <option value="M">Mujer</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-3">
                    <label for="id_fecha_naci">Fecha Nacimiento:</label> 
                    <input type="date" class="form-control" name="nacimiento" id="id_fecha_naci">
                </div>
                <div class="form-group col-12">
                    <label for="direction">Direccion:</label>
                    <textarea name="direction" class="form-control" id="direction" cols="30" rows="2"></textarea>
                </div>
                </div>
            </fieldset>
            <fieldset class="bg-white px-3 border mb-2">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Datos del sistema</legend>
                <div class="row">
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="areas">Área: <small class="text-danger">*</small></label>
                        <select name="id_area" class="form-control" id="areas" required>
                            <option></option>
                            @foreach ($areas as $area)
                                <option value="{{$area->id}}">{{$area->area}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="roles">Roles: <small class="text-danger">*</small></label>
                        <select name="roles[]" class="form-control" multiple id="roles" required>
                            <option></option>
                            @foreach ($roles as $rol)
                                <option value="{{$rol->id}}">{{$rol->rol}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="password-temp">Contraseña temporal: <small class="text-danger">*</small></label>
                        <input name="password" required type="text" id="password-temp" class="form-control" value="{{env('PASSWORD_DEFAULT') . date('Y') . '@'}}">
                    </div>
                </div>
                
            </fieldset>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-location-arrow mr-1"></i>                        
                    <span>Agregar</span>
                </button>
                <button class="btn btn-danger" type="reset">
                    <i class="far fa-trash-alt mr-1"></i>
                    <span>Limpiar</span>
                </button>
            </div>
        </form>
    </section>
@include('modal.usuarioCliente')
@endsection