@extends('general.index')
@section('head')
    <script src="{{asset('asset/programarRetornoGuia.js')}}"></script>
    <title>Programación Guías Retornadas</title>
@endsection
@section('body')
<div class="my-4">
    <div class="baner-modulo">
        <img src="{{asset('asset/img/intranet/agreement.png')}}" alt="Imagen de retorno de guías">
    </div>
    <h4 class="text-primary text-center">Programación Guías Retornadas</h4>
</div>
<section class="p-2 bg-white">
    <form id="frm_filtros_soli" class="d-flex justify-content-center flex-wrap justify-content-md-end form-group" style="gap: 5px 10px;">
        <div style="max-width: 200px;">
            <label for="filtro_fecha_recojo">Fecha Llegada</label>
            <input name="fechaLegada" type="date" value="{{date('Y-m-d')}}" class="form-control" id="filtro_fecha_recojo">
        </div>
        <div style="width: 300px;">
            <label for="cb-clusters">Clusters</label>
            <select id="cb-clusters" name="cluster" class="simple-select2">
                <option value="0">Todos</option>
                @foreach ($clusters as $clu)
                    <option value="{{$clu->id}}">{{$clu->name}} {{$clu->last_name}}</option>
                @endforeach
            </select>
        </div>
    </form>
    <div class="form-group">
        <h6 class="text-primary d-inline-block">Total retornos por programar:</h6>
        <h6 class="d-inline-block" id="total_retornos_programar"></h6>
    </div>
    <div class="form-group">
        <h6 class="text-primary">Retornos agrupados por Fch. Llegada: </h6>
        <div class="d-flex flex-wrap" style="gap: 10px;" id="fechas_llegadas_retornos"></div>
    </div>
    <div class="table-responsive form-group">
        <div class="form-group d-flex justify-content-sm-end" style="gap: 0 5px;">
            <h6 class="text-primary">Total retornos mostrados: </h6>
            <h6 id="total_retornos_mostrados"></h6>
        </div>
        <table class="table table-sm table-bordered" id="tabla-retorno">
            <thead>
                <tr>
                    <th>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="cehckRetornoTodo">
                            <label class="custom-control-label" for="cehckRetornoTodo"></label>
                        </div>
                    </th>
                    <th>N° Embarque</th>
                    <th>Fecha Emb</th>
                    <th>Empresa <br> Trasnporte</th>
                    <th>Fecha y Hr <br>Llegada</th>
                    <th>Cluster</th>
                    <th>Import Fact</th>
                    <th>Comprobantes</th>
                    <th>N° Comp</th>
                    <th>Total Bulto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="cuerpo-detalle-guias"></tbody>
        </table>
    </div>
    <div class="form-group">
        <button class="btn btn-outline-success" id="nexProgramacion">Siguiente</button>
    </div>
</section>
@include('modal.detallembarque')
@include('modal.programacionRetornoGuia')
@endsection