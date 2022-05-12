<section class="row mb-4">
    <div class="col-12">
        <h5 class="text-primary"><i class="fas fa-caret-right"></i> Recojos</h5>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4">
        <div class="carta">
            <div class="line bg-info"></div>
            <div class="principal">
                <span class="tipo">Del día</span>
                <span class="valor">{{$datos['recojodeldia']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-cash-register material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4">
        <div class="carta">
            <div class="line bg-info"></div>
            <div class="principal">
                <span class="tipo">Programados</span>
                <span class="valor">{{$datos['recoprogramado']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-money-bill-wave material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4">
        <div class="carta">
            <div class="line bg-info"></div>
            <div class="principal">
                <span class="tipo">Efectuados</span>
                <span class="valor">{{$datos['recoefectuado']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
    <div class="col-12">
        <h5 class="text-primary"><i class="fas fa-caret-right"></i> Manifiesto</h5>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4">
        <div class="carta">
            <div class="line bg-warning"></div>
            <div class="principal">
                <span class="tipo">Guías por manifestar</span>
                <span class="valor">{{$datos['manipendienteguia']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4">
        <div class="carta">
            <div class="line bg-warning"></div>
            <div class="principal">
                <span class="tipo">Guías hoy manifestar</span>
                <span class="valor">{{$datos['guiahoymanifestar']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4">
        <div class="carta">
            <div class="line bg-warning"></div>
            <div class="principal">
                <span class="tipo">Embarcar manifiestos</span>
                <span class="valor">{{$datos['manifiestoembarcar']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
    <div class="col-12">
        <h5 class="text-primary"><i class="fas fa-caret-right"></i> Embarques</h5>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4">
        <div class="carta">
            <div class="line bg-danger"></div>
            <div class="principal">
                <span class="tipo">Pendientes</span>
                <span class="valor">{{$datos['embarquependiente']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4">
        <div class="carta">
            <div class="line bg-danger"></div>
            <div class="principal">
                <span class="tipo">Efectuados</span>
                <span class="valor">{{$datos['embarqueefectuados']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4">
        <div class="carta">
            <div class="line bg-danger"></div>
            <div class="principal">
                <span class="tipo">Por informar</span>
                <span class="valor">{{$datos['embarqueporinformar']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
    <div class="col-12">
        <h5 class="text-primary"><i class="fas fa-caret-right"></i> Cluster</h5>
    </div>
    <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
        <div class="carta">
            <div class="line bg-success"></div>
            <div class="principal">
                <span class="tipo">Arribos por confirmar</span>
                <span class="valor">{{$datos['guiaporconfirmar']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
        <div class="carta">
            <div class="line bg-success"></div>
            <div class="principal">
                <span class="tipo">Entregas pendientes</span>
                <span class="valor">{{$datos['guiasenreparto']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
        <div class="carta">
            <div class="line bg-success"></div>
            <div class="principal">
                <span class="tipo">Entregas concretadas</span>
                <span class="valor">{{$datos['guiasentregadas']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
        <div class="carta">
            <div class="line bg-success"></div>
            <div class="principal">
                <span class="tipo">Guías por retornar</span>
                <span class="valor">{{$datos['guiasretornar']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
</section>