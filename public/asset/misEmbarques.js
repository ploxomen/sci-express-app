function loadPage(){
    const general = new General();
     let tablaEmbarques = $('#tabla-embarques').DataTable({
        ajax: {
            url: 'obtener-todos',
            method: 'POST',
            headers: general.jsonToken,
            data: function (d) {
                d.driver = $('#id_driver').val();
                d.dealer = $('#id_dealer').val();
                d.vehiculo = $('#id_vehiculo').val();
                d.estado = $('#estado_embarque').val();
                d.fechaInicio = $('#fecha_inicio').val();
                d.fechaFin = $('#id_fecha_fin').val();
            }
        },
        columns : [
            {data : 'nro_embarque'},
            {data : 'fecha_embarque'},
            {data : 'nom_estado'},
            {data : 'nomConductor'},
            {data : 'manifiesto_search', visible : false},
            {data : 'dealer'},
            {data : 'dealer2'},
            {data : 'placa'},
            {data : 'hr_inicio'},
            {data : 'hr_fin'},
            {
                data: 'id',
                width: '20%',
                render: function (data, type, row) {
                let template = "";
                row.manifiestos.forEach((v, k) => {
                    template += `<small class="badge badge-pill badge-dark m-1 text-12 cursor-pointer mostrar-manifiesto" data-manifiesto="${v.id}">${v.nro}</small>`;
                });
                
                return template;
            }},
            {
                data: "id",
                render : function(data,type,row){
                    return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-primary mr-2 editar-embarque" data-estado="${row.estado}" data-embarque="${data}" title="Editar embarque"><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-secondary mr-2 editar-embarque-fletes" data-estado="${row.estado}" data-embarque="${data}" title="Editar fletes de embarque"><i class="fas fa-pencil-alt"></i></button><button class="btn eliminar-embarque btn-sm btn-danger" data-embarque="${data}" data-estado="${row.estado}" title="Eliminar embarque"><i class="far fa-trash-alt"></i></button></div>`
                }
            }
        ],
        order: [
            0, 'desc'
        ]
    });
    $('.cb-filtros').on('select2:select',function(e){
        tablaEmbarques.draw();
    });
    let datosGlobales = {
        estadoEmbarque : 0,
        idEmbarque : 0,
        listaManifiesto: 0,
        changeTabla: 0
    }
    general.obtenerFlestes();
    let listaManifiestos = document.getElementById('lista-manifiestos'),
        listaManifiestoAgregar = document.getElementById('lista-manifiestos-pendientes'),
        listaGuias = document.getElementById('lista-guias');
    document.getElementById('tabla-embarques').addEventListener('click', async function (e) {
        if (e.target.classList.contains('eliminar-embarque')) {
            alertify.confirm('Alerta', '¿Estás seguro de eliminar este embarque?<br>Recuerda que al aceptar, los manifiestos asociados cambiaran de estado a <strong>GENERADO</strong>, también las guías asociadas a cada manifiesto pasaran al estado de <strong>MANIFESTADA</stron>', async () => {
                const datos = new FormData();
                datos.append('embarque', e.target.dataset.embarque);
                const response = await general.funcfetch('eliminar', datos);
                if (response.success) {
                    alertify.alert('Mensaje', response.success);
                    tablaEmbarques.draw();
                    return
                }
                alertify.alert('Error', response.error);
            }, () => {});
        }
        if (e.target.classList.contains('editar-embarque')) {
            datosGlobales.estadoEmbarque = e.target.dataset.estado;
            datosGlobales.idEmbarque = e.target.dataset.embarque;
            let datos = new FormData();
            datos.append('embarque', datosGlobales.idEmbarque);
            let response = await general.funcfetch('editar-obtener', datos);
            listaManifiestos.innerHTML = "";
            if (response.success[0]) {
                for (const key in response.success[0]) {
                    if (Object.hasOwnProperty.call(response.success[0], key)) {
                        const element = response.success[0][key];
                        const domcontent = document.getElementById('informe_' + key);
                        if (key == 'manifiestos') {
                            let template = "";
                            element.forEach(g => {
                                template += `<li class="form-group">
                                <div class="d-flex justify-content-between align-align-items-center">
                                    <div><strong>N° Manifiesto ${g.nro}</strong></div>
                                    <div><button type="button" data-manifiesto="${g.id}" data-nromanifiesto="${g.nro}" title="Eliminar este manifiesto del embarque" class="btn btn-sm btn-danger eliminar-manifiesto"><i class="far fa-trash-alt"></i></button></div>
                                </div></li>`;
                            });
                            listaManifiestos.innerHTML = template;
                            datosGlobales.listaManifiesto = element.length;
                            continue;
                        }
                        if (!domcontent) {
                            continue;
                        }
                        if (key == 'nro_embarque') {
                            domcontent.textContent = element;
                            continue;
                        }
                        domcontent.value = element;
                    }
                }
                $('.reset-update').trigger('change');
                $('#embarque-editar').modal('show');
            }
            let templateGuia = "";
            response.manifiesto.forEach(g => {
                const nroManifiesto = g.id.toString().padStart(5, '0');
                templateGuia += `<li class="form-group">
                <div class="d-flex justify-content-between align-align-items-center">
                    <div><strong>N° Manifiesto ${nroManifiesto}</strong></div>
                    <div><button type="button" data-manifiesto="${g.id}" data-nromanifiesto="${nroManifiesto}" title="Agregar manifiesto a este embarque" class="btn btn-sm btn-success agregar-manifiesto"><i class="fas fa-plus"></i></button></div>
                </div></li>`;
            });
            listaManifiestoAgregar.innerHTML = templateGuia ? templateGuia : '<li class="text-center">No se encontraron manifiestos</li>';
        }
        if (e.target.classList.contains('mostrar-manifiesto')) {
            let datos = new FormData();
            datos.append('manifiesto', e.target.dataset.manifiesto);
            datos.append('cluster', null);
            let response = await general.funcfetch('./../../manifiesto/editar-obtener', datos);
            listaGuias.innerHTML = "";
            if (response.success[0]) {
                for (const key in response.success[0]) {
                    if (Object.hasOwnProperty.call(response.success[0], key)) {
                        const element = response.success[0][key];
                        const domcontent = document.getElementById('informe_' + key);
                        if (key == 'guias_asociadas') {
                            let template = "";
                            element.forEach(g => {
                                template += `<li class="form-group">
                                <strong>N° Guía ${g.nro}</strong>
                                </li>`;
                            });
                            listaGuias.innerHTML = template;
                        }
                        if (key == 'listCluster') {
                            let tempcb = "";
                            element.forEach(c => {
                                tempcb += `<option value="${c.id}">${c.name} ${c.last_name}</option>`
                            });
                            document.getElementById('informe_clusters').innerHTML = tempcb;
                            continue;
                        }
                        if (!domcontent) {
                            continue;
                        }
                        if (key == 'nro_manifiesto') {
                            domcontent.textContent = element;
                            continue;
                        }
                        domcontent.value = element;
                    }
                }
                $('#manifiesto-mostrar').modal('show');
            }
        }
        if (e.target.classList.contains('editar-embarque-fletes')){
            const datos = new FormData();
            datos.append('embarque', e.target.dataset.embarque);
            const response = await general.funcfetch('mostrar-editar-fletes', datos);
            general.visualizarFletes(response, 'lista-ciudades-base', 'lista-ciudades-base-aereo');
            $('#modal-editar-fletes').modal('show');
            $('#modal-editar-fletes .calc-total').on('change', function (e) {
                general.calcularBultosFletes('#modal-editar-fletes', e.target.parentElement.parentElement, $(this).attr('data-base'), $(this).attr('data-transfer'));
            });
            $('#modal-editar-fletes .sub-flete').on('change', general.calcularSubFlete);
            $('#id_tota_bult').val(response.totales.total_bultos_paquete);
            $('#id_flet_esti').val(response.totales.flet_esti);
        }
    });
    document.getElementById('embarque-editar').onclick = e => {
        const getClass = e.target.classList;
        if (getClass.contains('eliminar-manifiesto') || getClass.contains('agregar-manifiesto')) {
            if (!datosGlobales.idEmbarque) {
                alertify.error('no se encontro el manifiesto');
                return
            }
            let datos = new FormData();
            datos.append('embarque', datosGlobales.idEmbarque);
            datos.append('manifiesto', e.target.dataset.manifiesto);
            let eliminarManifiesto = getClass.contains('eliminar-manifiesto');
            if (datosGlobales.listaManifiesto === 1 && eliminarManifiesto){
                alertify.alert('Alerta','El embarque debe de tener al menos un manifiesto');
                return
            }
            alertify.confirm('Mensaje', eliminarManifiesto ? 'Al eliminar el manifiesto pasará al estado <b>GENERADA</b>.<br>¿Deseas continuar de todas formas?' : '¿Estas seguro de agregar el manifiesto al embarque?', async () => {
                const response = await general.funcfetch(eliminarManifiesto ? 'eliminar-manifiesto' : 'agregar-manifiesto', datos);
                if (response.error) {
                    alertify.alert('Error', response.error);
                }
                if (response.success) {
                    let msgSuccess = response.success;
                    const listaGuiaDetalle = document.createElement('li');
                    listaGuiaDetalle.classList.add('form-group');
                    if (eliminarManifiesto) {
                        msgSuccess += parseInt(datosGlobales.estadoManifieso) >= 2 ? '<br>Recuerda modificar los fletes y asignar las guías de transporte en caso sea necesario' : '';
                        e.target.parentElement.parentElement.parentElement.remove();
                        datosGlobales.listaManifiesto--;
                        if (!listaManifiestos.children.length) {
                            datosGlobales.listaManifiesto = 0;
                            listaManifiestos.innerHTML = `<li class="form-group">No se encontraron manifiestos</li>`;
                        }
                    } else {
                        if (!datosGlobales.listaManifiesto) {
                            listaManifiestos.innerHTML = "";
                        }
                        listaGuiaDetalle.innerHTML = `<div class="d-flex justify-content-between align-align-items-center">
                            <div><strong>N° Manifiesto ${e.target.dataset.nromanifiesto}</strong></div>
                            <div><button type="button" data-manifiesto="${e.target.dataset.manifiesto}" title="Eliminar este manifiesto del embarque" class="btn btn-sm btn-danger eliminar-manifiesto"><i class="far fa-trash-alt"></i></button></div>
                        </div>`;
                        listaManifiestos.append(listaGuiaDetalle);
                        e.target.parentElement.parentElement.parentElement.remove();
                        if (!listaManifiestoAgregar.children.length) {
                            listaManifiestoAgregar.innerHTML = `<li class="form-group">No se encontraron manifiestos</li>`;
                        }
                    }
                    alertify.alert('Mensaje', msgSuccess).set('closable', false);
                    datosGlobales.changeTabla = 1;
                }
            }, () => {});
        }
    }
    document.getElementById('frm-aplicar-filtros').onsubmit = e => {
        e.preventDefault();
        tablaEmbarques.draw();
    }
    document.getElementById('update-embarque-form').onsubmit = e => {
        e.preventDefault();

    }
    $('#embarque-editar').on('hidden.bs.modal', (e) => {
        if(datosGlobales.changeTabla === 1){
            tablaEmbarques.draw();
        }
        datosGlobales.idEmbarque = 0;
        datosGlobales.listaManifiesto = 0;
        datosGlobales.changeTabla = 0;
    });
}
window.addEventListener('DOMContentLoaded',loadPage);