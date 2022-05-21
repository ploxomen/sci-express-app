<section class="row mb-4">
    <div class="form-group col-12 col-lg-4">
        <div class="carta">
            <div class="line bg-primary"></div>
            <div class="principal">
                <span class="tipo">Recojos Hoy</span>
                <span class="valor">{{$datos['rhoy']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-cash-register material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-lg-4">
        <div class="carta">
            <div class="line bg-danger"></div>
            <div class="principal">
                <span class="tipo">Embarques Hoy</span>
                <span class="valor">{{$datos['ehoy']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-shopping-cart material-icons"></i>
            </div>
        </div>
    </div>
    <div class="form-group col-12 col-lg-4">
        <div class="carta">
            <div class="line bg-warning"></div>
            <div class="principal">
                <span class="tipo">Informes Pendientes</span>
                <span class="valor">{{$datos['informespendientes']}}</span>
            </div>
            <div class="icono">
                <i class="fas fa-users material-icons"></i>
            </div>
        </div>
    </div>
</section>
{{-- <section class="bg-white p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="title">
            <h6 class="mb-0">N° Total Recojos</h6>
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
</section> --}}