function loadPage(){
    let general = new General();
    let tablaSoli = document.getElementById('tabla_solicitud');
    let frmSoli = document.getElementById('frm_recojo');
    let datosGlobales = {
        id_recojo : null,
        id_solicitud : null
    };
    $('#id_clientes').select2({
        theme : 'bootstrap',
        templateResult: general.tempSeleCli,
        placeholder: 'Seleccione un cliente'
    });
    tablaSoli.addEventListener('click',async function(event) {
        if(event.target.classList.contains('btn_editar')){
            frmSoli.reset();
            datosGlobales.id_recojo = null;
            datosGlobales.id_solicitud = null;
            let datos = new FormData();
            datos.append('id_solicitud',event.target.dataset.idsolicitud);
            let response = await general.funcfetch('recojos/obtener',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            datosGlobales.id_solicitud = event.target.dataset.idsolicitud;
            if(!response.recojos){
                alertify.success('listo para programar el recojo');
                changeEstadoInput(false,'Agregar');
            }else{
                for (const key in response.recojos) {
                    if (Object.hasOwnProperty.call(response.recojos, key)) {
                        const valor = response.recojos[key];
                        const dom = document.getElementById('id_' + key);
                        if(key == 'doc_recojo' && valor != null){
                            for (const e of document.querySelectorAll(`[name="doc_recojo"]`)) {
                                if(e.value == valor){
                                    e.checked = true;
                                }
                            }
                            continue;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                changeEstadoInput(false,'Actualizar');
                datosGlobales.id_recojo = response.recojos.id;
                alertify.success('recojo listo para modificar');
            }
        }
    })
    let btnSave = document.getElementById('btn-change-estado');
    let btnCancel = document.getElementById('btn-cancelar');
    function changeEstadoInput(active,tipo) {  
        btnSave.hidden = active;
        btnCancel.hidden = active;
        btnSave.textContent = tipo;
    }
    btnCancel.addEventListener('click',function(e){
        changeEstadoInput(true,'');
    });
    frmSoli.addEventListener('submit',async function(e){
        e.preventDefault();
        let url = datosGlobales.id_recojo !== null ? 'recojos/modificar' : 'recojos/agregar';
        let datos = new FormData(this);
        datos.append('id_solicitud',datosGlobales.id_solicitud);
        if(datosGlobales.id_recojo){
            datos.append('id_recojo',datosGlobales.id_recojo);
        }
        let response = await general.funcfetch(url,datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        changeEstadoInput(true,'');
        alertify.alert('Mensaje',response.success);
        obtenerSolicitud();
        this.reset();
    })
    async function obtenerSolicitud(){
        let datos = new FormData(document.getElementById('frm_filtros_soli'));
        let response = await general.obtenerSolicitud(datos);
        tablaSoli.innerHTML =  response;
        for (const e of document.querySelectorAll('.change-estado')) {
            e.addEventListener('change',function(){
                let datos = new FormData();
                datos.append('id_solicitud',this.dataset.idSolicitud);
                datos.append('estado_soli',this.value);
                general.funcfetch('recojos/modificar-estados',datos).then(data => {
                    if(data.error){
                        alertify.alert('Error',data.error);
                        return 
                    }
                    alertify.success(data.success);
                }).catch(error => {
                    alertify.alert('Error',error.message);
                    console.error(error);
                });
            });
        }
    }
    obtenerSolicitud();
    document.getElementById('id_estado_cli').addEventListener('change',()=>{
        obtenerSolicitud();
    })
    $('#id_clientes').on('select2:select',function(event){
        obtenerSolicitud();
    });
}

window.addEventListener('DOMContentLoaded',loadPage);