function loadPage(){
    let general = new General();
    let bodyCluster = document.getElementById('lista-clusters');
    const frmEdidCluster = document.getElementById('edid_cluster');
    let dataTableCluster = $('#tabla-cluster').DataTable({
        ajax : {
            url: 'obtener',
            method: 'POST',
            headers: general.jsonToken,
        },
        columns: [
            {data: null,searchable: false, orderable: false,},
            {data: 'n_document'},
            {data: 'nomcompleto'},
            {data: 'ruc_clu'},
            {data: 'razon_clu'},
            {data: 'departamento_clu'},
            {data: 'provincia_clu'},
            {data: 'distrito_clu'},
            {data: 'direccion_clu' },
            {data: 'cluster_id', searchable: false, orderable: false, name: 'id', 
            render: function(data){
                return`<div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><button class="btn btn-sm btn-primary update-cluster" data-toggle="tooltip" title="Editar cluster" data-cluster="${data}"><i class="fas fa-user-edit"></i></button><button data-toggle="tooltip" title="Eliminar cluster" data-cluster="${data}" class="btn btn-sm btn-danger delete-cluster"><i class="fas fa-user-times"></i></button></div>`
            }}
        ],
    });
    dataTableCluster.on('draw.dt', function () {
        let info = dataTableCluster.page.info();
        dataTableCluster.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });
    bodyCluster.addEventListener('click',async function(e){
        if(e.target.classList.contains('update-cluster')){
            let datos = new FormData();
            datos.append('id',e.target.dataset.cluster);
            let response = await general.funcfetch('obtener',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            general.transforData(response.cluster['lista_provincia_clu'],document.querySelector('#id_provincia_clu'));
            general.transforData(response.cluster['lista_distrito_clu'],document.querySelector('#id_distrito_clu'));
            for (const key in response.cluster) {
                if (Object.hasOwnProperty.call(response.cluster, key)) {
                    const valor = response.cluster[key];
                    const dom = document.getElementById('id_'+key);
                    if(!dom){
                        continue;
                    }                    
                    dom.value = valor;
                }
            }
            $('.cb-ubigeo, .simple-select2').trigger('change');
            $('#editar-cluster').modal("show");
        }
        if(e.target.classList.contains('delete-cluster')){
            alertify.confirm('Mensaje','¿Deseas eliminar a este cluster?',async ()=>{
                let datos = new FormData();
                datos.append('id',e.target.dataset.cluster);
                let response = await general.funcfetch('eliminar',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                alertify.alert('Mensaje',response.success,()=>{dataTableCluster.draw()});
            },()=>{});
        }
    });
    frmEdidCluster.addEventListener('submit',async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            let response = await general.funcfetch('update',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            alertify.alert('Mensaje',response.success);
            $('#editar-cluster').modal("hide");
            dataTableCluster.draw();
        } catch (error) {
            alertify.alert('Error',error.message);
            console.error(error);
        }
    })
    const btnActualizar = document.getElementById('btn-actualizar-cluster');
    btnActualizar.addEventListener('click',function(e){
        document.getElementById(this.dataset.click).click();
    });

}
window.addEventListener('DOMContentLoaded',loadPage);