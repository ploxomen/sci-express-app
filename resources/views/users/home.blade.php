@extends('general.index')
@section('head')
    <title>Inicio</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
@section('body')
    <style>
        .carta{
            position: relative;
            background: var(--color-secundario);
            padding: 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
        }
        .carta::before{
            content: "";
            display: block;
            width: 6px;
            height: 100%;
            position: absolute;
            left: 0;
            background: #9295ff;
        }
        .carta .principal .tipo{
            font-size: 15px;
            display: block;
            margin-bottom: 5px;
        }
        .carta .icono .material-icons{
            color: #3F80EA;
            background: #E0EAFC;
            padding: 10px;
            border-radius: 50%;
        }
        .carta .principal .valor{
            color: var(--color-letra-title);
        }
        .graficos{
            width: 100%;
            min-height: 350px !important;
        }
    </style>
    <h5>Panel de inicio</h5>
    <div class="p-3">
        <section class="row mb-4">
            <div class="form-group col-12 col-sm-6 col-lg-3">
                <div class="carta">
                    <div class="principal">
                        <span class="tipo">Ingreso del día</span>
                        <span class="valor">S/ 155.00</span>
                    </div>
                    <div class="icono">
                        <i class="fas fa-cash-register material-icons"></i>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 col-sm-6 col-lg-3">
                <div class="carta">
                    <div class="principal">
                        <span class="tipo">Ventas del mes</span>
                        <span class="valor">5</span>
                    </div>
                    <div class="icono">
                        <i class="fas fa-money-bill-wave material-icons"></i>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 col-sm-6 col-lg-3">
                <div class="carta">
                    <div class="principal">
                        <span class="tipo">Ventas pendientes</span>
                        <span class="valor">S/ 155.00</span>
                    </div>
                    <div class="icono">
                        <i class="fas fa-shopping-cart material-icons"></i>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 col-sm-6 col-lg-3">
                <div class="carta">
                    <div class="principal">
                        <span class="tipo">Clientes activos</span>
                        <span class="valor">150</span>
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
                    <h6 class="mb-0">Ventas del año</h6>
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
        <section class="p-3 mb-4">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="bg-white p-3 ventas-semanales">
                        <div>
                            <h6>Ventas de la semana</h6>
                        </div>
                        <div>
                            <canvas id="grafico-2" width="100%"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="bg-white p-3 ultima-notificacion">

                    </div>
                </div>
            </div>
        </section>
        
    </div>
    <script>
        const labels = [
            'Ene',
            'Feb',
            'Mar',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre'
        ];
        const data = {
            labels: labels,
            datasets: [{
                label: 'Ventas del año',
                backgroundColor: '#727CF5',
                borderColor: '#727CF5',
                barThickness: 15,
                data: [150, 100, 50, 55, 120, 30, 50, 150,150,120,120,140],
            }]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true
            }
        };
        const myChart = new Chart(
            document.getElementById('grafico-1').getContext('2d'),
            config
        );
        const data2 = {
        labels: [
            'Red',
            'Blue',
            'Yellow'
        ],
        datasets: [{
            label: 'My First Dataset',
            data: [300, 50, 100],
            backgroundColor: [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'
            ],
            hoverOffset: 4
        }]
        };
        const config2 = {
            type: 'doughnut',
            data: data2,
        };
        const myChart2 = new Chart(
            document.getElementById('grafico-2').getContext('2d'),
            config2
        );
    </script>

@endsection