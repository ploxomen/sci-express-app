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
                const index = response.valorEnvio.findIndex(w => w.valor.columna == c.id && w.valor.fila == ci.id);
                const cbSelected = document.createElement("select");
                cbSelected.classList.add("form-control","form-control-sm","change-valor-costo");
                cbSelected.append(new Option("0","0"));
                if(index >= 0){
                    response.valorEnvio[index].listaAsignacion.forEach(list => {
                        cbSelected.append(new Option(list.numero,list.id,list.id == response.valorEnvio[index].valor.id ? true : false));
                        cbSelected.dataset.columna = response.valorEnvio[index].valor.columna;
                        cbSelected.dataset.fila = response.valorEnvio[index].valor.fila;
                    });
                }
                template += `<td>${cbSelected.outerHTML}</td>`;
            });
            template += `</tr>`
        });
        $tablaFacturacionCosto.innerHTML = template;
        for (const cambio of document.querySelectorAll(".change-valor-costo")) {
            cambio.addEventListener("change",changeOptionCostoTabla);
        }
        template = `
        <tr>
            <th rowspan="2">N°</th>
            <th rowspan="2">Tipo Envio</th>`;
        let cabeceraEnvio = null;
        let contadorCabecera = 0;
        response.columnaEnvio.forEach((c,k) => {
            if(!cabeceraEnvio){
                cabeceraEnvio = c.envio;
                contadorCabecera++;
            }else{
                if(cabeceraEnvio != c.envio){
                    template += `<th colspan="${contadorCabecera}">
                        ${cabeceraEnvio}
                    </th>`
                    contadorCabecera = 1;
                    cabeceraEnvio = c.envio;
                }else{
                    contadorCabecera++;
                }
            }
            if((k+1) == response.columnaEnvio.length && contadorCabecera > 0){
                template += `<th colspan="${contadorCabecera}">
                    ${cabeceraEnvio}
                </th>`
            }
        });
        template += "</tr><tr>";
        response.columnaEnvio.forEach(c => {
            const boxInfinifo = infinito == c.kg_maximo ? `<i class="fas fa-infinity"></i>` : `<span id="text_kg_max_${c.id}">${c.kg_maximo}</span>`;
            template +=`<th id="columna_cabecera_costo_${c.id}">
                    <div class="d-flex justify-content-center" style="gap:5px;">
                        <div class="text-center">
                            <span id="text_kg_mini_${c.id}">${c.kg_minimo}</span>
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
    async function changeOptionCostoTabla(e){
        let datos = new FormData();
        datos.append("tipo","cambiar-valor-costo");
        datos.append("columna",this.dataset.columna);
        datos.append("fila",this.dataset.fila);
        datos.append("valor",this.value);
        try {
            const response = await general.funcfetch("cluster/acciones",datos);
            if(response.error){
                return alertify.error(response.error);
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al actualizar los valores");
        }
    }
    async function changeValor(e){
        if(e.target.value == e.target.dataset.valor){
            return
        }
        let datos = new FormData();
        datos.append("tipo","agregar-valor");
        datos.append("ciudad",e.target.dataset.fila);
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
    const $tablaFacturacion = document.querySelector("#facturacion-ciudades");
    const $cabeceraFacturacion = document.querySelector("#cabezara-facturacion-cluster");
    async function obtenerCostosClusters() {
        let datos = new FormData();
        datos.append("tipo","obtenerFacturacion");
        datos.append("cluster",$(cbCluster).val());
        try {
            const response = await general.funcfetch("cluster/acciones",datos);
            if(response.error){
                return alertify.error(response.error);
            }
            let template = "";
            $('[data-toggle="tooltip"]').tooltip('dispose');
            response.ciudadesBase.forEach((ci,k) => {
                template += `<tr id="fila_ciudades_${ci.id}" class="${ci.ciudad_alejada == "Complejo - Distante" || ci.ciudad_alejada == "Rural - Alejada" ? 'table-warning' : ''}">
                <td style="min-width: 20px !important;width: 20px !important; max-width: 20px !important;">${k + 1}</td>
                <td style="width: 120px !important;">${ci.nombre_ciudad}</td>`
                response.cabeceras.forEach(c => {
                    const index = response.valores.findIndex(w => w.ciudad === ci.id && w.cabecera == c.id);
                    if(index >= 0){
                        template += `<td><input type="number" step="0.01" data-valor="${response.valores[index].valor}" class="form-control form-control-sm change-valor" value="${response.valores[index].valor}" data-tipo="${response.valores[index].tipo}" data-columna="${response.valores[index].cabecera}" data-fila="${response.valores[index].ciudad}"></td>`;
                    }
                });
                template += `</tr>`
            });
            $tablaFacturacion.innerHTML = template;
            template = `
            <tr>
                <th rowspan="2">N°</th>
                <th rowspan="2">Ciudades</th>
            `;
            let cabeceraEnvio = null;
            let contadorCabecera = 0;
            response.cabeceras.forEach((c,k) => {
                if(!cabeceraEnvio){
                    cabeceraEnvio = c.envio;
                    contadorCabecera++;
                }else{
                    if(cabeceraEnvio != c.envio){
                        template += `<th colspan="${contadorCabecera}">
                            ${cabeceraEnvio}
                        </th>`
                        contadorCabecera = 1;
                        cabeceraEnvio = c.envio;
                    }else{
                        contadorCabecera++;
                    }
                }
                if((k+1) == response.cabeceras.length && contadorCabecera > 0){
                    template += `<th colspan="${contadorCabecera}">
                        ${cabeceraEnvio}
                    </th>`
                }
            });
            template += "</tr><tr>";
            response.cabeceras.forEach(c => {
                let colorCabecera = "text-primary";
                switch (c.tipo_calculo) {
                    case 'base':
                        colorCabecera = "text-danger";
                    break;
                    case 'adicional':
                        colorCabecera = "text-success";
                    break;
                }
                template +=`<th id="columna_cabecera_${c.id}">
                  <a href="javascript:void(0)" data-toggle="tooltip" title="${c.descripcion == null || c.descripcion == "" ? 'Sin descripción' : c.descripcion}" class="text-center ${colorCabecera}">${c.numero}</a>
                  <div class="d-flex justify-content-center" style="gap:5px;">
                    <button class="btn-tabla-facturacion btn-editar-cabecera" data-cabecera="${c.id}">
                        <i class="fas fa-pen text-info"></i>
                    </button>
                    <button class="btn-tabla-facturacion btn-eliminar-cabecera" data-cabecera="${c.id}">
                        <i class="fas fa-trash-alt text-danger"></i>
                    </button>
                  </div>
                </th>`
            })
            template += "</tr>";
            $cabeceraFacturacion.innerHTML = template;
            cabeceraMayor(response.cabeceras);
            for (const cambio of document.querySelectorAll(".change-valor")) {
                cambio.addEventListener("blur",changeValor);
            }
            renderizarCostoTabla(response);
            $('[data-toggle="tooltip"]').tooltip();
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener los datos");
        }
    }
    const $txtCabeceraNumero = document.querySelector("#id_cabecera_numero");
    document.querySelector("#btn-fact-cabecera").onclick = function(e){
        $('#modal-cabecera-cliente').modal("show");
        $txtCabeceraNumero.value = numeroCabecera;
    }
    $('#lista-tipo-cabecera').on("change.select2",function(e){
        const $base = document.querySelector("#box-kg-base");
        const valor = $(this).val() == "base" ? false : true;
        if(valor){
            e.target.parentElement.classList.replace("col-6","col-4");
        }else{
            e.target.parentElement.classList.replace("col-4","col-6");
        }
        $base.hidden = valor;
        $base.children[1].disabled = valor;
        const $default = document.querySelectorAll($base.dataset.default);
        const valorArray = !valor ? ["col-4","col-6"] : ["col-6","col-4"];
        $base.classList.replace(...valorArray);
        for (const d of $default) {
            d.classList.replace(...valorArray);
        }
    });
    let numeroCabecera = 0; 
    function cabeceraMayor(cabeceras){
        if(!cabeceras.length){
            numeroCabecera = 1;
            return
        }
        let numeroTemporal = 0;
        cabeceras.forEach(c => {
            if(c.numero > numeroTemporal){
                numeroTemporal = c.numero;
            }
        });
        numeroCabecera = numeroTemporal + 1;
    }
    obtenerCostosClusters();
    $('#lista-cluster').on("change.select2",function(e){
        obtenerCostosClusters();
        deshabilitarOpcion();
    });
    let idCabecera = null;
    document.querySelector("#agregar-fact-cliente").onclick = e => document.querySelector("#btn-cabecera-fac-cliente").click();
    document.querySelector("#cabecera-facturacion-cliente-form").onsubmit = async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        datos.append("tipo",idCabecera == null ? "agregar-cabecera-cluster" : "editar-cabecera-cluster");
        if(idCabecera != null){
            datos.append("cabecera",idCabecera);
        }
        datos.append("id_cluster",$("#lista-cluster").val());
        if(idCabecera == null){
            datos.append("numero",numeroCabecera);
        }
        try {
            const response = await general.funcfetch("cluster/acciones",datos);
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            if(response.success){
                alertify.success(response.success);
                $('#modal-cabecera-cliente').modal("hide");
                obtenerCostosClusters();
            }
        } catch (error) {
            alertify.error("error al agregar la cabecera de facturacion");
            console.error(error);
        }
    }
    $cabeceraFacturacion.onclick = async function(e){
        if(e.target.classList.contains("btn-editar-cabecera")){
            let datos = new FormData();
            datos.append("cluster",$('#lista-cluster').val());
            datos.append("cabecera",e.target.dataset.cabecera);
            datos.append("tipo","mostrar-editar-cluster");
            try {
                const response = await general.funcfetch("cluster/acciones",datos);
                if(response.datos){
                    idCabecera = e.target.dataset.cabecera;
                    document.querySelector("#agregar-fact-cliente").textContent = "Modificar";
                    for (const key in response.datos) {
                        if (Object.hasOwnProperty.call(response.datos, key)) {
                            const valor = response.datos[key];
                            const dom = document.querySelector("#" + key);
                            if(!dom){
                                continue;
                            }
                            if(key == "id_cabecera_numero"){
                                dom.setAttribute("max",response.maximo);
                                dom.disabled = false;
                            }
                            dom.value = valor;
                        }
                    }
                    $('.simple-select2').trigger("change");
                    $('#modal-cabecera-cliente .box-default').prop("hidden",true);
                    $('#modal-cabecera-cliente .box-default input').prop("disabled",true);
                    $('#modal-cabecera-cliente').modal("show");
                }
            } catch (error) {
                console.error(error);
                alertify.error("error al mostrar los datos de la cabecera");
            }
        }
        if(e.target.classList.contains("btn-eliminar-cabecera")){
            alertify.confirm("Alerta","¿Deseas eliminar esta cabecera? <br> Recuerda que una vez borrada se perderan todos los datos ingresados",async ()=>{
                let datos = new FormData();
                datos.append("cluster",$('#lista-cluster').val());
                datos.append("cabecera",e.target.dataset.cabecera);
                datos.append("tipo","eliminar-cabecera-cluster");
                try {
                    const response = await general.funcfetch("cluster/acciones",datos);
                    if(response.error){
                        return alertify.alert("Error",response.error);
                    }
                    if(response.success){
                        alertify.success(response.success);
                        obtenerCostosClusters();
                    }
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar la cabecera");
                }
            },()=>{})
        }
    }
    $('#modal-cabecera-cliente').on('hide.bs.modal', function (e) {
        $('#lista-tipo-cabecera').val("normal").trigger("change");
        idCabecera = null;
        document.querySelector("#agregar-fact-cliente").textContent = "Agregar";
        $txtCabeceraNumero.removeAttribute("max");
        $txtCabeceraNumero.disabled = true;
        $('#lista-via-envio').val(1).trigger("change");
        document.querySelector("#cabecera-facturacion-cliente-form").reset();
        $('#modal-cabecera-cliente .box-default').prop("hidden",false);
        $('#modal-cabecera-cliente .box-default input').prop("disabled",false);
    });
    document.querySelector("#btn-modal-fact-cabecera").onclick = function(e){
        $('#modal-cabecera-costo-cluster').modal("show");
    }
    const formCabeceraCosto = document.querySelector("#cabecera-facturacion-costo-cluster-form");
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