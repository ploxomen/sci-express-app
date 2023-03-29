function loadPage(){
    const gen = new General();
    const txtFecha = document.querySelector("#fecha_filtros");
    const consultaFiltro = async function(){
        let datos = new FormData();
        datos.append("fecha", txtFecha.value);
        const response = await gen.funcfetch('home/manager',datos);
        for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
                const valor = response[key];
                if (key == "embarque_total" || key == "entregas_total" || key == "recojo_total"){
                    document.querySelector("#box_progress_" + key).hidden = valor > 0 ? false : true;
                    document.querySelector("#box_ninguno_progress_" + key).hidden = valor > 0 ? true : false;
                    if(valor <= 0){
                        continue;
                    }
                    let porcentaje = 0;
                    let bg = "bg-danger";
                    switch (key) {
                        case 'embarque_total':
                            porcentaje =  Math.round((100 * response.embarque_completado) / valor);
                            document.querySelector("#filtro_progress_embarque_completado").textContent = response.embarque_completado;
                            break;
                        case 'recojo_total':
                            porcentaje = Math.round((100 * response.recojo_completado) / valor);
                            document.querySelector("#filtro_progress_recojo_completado").textContent = response.recojo_completado;
                            break;
                        case 'entregas_total':
                            porcentaje = Math.round((100 * response.entregas_completado) / valor);
                            document.querySelector("#filtro_progress_entregas_completado").textContent = response.entregas_completado;
                            break;
                    }
                    if (porcentaje >= 25 && porcentaje < 50) {
                        bg = 'bg-warning';
                    }
                    if (porcentaje >= 50 && porcentaje < 75) {
                        bg = 'bg-info';
                    }
                    if (porcentaje >= 75) {
                        bg = 'bg-success';
                    }
                    const progressBar = document.querySelector("#filtro_progress_p_"+key);
                    progressBar.classList.remove("bg-danger", "bg-warning", "bg-info","bg-success");
                    progressBar.classList.add(bg);
                    progressBar.setAttribute("aria-valuenow",porcentaje);
                    progressBar.textContent = porcentaje + "%";
                    progressBar.style.width = porcentaje + "%";
                    const progressBarNum = document.querySelector("#filtro_progress_" + key);
                    progressBarNum.textContent = valor;
                }
            }
        }
    }
    document.querySelector("#btn-aplicar").addEventListener("click", consultaFiltro);
    consultaFiltro();
}
window.addEventListener("DOMContentLoaded",loadPage);