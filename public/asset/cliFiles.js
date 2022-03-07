function loadPage(){
    let general = new General();
    let estado = document.getElementById('id_estado_cliente_file');
    $('#id_documento').select2({
        theme : 'bootstrap',
        templateResult: general.tempSeleCli,
        placeholder: 'Seleccione un cliente'
    });
    getConfigFiles($('#id_documento').val());
    $('#id_documento').on('select2:select',async function(event){
        getConfigFiles($('#id_documento').val());
    })
    let checkFiles = document.querySelectorAll('.change-doc');
    for (const element of checkFiles) {
        element.addEventListener('change',async function(event){
            if($('#id_documento').val() == ''){
                return false;
            }
            let datos = new FormData();
            let checkboxes = document.querySelectorAll(`[data-doc="${element.dataset.doc}"]`);
            let cantMarcCheck = 0;
            for (const ckeckbox of checkboxes) {
                if(ckeckbox.checked){
                    cantMarcCheck++;
                }
            }
            let cambEstaDoc = checkboxes.length == cantMarcCheck ? 1 : 0;
            datos.append('nro_doc',$('#id_documento').val());
            datos.append('valor',element.checked ? 1 : 0);
            datos.append('colum',element.name);
            datos.append('colum_doc',element.dataset.doc);
            datos.append('valor_doc',cambEstaDoc);
            try {
                let response = await general.funcfetch('change-files',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return false;
                }
                let valor_doc = document.getElementById('id_'+element.dataset.doc);
                let datosHtml = {
                    campo : valor_doc.id == 'id_estado_cliente_file' ? 'textContent' : 'value',
                    valor : cambEstaDoc ? 'SI' : 'NO'
                };
                if(valor_doc.id == 'id_estado_cliente_file'){
                    estado.classList.remove('text-danger','text-success');
                    if(cambEstaDoc){
                        estado.classList.add('text-success');
                    }else{
                        estado.classList.add('text-danger');
                    }
                    datosHtml.valor = cambEstaDoc ? 'APROBADO' : 'PENDIENTE';
                }
                valor_doc[datosHtml.campo] = datosHtml.valor;
                alertify.success('estado actualizado');
            } catch (error) {
                console.error(error);
            }
        })
    }
    async function getConfigFiles(nro_doc) {  
        let datos = new FormData();
        estado.classList.remove('text-danger','text-success');
        datos.append('nro_doc',nro_doc);
        try {
            let response = await general.funcfetch('get-archivos',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            if(response.files){
                for (const key in response.files) {
                    if (Object.hasOwnProperty.call(response.files, key)) {
                        const value = response.files[key];
                        const input = document.getElementById('id_' + key);
                        if(!input){
                            continue;
                        }
                        switch (input.dataset.type) {
                            case 'checkbox':
                                input.checked = value ? true : false;
                            break;
                            case 'text':
                                if(key === 'estado_cliente_file'){
                                    estado.classList.add(value == 'APROBADO' ? 'text-success' : 'text-danger');
                                }
                                input.textContent = value;
                            break;
                            case 'value':
                                input.value = value ? 'SI' : 'NO';
                            break;
                        }
                        
                    }
                }
            }
        } catch (error) {
            console.error(error);
        }
    }
    
}
window.addEventListener('DOMContentLoaded',loadPage);