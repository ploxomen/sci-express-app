<section class="row mb-4">
    <div class="form-group col-12 col-sm-6 col-lg-4">
        <div class="carta">
            <div class="line bg-warning"></div>
            <div class="principal">
                <span class="tipo">Guías de embarque pendientes</span>
                <span class="valor">{{$datos['guiasembarquependiente']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-cash-register material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4">
        <div class="carta">
            <div class="line bg-primary"></div>
            <div class="principal">
                <span class="tipo">Guías por manifestar</span>
                <span class="valor">{{$datos['guiasmanifestar']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-money-bill-wave material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4">
        <div class="carta">
            <div class="line bg-danger"></div>
            <div class="principal">
                <span class="tipo">Manifiestos por embarcar</span>
                <span class="valor">{{$datos['maniembarque']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
</section>