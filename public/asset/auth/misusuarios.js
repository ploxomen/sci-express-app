function loadPage(){
    let general = new General();
     let tablaUsuarios = $('#tabla-usuarios').DataTable({
        ajax : {
            url: 'obtener',
            method: 'POST',
            headers: general.jsonToken,
        },
        columns: [
            {data: 'n_document'},
            {data: 'name', render : function(data,type,full){
                return data + ' ' + full.last_name;
            }},
            {data: 'phone'},
            {data: 'email'},
            {data : 'area'},
            {data: 'status', render: function(data,type,full){
                let estado = '';
                switch (data) {
                    case 0:
                        estado = `<span class="text-danger">Eliminado</span>`;
                    break;
                    case 1:
                        estado = `<span class="text-success">Activo</span>`;
                    break;
                    case 2:
                        estado = `<span class="text-warning">Por activar</span>`;
                    break;
                     case 3:
                        estado = `<span class="text-primary">Restaurado</span>`;
                    break;
                    default:
                        estado = `<span class="text-danger">No definido</span>`;
                    break;
                }
                return estado;
            }},
            {data : 'id', searchable : false, orderable : false, render : function(){
                return `<div class='d-flex justify-content-center align-items-center flex-wrap' style="gap:5px;">
                <button class="btn btn-sm btn-primary btn-detalle" title="Ver más detalles"><i class="far fa-eye"></i></button>
                <button class="btn btn-sm btn-info btn-restore" title="Restaurar cuenta"><i class="fas fa-undo"></i></button>
                <button class="btn btn-sm btn-danger btn-delete" title="Eliminar cuenta"><i class="far fa-trash-alt"></i></button>
                </div>`
            }}
        ]
    });

}
window.addEventListener('DOMContentLoaded',loadPage);