function loadPage(){
    let general = new General();
    for (const btnEmbarque of document.querySelectorAll('.marcar-embarque')) {
        btnEmbarque.addEventListener('click',async function(event){
            event.preventDefault();
            const id = this.dataset.embarque;
            const empresasTrasporte = document.querySelectorAll('.transpotes_select_' + id);
            let empresaTrasporteSend = new Array();
            for (const empresa of empresasTrasporte) {
                if (empresa.value == "") {
                    alertify.alert('Alerta','Por favor seleccione una empresa de transporte para el <b>EMBARQUE N° - '+ id.padStart(5,'0') +'</b> con ciudad de destino <b>' + empresa.dataset.nomciudad + '</b>');
                    return
                }
                empresaTrasporteSend.push({
                    ciudadBase: empresa.dataset.ciudad,
                    transporte : empresa.value
                });
            }
            let datos = new FormData();
            datos.append('id_embarque',id);
            datos.append('ciudades',JSON.stringify(empresaTrasporteSend));
            try {
                let response = await general.funcfetch('actualizar-estado',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                if (response.mensaje) {
                    alertify.alert('Mensaje', response.mensaje,()=>{window.location.reload()});
                    return
                }
                alertify.alert('Mensaje','El <b>EMBARQUE N° - '+ id.toString().padStart(5,'0')  +'</b> actualizado correctamente',()=>{window.location.reload()});
            } catch (error) {
                alertify.alert('Error',error.message);
                console.error(error);
            }
        });
    }
}
window.addEventListener('DOMContentLoaded',loadPage);