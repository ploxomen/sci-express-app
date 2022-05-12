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
                alertify.success('seleccionado correctamente');
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
        datos.append('id_embarque',embarque);
        try {
            let response = await general.funcfetch('obtener-guias-embarque-informe',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            let template = ``;
            response.ciudades.forEach(c => {
                template += `
                    <li>
                      <div class="d-flex justify-content-between">
                        <div class="form-group">
                            <span><b>Ciudad Base:</b> ${c.nombreciudad}</span>
                        </div>
                        <div class="form group">
                            <input type="text" pattern="[0-9]{3}[-][0-9]{7}" maxlength="11" class="form-control form-control-sm informe-embarque" required placeholder="N° guía embarque">
                        </div>
                      </div>
                      <h6 class="text-primary">Guías embarcadas</h6>
                      <div class="form-row">`
                c.n_guias.forEach(g => {
                    template += `
                    <div class="form-group col-12 col-md-6 mis-guias-relacionadas" data-info="${g.id_guia}">
                        N° ${g.nro_guia}
                    </div>
                    `
                });
                template += `</div></li>`
            });
            document.getElementById('lista-guias-embarque').innerHTML = template;
            $('#insertar-guias-embarque').modal("show");
        } catch (error) {
            alertify.alert('Error',error.message);
            console.error(error);
        }
    });
    document.getElementById('frm-lista-guias-embarque').addEventListener('submit',async function(e){
        e.preventDefault();
        if(!embarque){
            return
        }
        let datos = new FormData(frmEmbarque);
        datos.append('id_embarque',embarque);
        let informe = new Array();
        for (const e of this.querySelector('#lista-guias-embarque').children) {
            for (const misGuias of e.querySelectorAll('.mis-guias-relacionadas')) {
                informe.push({
                    nro_guia_embarque : e.querySelector('.informe-embarque').value.trim(),
                    id_guia : misGuias.getAttribute('data-info')
                });
            }
            
        }
        datos.append('guias_embarque',JSON.stringify(informe));
        let response = await general.funcfetch('agregar-informe',datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        alertify.alert('Mensaje',response.success,()=>{
            $('#insertar-guias-embarque').modal("hide");
            resetForm();
            viewButtons(true);
            tablaEmbarquesPendi.draw();
        });
    });
    document.getElementById('btn-generar-informe').onclick = ()=>{
        document.getElementById('enviar-informe').click();
    }
    $('#insertar-guias-embarque').on('hidden.bs.modal', function (e) {
        document.getElementById('lista-guias-embarque').innerHTML = "";
    })
    function viewButtons(hidden){
        document.getElementById('btn_enviar_informe').hidden = hidden;
        document.getElementById('btn_clear_informe').hidden = hidden;
    }
    function resetForm(){
        frmEmbarque.reset();
        $('.simple-select2').val('').trigger("change");
    }
}
window.addEventListener('DOMContentLoaded',loadPage);