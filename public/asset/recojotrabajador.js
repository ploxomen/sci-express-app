function loadPage(){
    let general = new General();
    let selectRadio = document.querySelectorAll('.select-doc');
    let cantBulto = document.getElementById('id_cant_bulto');
    let nroGuias = document.getElementById('id_nro_guias_conso');
    function datos(id){
        let newDatos = new FormData();
        newDatos.append('id_recojo',id);
        if(cantBulto){
            newDatos.append('cant_bulto',cantBulto.value);
            if(!cantBulto.value){
                return false
            }
        }
        if(nroGuias){
            newDatos.append('nro_guias_conso',nroGuias.value);
            if(!nroGuias.value){
                return false
            }
        }
        if(selectRadio){
            for (const radio of selectRadio) {
                if(radio.checked){
                    newDatos.append('doc_recojo',radio.value);
                    break;
                }
            }
        }
        return newDatos;
    }
    for (const element of document.querySelectorAll('.change-recojo')) {
        element.addEventListener('click',async function(event){
            event.preventDefault();
            const idRecojo = this.dataset.recojo;
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
                alertify.alert('Mensaje','Hora marcada correctamente N° de solicitud <b>' + idRecojo.toString().padStart(5,'0') + '</b>',()=>{window.location.reload()});
            } catch (error) {
               alertify.alert('Error',error.message); 
            }
        });
    }

}
window.addEventListener('DOMContentLoaded',loadPage);