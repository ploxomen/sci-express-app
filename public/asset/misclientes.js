function loadedPage(){
    let general = new General();
    let tableCli = document.getElementById('table-cliente');
    let datatableCli = $('#tabla-clientes').DataTable({
        ajax : {
            url: 'obtener?tipo=info',
            method: 'POST',
            headers: general.jsonToken,
            data : function(d){d.estadoDoc = $('#id_estado_doc_cli').val()}
        },
        columns: [
            {data: null},
            {data: 'nro_doc', name: 'nro_doc'},
            {data: 'nom_cli', name: 'nom_cli'},
            {data: 'dir_sede_adr', name: 'dir_sede_adr'},
            {data: 'nom_rl', name: 'nom_rl'},
            {data: 'cel_rl', name: 'cel_rl'},
            {data: 'email_rl', name: 'email_rl'},
            {data: 'estado_doc', name: 'estado_doc', render : function(data){return `<span class="${data == 0 ? 'text-danger' : 'text-success'}">${data == 0 ? 'PENDIENTE' : 'COMPLETO'}</span>` }},
            {data: 'estado', name: 'estado', render : function(data,type,full,meta){return `<span class="${data == 0 ? 'text-danger' : 'text-success'}">${data == 0 ? 'INACTIVO' : 'ACTIVO'}</span>` }},
            {data: 'id', searchable: false, orderable: false, name: 'id', render: function(data){return`<div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><a href="agregar-modificar?nro_doc=${data}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Editar cliente"><i class="fas fa-user-edit"></i></a><button data-toggle="tooltip" title="Eliminar cliente" data-cliente="${data}" class="btn btn-sm btn-danger delete-cliente"><i class="fas fa-user-times"></i></button></div>`}}
        ],
    });
    tableCli.addEventListener('click',function(event){
        if(event.target.classList.contains('delete-cliente')){
            let nameCli = event.target.parentElement.parentElement.parentElement.children[1].textContent;

            alertify.confirm('Mensaje','¿Deseas eliminar al cliente <b>' + nameCli + '</b>?',async ()=>{
                try {
                    let datos = new FormData();
                    datos.append('id_cliente',event.target.dataset.cliente);
                    let response = await general.funcfetch('eliminar',datos);
                    if(response.success){
                        datatableCli.draw();
                        alertify.success(response.success);
                        return
                    }
                    alertify.alert('Error',response.error);
                } catch (error) {
                    console.log(error);
                }
            },()=>{});
            
        }
    });
    datatableCli.on('draw.dt', function () {
        let info = datatableCli.page.info();
        datatableCli.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });
    $('.cb-filter').change(function(){
        datatableCli.draw();
    });
}
window.addEventListener('DOMContentLoaded',loadedPage);