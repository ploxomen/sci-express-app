<section class="mb-4">
    <div class="container p-1">
        <div class="form-group p-1">
            <h5 class="text-primary">Recojos</h5>
            @php
                $porcentaje = 0;
                $bg = 'bg-danger';
                if($datos['totalrecojo'] > 0){
                    $porcentaje = round((100 * $datos['recojoconcretado']) / $datos['totalrecojo']);
                }
                if ($porcentaje >= 25 && $porcentaje < 50) {
                    $bg = 'bg-warning';
                }
                if ($porcentaje >= 50 && $porcentaje < 75) {
                    $bg = 'bg-info';
                }
                if ($porcentaje >= 75) {
                    $bg = 'bg-success';
                }
            @endphp
            @if ($datos['totalrecojo'] > 0)
                <div class="progress">
                    <div class="progress-bar {{$bg}} progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="{{$porcentaje}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$porcentaje}}%">{{$porcentaje}}%</div>
                </div>
                <div class="d-flex justify-content-between">
                    <div>
                        <b>{{$datos['recojoconcretado']}}</b>
                    </div>
                    <div>
                        <b>{{$datos['totalrecojo']}}</b>
                    </div>
                </div>
            @else   
                <h6>No se encontraron recojos programados</h6>
            @endif
            
        </div>
        <div class="form-group p-1">
            <h5 class="text-primary">Embarques</h5>
             @php
                $porcentaje = 0;
                $bg = 'bg-danger';
                if($datos['totalembarque'] > 0){
                    $porcentaje = round((100 * $datos['embarqueconcretado']) / $datos['totalembarque']);
                }
                if ($porcentaje >= 25 && $porcentaje < 50) {
                    $bg = 'bg-warning';
                }
                if ($porcentaje >= 50 && $porcentaje < 75) {
                    $bg = 'bg-info';
                }
                if ($porcentaje >= 75) {
                    $bg = 'bg-success';
                }
            @endphp
            @if ($datos['totalembarque'] > 0)
                <div class="progress">
                    <div class="progress-bar {{$bg}} progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="{{$porcentaje}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$porcentaje}}%">{{$porcentaje}}%</div>
                </div>
                <div class="d-flex justify-content-between">
                    <div>
                        <b>{{$datos['embarqueconcretado']}}</b>
                    </div>
                    <div>
                        <b>{{$datos['totalembarque']}}</b>
                    </div>
                </div>
            @else
                <h6>No se encontraron embarques programados</h6>
            @endif
            
        </div>
        <div class="form-group p-1">
            <h5 class="text-primary">Entregas</h5>
             @php
                $porcentaje = 0;
                $bg = 'bg-danger';
                if($datos['totalentregas'] > 0){
                    $porcentaje = round((100 * $datos['entregasconcretadas']) / $datos['totalentregas']);
                }
                if ($porcentaje >= 25 && $porcentaje < 50) {
                    $bg = 'bg-warning';
                }
                if ($porcentaje >= 50 && $porcentaje < 75) {
                    $bg = 'bg-info';
                }
                if ($porcentaje >= 75) {
                    $bg = 'bg-success';
                }
            @endphp
            @if ($datos['totalentregas'] > 0)
                <div class="progress">
                    <div class="progress-bar {{$bg}} progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="{{$porcentaje}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$porcentaje}}%">{{$porcentaje}}%</div>
                </div>
                <div class="d-flex justify-content-between">
                    <div>
                        <b>{{$datos['entregasconcretadas']}}</b>
                    </div>
                    <div>
                        <b>{{$datos['totalentregas']}}</b>
                    </div>
                </div>
            @else
                <h6>No se encontraron entregas programadas</h6>
            @endif
            
        </div>
    </div>
</section>