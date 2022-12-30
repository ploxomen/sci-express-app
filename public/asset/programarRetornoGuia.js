function loadPage() {
    const general = new General();
    const $frmFiltro = document.querySelector("#frm_filtros_soli"),
        $tablaRetorno = document.querySelector("#cuerpo-detalle-guias"),
        $btnCheckRetorno = document.querySelector("#cehckRetornoTodo");
    const inforLlegada = document.querySelector("#total_retornos_mostrados"),
        infoTotalRetornos = document.querySelector("#total_retornos_programar"),
        $filtroFechas = document.querySelector("#fechas_llegadas_retornos");
    let urlPeticion = window.location.origin + '/intranet/comprobante/guias/retornadas';
    async function mostrarGuiasParaProgramar(completo) {
        let datos = new FormData($frmFiltro);
        datos.append('tipo','mostrar-guias-programar');
        if(completo){
            datos.append('completo',true);
        }
        const response = await general.funcfetch(urlPeticion,datos);
        if(response.data){
            let template = "";
            response.data.forEach(r => {
                template +=`
                <tr>
                    <td>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="checkRetorno${r.id}" value="${r.id}">
                            <label class="custom-control-label" for="checkRetorno${r.id}"></label>
                        </div>
                    </td>
                    <td>${r.nroRetorno}</td>
                    <td>${r.fechaRetorno}</td>
                    <td>${r.empresa}</td>
                    <td>${r.fechaHrLlegada}</td>
                    <td>${r.nombreCluster}</td>
                    <td>S/ ${r.importe_embarque}</td>
                    <td>${r.tipo_comprobantes.replace(/;/i,"<br>")}</td>
                    <td>${r.nro_guias.replace(/;/i,"<br>")}</td>
                    <td>${r.total_bultos}</td>
                    <td><button class="btn btn-sm btn-outline-info" data-retorno="${r.id}"><i class="fas fa-eye"></i></button></td>
                </tr>`
            });
            $tablaRetorno.innerHTML = template == "" ? `<tr><td colspan="100%" class="text-center">No se encontraron retornos</td></tr>` : template;
            inforLlegada.textContent = template == "" ? 0 : response.data.length;
        }
        if(response.total){
            let nuevaFecha = [];
            response.total.forEach(f => {
                const i = nuevaFecha.findIndex( v => v.fecha == f.fechaLlegada);
                if(i >= 0){
                    nuevaFecha[i].cantidad++;
                }else{
                    nuevaFecha.push({
                        fecha: f.fechaLlegada,
                        cantidad: 1
                    });
                }
            });
            let template = "";
            nuevaFecha.forEach(nf => {
                template += `<div class="bg-light px-2 py-1 rounded-pill">
                <span>${nf.fecha}: </span>
                <b>${nf.cantidad}</b>
                </div>`
            });
            $filtroFechas.innerHTML = template == "" ? `<span class="text-danger">SIN RESULTADOS</span>` : template;
            infoTotalRetornos.textContent = response.total.length;
        }
    }
    mostrarGuiasParaProgramar(true);
    const fechaLlegadaFiltro = document.querySelector("#filtro_fecha_recojo");
    fechaLlegadaFiltro.onchange = e => {
        mostrarGuiasParaProgramar(false);
    }
    $('#cb-clusters').on('select2:select',function(e) {
        mostrarGuiasParaProgramar(false);
    });
    $btnCheckRetorno.onchange = function(e){
        for (const tr of  $tablaRetorno.children) {
            const checkbox = tr.querySelector("input[type='checkbox']");
            if(checkbox){
                checkbox.checked = this.checked;
            }
        }
    }
    $tablaRetorno.onclick = async function(e){
        if(e.target.classList.contains("btn-outline-info")){
            let datos = new FormData();
            datos.append('tipo','mostrar-guias-bultos');
            datos.append('retornada',e.target.dataset.retorno);
            const resultado = await general.funcfetch(urlPeticion,datos);
            if(resultado.bultos){
                for (const key in resultado.bultos) {
                    if (Object.hasOwnProperty.call(resultado.bultos, key)) {
                        const valor = resultado.bultos[key];
                        const dom = document.querySelector("#id_modal_" + key);
                        if(!dom){
                            continue;
                        }
                        dom.textContent = !valor ? "-" : valor;
                    }
                }
                $('#detalle-embarque').modal("show");
            }
        }
    }
    $('#detalle-embarque').on('hidden.bs.modal',function(e){
        $('.clear-text').text('-');
    });
    let codigosRetornos = [];
    document.querySelector("#nexProgramacion").onclick = function(e){
        e.preventDefault();
        const checkbox = $tablaRetorno.querySelectorAll("input[type='checkbox']:checked");
        if(!checkbox.length){
            return alertify.error("debe seleccionar al menos un retorno para programar");
        }
        for (const input of checkbox) {
            codigosRetornos.push({
                codigo: input.value
            });
        }
        document.querySelector("#id_dia_recojo").value = fechaLlegadaFiltro.value;
        $('#programar-recojo').modal("show");
    }
    $('#programar-recojo').on('hidden.bs.modal',function(e){
        $('#programar-recojo .simple-select2').val("").trigger("change");
        codigosRetornos = [];
    });
    document.querySelector("#btn-programar-recojo").onclick = e => document.querySelector("#programar-recojo-modal").click();
    document.querySelector("#form-programar-recojo").onsubmit = async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        datos.append('tipo','programar-retornos');
        datos.append('retornos',JSON.stringify(codigosRetornos));
        try {
            const response = await general.funcfetch(urlPeticion,datos);
            if(response.error){
                return alertify.confirm("Error",response.error);
            }
            alertify.success(response.success);
            $('#programar-recojo').modal("hide");
            mostrarGuiasParaProgramar(true);
        } catch (error) {
            console.error(error);
            alertify.error("error al programar los retornos");
        }
    }
}
window.addEventListener("DOMContentLoaded",loadPage);