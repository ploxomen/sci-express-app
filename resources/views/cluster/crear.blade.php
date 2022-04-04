@extends('general.index')
@section('head')
    <script src="{{asset('asset/crearcluster.js')}}"></script>
    <title>Agregar cluster</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="my-4">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/networking.png')}}" alt="Imagen de creación de cluster">
            </div>
            <h3 class="text-primary text-center">Crear cluster</h3>
        </div>
        <form id="frm_cluster" class="py-3 bg-white row">
            <div class="col-12 form-group col-md-6 col-lg-3 col-xl-4">
                <label for="id_n_document">N° D.N.I <small class="text-danger">*</small></label>
                <input type="text" class="form-control" id="id_n_document" required name="n_document" minlength="8" maxlength="8">
            </div>
            <div class="col-12 form-group col-md-6 col-lg-3 col-xl-4">
                <label for="id_name">Nombres <small class="text-danger">*</small></label>
                <input type="text" class="form-control" id="id_name" required name="name" maxlength="255">
            </div>
            <div class="col-12 form-group col-md-6 col-lg-3 col-xl-4">
                <label for="id_last_name">Apellidos <small class="text-danger">*</small></label>
                <input type="text" class="form-control" id="id_last_name" required name="last_name" maxlength="255">
            </div>
            <div class="col-12 form-group col-md-6 col-lg-3 col-xl-4">
                <label for="id_ruc_clu">N° R.U.C</label>
                <input type="text" class="form-control" id="id_ruc_clu" name="ruc_clu" minlength="11" maxlength="11">
            </div>
            <div class="col-12 form-group col-md-6 col-lg-3 col-xl-4">
                <label for="id_razon_clu">Razón Social</label>
                <input type="text" class="form-control" id="id_razon_clu" name="razon_clu">
            </div>
            <div class="col-12 form-group col-md-6 col-lg-3 col-xl-4">
                <label for="id_email">Correo <small class="text-danger">*</small></label>
                <input type="email" class="form-control" id="id_email" name="email" required>
                <small class="text-danger">Recuerda que el correo será el usuario del cluster</small>
            </div>
             <div class="col-12 form-group col-md-6 col-lg-3 col-xl-4">
                <label for="id_password">Contraseña Temp<small class="text-danger">*</small></label>
                <input type="text" class="form-control" value="sistemaenvio{{date('Y')}}" id="id_password" name="password" required>
            </div>
            <div class="col-12 form-group col-md-6 col-lg-3 col-xl-4">
                <label for="id_phone">Contacto principal</label>
                <input type="tel" class="form-control" id="id_phone" name="phone" minlength="9" maxlength="9">
                <small class="text-info">Es el número de celular personal del cluster</small>
            </div>
            <div class="col-12 form-group col-md-6 col-lg-3 col-xl-4">
                <label for="id_inst_fina_clu">Inst Financiera</label>
                <select id="id_inst_fina_clu" class="form-control simple-select2" name="inst_fina_clu">
                    <option></option>
                    <option value="BANCO DE CRÉDITO DEL PERÚ">BANCO DE CRÉDITO DEL PERÚ</option>
                    <option value="BANCO DE LA NACIÓN">BANCO DE LA NACIÓN</option>
                    <option value="BANCO INTERAMERICADO DE FINANZAS (BanBif)">BANCO INTERAMERICADO DE FINANZAS (BanBif)</option>
                    <option value="BANCO PICHINCHA">BANCO PICHINCHA</option>
                    <option value="BBVA">BBVA</option>
                    <option value="INTERBANK">INTERBANK</option>
                    <option value="MI BANCO">MI BANCO</option>
                    <option value="SCOTIABANK">SCOTIABANK</option>
                </select>
            </div>
            <div class="col-12 form-group col-md-6 col-lg-3 col-xl-4">
                <label for="id_nro_cuenta_clu">N° Cuenta</label>
                <input type="text" class="form-control" name="nro_cuenta_clu" id="id_nro_cuenta_clu">
            </div>
            <div class="col-12 form-group col-md-6 col-lg-3 col-xl-4">
                <label for="id_departamento_clu">Departamento <small class="text-danger">*</small></label>
                <select id="id_departamento_clu" name="departamento_clu" required class="form-control cb-ubigeo" data-consulta="provincia" data-info="id_provincia_clu" data-info-distrito="id_distrito_clu">
                    <option value="" hidden>Seleccione un departamento</option>
                    @foreach ($departamentos as $depar)
                        <option value="{{$depar->id_ubigeo}}">{{$depar->nombre_ubigeo}}</option>
                    @endforeach
                </select>
            </div>
             <div class="col-12 form-group col-md-6 col-lg-3 col-xl-4">
                <label for="">Provincia <small class="text-danger">*</small></label>
                <select class="form-control cb-ubigeo" id="id_provincia_clu" name="provincia_clu" data-consulta="distrito"  data-info="id_distrito_clu" required></select>
            </div>
            <div class="col-12 form-group col-md-6 col-lg-3 col-xl-4">
                <label for="id_distrito_clu">Distrito <small class="text-danger">*</small></label>
                <select class="form-control cb-ubigeo" id="id_distrito_clu" name="distrito_clu" required></select>
            </div>
            <div class="col-12 form-group">
                <label for="id_direccion_clu">Dirección <small class="text-danger">*</small></label>
                <textarea class="form-control" name="direccion_clu" id="id_direccion_clu" required></textarea>
            </div>
            <div class="from-group col-12">
                <button class="btn btn-success" type="submit">Agregar</button>
                <button class="btn btn-danger" type="reset">Limpiar</button>
            </div>
        </form>
    </section>
@endsection