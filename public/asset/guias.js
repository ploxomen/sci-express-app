function pageLoaded() {
    let general = new General();
    $(general.$loader).hide();
    let newCliCli = false;
    let tipoCbRemi = null;
    let comprobantesAgregados = {
        tipos: [],
        series: [],
    };
    let comprobantesGenerales = [];
    const tableDetalleGuia = document.getElementById("tabla-detalle");
    const $cbCliente = document.getElementById("id_remi_nomb");
    $("#id_comp_clie")
        .select2({
            theme: "bootstrap",
            width: "100%",
        })
        .on("select2:select", function (e) {
            general.validarSeleccionComprobantes(
                $(this).val(),
                "#id_comp_clie",
                "#id_nro_comp_cli"
            );
        });
    $("#id_nro_comp_cli").select2({
        theme: "bootstrap",
        width: "100%",
        tags: true,
    });
    $(".comprobantes-guias").select2({
        theme: "bootstrap",
        width: "100%",
        tags: true,
    });
    const dataGuias = [
        {
            action: "guia",
            label: "N° Guía",
            pattern: "[0-9]{3}[-][0-9]{7}",
            maxlength: 11,
            requiredDataClient: true,
        },
        {
            action: "os",
            label: "N° OS",
            pattern: "[0-9]{7}",
            maxlength: 7,
            requiredDataClient: false,
        },
    ];
    for (const guia of document.getElementsByName("select_comprobantes")) {
        guia.addEventListener("change", function (e) {
            e.preventDefault();
            dataGuias.forEach((v, k) => {
                if (v.action == e.target.dataset.action) {
                    general.changeTipoGuiaFormato(
                        "id_nro_guia",
                        e.target.dataset.value,
                        v.maxlength,
                        v.pattern,
                        v.label,
                        v.requiredDataClient
                    );
                }
            });
        });
    }
    $("#id_dest_dist").on("select2:select", async function (e) {
        general.getCiudadesFinales(
            $(this).val(),
            $(this).attr("data-ciudades")
        );
    });
    $("#id_nro_comp_cli").on("change.select2", function (e) {
        $("#id_cant_comp").val($(this).val() ? $(this).val().length : 0);
    });
    document
        .getElementById("add-delete-detalle")
        .addEventListener("click", function (e) {
            e.preventDefault();
            if (
                tableDetalleGuia.children.length === 1 &&
                $("#id_estado").val() === "1"
            ) {
                alertify.error(
                    "la guía activa debe de tener al menos un detalle"
                );
                return;
            }
            const nroChildren = tableDetalleGuia.children.length;
            if (
                $("#id_estado").val() === "3" &&
                tableDetalleGuia.children.length === 1
            ) {
                $("#tabla-detalle .simple-select2").val("").trigger("change");
                $("#tabla-detalle .data-texto").val("");
                $("#tabla-detalle .data-anulada-int")
                    .val("0")
                    .trigger("change");
                $("#tabla-detalle .data-anulada-decimal")
                    .val("0")
                    .trigger("change");
                return;
            }
            indicesNavegaciones.pop();
            tableDetalleGuia.children[nroChildren - 1].remove();
            alertify.success("ultimo detalle eliminado");
        });
    let navDetalleEnvios = [];
    const navegacionEnvases = document.getElementById("navegacion-header");
    let indicesNavegaciones = [];
    $($cbCliente).on("select2:select", async function (e) {
        indicesNavegaciones = [];
        tipoCbRemi = await general.getDataRemitenteGuia(
            this,
            "#id_remi_prov",
            "#id_remi_dist",
            "#id_remi_depa",
            "#id_remi_tipo_docu",
            "#id-invitado-remitente"
        );
        navDetalleEnvios = window.detallesEnvios;
        navegacionEnvases.hidden = !navDetalleEnvios.length;
        for (const tr of tableDetalleGuia.children) {
            indicesNavegaciones.push(0);
            if (tr.querySelector(".detalle-envio")) {
                tr.querySelector(".detalle-envio").remove();
            }
            if (navDetalleEnvios.length) {
                navegarValores(tr, navDetalleEnvios[0]);
            }
            if (navDetalleEnvios.length > 1) {
                const td = document.createElement("td");
                td.className = "detalle-envio";
                td.innerHTML = `
                <div class="sort-buttons">
                    <button class="sort-button" type="button">
                        <span class="sort-icon sort-up text-secondary"></span>
                    </button>
                    <button class="sort-button" type="button">
                        <span class="sort-icon sort-down text-secondary"></span>
                    </button>
                </div>
                `;
                tr.append(td);
            }
        }
    });
    function navegarValores(tr, valores) {
        tr.querySelector('[data-name="largo"]').value = valores.largo;
        tr.querySelector('[data-name="ancho"]').value = valores.ancho;
        tr.querySelector('[data-name="altura"]').value = valores.altura;
        $(tr.querySelector('[data-name="embase_embalaje"]'))
            .val(valores.envase_embalaje)
            .trigger("change");
        const evento = new Event("change", { bubbles: true });
        tr.querySelector('[data-name="largo"]').dispatchEvent(evento);
    }
    tableDetalleGuia.onclick = (e) => {
        if (e.target.classList.contains("sort-icon")) {
            const indice = Array.from(tableDetalleGuia.children).indexOf(
                e.target.closest("tr")
            );
            if (
                indicesNavegaciones[indice] >= 0 &&
                indicesNavegaciones[indice] < navDetalleEnvios.length
            ) {
                const valor = e.target.classList.contains("sort-up")
                    ? indicesNavegaciones[indice] + 1
                    : indicesNavegaciones[indice] - 1;
                indicesNavegaciones[indice] =
                    valor < 0
                        ? 0
                        : valor >= navDetalleEnvios.length
                        ? navDetalleEnvios.length - 1
                        : valor;
            }
            e.target
                .closest(".sort-buttons")
                .querySelector(".sort-down").style.borderTopColor =
                indicesNavegaciones[indice] <= 0
                    ? "var(--secondary)"
                    : "var(--info)";
            e.target
                .closest(".sort-buttons")
                .querySelector(".sort-up").style.borderBottomColor =
                indicesNavegaciones[indice] >= navDetalleEnvios.length - 1
                    ? "var(--secondary)"
                    : "var(--info)";
            if (navDetalleEnvios[indicesNavegaciones[indice]]) {
                navegarValores(
                    tableDetalleGuia.children[indice],
                    navDetalleEnvios[indicesNavegaciones[indice]]
                );
            }
        }
    };

    const $formGuiaRemitente = document.getElementById("guia-remitente-form");
    $formGuiaRemitente.addEventListener("submit", async function (e) {
        e.preventDefault();
        let idGuia = await generarGuia(frmGuia, false);
        if(!idGuia){
            return false;
        }
        let data = new FormData($formGuiaRemitente);
        data.append("guia", idGuia);
        generarComprobanteFetch("guia-remision-remitente", data);
    });
    $("#id-invitado-remitente").on("select2:select", async function (e) {
        general.getCCInvitados("#id-invitado-remitente", $cbCliente);
    });
    let frmGuia = document.getElementById("frm_guia");
    const obtenerDatosDetalle = () => {
        let detalles = new Array();
        for (const e of tableDetalleGuia.children) {
            let objeto = new Object();
            let deta = e.querySelectorAll("input, select, textarea");
            console.log(deta);
            for (const ele2 of deta) {
                objeto[ele2.dataset.name] = ele2.value;
                if( ele2.dataset.name === "tipo_envio"){
                    objeto["tipo_envio_text"] = ele2.selectedOptions[0].text;
                }
                if( ele2.dataset.name === "embase_embalaje"){
                    objeto["embase_embalaje_SUNAT"] = general.findUnitOfMeasureSUNAT(ele2.value)?.id || general.defaultUnitMeasureSUNAT.id;
                }
            }
            if (!general.isObjEmpty(objeto)) {
                detalles.push(objeto);
            }
        }
        return detalles;
    };
    const dataFormPrincipal = (form, idCliente) => {
        let datos = new FormData(form);

        if (datos.has("valor_complejo_distante")) {
            datos.append(
                "ciudad_alejada",
                document.getElementById("id-tipo-complejo-distante").dataset
                    .tipo
            );
        }
        datos.append(
            "nro_comp_cli",
            [...new Set(comprobantesAgregados.series)].join(";")
        );
        datos.append(
            "comp_clie",
            [...new Set(comprobantesAgregados.tipos)].join(";")
        );
        datos.append("id_cliente_facturacion", idCliente);
        datos.append("tipo_guia", tipoCbRemi);
        datos.append(
            "comprobantes_general",
            JSON.stringify(comprobantesGenerales)
        );

        return datos;
    };
    const $formTransportista = document.getElementById(
        "guia-transportista-form"
    );
    const generarComprobanteFetch = (url, data) => {
        $(general.$loader).show();
        fetch("/intranet/facturacion-sunat/" + url, {
            method: "POST",
            body: data,
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.errors) {
                    throw new Error(data.errors);
                }
                window.open(`/storage/documentos/${data.serie_cpe}-${data.correlativo_cpe}.pdf`, "_target");
                alertify.alert("Mensaje", "Comprobante generado correctamente", () =>
                    window.location.reload()
                );
            })
            .catch((error) => {
                console.log(error.message);
                console.error(error);
                alertify.alert("Error",error.message);
            })
            .finally(() => $(general.$loader).hide());
    };
    $formTransportista.onsubmit = async (event) => {
        event.preventDefault();
        let idGuia = await generarGuia(frmGuia, false);
        if(!idGuia){
            return false;
        }
        let data = new FormData($formTransportista);
        data.append("guia", idGuia);
        generarComprobanteFetch("guia-remision-transportista", data);
    };
    const generarGuia = async (form, reload) => {
        const idCliente =
            tipoCbRemi == "proveedor"
                ? $cbCliente.selectedOptions[0].getAttribute("data-id-cliente")
                : $($cbCliente).val();
        let datos = dataFormPrincipal(form, idCliente);
        let detalles = obtenerDatosDetalle();
        detalles = detalles.map(({tipo_envio_text, ...resto}) => resto);
        if (detalles.length) {
            datos.append("detalle_guia", JSON.stringify(detalles));
        }
        if (
            !detalles.length &&
            document.getElementById("id_estado").value != "3"
        ) {
            alertify.alert("Mensaje", "Por favor agregue detalles a la guía");
            return;
        }
        const textOs = $("#select-os").prop("checked")
            ? "Orden de Servicio"
            : "Guía";
        const continuar = await general.alertifyConfirmPromise(
            "Alerta",
            "Al continuar se generará una nueva " +
                textOs +
                ".<br>¿Deseas continuar?"
        );
        if (!continuar) return null;
        if (newCliCli) {
            const guardarCliente = await general.alertifyConfirmPromise(
                "Mensaje",
                "Se ha detectado un nuevo cliente.<br>¿Deseas guardarlo?"
            );
            datos.append("add_cli_bool", guardarCliente);
            if (guardarCliente) {
                datos.append("add_cli_cli", idCliente);
            }
        }
        $(general.$loader).show();
        const guia = await addGuia(datos, reload);
        $(general.$loader).hide();
        return guia;
    };
    const colocarDatosInputCB = function (event) {
        const opcionSelccionada = this.selectedOptions[0];
        const $selectores = event.target.dataset.input.split(',');
        const values = event.target.dataset.value.split(',');
        $selectores.forEach((selector,key) => {
            document.querySelector(selector).value = opcionSelccionada.dataset[values[key]]
        })
        
    };
    const habilitarDeshHabilitarInputs = (clase, valor) => {
        for (const element of document.querySelectorAll(`.${clase}`)) {
            const inputs = element.querySelector("input, select");
            element.hidden = valor;
            if (!inputs) {
                continue;
            }
            inputs.value = "";
            inputs.disabled = valor;
        }
    };
    const modTrasladoDOM = function (event) {
        const classOculto =
            event.target.id === "mod-trans-public"
                ? "mod-trans-priva"
                : "mod-trans-public";

        habilitarDeshHabilitarInputs(classOculto, !event.checked);
        habilitarDeshHabilitarInputs(event.target.id, false);
    };
    for (const cbRelacion of document.querySelectorAll(".cb-relacionado")) {
        cbRelacion.addEventListener("change", colocarDatosInputCB);
    }
    for (const cbRadioTipoMod of document.querySelectorAll(
        ".cambio-mod-traslado"
    )) {
        cbRadioTipoMod.addEventListener("change", modTrasladoDOM);
    }
    const $btnSeleccionRemitente = document.getElementById(
        "btn-seleccion-remitente"
    );
    const $btnSeleccionTransportista = document.getElementById(
        "btn-seleccion-transportista"
    );
    $btnSeleccionTransportista.onclick = (event) => {
        colocarComprobantes("#cb-comprobantes-transportista");
        datosFormularioLlenar("trans");
        llenarDetaalesGuias(
            "items-guia-transportista",
            "bulto-total-transportista"
        );
        toogleModalesGuias("#guia-transportista", "#seleccion-comprobantes");
        llenarObservacionesGuiaTransportista();
    };
    $btnSeleccionRemitente.onclick = (event) => {
        datosFormularioLlenar("");
        llenarDetaalesGuias("items-guia-remitente", "bulto-total-remitente");
        toogleModalesGuias("#guia-remitente", "#seleccion-comprobantes");
    };
    const datosFormularioLlenar = (id) => {
        const data = {
            "txt-fecha-emision": document.getElementById("id_fech_emis").value,
            "txt-fecha-traslado": document.getElementById("id_fech_tras").value,
            "txt-emi-razon-social":
                document.getElementById("id_remi_nomb").selectedOptions[0].text,
            "txt-emi-tipo-doc":
                document.getElementById("id_remi_tipo_docu").value,
            "txt-emi-num-doc":
                document.getElementById("id_remi_nro_docu").value,
            "txt-partida":
                document.getElementById("id_remi_dire").value,
            "txt-dest-razon-social":
                document.getElementById("id_dest_nomb").selectedOptions[0].text,
            "txt-dest-tipo-doc":
                document.getElementById("id_dest_tipo_docu").value,
            "txt-dest-num-doc":
                document.getElementById("id_dest_nro_docu").value,
            "txt-llegada":
                document.getElementById("id_dest_dire").value
        };
        for (const key in data) {
            if (!Object.hasOwn(data, key)) continue;
            const element = !id
                ? document.getElementById(key)
                : document.getElementById(key + "-" + id);
            if(!element){
                continue;
            }
            element.value = data[key];
        }
    };
    const totalBultoKG = () => {
        const detalles = obtenerDatosDetalle();
        return detalles.reduce(
            (acumulador, elementoActual) =>
                acumulador + parseFloat(elementoActual.kg),
            0
        );
    };
    const colocarComprobantes = function (idCbComprobante) {
        const comprobantes = $("#id_nro_comp_cli").val() || [];
        comprobantes.forEach((comprobante) => {
            $(idCbComprobante).append(
                new Option(comprobante, comprobante, true, true)
            );
        });
        $(idCbComprobante).val(comprobantes).trigger("change");
    };
    const llenarObservacionesGuiaTransportista = () => {
        const detalles = obtenerDatosDetalle();
        let template = "";
        detalles.forEach((detalle, index) => {
            template += `${detalle.cantidad} ${detalle.embase_embalaje} - ${detalle.kg} Kg - ${detalle.largo} Lar., ${detalle.ancho} Anc., ${detalle.altura} Alt\n`;
        });
        document.querySelector("#guia-transportista-form #txt-observaciones").value = template;
    }
    const llenarDetaalesGuias = (tbody, totalBultos) => {
        const detalles = obtenerDatosDetalle();
        let template = "";
        detalles.forEach((detalle, index) => {
            const detalleStr = `${detalle.grupo_envio} - ${detalle.tipo_envio_text}`;
            const unit = general.findUnitOfMeasureSUNAT(detalle.embase_embalaje);
            template += `<tr>
                <td>${index + 1}</td>
                <td>${detalle.descripcion ? detalleStr + " - " + detalle.descripcion : detalleStr}</td>
                <td>${detalle.cantidad}</td>
                <td>${unit ? unit.unit : general.defaultUnitMeasureSUNAT.unit}</td>
            </tr>`;
        });
        document.getElementById(tbody).innerHTML = template;
        document.getElementById(totalBultos).textContent = totalBultoKG();
    };
    const toogleModalesGuias = (modalMostrar, modalOcultar) => {
        $(modalOcultar).modal("hide");
        setTimeout(() => {
            $(modalMostrar).modal("show");
        }, 500);
    };
    frmGuia.addEventListener("submit", async function (event) {
        event.preventDefault();
        const botonPresionado = event.submitter;
        if (botonPresionado.value == "generar") {
            return generarGuia(frmGuia, true);
        }
        $("#seleccion-comprobantes").modal("show");
    });
    $(".calculo-dia").on("select2:select", function () {
        general.calculoDiasTrancitoCb(
            "#id_remi_depa",
            "#id_dest_depa",
            "#id_dest_prov",
            "#id_dias_tran",
            "#id_via_envi"
        );
        const distrito =
            $(this).attr("data-cli-cli") == "distrito"
                ? $(this).val()
                : $($(this).attr("data-distrito")).val();
        const tipo =
            $(this).attr("data-cli-cli") == "distrito" ? "distrito" : "ciudad";
        general.consultaComplejoDistante(
            distrito,
            tipo,
            "#box-complejo-distante",
            tipo == "ciudad" ? $(this).val() : null
        );
    });
    async function addGuia(datos, reload) {
        try {
            let response = await general.funcfetch("guias/agregar-guia", datos);
            if (response.error) {
                alertify.alert("Error", response.error);
                return;
            }
            if (response.alerta) {
                alertify.alert("Alerta", response.alerta);
                return;
            }
            newCliCli = false;
            if (!reload) return response?.id_guia;
            alertify.alert("Mensaje", response.success, () => {
                window.location.reload();
            });
        } catch (error) {
            console.log(error);
            console.error(error);
            alertify.alert("Error", error);
        }
    }
    document
        .getElementById("id_estado")
        .addEventListener("change", function (e) {
            general.changeTipoGuia(this.value);
        });
    document
        .getElementById("add-new-detalle")
        .addEventListener("click", function (e) {
            general.agregarNuevoDetalleGuia("id_estado", ".simple-select2");
            indicesNavegaciones.push(0);
            if (navDetalleEnvios[0]) {
                navegarValores(
                    tableDetalleGuia.children[
                        tableDetalleGuia.children.length - 1
                    ],
                    navDetalleEnvios[0]
                );
            }
        });
    $("#id_dest_nomb").on("select2:select", async function (e) {
        newCliCli = await general.getDataDestGuia(
            this,
            "#id_remi_depa",
            "#id_dest_tipo_docu",
            "#id_dest_depa",
            "#id_dest_prov",
            "#id_dest_dist",
            "#id_ciudad_destino_final",
            "#id_dias_tran",
            "#id_via_envi",
            "#box-complejo-distante",
            0
        );
    });
    const btnAgregarComprobante = document.querySelector(
        "#btn-agregar-comprobantes"
    );
    const contenidoComprobantes = document.querySelector("#lista-comprobantes");
    const guardarComprobantes = document.querySelector("#guardar-comprobantes");
    const valoresComprobantes = (tipoComprobante) => {
        switch (tipoComprobante) {
            case "FACTURA ELECTRÓNICA":
                return "F";
                break;
            case "GUIA DE REMISIÓN (R) ELECTRÓNICA":
                return "G";
                break;
            case "BOLETA ELECTRÓNICA":
                return "B";
                break;
            case "ORDEN DE COMPRA":
                return "OC";
                break;
            case "OTRO":
                return "OT";
                break;
            case "GUIA DE INTERNAMIENT":
                return "GI";
                break;
            default:
                return "";
                break;
        }
    };
    guardarComprobantes.onclick = (e) => {
        $("#id_nro_comp_cli").empty();
        comprobantesAgregados.tipos = [];
        comprobantesAgregados.series = [];
        comprobantesGenerales = [];
        for (const element of contenidoComprobantes.children) {
            comprobantesAgregados.tipos.push(
                element.querySelector('[data-type="comprobante"]').value
            );
            if (
                element.querySelector('[data-type="comprobante"]').value ===
                "NINGUNO"
            ) {
                continue;
            }
            comprobantesGenerales.push({
                tipo: element.querySelector('[data-type="comprobante"]').value,
                serie: element.querySelector('[data-type="serie"]').value,
            });
            comprobantesAgregados.series.push(
                `${valoresComprobantes(
                    element.querySelector('[data-type="comprobante"]').value
                )}/${element.querySelector('[data-type="serie"]').value}-${
                    element.querySelector('[data-type="numero"]').value
                }`
            );
        }
        comprobantesAgregados.series.forEach(function (valor) {
            $("#id_nro_comp_cli").append(new Option(valor, valor, true, true));
        });
        $("#id_comp_clie").val(comprobantesAgregados.tipos).trigger("change");
        $("#id_cant_comp").val(comprobantesAgregados.series.length);
        $("#agregar-comprobantes").modal("hide");
    };
    contenidoComprobantes.onclick = function (e) {
        if (e.target.classList.contains("btn-danger")) {
            if (contenidoComprobantes.children.length === 1) {
                return alertify.alert(
                    "Mensaje",
                    "Como mínimo debe haber un comprobante"
                );
            }
            e.target.closest("li").remove();
        }
    };
    btnAgregarComprobante.onclick = (e) => {
        $("#lista-comprobantes select").select2("destroy");
        const liCopia = contenidoComprobantes.children[0].cloneNode(true);
        for (const element of liCopia.querySelectorAll("input, select")) {
            element.value = "";
            if (element.dataset.type === "serie") {
                element.innerHTML = "";
            }
        }
        contenidoComprobantes.append(liCopia);
        for (const element of contenidoComprobantes.querySelectorAll(
            "select"
        )) {
            $(element).select2({
                theme: "bootstrap",
                width: "100%",
                placeholder: element.classList.contains("simple-select2")
                    ? "Seleccione una opción"
                    : "Seleccione o agregue una opción",
                tags: !element.classList.contains("simple-select2"),
            });
            if (element.dataset.type === "comprobante") {
                $(element).on("select2:select", generarSeries);
            }
        }
    };
    function generarSeries(e) {
        console.log(e);
        let selectedOption = e.target.selectedOptions[0];
        let series = selectedOption.getAttribute("data-series");
        e.target.closest("li").querySelector("[data-type='serie']").innerHTML =
            "";
        if (series) {
            series = series.split(";");
            series.forEach((serie) => {
                e.target
                    .closest("li")
                    .querySelector("[data-type='serie']")
                    .append(new Option(serie, serie));
            });
        }
    }
    $("#agregar-comprobantes [data-type='comprobante']").on(
        "select2:select",
        generarSeries
    );
}
function calcVolumen(event) {
    if (parseFloat(event.target.value) < 0 || isNaN(event.target.value)) {
        event.target.value = 0;
    }
    const fila = event.target.parentElement.parentElement;
    let total = 1;
    for (let i = 6; i < 9; i++) {
        total = total * parseFloat(fila.children[i].children[0].value);
    }
    fila.children[9].children[0].value = (total / 6000).toFixed(2);
    const e = new Event("change");
    fila.children[9].children[0].dispatchEvent(e);
}
function calcTotal(event, num, dom) {
    if (parseInt(event.target.value) < 0 || isNaN(event.target.value)) {
        event.target.value = 0;
    }
    const filaTotal = document.getElementById("tabla-detalle");
    let total = 0;
    let isFloat = false;
    for (let i = 0; i < filaTotal.children.length; i++) {
        isFloat =
            filaTotal.children[i].children[num].children[0].getAttribute(
                "step"
            ) == "0.01"
                ? true
                : false;
        total += isFloat
            ? parseFloat(filaTotal.children[i].children[num].children[0].value)
            : parseInt(filaTotal.children[i].children[num].children[0].value);
    }
    document.getElementById(dom).value = isFloat ? total.toFixed(2) : total;
}
window.addEventListener("DOMContentLoaded", pageLoaded);
