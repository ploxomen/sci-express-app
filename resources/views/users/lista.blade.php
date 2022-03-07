@extends('general.index')
@section('head')
    <title>Mis usuarios</title>
    <script src="{{asset('asset/auth/users.js')}}"></script>
@endsection
@section('body')
<div class="my-4">
    <div class="baner-modulo">
        <img src="{{asset('asset/img/intranet/man.png')}}" alt="Imagen de clientes">
    </div>
    <h3 class="text-primary text-center">Mis usuario</h3>
</div>
<section class="p-2">
    <div class="bg-white p-3 mb-3">
        <form action="" class="row">
            <div class="form-group col-12 col-md-6 col-xl-3">
                <label for="">Buscar:</label>
                <input type="search" class="form-control" placeholder="Nombre y apellidos">
            </div>
            <div class="form-group col-12 col-md-6 col-xl-3">
                <label for="">Estado:</label>
                <select name="" id="" class="form-control">
                    <option value="">Activo</option>
                    <option value="">Desactivado</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-xl-3">
                <label for="">Área:</label>
                <select name="" id="" class="form-control">
                    <option value="">Ventas</option>
                    <option value="">Embarque</option>
                </select>
            </div>
            <div class="form-group col-12 col-md-5 col-xl-3">
                <label for="">Paginación:</label>
                <select name="" id="" class="form-control">
                    <option value="">10</option>
                    <option value="">25</option>
                    <option value="">50</option>
                    <option value="">100</option>
                    <option value="">Todos</option>
                </select>
            </div>
            
        </form>
    </div>
    <div class="bg-white p-3">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Nombre y Apellido</th>
                        <th>Celular</th>
                        <th>Correo</th>
                        <th>Área</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Jean Pier Carrasco Tamariz</td>
                        <td>924777699</td>
                        <td>jeanpi.jpct@gmail.com</td>
                        <td>Sistemas</td>
                        <td class="text-success">Activo</td>
                        <td>
                            <button class="btn btn-danger btn-sm"><span class="material-icons">delete</span></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection