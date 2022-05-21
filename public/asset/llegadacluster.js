function loadPage(){
    //MIS GUIAS
    let general = new General();
    const tablaGuiaClus = document.getElementById('tabla-guias-cluster');
    let dataGuias = $('#tabla-asigancion-guias').DataTable({
        ajax : {
            url: 'llegada/mis-guias',
            method: 'POST',
            headers: general.jsonToken,
            data : function(d){d.estado = $('#estado_guias').val()}
        },
        columns: [
            {data: 'nro_guia'},
            {data: 'fecha_emi_guia'},
            {data: 'nro_manifiesto'},
            {data: 'fecha_emi_mani'},
            {data: 'prov_orig'},
            {data: 'prov_dest'},
            {data: 'guia_tipo'},
            {data: 'situ',render : function(data,type,full){
                if(full.guia_tipo == 'ANULADA' || full.guia_tipo=='INACTIVA'){
                    return `<span class='text-danger'>Sin acciones</span>`;
                }
                let tempEstado = '';
                full.lista_estados.forEach(estado => {
                    tempEstado += `<option value="${estado.id}" ${estado.id == data ? 'selected' : ''}>${estado.tipo}</option>`;
                });
                return `<select class="form-control form-control-sm change-estado" data-guia="${full.id}">${tempEstado}</select>`;
            }},
            {data: 'id', searchable: false, orderable: false,render: function(data){
                return `<button class="btn btn-primary btn-sm ver-detalles-cluster-guia" title="Ver más detalles de la guia" data-guia="${data}"><i class="fas fa-eye"></i></button>`
            }}
        ],
    });
    for (const filtro of document.querySelectorAll('.cb-filtros-guias')) {
        filtro.addEventListener('change',function(e){
            dataGuias.draw();
        });
    }
    tablaGuiaClus.addEventListener('click',async function(event){
        if(event.target.classList.contains('ver-detalles-cluster-guia')){
            let datos = new FormData();
            datos.append('id_guia',event.target.dataset.guia);
            let response = await general.funcfetch('llegada/guia-detalle-cluster',datos);
            verDetalleGuiaCluster(response);
        }
    });
    dataGuias.on('draw.dt', function () {
        for (const cbos of document.querySelectorAll('.change-estado')) {
            cbos.addEventListener('change',async function(event){
                let datos = new FormData();
                datos.append('id_guia',this.dataset.guia);
                datos.append('estado',this.value);
                let response = await general.funcfetch('llegada/cambiar-estado-guias',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return 
                }
                alertify.success(response.success);
            });
        }
    });
    function verDetalleGuiaCluster(data) {
        if(!data.resultados[0]){
            return
        }
        for (const key in data.resultados[0]) {
            if (Object.hasOwnProperty.call(data.resultados[0], key)) {
                const value = data.resultados[0][key];
                const dom = document.getElementById('id_modal2_' + key);
                if(!dom){
                    continue;
                }
                dom.textContent = value;
            }
        }
        let template = '';
        if(data.resultados[0].detalles_guias){
            data.resultados[0].detalles_guias.forEach(dg => {
                template += `
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-6 col-lg-3"><b class="d-block text-secondary">Grupo Envio</b><span>${dg.grupo_envio}</span></div>
                            <div class="col-6 col-lg-3"><b class="d-block text-secondary">Tipo Envio</b><span>${dg.tipo_envio}</span></div>
                            <div class="col-6 col-lg-3"><b class="d-block text-secondary">Embase o Embalaje</b><span>${dg.embase_embalaje}</span></div>
                            <div class="col-6 col-lg-3"><b class="d-block text-secondary">Cantidad</b><span>${dg.cantidad}</span></div>
                            <div class="col-12"><b class="d-block text-secondary">Descripción</b><span>${!dg.descripcion ? 'No definido' : dg.descripcion}</span></div>
                        </div>
                    </div>
                </div>`
            });
            document.getElementById('id_modal2_detalle_guias').innerHTML = template;
        }
        $('#detalle-guias-llegada-cluster').modal('show');
    }
}
window.addEventListener('DOMContentLoaded',loadPage);