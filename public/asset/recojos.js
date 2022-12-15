function loadPage(){
    let general = new General();
    let asignacion = null;
 
    const tablaSolicitudRecojo = document.getElementById('tabla-cuerpo-soli');
    const detalleFecha = document.getElementById('id_fechas_recojos_solicitud');
    
    async function getSolicitudFecha(){
        let datos = new FormData();
        datos.append('tipo', 'obter-fechas');
        try {
            let response = await general.funcfetch('obtener-programacion', datos);
            if(response.error){
                return alertify.error(response.error);
            }
            let total = 0;
            let template = "";
            response.success.forEach(so => {
                total+= so.total;
                template += `<div class="bg-light px-2 py-1 rounded-pill">
                <span>${so.fecha}:</span> 
                <b>${so.total}</b>
                </div>`
            });
            document.getElementById('total_solicitudes_mostradas_programar').textContent = total;
            detalleFecha.innerHTML = template == "" ? "<span class='text-danger'>SIN RESULTADOS</span>" : template;
        } catch (error) {
            alertify.error('error al obtener las solicitudes por fecha');
            console.error(error);
        }

    }
    getSolicitudFecha();
    async function getSolitudes(fecha,trayecto){
        let datos = new FormData();
        datos.append('fechaRecojo',fecha);
        datos.append('trayecto', trayecto);
        datos.append('tipo','obter-solicitudes');
        try {
            let response = await general.funcfetch('obtener-programacion', datos);
            if(response.error){
                return alertify.error(response.error);
            }
            let template = "";
            response.success.forEach(so => {
                let claseTr = 'table-warning';
                if (so.checked){
                    claseTr = 'table-success';
                }
                if(so.estado == 6){
                    claseTr = 'table-info';
                }
                const checkbox = so.checked ? `<input type="checkbox" data-dia="${so.dia_recojo_val}" data-ciudad="${so.ciudad_comparar}" class="checked-solicitud" value="${so.id}" data-trayecto="${so.trayecto}">` : '';
                template += `<tr class="${claseTr}">
                <td>${checkbox}</td>
                <td>${so.nro_soli}</td>
                <td>${so.fecha_recojo + ' ' + so.hr_estimado}</td>
                <td>${so.nro_doc}</td>
                <td>${so.nom_cli}</td>
                <td>${so.nomb_prov}</td>
                <td>${so.tipo}</td>
                <td>${so.depart_cli_recojo}</td>
                <td>${so.ciudad_cli_recojo}</td>
                <td>${so.distrito_cli_recojo}</td>
                <td>${so.direccion_recojo}</td>
                <td>${so.total_bulto}</td>
                </tr>`
            });
            tablaSolicitudRecojo.innerHTML = template == "" ? `<tr><td colspan="100%" class="text-center">SIN RESULTADOS</td></tr>` : template;
            document.getElementById('total_solicitudes_mostradas').textContent = response.success.length;
            for (const solicitudes of document.querySelectorAll('#tabla-cuerpo-soli .checked-solicitud')) {
                solicitudes.onchange = seleccionarSolicitud;
            }
        } catch (error) {
            alertify.error('error al obtener las solicitudes para ser programadas');
            console.error(error);
        }
    }
    function seleccionarSolicitud(e){
        let arrayTrayecto = this.dataset.trayecto == '1' || this.dataset.trayecto == '3' ? [1,3] : [2,4];
        const isProvincia = this.dataset.trayecto == '2' || this.dataset.trayecto == '4' ? this.dataset.ciudad : false;

        let seleccionar = false;
        const listSeleccion = document.querySelectorAll('#tabla-cuerpo-soli .checked-solicitud');
        for (const selection of listSeleccion) {
            if (arrayTrayecto.indexOf(+selection.dataset.trayecto) === -1){
                selection.checked = false;
                selection.disabled = true;
            }else if (isProvincia !== false && selection.dataset.ciudad !== isProvincia){
                selection.checked = false;
                selection.disabled = true;
            }
            if (selection.checked && !seleccionar){
                seleccionar = true;
            }
        }
        if (!seleccionar){
            for (const selection of listSeleccion) {
                selection.disabled = false;
            }
        }
    }
    document.getElementById('seleccionar_todo').onchange = function (e) {  
        const unSelector = document.querySelectorAll('#tabla-cuerpo-soli .checked-solicitud');
        if (!unSelector.length){
            return
        }
        for (const se of unSelector) {
            se.disabled = false;
        }
        const trayecto = unSelector[0].dataset.trayecto;
        let arrayTrayecto = trayecto == '1' || trayecto == '3' ? [1, 3] : [2, 4];
        const isProvincia = unSelector[0].dataset.trayecto == '2' || unSelector[0].dataset.trayecto == '4' ? unSelector[0].dataset.ciudad : false;
        if(this.checked){
            $(unSelector[0]).prop('checked',true);
            $(unSelector[0]).trigger('change');
        }
        [...unSelector].forEach((v,k) => {
            if(!this.checked){
                v.checked = false;
            }
            if (this.checked && k > 0){
                console.log(arrayTrayecto.indexOf(+v.dataset.trayecto) ,isProvincia)

                if (arrayTrayecto.indexOf(+v.dataset.trayecto) >= 0 && isProvincia === false){
                    v.checked = this.checked;
                }else if(isProvincia !== false && v.dataset.ciudad == isProvincia){
                    v.checked = this.checked;
                }
            }
        });
    }
    const $filtros = [document.getElementById('filtro_fecha_recojo'), document.getElementById('filtro_id_trayecto')];
    const [$diaFiltro,$trayectoFiltro] = $filtros;
    getSolitudes($diaFiltro.value, $trayectoFiltro.value);
    $diaFiltro.onchange = function(e){
        e.preventDefault();
        getSolitudes(this.value, $trayectoFiltro.value);
    }
    $trayectoFiltro.onchange = function (e) {
        e.preventDefault();
        getSolitudes($diaFiltro.value, this.value);
    }
    function verifTrayecto(elements,disabled){
        elements.forEach(e => {
            document.getElementById(e).disabled = disabled;
            document.getElementById(e).parentElement.hidden = disabled;  
        });
    }
    let listaSolicitudes = [];
    let fechaRecojo = null;
    document.getElementById('siguiente_programar').onclick = function(e){
        const selectionSoli = document.querySelectorAll('#tabla-cuerpo-soli .checked-solicitud:checked');
        if (!selectionSoli.length){
            return alertify.error('por favor selecciona una o mas solicitudes');
        }
        verifTrayecto(['id_id_cluster', 'id_id_conductor', 'id_id_dealer'], true);
        for (const soli of selectionSoli) {
            listaSolicitudes.push(soli.value);
        }
        if (selectionSoli[0].dataset.trayecto == '4' || selectionSoli[0].dataset.trayecto == '2'){
            verifTrayecto(['id_id_cluster'], false);
            asignacion = 'CLUSTER';
            filterClusters(selectionSoli[0].dataset.ciudad);
        }else{
            verifTrayecto(['id_id_conductor', 'id_id_dealer'], false);
            asignacion = "CONDUCTOR Y DEALER";
        }
        fechaRecojo = selectionSoli[0].dataset.dia;
        document.getElementById('modal_nro_solcicitudes').textContent = listaSolicitudes.length;
        document.getElementById('id_dia_recojo').value = fechaRecojo;
        $('#programar-recojo').modal('show');
    }
    $('#programar-recojo').on('hidden.bs.modal', function (event) {
        listaSolicitudes = [];
        fechaRecojo = null;
        deleteClusterOption.forEach(v => {
           $('#id_id_cluster').append(`<option value="${v.valor}" data-provincia="${v.provincia}">${v.texto}</option>`);
        })
        deleteClusterOption = [];
        document.getElementById('text-info-danger-cluster').hidden = true;
    });
    let deleteClusterOption = [];

    function filterClusters(codProvincia){
        const cbClusters = document.getElementById('id_id_cluster');
        deleteClusterOption = [];
        for (const clusters of cbClusters.children) {
            if (+clusters.dataset.provincia !== +codProvincia && clusters.value != ''){
                deleteClusterOption.push({
                    provincia: clusters.dataset.provincia,
                    valor : clusters.value,
                    texto: clusters.innerText
                });
            }
        }
        if ((cbClusters.children.length - 1) == deleteClusterOption.length){
            deleteClusterOption = [];
            document.getElementById('text-info-danger-cluster').hidden = false;
        }else{
            deleteClusterOption.forEach(v => {
                document.querySelector(`#id_id_cluster [value="${v.valor}"]`).remove();
            });
            document.getElementById('text-info-danger-cluster').hidden = true;
        }
    }
    document.getElementById('btn-programar-recojo').onclick = e => { document.getElementById('programar-recojo-modal').click()}
    document.getElementById('form-programar-recojo').onsubmit = async function(e){
        e.preventDefault();
        if (!listaSolicitudes.length){
            return
        }
        let datos = new FormData(this);
        datos.append('fecha_recojo', fechaRecojo);
        datos.append('solicitudes', JSON.stringify(listaSolicitudes));
        datos.append('responsable_recojo', asignacion);
        try {
            let response = await general.funcfetch(window.location.origin + '/intranet/pedidos/recojos/agregar',datos);
            if(response.error){
                return alertify.alert('Error',response.error);
            }
            $('#programar-recojo').modal('hide');
            alertify.success(response.success);
            getSolicitudFecha();
            getSolitudes($diaFiltro.value, $trayectoFiltro.value);
        } catch (error) {
            alertify.error('error al programar el recojo de las solicitudes');
            console.error(error);
        }
    }
}

window.addEventListener('DOMContentLoaded',loadPage);