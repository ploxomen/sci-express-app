function loadPage(){
    let general = new General();
    for (const btnEmbarque of document.querySelectorAll('.marcar-embarque')) {
        btnEmbarque.addEventListener('click',async function(event){
            event.preventDefault();
            const id = this.dataset.embarque;
            const empresa = document.getElementById('transpotes_select_' + id);
            let datos = new FormData();
            datos.append('id_embarque',id);
            if(empresa){
                if(empresa.value == ''){
                    alertify.error('por favor selecione la empresa de transporte');
                    return
                }
                datos.append('empresa',empresa.value);
            }
            try {
                let response = await general.funcfetch('actualizar-estado',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                alertify.alert('Mensaje','Embarque N° '+ id.toString().padStart(5,'0')  +' actualizado correctamente',()=>{window.location.reload()})
            } catch (error) {
                alertify.alert('Error',error.message);
                console.error(error);
            }
        });
    }
}
window.addEventListener('DOMContentLoaded',loadPage);