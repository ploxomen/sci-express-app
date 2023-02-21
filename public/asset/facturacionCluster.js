function loadPage(){
    $('[data-toggle="tooltip"]').tooltip();
    let general = new General();
    $('#lista-cluster, #lista-cluster-copiar').select2({
        theme : 'bootstrap',
        templateResult: general.tempSeleCli,
        placeholder: 'Seleccione un cluster'
    });
    const cbClientesAtras = document.querySelector("#btn-anterior-cliente");
    const cbClientesAdelante = document.querySelector("#btn-siguiente-cliente");
    const cbCluster = document.querySelector("#lista-cluster");
    cbClientesAtras.onclick = selectedIndex;
    cbClientesAdelante.onclick = selectedIndex;
    function selectedIndex(e){
        let valorSelectIndex = cbCluster.selectedIndex;
        if(e.target.dataset.tipo == "atras"){
            cbClientesAdelante.disabled = false;
            valorSelectIndex = valorSelectIndex - 1
            if(valorSelectIndex === 0){
                cbClientesAtras.disabled = true;
            }
        }
        if(e.target.dataset.tipo == "adelante"){
            cbClientesAtras.disabled = false;
            valorSelectIndex = valorSelectIndex + 1
            if((valorSelectIndex + 1) === cbCluster.length){
                cbClientesAdelante.disabled = true;
            }
        }
        cbCluster.selectedIndex = valorSelectIndex;
        $(cbCluster).trigger("change");
    }
    const infinito = 999999999.99;
    const $tablaFacturacionCosto = document.querySelector("#facturacion-ciudades-costo");
    const $tablaCabeceraFacturacionCosto = document.querySelector("#cabezara-facturacion-cluster-costo");
    function renderizarCostoTabla(response){
        let template = "";
        response.filaEnvio.forEach((ci,k) => {
            template += `<tr id="fila_ciudades_costo:${ci.id}">
            <td>${k + 1}</td>
            <td>${ci.tipo_envio}</td>`
            response.columnaEnvio.forEach(c => {
                const index = response.valorEnvio.findIndex(w => w.columna == c.id && w.fila == ci.id);
                const txtValor = document.createElement("input");
                txtValor.classList.add("form-control","form-control-sm","change-valor-costo");
                txtValor.type = "number";
                txtValor.step = "0.01";
                txtValor.min = "0.00";
                txtValor.dataset.valor = response.valorEnvio[index].valor;
                if(index >= 0){
                    txtValor.setAttribute("value",response.valorEnvio[index].valor);
                    txtValor.dataset.columna = response.valorEnvio[index].columna;
                    txtValor.dataset.fila = response.valorEnvio[index].fila;
                }
                template += `<td>${txtValor.outerHTML}</td>`;
            });
            template += `</tr>`
        });
        $tablaFacturacionCosto.innerHTML = template;
        for (const cambio of document.querySelectorAll(".change-valor-costo")) {
            cambio.addEventListener("blur",changeValor);
        }
        template = `
        <tr>
            <th rowspan="2">N°</th>
            <th rowspan="2">Tipo Envio</th>
        </tr>
        <tr>`;
        response.columnaEnvio.forEach(c => {
            let bgTipoEnv = "text-primary";
            switch (c.tipo_calculo) {
                case 'base':
                    bgTipoEnv = "text-danger";
                break;
                case 'adicional':
                    bgTipoEnv = "text-success";
                break;
            }
            const boxInfinifo = infinito == c.kg_maximo ? `<i class="fas fa-infinity ${bgTipoEnv}"></i>` : `<span class="${bgTipoEnv}" id="text_kg_max_${c.id}">${c.kg_maximo}</span>`;
            template +=`<th id="columna_cabecera_costo_${c.id}">
                    <div class="d-flex justify-content-center" style="gap:5px;">
                        <div class="text-center">
                            <span id="text_kg_mini_${c.id}" class="${bgTipoEnv}">${c.kg_minimo}</span>
                        </div>
                        <div class="text-center">
                            <i class="far fa-arrow-alt-circle-right text-info"></i>
                        </div>
                        <div class="text-center">
                            ${boxInfinifo}
                        </div>
                    </div>
                    <div class="d-flex justify-content-center" style="gap:5px;">
                        <button data-cabecera="${c.id}" class="btn-tabla-facturacion btn-editar-cabecera-costo">
                            <i class="fas fa-pen text-info"></i>
                        </button>
                        <button class="btn-tabla-facturacion btn-eliminar-cabecera-costo" data-cabecera="${c.id}">
                            <i class="fas fa-trash-alt text-danger"></i>
                        </button>
                    </div>
            </th>`
        })
        template += "</tr>";
        $tablaCabeceraFacturacionCosto.innerHTML = template;
    }
    async function changeValor(e){
        if(e.target.value == e.target.dataset.valor){
            return
        }
        let datos = new FormData();
        datos.append("tipo","agregar-valor");
        datos.append("tipoEnvio",e.target.dataset.fila);
        datos.append("cabecera",e.target.dataset.columna);
        datos.append("monto",e.target.value);
        try {
            const response = await general.funcfetch("cluster/acciones",datos);
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            e.target.dataset.valor = e.target.value;
        } catch (error) {
            console.error(error);
            alertify.error("error al cambiar el monto");
        }
    }
    $tablaCabeceraFacturacionCosto.onclick = async function(e){
        if(e.target.classList.contains("btn-editar-cabecera-costo")){
            let datos = new FormData();
            datos.append("tipo","mostrar-costo-editar");
            datos.append("cluster",$(cbCluster).val());
            datos.append("cabecera",e.target.dataset.cabecera);
            try {
                const response = await general.funcfetch("cluster/acciones",datos);
                if(response.success){
                    idCostoCabecera = e.target.dataset.cabecera;
                    btnModalAgregarCosto.textContent = "Editar";
                    for (const key in response.success) {
                        if (Object.hasOwnProperty.call(response.success, key)) {
                            const valor = response.success[key];
                            const dom = document.querySelector("#id_cabecera_" + key);
                            if(!dom){
                                continue;
                            }
                            if(key == "kg_maximo" && valor == infinito){
                                checkInfinito.checked = true;
                                txtMaximoCostoKg.value = "";
                                txtMaximoCostoKg.disabled = true;
                                continue;
                            }
                            dom.value = valor;
                        }
                    }
                    $('#modal-cabecera-costo-cluster .simple-select2').trigger("change");
                    $('#modal-cabecera-costo-cluster').modal("show");
                }
            } catch (error) {
                alertify.error("error al obtener los datos de la cabecera");
                console.error(error);
            }
        }
        if(e.target.classList.contains("btn-eliminar-cabecera-costo")){
            alertify.confirm("Alerta","¿Deseas eliminar esta cabecera? Al hacerlo se eliminaran todos los datos asociados a ella",async ()=>{
                let datos = new FormData();
                datos.append("tipo","eliminar-costo-editar");
                datos.append("cluster",$(cbCluster).val());
                datos.append("cabecera",e.target.dataset.cabecera);
                try {
                    const response = await general.funcfetch("cluster/acciones",datos);
                    if(response.error){
                        return alertify.alert("Error",response.error);
                    }
                    alertify.success(response.success);
                    obtenerCostosClusters(response);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar la cabecera");
                }
            },()=>{})
        }
    }
    async function obtenerCostosClusters() {
        let datos = new FormData();
        datos.append("tipo","obtenerFacturacion");
        datos.append("cluster",$(cbCluster).val());
        try {
            const response = await general.funcfetch("cluster/acciones",datos);
            if(response.error){
                return alertify.error(response.error);
            }
            renderizarCostoTabla(response);
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener los datos");
        }
    }
    obtenerCostosClusters();
    $('#lista-cluster').on("change.select2",function(e){
        obtenerCostosClusters();
        deshabilitarOpcion();
    });
    document.querySelector("#btn-modal-fact-cabecera").onclick = function(e){
        $('#modal-cabecera-costo-cluster').modal("show");
    }
    const formCabeceraCosto = document.querySelector("#cabecera-facturacion-costo-cluster-form");
    $('#id_cabecera_tipo_calculo').on("change.select2",function(e){
        const $base = document.querySelector("#id_cabecera_kg_base");
        const valor = $(this).val() == "base" ? false : true;
        $base.parentElement.hidden = valor;
        $base.disabled = valor;
    });
    const txtMaximoCostoKg = document.querySelector("#id_cabecera_kg_maximo");
    const checkInfinito = document.querySelector("#checkInfinito");
    const btnModalAgregarCosto = document.querySelector("#agregar-fact-costo-cluster");
    btnModalAgregarCosto.onclick = e => document.querySelector("#btn-cabecera-fac-costo-cluster").click();
    checkInfinito.onchange = function(e){
        txtMaximoCostoKg.value = "";
        txtMaximoCostoKg.disabled = this.checked;
    }
    $('#modal-cabecera-costo-cluster').on('hide.bs.modal', function (e) {
        formCabeceraCosto.reset();
        $('#modal-cabecera-costo-cluster .simple-select2').trigger("change");
        checkInfinito.checked = false;
        txtMaximoCostoKg.disabled = false;
        btnModalAgregarCosto.textContent = "Agregar";
        idCostoCabecera = null;
    });
    let idCostoCabecera = null;
    formCabeceraCosto.onsubmit = async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        datos.append("tipo", idCostoCabecera == null ? "agregar-cabecera" : "editar-cabecera");
        datos.append("id_cluster",$(cbCluster).val());
        if(idCostoCabecera){
            datos.append("cabecera",idCostoCabecera);
        }
        try {
            const response = await general.funcfetch("cluster/acciones",datos);
            if(response.error){
                return alertify.error(response.error);
            }
            if(response.success){
                alertify.success(response.success);
                obtenerCostosClusters(response);
                $('#modal-cabecera-costo-cluster').modal("hide");
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar la cabecera a la tabla");
        }
    }
    document.querySelector("#btn-importar").onclick = e => $('#modal-copiar-cluster').modal("show");
    document.querySelector("#btnImportarDataCliente").onclick = e => document.querySelector("#btnImportDataCliente").click();
    const frmCopiData = document.querySelector("#from-copiar-cliente");
    frmCopiData.onsubmit = function(e){
        e.preventDefault();
        const cluster = $( "#lista-cluster option:selected" ).text();
        const clusterImport = $( "#lista-cluster-copiar option:selected" ).text();
        alertify.confirm("Mensaje","Al continuar se importaran los datos del cluster <b>" + cluster + "</b> al cluster <b>" + clusterImport + "</b>"+ " recuerde que los datos existentes del cluster <b>" + clusterImport + "</b> seran eliminados.<br>¿Deseas importarlo de todas formas?",async ()=>{
            let datos = new FormData(this);
            datos.append("tipo","importarData");
            datos.append("cluster",$(cbCluster).val());
            try {
                const response = await general.funcfetch("cluster/acciones",datos);
                if(response.success){
                    alertify.success(response.success);
                    $('#modal-copiar-cluster').modal("hide");
                }
                if(response.error){
                    return alertify.alert("Error",response.error);
                }
            } catch (error) {
                console.error(error);
                alertify.error("error al importar los datos del cluster");
            }
        },()=>{});
    }
    function deshabilitarOpcion(){
        $("#lista-cluster-copiar option").prop("disabled",false);
        const valorCliente = $("#lista-cluster").val();
        $("#lista-cluster-copiar option[value='" + valorCliente +"']").prop("disabled",true);
    }
    deshabilitarOpcion();
}
window.addEventListener("DOMContentLoaded",loadPage);