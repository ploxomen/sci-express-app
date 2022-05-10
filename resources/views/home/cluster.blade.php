<section class="row mb-4">
    <div class="form-group col-12 col-sm-6 col-lg-4 col-xl-3">
        <div class="carta">
            <div class="line bg-warning"></div>
            <div class="principal">
                <span class="tipo">Arribos por confirmar</span>
                <span class="valor">{{$datos['arriboconfirmar']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-cash-register material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4 col-xl-3">
        <div class="carta">
            <div class="line bg-primary"></div>
            <div class="principal">
                <span class="tipo">Entregas pendientes</span>
                <span class="valor">{{$datos['entregaspendientes']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-money-bill-wave material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4 col-xl-3">
        <div class="carta">
            <div class="line bg-danger"></div>
            <div class="principal">
                <span class="tipo">Entregas concretadas</span>
                <span class="valor">{{$datos['guiasentregadas']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4 col-xl-3">
        <div class="carta">
            <div class="line bg-info"></div>
            <div class="principal">
                <span class="tipo">Guías por retornar</span>
                <span class="valor">{{$datos['cargosretornar']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
</section>