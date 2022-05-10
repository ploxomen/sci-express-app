<section class="row mb-4">
    <div class="form-group col-12 col-sm-6 col-lg-3">
        <div class="carta">
            <div class="principal">
                <span class="tipo">N° Solicitudes por asignar</span>
                <span class="valor">{{$datos['nsasolicitud']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-cash-register material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-3">
        <div class="carta">
            <div class="principal">
                <span class="tipo">N° guias por asignar</span>
                <span class="valor">{{$datos['nsaguias']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-money-bill-wave material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-3">
        <div class="carta">
            <div class="principal">
                <span class="tipo">N° Manifiesto por asignar</span>
                <span class="valor">{{$datos['nsamanifiesto']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-3">
        <div class="carta">
            <div class="principal">
                <span class="tipo">Embarques pendientes</span>
                <span class="valor">{{$datos['nsembarque']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-users material-icons"></i>
            </div>
        </div>
    </div>
</section>
<section class="bg-white p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="title">
            <h6 class="mb-0">N° Guias por año</h6>
        </div>
        <div class="control d-flex align-items-center">
            <button class="rounded-circle btn btn-sm btn-primary mr-3">
                <i class="fas fa-arrow-down"></i>                    
            </button>
            <select class="form-control form-control-sm" style="width: 100px;">
                <option value="2022">2022</option>
                <option value="2022">2021</option>
            </select>
        </div>
    </div>
    <div class="ventas-mes-grafico">
        <canvas class="graficos" id="grafico-1"></canvas>
    </div>
</section>