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
            {data: 'format_fecha'},
            {data: 'hr_inicio'},
            {data: 'hr_estimado'},
            {data: 'hr_fin'},
            {data: 'tota_bult'},
            {data: 'kpi'},
            {data: 'flet_esti'},
            {data: 'tot_mani'},
            {data: 'id', searchable: false, orderable: false, name: 'id', render: function(data){return`<button class="btn btn-danger btn-sm obtener-detalle" title="Agregar informe del embarque" data-embarque="${data}"><i class="fas fa-edit"></i></button>`}}
        ],
    });
    listaEmbarque.addEventListener('click',async function(event){
        const dom = event.target;
        if(dom.classList.contains('obtener-detalle')){
            let datos = new FormData();
            datos.append('id_embarque',dom.dataset.embarque);
            try {
                let response = await general.funcfetch('obtener-informe-detalle',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                if(response.embarque){
                    console.log(response.embarque);
                    embarque = response.embarque.id;
                    let nroBultos = {
                        bulto_total: isNaN(parseInt(response.embarque.total_bultos_paquete)) ? 0 : parseInt(response.embarque.total_bultos_paquete),
                        embarque_nro_cajas: 0,
                        embarque_nro_jabas: 0,
                        embarque_nro_cajones: 0,
                        embarque_nro_sobres: 0,
                        embarque_nro_paquetes: 0,
                        embarque_num_otros: 0
                    };
                    let template = "";
                    let cbComprobante = null,
                        cbNroComprobantes = "";
                    
                    response.embarque.manfiestos.forEach(e => {
                        for (const key in nroBultos) {
                            if (Object.hasOwnProperty.call(nroBultos, key)) {
                                nroBultos[key] += isNaN(parseInt(e[key])) ? 0 : parseInt(e[key]);
                            }
                        }
                        cbNroComprobantes = "";
                        cbComprobante = e.comprobantes ? e.comprobantes.split(';') : null;
                        if (e.nro_comprobantes){
                            e.nro_comprobantes = e.nro_comprobantes.split(';');
                            e.nro_comprobantes.forEach(nro => {
                                cbNroComprobantes += `<option value="${nro}" selected>${nro}</option>`;
                            });
                        }
                        template += `
                        <li data-ciudad="${e.cod_ciudad}" data-base="${e.tipo_base}">
                            <div class="form-row">
                                <div class="form-group col-7 col-lg-4">
                                    <b>Ciudad Base:</b><span> ${e.nombreciudad}</span>
                                </div>
                                <div class="form-group col-5 col-lg-8">
                                    <b>Envio:</b><span> ${e.via_envio}</span>
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-5">
                                    <label for="id_comprobantes_${e.id}">Comprobantes <small class="text-danger">*</small></label>
                                    <select id="id_comprobantes_${e.id}" class="form-control simple-select2 form-control-sm" multiple required>
                                        <option value="GUIA TRANSPORTISTA" ${cbComprobante ? cbComprobante.indexOf('GUIA TRANSPORTISTA') : ''}>GUIA TRANSPORTISTA</option>
                                        <option value="FACTURA" ${cbComprobante ? cbComprobante.indexOf('FACTURA') : ''}>FACTURA</option>
                                    </select>
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-5">
                                    <label for="id_nro_comprobantes_${e.id}">N° Comprobantes <small class="text-danger">*</small></label>
                                    <select id="id_nro_comprobantes_${e.id}" required multiple class="form-control cb-select2-tags form-control-sm">${cbNroComprobantes}</select>
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-2">
                                    <label for="id_importe_factura_${e.id}">Importe Factura <small class="text-danger">*</small></label>
                                    <input type="number" value="${e.importe_factura}" required id="id_importe_factura_${e.id}" class="form-control form-control-sm" step="0.01" min="0">
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-3">
                                    <label for="empresa_trasporte_${e.id}">Empresa <small class="text-danger">*</small></label>
                                    <input type="text" class="form-control form-control-sm" id="empresa_trasporte_${e.id}" disabled value="${e.empresa_transporte}">
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-3">
                                    <label for="id_embarque_fch_emba_${e.id}">Fecha embarque <small class="text-danger">*</small></label>
                                    <input type="date" id="id_embarque_fch_emba_${e.id}" value="${response.embarque.fch_emba}" required class="form-control form-control-sm" disabled>
                                </div>
                                <div class="form-group col-6 col-lg-3">
                                    <label for="id_fecha_llegada_${e.id}">Fecha llegada <small class="text-danger">*</small></label>
                                    <input type="date" id="id_fecha_llegada_${e.id}" value="${e.fecha_llegada}" class="form-control form-control-sm" required>
                                </div>
                                <div class="form-group col-6 col-lg-3">
                                    <label for="id_hr_llegada_${e.id}">Hr llegada <small class="text-danger">*</small></label>
                                    <input type="time" id="id_hr_llegada_${e.id}" value="${e.hr_llegada}" class="form-control form-control-sm" required>
                                </div>
                                `
                                if (e.guiasEmbarque){
                                    template += `
                                    <div class="col-12 form-group border-top pt-2"></div>
                                    <div class="col-12 form-group col-md-6 col-lg-8">
                                        <h6 class="text-info mb-0">Guía de embarque - ${e.nombreciudad}</h6>
                                    </div>
                                    <div class="col-12 col-md-6 form-group col-lg-4">
                                        <input type="text" pattern="[0-9]{3}[-][0-9]{7}" required value="${e.guiasEmbarque[0].nro_guia_embarque ? e.guiasEmbarque[0].nro_guia_embarque : ''}" maxlength="11" class="form-control form-control-sm informe-embarque" required placeholder="N° guía embarque">
                                    </div>
                                    <div class="form-group col-12">
                                    `
                                    e.guiasEmbarque.forEach(g => {
                                        template += `
                                            <span class="p-2" data-guia="${g.id}">N° ${g.nro_guias}</span>
                                        `
                                    });
                                    template += `</div>`;
                                }
                                template += "</div></li>";
                    });
                    for (const key in nroBultos) {
                        if (Object.hasOwnProperty.call(nroBultos, key)) {
                            if (key == 'bulto_total'){
                                document.getElementById('informe_' + key).textContent = nroBultos[key];
                                continue;
                            }
                            document.getElementById('informe_' + key).value = nroBultos[key];
                        }
                    }
                    document.getElementById('informes-embarques').innerHTML = template;
                    $('#modal-informe-embarque').modal('show');
                    $('.simple-select2').select2({
                        theme: 'bootstrap',
                        width: '100%',
                        placeholder: 'Seleccione una opción'
                    });
                    $('.cb-select2-tags').select2({
                        theme: 'bootstrap',
                        width: '100%',
                        placeholder : 'Seleccione o agregue una opción',
                        tags: true
                    });
                }
                // embarque = dom.dataset.embarque;
                // if(response.embarque){
                //     for (const key in response.embarque) {
                //         if (Object.hasOwnProperty.call(response.embarque, key)) {
                //             const value = response.embarque[key];
                //             const dom = document.getElementById('id_embarque_'+key);
                //             if(!dom){
                //                 continue;
                //             }
                //             dom.value = value;
                //         }
                //     }
                // }
            } catch (error) {
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
    // document.getElementById('frm-lista-guias-embarque').addEventListener('submit',async function(e){
    //     e.preventDefault();
    //     if(!embarque){
    //         return
    //     }
    //     let datos = new FormData(frmEmbarque);
    //     datos.append('id_embarque',embarque);
    //     let informe = new Array();
    //     for (const e of this.querySelector('#lista-guias-embarque').children) {
    //         for (const misGuias of e.querySelectorAll('.mis-guias-relacionadas')) {
    //             informe.push({
    //                 nro_guia_embarque : e.querySelector('.informe-embarque').value.trim(),
    //                 id_guia : misGuias.getAttribute('data-info')
    //             });
    //         }
            
    //     }
    //     datos.append('guias_embarque',JSON.stringify(informe));
    //     let response = await general.funcfetch('agregar-informe',datos);
    //     if(response.error){
    //         alertify.alert('Error',response.error);
    //         return
    //     }
    //     alertify.alert('Mensaje',response.success,()=>{
    //         $('#insertar-guias-embarque').modal("hide");
    //         resetForm();
    //         viewButtons(true);
    //         tablaEmbarquesPendi.draw();
    //     });
    // });
    // document.getElementById('btn-generar-informe').onclick = ()=>{
    //     document.getElementById('enviar-informe').click();
    // }
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