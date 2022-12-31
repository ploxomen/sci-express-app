function loadPage() {
    const general = new General();
    for (const btnRecojer of document.querySelectorAll(".btn-recoger")) {
        btnRecojer.onclick = function(e){
            e.preventDefault();
            alertify.confirm("Mensaje","Al aceptar estas confirmando que recogiste los bultos enviados por el cluster " + e.target.dataset.cluster + ".<br>¿Deseas continuar?",async () => {
                let datos = new FormData();
                datos.append("tipo","update-retorno");
                datos.append("retorno",e.target.dataset.retorno);
                datos.append("recogo",e.target.dataset.recogo);
                try {
                    const response = await general.funcfetch("acciones",datos);
                    if(response.success){
                        return alertify.alert("Mensaje","Recojo actualizado correctamente",()=>{window.location.reload()});
                    }
                    alertify.error(response.error);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al acutalizar el fecha y hora del recojo");
                }
            },()=>{})
        }
    }
    for (const btnRecojer of document.querySelectorAll(".btn-finalizar")) {
        btnRecojer.onclick = function(e){
            e.preventDefault();
            alertify.confirm("Mensaje","Al aceptar estas confirmando que has recogido todos los bultos asignados. <br>¿Deseas continuar?",async () => {
                let datos = new FormData();
                datos.append("tipo","finalizar-retorno");
                datos.append("recogo",e.target.dataset.recogo);
                try {
                    const response = await general.funcfetch("acciones",datos);
                    if(response.success){
                        return alertify.alert("Mensaje","Recojo actualizado correctamente",()=>{window.location.reload()});
                    }
                    alertify.error(response.error);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al finalizar el recojo");
                }
            },()=>{})
        }
    }
}
window.addEventListener("DOMContentLoaded",loadPage);