function loadPage(){
    let general = new General();
    let listaEmbarque = document.getElementById('listas-embarque');
    let frmEmbarque = document.getElementById('form_embarque_informe');
    let embarque = null;
    let tablaEmbarquesPendi = $('#embarques-pendientes').DataTable({
        ajax : {
            url: 'obtener-informe',
            method: 'POST',
            headers: general.jsonToken
        },
        columns: [
            {data: 'nro_embarque'},
            {data: 'empresa'},
            {data: 'format_fecha'},
            {data: 'hr_inicio'},
            {data: 'hr_estimado'},
            {data: 'hr_fin'},
            {data: 'tota_bult'},
            {data: 'kpi'},
            {data: 'flet_esti'},
            {data: 'tot_mani'},
            {data: 'id', searchable: false, orderable: false, name: 'id', render: function(data){return`<button class="btn btn-danger obtener-detalle" title="Agregar informe del embarque" data-embarque="${data}"><i class="fas fa-edit"></i></button>`}}
        ],
    });
    listaEmbarque.addEventListener('click',async function(event){
        const dom = event.target;
        if(dom.classList.contains('obtener-detalle')){
            embarque = null;
            resetForm();
            viewButtons(false);
            let datos = new FormData();
            datos.append('id_embarque',dom.dataset.embarque);
            try {
                let response = await general.funcfetch('obtener-informe-detalle',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                embarque = dom.dataset.embarque;
                if(response.embarque){
                    for (const key in response.embarque) {
                        if (Object.hasOwnProperty.call(response.embarque, key)) {
                            const value = response.embarque[key];
                            const dom = document.getElementById('id_embarque_'+key);
                            if(!dom){
                                continue;
                            }
                            dom.value = value;
                        }
                    }
                }
            } catch (error) {
                embarque = null;
                alertify.alert('Error',error.message);
                console.error(error);
            }
        }
    });
    frmEmbarque.addEventListener('reset',function(){
        resetForm();
        viewButtons(true);
    });
    frmEmbarque.addEventListener('submit',async function(event){
        event.preventDefault();
        if(!embarque){
            return
        }
        let datos = new FormData(this);
        if(totalBultos() === 0){
            alertify.error('el total de bultos debe de ser mayor que 0');
            return
        }
        datos.append('total_bultos_paquete',totalBultos());
        datos.append('id_embarque',embarque);
        try {
            let response = await general.funcfetch('agregar-informe',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            alertify.alert('Mensaje',response.success,()=>{
                resetForm();
                viewButtons(true);
                tablaEmbarquesPendi.draw();
            });
        } catch (error) {
            alertify.alert('Error',error.message);
            console.error(error);
        }
    })
    function viewButtons(hidden){
        document.getElementById('btn_enviar_informe').hidden = hidden;
        document.getElementById('btn_clear_informe').hidden = hidden;
    }
    function resetForm(){
        frmEmbarque.reset();
        $('.simple-select2').val('').trigger("change");
    }
    for (const e of document.querySelectorAll('.calc-total')) {
        e.addEventListener('change',function(e){
            document.getElementById('total_bultos').value = general.totalBultos('.calc-total');
        });
    }
}
window.addEventListener('DOMContentLoaded',loadPage);