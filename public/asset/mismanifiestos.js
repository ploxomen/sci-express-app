function loadPage(){
    let datosGlobales = {
        idManifiesto: 0,
        estadoManifieso: 0,
        listaGuiasManifiesto : 0,
        listaGuias : 0
    };
    const cbTrayecto = document.getElementById('cb_trayecto');
    const listaGuias = document.getElementById('lista-guias'),
        listaGuiasAgregar = document.getElementById('lista-guias-pendientes');
    let general = new General();
    let tablaMisManifiestos = $('#tabla-manifiestos').DataTable({
        ajax: {
            url: 'obtener-todos',
            method: 'POST',
            headers: general.jsonToken,
            data: function (d) {
                d.trayecto = $('#cb-trayectos').val();
                d.provOrigen = $('#cb-provincia-origen').val();
                d.provFinal = $('#cb-provincia-final').val();
                d.cluster = $('#cb-cluster').val();
                d.estado = $('#cb-estado').val();
                d.fechaInicio = $('#fecha_inicio').val();
                d.fechaFin = $('#id_fecha_fin').val();
            }
        },
        columns : [
            {data : 'nro_manifiesto'},
            {data : 'fecha_manifiesto'},
            {data : 'tipo'},
            {data : 'via_envio'},
            {data : 'nro_guias_total', visible : false},
            {data : 'nom_prov_orig'},
            {data : 'nom_prov_dest'},
            {data : 'nombre_cluster'},
            {data : 'situacion'},
            {data : 'id_responsable',render:function(data,type,row){
                let template = `<select class="asignar-embarque" data-responsable-embarque="${data}" ${row.num_estado == 6 ? 'disabled' : ''}>`
                if (row.listUsu.length > 0){
                    template += `<option></option>`;
                    row.listUsu.forEach(u => {
                        template += `<option value="${u.id}" ${u.id == data ? 'selected' : ''}>${u.name} ${u.last_name}</option>`;
                    });
                }
                template += "</select>";
                return template;
            }},
            {
                data: 'id',
                render: function (data, type, row) {
                let template = "";
                row.guias_asociadas?.forEach((v,k) => {
                    template += `<small class="badge badge-pill badge-dark m-1 cursor-pointer view-guia" data-guia="${v.id}">${v.nro}</small>`;
                });
                return template;
            }},
            {
                data: "id",
                render : function(data,type,row){
                    return `<button class="btn btn-sm btn-primary mr-2 editar-manifiesto" data-estado="${row.num_estado}" data-cluster="${row.id_cluster}" data-manifiesto="${data}" title="Editar manifiesto"><i class="fas fa-edit"></i></button><button class="btn eliminar-manifiesto btn-sm btn-danger" data-manifiesto="${data}" data-estado="${row.num_estado}" ${row.num_estado >= 2 ? 'disabled' : ''} title="Eliminar manifiesto"><i class="far fa-trash-alt"></i></button>`
                }
            }
        ],
        order: [
            0, 'desc'
        ]
    });
    tablaMisManifiestos.on('draw.dt', function () {
        $('.asignar-embarque').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        }).on('select2:select', async function (event) {
            alertify.success('responsable cambiado correctamente');
        });
    });
    $('.filtro').on('select2:select',function(){
        tablaMisManifiestos.draw();
    });
    document.getElementById('filtro-manifiesto').onsubmit = e =>{
        e.preventDefault();
        tablaMisManifiestos.draw();
    };
    document.getElementById('tabla-manifiestos').addEventListener('click',async function(e){
        if (e.target.classList.contains('eliminar-manifiesto')){
            alertify.confirm('Alerta','¿Estás seguro de eliminar este manifiesto?<br>Recuerda que al aceptar, las guías asociadas cambiaran de estado a <strong>POR MANIFESTAR</strong>, también se eliminará del embarque en el cual se encuntra asociado',async ()=>{
                const datos = new FormData();
                datos.append('manifiesto',e.target.dataset.manifiesto);
                const response = await general.funcfetch('eliminar', datos);
                if(response.success) {
                    const msjDelete = parseInt(e.target.dataset.estado) >= 2 ? '<br>No olvidar que se debe modificar el <b>EMBARQUE</b> relacionado al manifiesto eliminado' : '';
                    alertify.alert('Mensaje', response.success + msjDelete);
                    tablaMisManifiestos.draw();
                    return
                }
                alertify.alert('Error', response.error);
            },()=>{});
        }
        if (e.target.classList.contains('editar-manifiesto')) {
            datosGlobales.estadoManifieso = e.target.dataset.estado;
            datosGlobales.idManifiesto = e.target.dataset.manifiesto;
            let datos = new FormData();
            datos.append('manifiesto', datosGlobales.idManifiesto);
            datos.append('cluster', e.target.dataset.cluster);
            let response = await general.funcfetch('editar-obtener', datos);
            document.getElementById('informe_clusters').innerHTML = "";
            listaGuias.innerHTML = "";
            if(response.success[0]){
                for (const key in response.success[0]) {
                    if (Object.hasOwnProperty.call(response.success[0], key)) {
                        const element = response.success[0][key];
                        const domcontent = document.getElementById('informe_' + key);
                        if (key == 'guias_asociadas'){
                            listaGuias.innerHTML = "";
                            let template = "";
                            element.forEach(g => {
                                template += `<li class="form-group">
                                <div class="d-flex justify-content-between align-align-items-center">
                                    <div><strong>N° Guía ${g.nro}</strong></div>
                                    <div><button type="button" data-guia="${g.id}" title="Eliminar esta guía del manifiesto" class="btn btn-sm btn-danger eliminar-guia"><i class="far fa-trash-alt"></i></button></div>
                                </div></li>`;
                            });
                            listaGuias.innerHTML = template;
                            datosGlobales.listaGuiasManifiesto = element.length;
                            if(!element.length){
                                cbTrayecto.disabled = false;
                            }
                        }
                        if (key == 'listCluster') {
                            let tempcb = "";
                            element.forEach(c => {
                                tempcb += `<option value="${c.id}">${c.name} ${c.last_name}</option>`
                            });
                            document.getElementById('informe_clusters').innerHTML = tempcb;
                            continue;
                        }
                        if(!domcontent){
                            continue;
                        }
                        if(key == 'nro_manifiesto'){
                            domcontent.textContent = element;
                            continue;
                        }
                        domcontent.value = element;
                    }
                }
                if (response.success[0].existEmbarque){
                    for (const opt of document.querySelectorAll("#informe_num_estado option")) {
                        if (+opt.value === 1){
                            opt.setAttribute("disabled","disabled");
                        }
                        if (+opt.value > 1){
                            opt.removeAttribute("disabled");
                        }
                    }
                }
                $('#informe_num_estado').trigger("change");
                $('#manifiesto-editar').modal('show');
            }
            let templateGuia = "";
            response.guias.forEach(g => {
                const nro_guia = g.prefijo ? g.prefijo.toString().padStart(3, '0') + '-' + g.nro_guia.toString().padStart(7, '0') : g.nro_guia.toString().padStart(7, '0');
                templateGuia += `<li class="form-group">
                <div class="d-flex justify-content-between align-align-items-center">
                    <div><strong>N° Guía ${nro_guia}</strong></div>
                    <div><button type="button" data-guia="${g.id}" data-nroguia="${nro_guia}" title="Agregar guía a este manifiesto" class="btn btn-sm btn-success agregar-guia"><i class="fas fa-plus"></i></button></div>
                </div></li>`;
            });
            listaGuiasAgregar.innerHTML = templateGuia ? templateGuia : '<li class="text-center">No se encontraron guías</li>';
        }
        if(e.target.classList.contains('view-guia')) {
            let datos = new FormData();
            datos.append('guia', e.target.dataset.guia);
            datos.append('tipo', 'detalle');
            let response = await general.funcfetch('./../comprobante/guias/obtener-detalles', datos);
            if (response.error) {
                alertify.alert('Error', response.error);
                return
            }
            general.verDetalleGuia(response.datos);
        }
    });
    for (const reporte of document.querySelectorAll(".reporte-general")) {
        reporte.addEventListener("click", function (e) {
            window.open(window.origin + "/intranet/manifiesto/reporte/" + this.dataset.tipo + "/" + $('#fecha_inicio').val() + "/" + $('#id_fecha_fin').val(), "_blank");
        });
    }
    document.getElementById('manifiesto-editar').onclick = e => {
        const getClass = e.target.classList;
        if (getClass.contains('eliminar-guia') || getClass.contains('agregar-guia')) {
            if(!datosGlobales.idManifiesto){
                alertify.error('no se encontro el manifiesto');
                return
            }
            const eliminarGuia = getClass.contains('eliminar-guia');
            if (datosGlobales.listaGuias === 1 && eliminarGuia) {
                alertify.alert('Alerta', 'El manifiesto debe de tener al menos una guía');
                return
            }
            let datos = new FormData();
            datos.append('manifiesto', datosGlobales.idManifiesto);
            datos.append('guia',e.target.dataset.guia);
            alertify.confirm('Mensaje', eliminarGuia ? 'Al eliminar la guía pasará al estado <b>POR MANIFESTAR</b>.<br>¿Deseas continuar de todas formas?' : '¿Estas seguro de agregar la guía al manifiesto?', async () => {
                const response = await general.funcfetch(eliminarGuia ? 'eliminar-guia' : 'agregar-guia-manual', datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                }
                if(response.success){
                    
                    let msgSuccess = response.success;
                    const listaGuiaDetalle = document.createElement('li');
                    listaGuiaDetalle.classList.add('form-group');
                    if (eliminarGuia){
                        msgSuccess += datosGlobales.estadoManifieso >= 2 ? '<br>Recuerda modificar el embarque correspondiente a este manifiesto' : '';
                        datosGlobales.listaGuias--;
                        e.target.parentElement.parentElement.parentElement.remove();
                        if (!listaGuias.children.length) {
                            datosGlobales.listaGuiasManifiesto = 0;
                            listaGuias.innerHTML = `<li class="form-group">No se encontraron guías</li>`;
                        }
                    }else{
                        if (!datosGlobales.listaGuiasManifiesto){
                            listaGuias.innerHTML = "";
                        }
                        listaGuiaDetalle.innerHTML = `<div class="d-flex justify-content-between align-align-items-center">
                            <div><strong>N° Guía ${e.target.dataset.nroguia}</strong></div>
                            <div><button type="button" data-guia="${e.target.dataset.guia}" title="Eliminar esta guía del manifiesto" class="btn btn-sm btn-danger"><i class="far fa-trash-alt"></i></button></div>
                        </div>`;
                        listaGuias.append(listaGuiaDetalle);
                        e.target.parentElement.parentElement.parentElement.remove();
                        if(!listaGuiasAgregar.children.length){
                            listaGuiasAgregar.innerHTML = `<li class="form-group">No se encontraron guías</li>`;
                        }
                    }
                    alertify.alert('Mensaje', msgSuccess).set('closable', false);
                }
            },()=>{});
        }
    }
    $('#manifiesto-editar').on('hidden.bs.modal',(e)=>{
        datosGlobales.estadoManifieso = 0;
        datosGlobales.idManifiesto = 0;
        datosGlobales.listaGuiasManifiesto = 0;
        datosGlobales.listaGuias = 0;
        cbTrayecto.disabled = true;
        for (const opt of document.querySelectorAll("#informe_num_estado option")) {
            if (+opt.value === 1) {
                opt.removeAttribute("disabled");
            }
            if (+opt.value > 1) {
                opt.setAttribute("disabled","disabled");
            }
        }
    });
    document.getElementById('update-manifiesto-form').onsubmit = function(e){
        e.preventDefault();
        let datos = new FormData(this);
        let msgChangeEstado = "";
        if (+datos.get('num_estado') != datosGlobales.estadoManifieso){
            msgChangeEstado = "Se a detectado el cambio de estado en este manifiesto, al aceptar se actualizaran tambien los estados de aquellas <b>GUIAS/OS</b> que aún no están en <b>REPARTO</b><br>.";
        }else{
            datos.delete('num_estado');
        }
        datos.append('manifiesto', datosGlobales.idManifiesto);
        alertify.confirm('Mensaje', msgChangeEstado + '¿Estás seguro de actualizar los datos este manifiesto?', async () => {
            const response = await general.funcfetch('actualizar', datos);
            if(response.success){
                alertify.success(response.success);
                tablaMisManifiestos.draw();
                $('#manifiesto-editar').modal('hide');
                return
            }
            alertify.alert('Error',response.error);
        },()=>{});
    }
    document.getElementById('actualizar-manifiesto').onclick = e=>{
        document.getElementById('update-manifiesto').click();
    }
}
window.addEventListener('DOMContentLoaded', loadPage);
