function loadPage(){
    let general = new General();
    function datos(id){
        let newDatos = new FormData();
        newDatos.append('id_recojo',id);
        const cantBulto = document.getElementById('id_cant_bulto_'+id);
        const nro_guias_conso = $('#id_nro_guias_conso_' + id);
        const una_guia = document.getElementById('id_una_guia_' + id);
        const varias_guias = document.getElementById('id_varias_guias_' + id);

        if(cantBulto){
            newDatos.append('cant_bulto',cantBulto.value);
            if(!cantBulto.value){
                return false
            }
        }
        console.log(nro_guias_conso.val());
        // return false;
        if(nro_guias_conso.length > 0){
            if(nro_guias_conso.val() == null){
                return false
            }
            newDatos.append('nro_guias_conso',nro_guias_conso.val().join(';'));
            
        }
        if(una_guia && una_guia.checked){
            newDatos.append('doc_recojo',una_guia.value);
        }
        if(varias_guias && varias_guias.checked){
            newDatos.append('doc_recojo',varias_guias.value);
        }
        if(una_guia && varias_guias && !una_guia.checked && !varias_guias.checked){
            return false;
        }
        return newDatos;
    }
    for (const element of document.querySelectorAll('.change-recojo')) {
        element.addEventListener('click',async function(event){
            event.preventDefault();
            const idRecojo = this.dataset.recojo;
            const nroSolicitud = this.dataset.solicitud;
            const ndatos = datos(idRecojo);
            if(ndatos === false){
                alertify.alert('Mensaje','Por favor asegurate que los campos de documento recojo, N° de guías consolidadas y cantida de bultos esten llenadas correctamente');
                return
            }
            try {
                let response = await general.funcfetch('actualizar-trabajador',ndatos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                alertify.alert('Mensaje', 'Hora marcada correctamente N° de solicitud <b>' + nroSolicitud + '</b>',()=>{window.location.reload()});
            } catch (error) {
               alertify.alert('Error',error.message); 
            }
        });
    }

}
window.addEventListener('DOMContentLoaded',loadPage);