function loadPage(){
    let general = new General();
    const tbodyVehiculo = document.getElementById('lista-vehiculos');
    let tbodyVehiculos = $('#tabla-vehiculos').DataTable({
        ajax : {
            url: 'obtener',
            method: 'POST',
            headers: general.jsonToken,
        },
        columns: [
            {data: null,searchable: false, orderable: false,},
            {data: 'placa'},
            {data: 'year'},
            {data: 'marca'},
            {data: 'modelo'},
            {data: 'id', searchable: false, orderable: false, name: 'id', 
            render: function(data){
                return`<div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><button class="btn btn-sm btn-primary update-vehiculo" data-toggle="tooltip" title="Editar vehículo" data-vehiculo="${data}"><i class="fas fa-user-edit"></i></button><button data-toggle="tooltip" title="Eliminar vehículo" data-vehiculo="${data}" class="btn btn-sm btn-danger delete-vehiculo"><i class="fas fa-user-times"></i></button></div>`
            }}
        ],
    });
    tbodyVehiculos.on('draw.dt', function () {
        let info = tbodyVehiculos.page.info();
        tbodyVehiculos.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });
    tbodyVehiculo.addEventListener('click',async function(e){
        if(e.target.classList.contains('update-vehiculo')){
            let datos = new FormData();
            datos.append('vehiculo_id',e.target.dataset.vehiculo);
            let response = await general.funcfetch('obtener',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            document.getElementById('vehiculo_id').value = response.vehiculo.id;
            for (const key in response.vehiculo) {
                if (Object.hasOwnProperty.call(response.vehiculo, key)) {
                    const valor = response.vehiculo[key];
                    const dom = document.getElementById('id_' + key);
                    if(!dom){
                        continue;
                    }
                    dom.value = valor;
                }
            }
            $('#modal-nueva-vehiculo').modal('show');
        }
        if(e.target.classList.contains('delete-vehiculo')){
            alertify.confirm('Mensaje','¿Deseas eliminar este vehículo de transporte?',async ()=>{
                let datos = new FormData();
                datos.append('vehiculo_id',e.target.dataset.vehiculo);
                let response = await general.funcfetch('eliminar',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                alertify.success(response.success);
                tbodyVehiculos.draw();
            },()=>{})
            
        }
    });
    document.getElementById('btn-save').addEventListener('click',function(e){
        document.getElementById('btn-send-frm').click();
    });
    document.getElementById('frm_vehiculo').addEventListener('submit',async function(e){
        e.preventDefault();
        const id = document.getElementById('vehiculo_id');
        let ruta = 'nuevo';
        let datos = new FormData(this);
        if(id.value != ''){
            datos.append('id_vehiculo',id.value);
            ruta = 'actualizar';
        }
        let response = await general.funcfetch(ruta,datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        alertify.success(ruta == 'nuevo' ? 'Vehículo de transporte agregado correctamente' : 'Vehículo de transporte actualizado correctamente');
        tbodyVehiculos.draw();
        $('#modal-nueva-vehiculo').modal('hide');
    });
    $('#modal-nueva-vehiculo').on('hidden.bs.modal', function (e) {
        document.getElementById('vehiculo_id').value = "";
        document.getElementById('frm_vehiculo').reset();
    })
}
window.addEventListener('DOMContentLoaded',loadPage);