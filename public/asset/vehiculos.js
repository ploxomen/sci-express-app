function loadPage(){
    let general = new General();
    const tbodyVehiculos = document.getElementById('lista-vehiculos');
    let dataTableEmpresas = $('#tabla-vehiculos').DataTable({
        ajax : {
            url: 'obtener',
            method: 'POST',
            headers: general.jsonToken,
        },
        columns: [
            {data: null,searchable: false, orderable: false,},
            {data: 'empresa'},
            {data: 'nro_contacto'},
            {data: 'correos'},
            {data: 'direccion'},
            {data: 'id', searchable: false, orderable: false, name: 'id', 
            render: function(data){
                return`<div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><button class="btn btn-sm btn-primary update-empresa" data-toggle="tooltip" title="Editar cluster" data-empresa="${data}"><i class="fas fa-user-edit"></i></button><button data-toggle="tooltip" title="Eliminar cluster" data-empresa="${data}" class="btn btn-sm btn-danger delete-empresa"><i class="fas fa-user-times"></i></button></div>`
            }}
        ],
    });
    tbodyVehiculos.on('draw.dt', function () {
        let info = tbodyVehiculos.page.info();
        tbodyVehiculos.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });
}
window.addEventListener('DOMContentLoaded',loadPage);