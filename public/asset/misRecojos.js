function loadPage(){
    let idRecojo = null;
    let general = new General();
    let tablaRecojos = $('#tabla-recojos').DataTable({
        ajax: {
            url: 'mis-recojos',
            method: 'POST',
            headers: general.jsonToken,
            data: function (d) {
                d.estado = $('#estado_recojo').val();
                d.fechaInicio = $('#filtro_recojo_inicio').val();
                d.fechaFin = $('#filtro_recojo_fin').val();
            }
        },
        columns: [{
            data: 'nro_recojo'
        },
        {
            data: 'fecha_recojo'
        },
        {
            data: 'responsable_recojo'
        },
        {
            data: 'cantidad_solicitud'
        },
        {
            data: 'responsable'
        },
        {
            data: 'fecha_hr_creada'
        },
        {
            data: 'estado'
        },
        {
            data: 'id',
            render: function (data) {
                return `
                <button class="btn btn-primary mr-1 btn-sm view-update-recojo" data-recojo="${data}">
                    <i class="fas fa-edit"></i>
                </button> 
                <button class="btn btn-sm btn-warning reprogramar-solicitudes" data-recojo="${data}" title="Reprogramar todas las solicitudes">
                    <i class="fas fa-undo-alt"></i>
                </button>
                <button class="btn btn-danger btn-sm delete-recojo" data-recojo="${data}">
                    <i class="fas fa-trash-alt"></i>
                </button>`;
            }
        }]
    });
    document.getElementById('filtro-ver-solicitudes').onsubmit = function(e){
        e.preventDefault();
        tablaRecojos.draw();
    }
    for (const filtros of document.querySelectorAll('.cb-filtros')) {
        filtros.addEventListener('change',() => {tablaRecojos.draw()});
    }
    function programSolicitud(contenidoEditarSolicitud,msgConfirm,tipo,e){
        if (contenidoEditarSolicitud.children.length === 1) {
            return alertify.error('el recojo debe tener como minimo una solicitud');
        }
        alertify.confirm('Alerta', msgConfirm, async () => {
            let datos = new FormData();
            datos.append('tipo', tipo);
            datos.append('recojo', idRecojo);
            datos.append('detalleRecojo', e.target.dataset.detalleRecojo);
            try {
                let response = await general.funcfetch('modificaciones-admin', datos);
                if (response.error) {
                    return alertify.error(response.error);
                }
                $(e.target.dataset.collapse).collapse('dispose');
                document.querySelector(e.target.dataset.parent).remove();
                alertify.success(response.success);
                tablaRecojos.draw();
            } catch (error) {
                alertify.error('error al eliminar el detalle del recojo');
                console.error(error);
            }
        }, () => { })
    }
    let contenidoEditarSolicitud = document.getElementById('mostarSolicitudesEditar');
    contenidoEditarSolicitud.onclick = function(e){
        if (e.target.classList.contains('delete-solicitud')){
            programSolicitud(contenidoEditarSolicitud, '¿Estás seguro de eliminar este detalle del recojo?<br>Recuerda que la solicitud regresara al estado PENDIENTE','delete-solicitud',e);
        }
        if (e.target.classList.contains('revertir-solicitud')){
            programSolicitud(contenidoEditarSolicitud, '¿Estás seguro de reprogramar esta solicitud?', 'reprogramar-solicitud', e);
        }
    }
    $('#modal-recojo-modificar').on('hidden.bs.modal', function (event) {
        idRecojo = null;
    })
    document.getElementById('tabla-cuerpo-recojos').onclick = async function(e){
        if (e.target.classList.contains('view-update-recojo')){
            contenidoEditarSolicitud.innerHTML = ""; 
            let datos = new FormData();
            idRecojo = e.target.dataset.recojo;
            datos.append('recojo', idRecojo);
            datos.append('tipo','ver-actualizar');
            const response = await general.funcfetch('modificaciones-admin',datos);
            if(response.error){
                return alertify.error(response.error);
            }
            response.detalleRecojo.forEach(solicitud => {
                const cardSolicitud = createElementDetalleSolicitud(solicitud, response.vehiculos, response.dealers, response.conductores, response.clusters);
                contenidoEditarSolicitud.append(cardSolicitud);
            });
            $('#modal-recojo-modificar').modal('show');
            $('#modal-recojo-modificar .simple-select2').select2({
                theme: 'bootstrap',
                width: '100%',
                placeholder: 'Seleccione una opción'
            });
            $('#modal-recojo-modificar .cb-mult-nuevo').select2({
                theme: 'bootstrap',
                width: '100%',
                tags: true
            });
            $('#modal-recojo-modificar .collapse').collapse('show');
        }
        if (e.target.classList.contains('delete-recojo')){
            alertify.confirm('Alerta','¿Estas seguro de eliminar el recojo?<br>Recuerda que todas las solicitudes contenidas pasaran al estado PENDIENTE',async ()=>{
                let datos = new FormData();
                datos.append('idRecojo', e.target.dataset.recojo);
                datos.append('tipo', 'eliminar-recojo');
                const response = await general.funcfetch('modificaciones-admin', datos);
                if (response.error) {
                    return alertify.error(response.error);
                }
                alertify.success(response.success);
                tablaRecojos.draw();
            },()=>{})
        }
        if (e.target.classList.contains('reprogramar-solicitudes')){
            alertify.confirm('Alerta', '¿Estas seguro de reprogramar todas las solicitudes que se encuentrar en este recojo?', async () => {
                let datos = new FormData();
                datos.append('idRecojo', e.target.dataset.recojo);
                datos.append('tipo', 'reprogramar-solictudes');
                const response = await general.funcfetch('modificaciones-admin', datos);
                if (response.error) {
                    return alertify.error(response.error);
                }
                alertify.success(response.success);
                tablaRecojos.draw();
            }, () => { })
        }
    }

    function createElementDetalleSolicitud(solicitud,vehiculos,dealers,conductores,clusters){
        const card = document.createElement('div');
        card.id = `idCardSolicitud${solicitud.id}`;
        const cardHeader = document.createElement('div');
        cardHeader.classList.add('card-header');
        cardHeader.id = `cardHeaderSolicitud${solicitud.id}`
        cardHeader.innerHTML = `
        <h2 class="mb-0">
            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#bodyCollapset${solicitud.id}" aria-expanded="false" aria-controls="bodyCollapset${solicitud.id}">
            <b>Solicitud N° - ${solicitud.nro_solicitud}</b>
            </button>
            <button class="btn btn-danger float-right btn-sm delete-solicitud" data-parent="#idCardSolicitud${solicitud.id}" data-collapse="#bodyCollapset${solicitud.id}" data-detalle-recojo="${solicitud.id}" title="Eliminar solicitud">
                <i class="fas fa-trash-alt"></i>
            </button>
            <button class="btn btn-warning float-right mr-1 btn-sm revertir-solicitud" data-parent="#idCardSolicitud${solicitud.id}" data-collapse="#bodyCollapset${solicitud.id}" data-detalle-recojo="${solicitud.id}" title="Reprogramar solicitud">
                <i class="fas fa-undo-alt"></i>
            </button>
        </h2>
        `;
        const cardBody = document.createElement('div');
        cardBody.classList.add("collapse");
        const vehiculosSelect = document.createElement('select');
        const labelVehiclulos = document.createElement('label');
        const contenedorVehiculo = document.createElement('div');
        contenedorVehiculo.classList.add("col-12","col-md-6");
        vehiculosSelect.id = `idVehiculoSolicitud${solicitud.id}`;
        labelVehiclulos.htmlFor = vehiculosSelect.id;
        labelVehiclulos.textContent = "Placa de vehículo";
        vehiculosSelect.classList.add("form-control","form-control-sm","simple-select2");
        vehiculos.forEach(ve => {
            const optionVehiculo = document.createElement("option");
            optionVehiculo.value = ve.id;
            optionVehiculo.text = ve.placa;
            optionVehiculo.selected = ve.id == solicitud.id_vehiculo ? true : false;
            vehiculosSelect.append(optionVehiculo);
        });
        contenedorVehiculo.append(labelVehiclulos, vehiculosSelect);
        let contenedorCluster = null;
        if (solicitud.id_cluster){
            contenedorCluster = document.createElement('div');
            contenedorCluster.classList.add("col-12", "col-md-6");
            const clusterSelect = document.createElement('select');
            const labelCluster = document.createElement('label');
            clusterSelect.id = `idClusterSolicitud${solicitud.id}`;
            labelCluster.htmlFor = clusterSelect.id;
            labelCluster.textContent = "Cluster Disponibles";
            clusterSelect.classList.add("form-control", "form-control-sm", "simple-select2");
            clusters.forEach(clu => {
                const optionCluser = document.createElement("option");
                optionCluser.value = clu.id_cluster;
                optionCluser.text = clu.name + " " + clu.last_name;
                if (clu.id_cluster === solicitud.id_cluster) {
                    optionCluser.setAttribute('selected', 'selected')
                }
                clusterSelect.append(optionCluser);
            });
            contenedorCluster.append(labelCluster, clusterSelect);
        }
        let contenedorDealer = null;
        if (solicitud.id_dealer) {
            const dealaerSelect = document.createElement('select');
            const labelDealer = document.createElement('label');
            dealaerSelect.id = `idDealerSolicitud${solicitud.id}`;
            labelDealer.htmlFor = dealaerSelect.id;
            labelDealer.textContent = "Nombre dealer";
            contenedorDealer = document.createElement('div');
            contenedorDealer.classList.add("col-12", "col-md-6");
            dealaerSelect.classList.add("form-control", "form-control-sm", "simple-select2");
            dealers.forEach(di => {
                const optionDealer = document.createElement("option");
                optionDealer.value = di.id;
                optionDealer.text = di.name + " " + di.last_name;
                optionDealer.selected = di.id == solicitud.id_dealer ? true : false;
                dealaerSelect.append(optionDealer);
            });
            contenedorDealer.append(labelDealer, dealaerSelect);
        }
        let contenedorConductor = null;
        if (solicitud.id_conductor) {
            contenedorConductor = document.createElement('div');
            contenedorConductor.classList.add("col-12", "col-md-6");
            const conductorSelect = document.createElement('select');
            const labelConductor = document.createElement('label');
            conductorSelect.id = `idConductorSolicitud${solicitud.id}`;
            conductorSelect.classList.add("form-control", "form-control-sm", "simple-select2");
            labelConductor.htmlFor = conductorSelect.id;
            labelConductor.textContent = "Nombre driver";
            conductores.forEach(co => {
                const optionConductor = document.createElement("option");
                optionConductor.value = co.id;
                optionConductor.text = co.name + " " + co.last_name;
                optionConductor.selected = co.id == solicitud.id_conductor ? true : false;
                conductorSelect.append(optionConductor);
            });
            contenedorConductor.append(labelConductor, conductorSelect);
        }
        let cbDocumentos = document.createElement('select');
        cbDocumentos.id = `idGuiasConsolidadas${solicitud.id}`;
        cbDocumentos.multiple = true;
        cbDocumentos.classList.add('form-control','form-control-sm','cb-mult-nuevo');
        if (solicitud.nro_guias_consolidadas !== null && solicitud.nro_guias_consolidadas != ''){
            solicitud.nro_guias_consolidadas.split(';').forEach(documento => {
                const optionDocumento = document.createElement("option");
                optionDocumento.value = documento;
                optionDocumento.text = documento;
                optionDocumento.setAttribute('selected','selected');
                cbDocumentos.append(optionDocumento);
            })
        }
        cardBody.id = `bodyCollapset${solicitud.id}`;
        cardBody.setAttribute('aria-labelledby', cardHeader.id);
        cardBody.setAttribute('data-parent', '#mostarSolicitudesEditar');
        cardBody.innerHTML = `
        <div class="card-body">
            <div class="form-row">
                <div class="col-12">
                    <div class="d-flex text-info" style="gap: 0 5px;">
                        <h6 class="text-primary">${solicitud.tipoClienteFinal}:</h6>
                        <h6 class="text-secondary">${solicitud.clienteFinal}</h6>
                    </div>
                </div>
                <div class="col-6">
                    <b title="Fecha de recojo programado">Fecha Recojo Prog.: </b>
                    <span>${solicitud.dia_programado}</span>
                </div>
                <div class="col-6">
                    <b title="Hora de recojo estimado">Hr. Recojo Est.: </b>
                    <span>${solicitud.hr_recojo_estimado}</span>
                </div>
                ${contenedorVehiculo.outerHTML}
                ${contenedorCluster !== null ? contenedorCluster.outerHTML : ''}
                ${contenedorCluster !== null ? '' : '<div class="col-12"></div>'}
                ${contenedorDealer !== null ? contenedorDealer.outerHTML : ''}
                ${contenedorConductor !== null ? contenedorConductor.outerHTML : ''}
                <div class="col-6 col-lg-3">
                    <label for="idFechaRecojo${solicitud.id}">Fecha Recojo</label>
                    <input type="date" class="form-control form-control-sm" id="idFechaRecojo${solicitud.id}" value="${solicitud.fecha_recojida}">
                </div>
                <div class="col-6 col-lg-3">
                    <label for="idHrRecojoLlegada${solicitud.id}">Hr. Recojo Llegada</label>
                    <input type="time" class="form-control form-control-sm" id="idHrRecojoLlegada${solicitud.id}" value="${solicitud.hr_recojida_llegada}">
                </div>
                <div class="col-6 col-lg-3">
                    <label for="idHrRecojoDespacho${solicitud.id}">Hr. Recojo Despacho</label>
                    <input type="time" class="form-control form-control-sm" id="idHrRecojoDespacho${solicitud.id}" value="${solicitud.hr_recojida_despacho}">
                </div>
                <div class="col-6 col-lg-3">
                    <label for="idHrRecojoSalida${solicitud.id}">Hr. Recojo Salida</label>
                    <input type="time" class="form-control form-control-sm" id="idHrRecojoSalida${solicitud.id}" value="${solicitud.hr_recojida_salida}">
                </div>
                <div class="col-6 col-lg-3">
                    <label for="idDocumentoRecojo${solicitud.id}">Documento Recojo</label>
                    <select id="idDocumentoRecojo${solicitud.id}" class="form-control form-control-sm simple-select2">
                        <option></option>
                        <option value="UNA GUIA" ${solicitud.documento_recojo == 'UNA GUIA' ? 'selected' : ''}>UNA GUIA</option>
                        <option value="VARIAS GUIAS" ${solicitud.documento_recojo == 'VARIAS GUIAS' ? 'selected': ''}>VARIAS GUIAS</option>
                    </select>
                </div>
                <div class="col-6 col-lg-6">
                    <label for="${cbDocumentos.id}">Guias Consolidadas</label>
                    ${cbDocumentos.outerHTML} 
                </div>
                <div class="col-6 col-lg-3">
                    <label for="idCantidadBultos${solicitud.id}">Bultos</label>
                    <input type="number" class="form-control form-control-sm" id="idCantidadBultos${solicitud.id}" value="${solicitud.bultos_totales_recojidos}">
                </div>
            </div>
        </div>
        `
        card.classList.add('card');
        card.append(cardHeader,cardBody);
        return card;
    }

}
window.addEventListener('DOMContentLoaded',loadPage);