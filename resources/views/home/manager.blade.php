<section class="mb-4">
    <div class="container p-1">
        <div class="mb-3 p-3 bg-white">
            <h4 class="text-primary"><i class="fas fa-caret-right"></i> Filtros por fechas</h4>
            <form action="" class="form-group row">
                <div class="for-group col-10 col-md-6 col-lg-3">
                    <label for="fecha_filtros">Fecha Filtro</label>
                    <input type="date" value="{{date('Y-m-d')}}" id="fecha_filtros" class="form-control form-control-sm">
                </div>
                <div class="for-group col-2 col-md-6 col-lg-3">
                    <button type="button" class="btn btn-sm btn-light" id="btn-aplicar">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            <div class="form-group p-1">
                <h5 class="text-primary"><i class="fas fa-truck-pickup"></i> Recojos</h5>
                <div id="box_progress_recojo_total" hidden>
                    <div class="progress">
                        <div id="filtro_progress_p_recojo_total" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0;">0%</div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <b id="filtro_progress_recojo_completado">0</b>
                        </div>
                        <div>
                            <b id="filtro_progress_recojo_total">0</b>
                        </div>
                    </div>
                </div>
                <div id="box_ninguno_progress_recojo_total">
                    <h6>No se encontraron recojos programados</h6>
                </div>
            </div>
            <div class="form-group p-1">
                <h5 class="text-primary"><i class="fas fa-truck-moving"></i> Embarques</h5>
                <div id="box_progress_embarque_total" hidden>
                    <div class="progress">
                        <div id="filtro_progress_p_embarque_total" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0;">0%</div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <b id="filtro_progress_embarque_completado">0</b>
                        </div>
                        <div>
                            <b id="filtro_progress_embarque_total">0</b>
                        </div>
                    </div>
                </div>
                <div id="box_ninguno_progress_embarque_total">
                    <h6>No se encontraron embarques programados</h6>
                </div>
            </div>
            <div class="form-group p-1">
                <h5 class="text-primary"><i class="fas fa-truck"></i> Entregas</h5>
                <div id="box_progress_entregas_total" hidden>
                    <div class="progress">
                        <div id="filtro_progress_p_entregas_total" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0;">0%</div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <b id="filtro_progress_entregas_completado">0</b>
                        </div>
                        <div>
                            <b id="filtro_progress_entregas_total">0</b>
                        </div>
                    </div>
                </div>
                <div id="box_ninguno_progress_entregas_total">
                    <h6>No se encontraron entregqas programadas</h6>
                </div>
            </div>
        </div>
        <div class="form-group p-3 bg-white">
            <h4 class="text-primary"><i class="fas fa-caret-right"></i> Datos del mes de {{$datos['mes']}}</h4>
            <div class="row">
                <div class="form-group col-12 col-sm-6 col-lg-4">
                    <div class="carta border">
                        <div class="line bg-primary"></div>
                        <div class="principal">
                            <span class="tipo">Recojos Totales</span>
                            <span class="valor">{{$datos['recojoTotal']}}</span>
                        </div>
                        <div class="icono">
                            <i class="fas fa-cash-register material-icons"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-lg-4">
                    <div class="carta border">
                        <div class="line bg-primary"></div>
                        <div class="principal">
                            <span class="tipo">Recojos completados</span>
                            <span class="valor">{{$datos['recojoPendiente']}}</span>
                        </div>
                        <div class="icono">
                            <i class="fas fa-money-bill-wave material-icons"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-lg-4">
                    <div class="carta border">
                        <div class="line bg-primary"></div>
                        <div class="principal">
                            <span class="tipo">Recojos faltantes</span>
                            <span class="valor">{{$datos['recojoTotal']-$datos['recojoPendiente']}}</span>
                        </div>
                        <div class="icono">
                            <i class="fas fa-money-bill-wave material-icons"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-lg-4">
                    <div class="carta border">
                        <div class="line bg-danger"></div>
                        <div class="principal">
                            <span class="tipo">Embarques Totales</span>
                            <span class="valor">{{$datos['embarqueTotal']}}</span>
                        </div>
                        <div class="icono">
                            <i class="fas fa-shopping-cart material-icons"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-lg-4">
                    <div class="carta border">
                        <div class="line bg-danger"></div>
                        <div class="principal">
                            <span class="tipo">Embarques completados</span>
                            <span class="valor">{{$datos['embarquePendiente']}}</span>
                        </div>
                        <div class="icono">
                            <i class="fas fa-shopping-cart material-icons"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-lg-4">
                    <div class="carta border">
                        <div class="line bg-danger"></div>
                        <div class="principal">
                            <span class="tipo">Embarques faltantes</span>
                            <span class="valor">{{$datos['embarqueTotal']-$datos['embarquePendiente']}}</span>
                        </div>
                        <div class="icono">
                            <i class="fas fa-shopping-cart material-icons"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-lg-4">
                    <div class="carta border">
                        <div class="line bg-success"></div>
                        <div class="principal">
                            <span class="tipo">Entregas Totales</span>
                            <span class="valor">{{$datos['entregaTotal']}}</span>
                        </div>
                        <div class="icono">
                            <i class="fas fa-shopping-cart material-icons"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-lg-4">
                    <div class="carta border">
                        <div class="line bg-success"></div>
                        <div class="principal">
                            <span class="tipo">Entregas completadas</span>
                            <span class="valor">{{$datos['entregaPendiente']}}</span>
                        </div>
                        <div class="icono">
                            <i class="fas fa-shopping-cart material-icons"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-lg-4">
                    <div class="carta border">
                        <div class="line bg-success"></div>
                        <div class="principal">
                            <span class="tipo">Entregas faltantes</span>
                            <span class="valor">{{$datos['entregaTotal']-$datos['entregaPendiente']}}</span>
                        </div>
                        <div class="icono">
                            <i class="fas fa-shopping-cart material-icons"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>