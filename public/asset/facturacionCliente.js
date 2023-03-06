function loadPage(){
    let general = new General();
    $('#lista-clientes, #lista-clientes-copiar').select2({
        theme : 'bootstrap',
        templateResult: general.tempSeleCli,
        placeholder: 'Seleccione un cliente'
    });
    const infinito = 999999999.99;
    const $tablaFacturacion = document.querySelector("#facturacion-ciudades");
    const $txtCabeceraNumero = document.querySelector("#id_cabecera_numero");
    const $cabeceraFacturacion = document.querySelector("#cabezara-facturacion-cliente");
    const $tablaFacturacionCosto = document.querySelector("#facturacion-ciudades-costo");
    const $tablaCabeceraFacturacionCosto = document.querySelector("#cabezara-facturacion-cliente-costo");
    const checkInfinito = document.querySelector("#checkInfinito");
    const txtMaximoCostoKg = document.querySelector("#id_cabecera_maximo_costo");
    const formCabeceraCosto = document.querySelector("#cabecera-facturacion-costo-cliente-form");
    const btnModalAgregarCosto = document.querySelector("#agregar-fact-costo-cliente");
    let numeroCabecera = 0;
    let idCostoCabecera = null;
    $tablaCabeceraFacturacionCosto.onclick = async function(e){
        if(e.target.classList.contains("btn-editar-cabecera-costo")){
            let datos = new FormData();
            datos.append("tipo","mostrar-costo-editar");
            datos.append("cliente",$('#lista-clientes').val());
            datos.append("cabecera",e.target.dataset.cabecera);
            try {
                const response = await general.funcfetch("acciones",datos);
                if(response.success){
                    idCostoCabecera = e.target.dataset.cabecera;
                    btnModalAgregarCosto.textContent = "Editar";
                    for (const key in response.success) {
                        if (Object.hasOwnProperty.call(response.success, key)) {
                            const valor = response.success[key];
                            const dom = document.querySelector("#" + key);
                            if(!dom){
                                continue;
                            }
                            if(key == "id_cabecera_maximo_costo" && valor == infinito){
                                checkInfinito.checked = true;
                                txtMaximoCostoKg.value = "";
                                txtMaximoCostoKg.disabled = true;
                                continue;
                            }
                            dom.value = valor;
                        }
                    }
                    $('#modal-cabecera-costo-cliente .simple-select2').trigger("change");
                    $('#modal-cabecera-costo-cliente').modal("show");
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
                datos.append("cliente",$('#lista-clientes').val());
                datos.append("cabecera",e.target.dataset.cabecera);
                try {
                    const response = await general.funcfetch("acciones",datos);
                    if(response.error){
                        return alertify.alert("Error",response.error);
                    }
                    alertify.success(response.success);
                    renderizarCostoTabla(response);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar la cabecera");
                }
            },()=>{})
        }
    }
    async function changeOptionCostoTabla(e){
        let datos = new FormData();
        datos.append("tipo","cambiar-valor-costo");
        datos.append("columna",this.dataset.columna);
        datos.append("fila",this.dataset.fila);
        datos.append("valor",this.value);
        try {
            const response = await general.funcfetch("acciones",datos);
            if(response.error){
                return alertify.error(response.error);
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al actualizar los valores");
        }
    }
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
                <th rowspan="2" scope="col">N°</th>
                <th rowspan="2" scope="col">Tipo Envio</th>
            `;
        let cabeceraEnvio = null;
        let contadorCabecera = 0;
        response.columnaEnvio.forEach((c,k) => {
            if(!cabeceraEnvio){
                cabeceraEnvio = c.envio;
                contadorCabecera++;
            }else{
                if(cabeceraEnvio != c.envio){
                    template += `<th colspan="${contadorCabecera}" scope="col">
                        ${cabeceraEnvio}
                    </th>`
                    contadorCabecera = 1;
                    cabeceraEnvio = c.envio;
                }else{
                    contadorCabecera++;
                }
            }
            if((k+1) == response.columnaEnvio.length && contadorCabecera > 0){
                template += `<th colspan="${contadorCabecera}" scope="col">
                    ${cabeceraEnvio}
                </th>`
            }
        });
        template += "</tr><tr>";
        response.columnaEnvio.forEach(c => {
            const boxInfinifo = infinito == c.kg_maximo ? `<i class="fas fa-infinity"></i>` : `<span id="text_kg_max_${c.id}">${c.kg_maximo}</span>`;
            template +=`<th id="columna_cabecera_costo_${c.id}" scope="col">
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
    async function verFacturacionCliente(){
        const cliente = $("#lista-clientes").val();
        let datos = new FormData();
        datos.append("tipo","mostrar");
        datos.append("cliente",cliente);
        try {
            const response = await general.funcfetch("acciones",datos);
            let template = "";
            $('[data-toggle="tooltip"]').tooltip('dispose');
            response.ciudadesBase.forEach((ci,k) => {
                const bgTd = ci.fau ? 'table-warning' : 'table-light';
                template += `<tr id="fila_ciudades_${ci.id}" class="${ci.fau ? 'table-warning' : ''}">
                <td class="${bgTd}" style="min-width: 20px !important;width: 20px !important; max-width: 20px !important;">${k + 1}</td>
                <td class="sticki ${bgTd}" style="width: 120px !important;">${ci.ciudad}</td>`
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
                <th rowspan="2" scope="col">N°</th>
                <th rowspan="2" scope="col" class="sticki table-light">Ciudades</th>
            `;
            let cabeceraEnvio = null;
            let contadorCabecera = 0;
            response.cabeceras.forEach((c,k) => {
                if(!cabeceraEnvio){
                    cabeceraEnvio = c.envio;
                    contadorCabecera++;
                }else{
                    if(cabeceraEnvio != c.envio){
                        template += `<th colspan="${contadorCabecera}" scope="col">
                            ${cabeceraEnvio}
                        </th>`
                        contadorCabecera = 1;
                        cabeceraEnvio = c.envio;
                    }else{
                        contadorCabecera++;
                    }
                }
                if((k+1) == response.cabeceras.length && contadorCabecera > 0){
                    template += `<th colspan="${contadorCabecera}" scope="col">
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
                template +=`<th id="columna_cabecera_${c.id}" scope="col">
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
            alertify.error("error al mostrar la configuracion de facturacion");
            console.error(error);
        }
    }
    let idCabecera = null;
    $cabeceraFacturacion.onclick = async function(e){
        if(e.target.classList.contains("btn-editar-cabecera")){
            let datos = new FormData();
            datos.append("cliente",$('#lista-clientes').val());
            datos.append("cabecera",e.target.dataset.cabecera);
            datos.append("tipo","mostrar-editar");
            try {
                const response = await general.funcfetch("acciones",datos);
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
                datos.append("cliente",$('#lista-clientes').val());
                datos.append("cabecera",e.target.dataset.cabecera);
                datos.append("tipo","eliminar-cabecera");
                try {
                    const response = await general.funcfetch("acciones",datos);
                    if(response.error){
                        return alertify.alert("Error",response.error);
                    }
                    if(response.success){
                        alertify.success(response.success);
                        verFacturacionCliente();
                    }
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar la cabecera");
                }
            },()=>{})
        }
    }
    verFacturacionCliente();
    $('#lista-clientes').on("change.select2",function(e){
        numeroCabecera = 0;
        verFacturacionCliente();
        deshabilitarOpcion();
    });
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
    document.querySelector("#btn-modal-fact-cabecera").onclick = function(e){
        $('#modal-cabecera-cliente').modal("show");
        $txtCabeceraNumero.value = numeroCabecera;
    }
    document.querySelector("#id-monto-default").addEventListener("change",function(e){
        const input = document.querySelector("#id-monto-default-fau");
        if(!input.value){
            input.value = this.value;
        }
    });
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
    document.querySelector("#agregar-fact-cliente").onclick = e => document.querySelector("#btn-cabecera-fac-cliente").click();
    document.querySelector("#cabecera-facturacion-cliente-form").onsubmit = async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        datos.append("tipo",idCabecera == null ? "agregar-cabecera" : "editar-cabecera");
        if(idCabecera != null){
            datos.append("cabecera",idCabecera);
        }
        datos.append("id_cliente",$("#lista-clientes").val());
        if(idCabecera == null){
            datos.append("numero",numeroCabecera);
        }
        try {
            const response = await general.funcfetch("acciones",datos);
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            if(response.success){
                alertify.success(response.success);
                $('#modal-cabecera-cliente').modal("hide");
                verFacturacionCliente();
            }
        } catch (error) {
            alertify.error("error al agregar la cabecera de facturacion");
            console.error(error);
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
            const response = await general.funcfetch("acciones",datos);
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            e.target.dataset.valor = e.target.value;
        } catch (error) {
            console.error(error);
            alertify.error("error al cambiar el monto");
        }
    }
    const cbClientesAtras = document.querySelector("#btn-anterior-cliente");
    const cbClientesAdelante = document.querySelector("#btn-siguiente-cliente");
    cbClientesAtras.onclick = selectedIndex;
    cbClientesAdelante.onclick = selectedIndex;
    function selectedIndex(e){
        const cbClientes = document.querySelector("#lista-clientes");
        let valorSelectIndex = cbClientes.selectedIndex;
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
            if((valorSelectIndex + 1) === cbClientes.length){
                cbClientesAdelante.disabled = true;
            }
        }
        cbClientes.selectedIndex = valorSelectIndex;
        $(cbClientes).trigger("change");
    }
    document.querySelector("#btn-sincronizar").onclick = function(e){
        alertify.confirm("Mensaje",'Al continuar se procederán a agregar aquellas ciudades base de los cluster que no se visualizan en la tabla de facturación<br>¿Deseas continuar de todas formas?',async ()=>{
            let datos = new FormData();
            datos.append("tipo","sincronizar-ciudades");
            const response = await general.funcfetch("acciones",datos);
            let temp = "";
            if(response.ciudades && response.ciudades.length){
                temp = "Las siguientes ciudades han sido agregadas <br><ol class='pl-2' style='columns: 2;-webkit-columns: 2;-moz-columns: 2;'><li class='text-success'>" + response.ciudades.map(c => c.ciudad).join("</li><li class='text-success'>") + "</li></ol>";
            }
            if(response.ciudadesEliminar && response.ciudadesEliminar.length){
                temp += "Las siguientes ciudades han sido eliminadas <br><ol class='pl-2' style='columns: 2;-webkit-columns: 2;-moz-columns: 2;'><li class='text-danger'>" + response.ciudadesEliminar.map(c => c.ciudad).join("</li><li class='text-danger'>") + "</li></ol>";
            }
            if(response.ciudadesEliminar.length || response.ciudades.length){
                alertify.alert("Mensaje",temp);
                verFacturacionCliente();
            }
            if(!response.ciudades.length && !response.ciudadesEliminar.length){
                alertify.alert("Mensaje","No se encontraron ciudades base de los cluster para ser agregadas a la tabla de facturación");
            }
        },()=>{})
    }
    
    checkInfinito.onchange = function(e){
        txtMaximoCostoKg.value = "";
        txtMaximoCostoKg.disabled = this.checked;
    }
    btnModalAgregarCosto.onclick = e => document.querySelector("#btn-cabecera-fac-costo-cliente").click();
    document.querySelector("#btn-modal-fact-cabecera-costo").onclick = function(e){
        $('#modal-cabecera-costo-cliente').modal("show");
    }
    $('#modal-cabecera-costo-cliente').on('hide.bs.modal', function (e) {
        formCabeceraCosto.reset();
        $('#modal-cabecera-costo-cliente .simple-select2').trigger("change");
        checkInfinito.checked = false;
        txtMaximoCostoKg.disabled = false;
        btnModalAgregarCosto.textContent = "Agregar";
        idCostoCabecera = null;
    });
    formCabeceraCosto.onsubmit = async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        datos.append("tipo", idCostoCabecera == null ? "agregar-costo-cabecera" : "editar-costo-cabecera");
        datos.append("id_cliente",$("#lista-clientes").val());
        if(idCostoCabecera){
            datos.append("cabecera",idCostoCabecera);
        }
        try {
            const response = await general.funcfetch("acciones",datos);
            if(response.success){
                alertify.success(response.success);
                renderizarCostoTabla(response);
                $('#modal-cabecera-costo-cliente').modal("hide");
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar la cabecera a la tabla");
        }
    }
    function deshabilitarOpcion(){
        $("#lista-clientes-copiar option").prop("disabled",false);
        const valorCliente = $("#lista-clientes").val();
        $("#lista-clientes-copiar option[value='" + valorCliente +"']").prop("disabled",true);
    }
    $('#modal-copiar-cliente').on('hide.bs.modal', function (e) {
        $("#lista-clientes-copiar").val("").trigger("change");
    });
    deshabilitarOpcion();
    document.querySelector("#btn-importar").onclick = e => $('#modal-copiar-cliente').modal("show");
    document.querySelector("#btnImportarDataCliente").onclick = e => document.querySelector("#btnImportDataCliente").click();
    const frmCopiData = document.querySelector("#from-copiar-cliente");
    frmCopiData.onsubmit = function(e){
        e.preventDefault();
        const cliente = $( "#lista-clientes option:selected" ).text();
        const clienteImpor = $( "#lista-clientes-copiar option:selected" ).text();
        alertify.confirm("Mensaje","Al continuar se importaran los datos del cliente <b>" + cliente + "</b> al cliente <b>" + clienteImpor + "</b>"+ " recuerde que los datos existentes del cliente <b>" + clienteImpor + "</b> seran eliminados.<br>¿Deseas importarlo de todas formas?",async ()=>{
            let datos = new FormData(this);
            datos.append("tipo","importarData");
            datos.append("cliente",$("#lista-clientes").val());
            try {
                const response = await general.funcfetch("acciones",datos);
                if(response.success){
                    alertify.success(response.success);
                    $('#modal-copiar-cliente').modal("hide");
                }
                if(response.error){
                    return alertify.alert("Error",response.error);
                }
            } catch (error) {
                console.error(error);
                alertify.error("error al importar los datos del cliente");
            }
        },()=>{});
    }
}
window.addEventListener("DOMContentLoaded",loadPage);