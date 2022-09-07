function loadPage(){
    let general = new General();
    let idFlete = null;
    let dataFletes = $('#tabla-fletes').DataTable({
        ajax : {
            url: 'ver',
            method: 'POST',
            headers: general.jsonToken
        },
        columns: [
            {data: null,searchable: false, orderable: false},
            {data: 'ciudad_base'},
            {data: 'sobres',render : formateoMoneda},
            {data: 'cajas',render: formateoMoneda},
            {data: 'cajones',render: formateoMoneda},
            {data: 'paquetes',render: formateoMoneda},
            {data: 'jabas',render: formateoMoneda},
            {data: 'parihuelas',render: formateoMoneda},
            {data: 'id', searchable: false, orderable: false, name: 'id', render: function(data,type,row){return`<div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><button data-flete="${data}" class="btn btn-sm btn-primary edidt-flete"><i class="fas fa-pen"></i></button><button data-flete="${data}" class="btn btn-sm btn-danger delete-flete" data-ciudad="${row.ciudad_base}"><i class="fas fa-trash-alt"></i></button></div>`}}
        ],
    });
    function formateoMoneda(valor){
        return (parseFloat(valor)).toLocaleString('es-PE',{
            style: 'currency',
            currency: 'PEN'
        });
    }
    dataFletes.on('draw.dt', function () {
        let info = dataFletes.page.info();
        dataFletes.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });
    document.getElementById('tabla-fletes').onclick = async function(e){
        if(e.target.classList.contains('delete-flete')){
            alertify.confirm('Alerta','¿Estás seguro de eliminar el flete de la ciudad base <b>' + e.target.dataset.ciudad + '</b>?',async ()=>{
                let datos = new FormData()
                datos.append('flete',e.target.dataset.flete);
                try {
                    let response = await general.funcfetch('eliminar',datos);
                    alertify.success(response.success);
                    dataFletes.draw();
                } catch (error) {
                    alertify.error('Error al eliminar el flete');
                    console.error(error);
                }
            },()=>{})
        }
        if (e.target.classList.contains('edidt-flete')){
            let datos = new FormData()
            datos.append('flete', e.target.dataset.flete);
            try {
                let response = await general.funcfetch('ver', datos);
                for (const key in response.flete) {
                    if (Object.hasOwnProperty.call(response.flete, key)) {
                        const valor = response.flete[key];
                        const dom = document.getElementById('id_text_' + key);
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                document.getElementById('titulo-modal-flete').textContent = 'Modificar Flete';
                document.getElementById('agregar-fletes').textContent = 'Actualizar';
                idFlete = e.target.dataset.flete;
                $('#modal-detalle-misfletes').modal('show');
            } catch (error) {
                alertify.error('Error al obtener el flete');
                console.error(error);
            }
        }
    }
    document.getElementById('agregar-fletes').onclick = function(e){
        document.getElementById('send-fletes').click();
    }
    document.getElementById('modificar-fletes').onsubmit = async function(e){
        e.preventDefault();
        let url = 'agregar';
        let datos = new FormData(this);
        if(idFlete){
            url = 'actualizar';
            datos.append('flete', idFlete);
        }
        let response = await general.funcfetch(url, datos);
        if(response.alerta){
            alertify.alert('Alerta',response.alerta);
            return
        }
        this.reset();
        alertify.success(response.success);
        dataFletes.draw();
        $('#modal-detalle-misfletes').modal('hide');
    }
    $('#modal-detalle-misfletes').on('hidden.bs.modal',function(e){
        document.getElementById('titulo-modal-flete').textContent = 'Agregar Flete';
        document.getElementById('agregar-fletes').textContent = 'Agregar';
        idFlete = null;
        document.getElementById('modificar-fletes').reset();
    });



}
window.addEventListener('DOMContentLoaded',loadPage);