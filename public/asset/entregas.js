function loadPage(){
    const general = new General();
    const cbCluster = document.getElementById('nombre_cluster');
    let tablaEntrega = $('#tabla-entrega').DataTable({
        ajax : {
            url: window.location.origin + '/intranet/pedidos/entrega/lista',
            method: 'POST',
            headers: general.jsonToken,
            data: function(d){
                d.tipo = 'consulta';
                d.fecha_confirmacion = $('#fecha_confirmacion').val();
                d.fecha_reparto = $('#fecha_reparto').val();
                d.estado_guia = $('#estado_guia').val();
                if(cbCluster){
                    d.nombre_cluster = $('#nombre_cluster').val();
                }else{
                    d.ciudad_final = $('#cluster_destino_final').val();
                    d.tipo_ciudad = $('#cluster_destino_final>option:selected').attr('data-tipo');
                }
            }
        },
        columns: [
            {data: 'nro_guia'},
            {data: 'orde_comp'},
            {data: 'fecha_venc_compra'},
            {data: 'remi_nomb'},
            {data: 'dest_nomb'},
            {data: 'nom_estado'},
            {data: 'tot_cant'},
            {data: 'id',render:function(data,type,row){
                return `<button data-guia="${data}" data-cluster="${row.id_cluster}" class="btn btn-sm btn-danger detalle-entregas" title="Editar entrega"><i class="fas fa-edit"></i></button>`
            }}
        ],
        createdRow : function(row,data){
            let bgTable = 'table-success';
            if(data.situ == 7){
                bgTable = 'table-warning';
            }
            if(data.situ == 14){
                bgTable = 'table-danger';
            }
            $(row).addClass(bgTable);
        }
    });
    document.getElementById('cuerpo-entregas').addEventListener('click',async function(e){
        if(e.target.classList.contains('detalle-entregas')){
            let datos = new FormData();
            datos.append('cluster',e.target.dataset.cluster);
            datos.append('guia',e.target.dataset.guia);
            datos.append('tipo','consulta-detalle');
            let response = await general.funcfetch(window.location.origin + '/intranet/pedidos/entrega/lista',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            for (const key in response.success) {
                if (Object.hasOwnProperty.call(response.success, key)) {
                    const element = response.success[key];
                    const dom = document.getElementById('id_modal_' + key);
                    if(!dom){
                        continue;
                    }
                    $(dom).val(element == null ? "" : element);
                }
            }
            $('#modal-detalle-entregas .simple-select2').trigger("change");
            $('#modal-detalle-entregas').modal("show");
        }
    });
    if(cbCluster){
        $('#nombre_cluster').on('select2:select',async function(e){
            const domkpis = document.getElementById('kpis_clusters');
            if($(this).val() == '0'){
                domkpis.innerHTML = `<div class="form-group col-12 text-center"><span class="text-danger">Por favor seleccione a un cluster</span></div>`;
                return
            }
            let datos = new FormData();
            datos.append('id_cluster',$(this).val());
            datos.append('tipo','kpi');
            let response = await general.funcfetch(window.location.origin + '/intranet/pedidos/entrega/lista',datos);
            let template = "";
            for (const key in response.success) {
                if (Object.hasOwnProperty.call(response.success, key)) {
                    const element = response.success[key];
                    template += `<div class="form-group col-12 col-md-6 col-lg-3"><div class="p-1 border"><strong>${key} :</strong><div class="d-flex gap-10">`;
                    let color =  key == 'Incidencias' ? 'text-danger' : 'text-success';
                    if(element.porcentaje < 25 && key != 'Incidencias'){
                        color = 'text-danger';
                    }
                    if(element.porcentaje >= 25 && element.porcentaje < 50 && key != 'Incidencias'){
                        color = 'text-warning';
                    }
                    if(element.porcentaje >= 50 && element.porcentaje < 75 && key != 'Incidencias'){
                        color = 'text-info';
                    }
                    template += `
                    <div class="w-50 p-1 text-center">
                        <span class="${color}">${element.completadas}</span>
                        <span>/</span>
                        <span class="${key == 'Incidencias' ? 'text-danger' : 'text-success'}">${element.total}</span>
                    </div>
                    <div class="w-50 p-1 text-center">
                        <span class="{${color}}">${element.porcentaje}%</span>
                        <span>/</span>
                        <span class="${key == 'Incidencias' ? 'text-danger' : 'text-success'}">${element.total == 0 ? '0' : '100'}%</span>
                    </div></div></div></div>
                    `
                }
            }
            domkpis.innerHTML = template;
        });
    }
    for (const element of document.querySelectorAll('.change-entrega-fecha')) {
        element.addEventListener('change',function(e){
            tablaEntrega.draw();
        });
    }
    $('.cb-filtros').on('select2:select',function(e){
        tablaEntrega.draw();
    });
    document.getElementById('frm-update-reparto').addEventListener('submit',async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        let response = await general.funcfetch(window.location.origin + '/intranet/pedidos/entrega/actualizar',datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        alertify.success(response.success);
        $('#modal-detalle-entregas').modal("hide");
        tablaEntrega.draw();
    })
    document.getElementById('modificar-detalle-entrega').addEventListener('click',function(e){
        document.getElementById('modificar-entregas-sectorista').click();
    });
}
window.addEventListener('DOMContentLoaded',loadPage);