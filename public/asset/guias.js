function pageLoaded() {
    let general = new General();
    let newCliCli = false;
    let tipoCbRemi = null;
    let comprobantesAgregados = {
        tipos: [],
        series: [],
    };
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

    $("#id-invitado-remitente").on("select2:select", async function (e) {
        general.getCCInvitados("#id-invitado-remitente", $cbCliente);
    });
    let frmGuia = document.getElementById("frm_guia");
    frmGuia.addEventListener("submit", async function (event) {
        event.preventDefault();
        let datos = new FormData(this);
        const idCliente =
            tipoCbRemi == "proveedor"
                ? $cbCliente.selectedOptions[0].getAttribute("data-id-cliente")
                : $($cbCliente).val();
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
        let detalles = new Array();
        for (const e of tableDetalleGuia.children) {
            let objeto = new Object();
            let deta = e.querySelectorAll("input, select, textarea");
            for (const ele2 of deta) {
                objeto[ele2.dataset.name] = ele2.value;
            }
            if (!general.isObjEmpty(objeto)) {
                detalles.push(objeto);
            }
        }
        if (
            !detalles.length &&
            document.getElementById("id_estado").value != "3"
        ) {
            alertify.alert("Mensaje", "Por favor agregue detalles a la guía");
            return;
        }
        if (detalles.length) {
            datos.append("detalle_guia", JSON.stringify(detalles));
        }
        const textOs = $("#select-os").prop("checked")
            ? "Orden de Servicio"
            : "Guía";
        const confirmDatos = alertify.confirm(
            "Alerta",
            "Al continuar se generará una nueva " +
                textOs +
                ", asegúrese de que estén correctamente llenadas.<br>¿Deseas continuar de todas maneras?",
            null,
            null
        );
        confirmDatos.set("onok", function () {
            if (newCliCli) {
                setTimeout(function () {
                    const nuevoCliente = alertify.confirm(
                        "Mensaje",
                        "Se a detectado un nuevo cliente del cliente. <br> ¿Deseas guardarlo para la proxima vez?",
                        null,
                        null
                    );
                    nuevoCliente.set({
                        invokeOnCloseOff: true,
                        closable: false,
                        labels: {
                            ok: "Si",
                            cancel: "No",
                        },
                    });
                    nuevoCliente.set("onok", function () {
                        datos.append("add_cli_bool", true);
                        datos.append("add_cli_cli", idCliente);
                        addGuia(datos);
                    });
                    nuevoCliente.set("oncalcel", function () {
                        datos.append("add_cli_bool", false);
                        addGuia(datos);
                    });
                }, 200);
            } else {
                addGuia(datos);
            }
        });
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
    async function addGuia(datos) {
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
            alertify.alert("Mensaje", response.success, () => {
                window.location.reload();
            });
        } catch (error) {
            console.error(error);
            alertify.alert("Error", error.message);
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
        for (const element of liCopia.querySelectorAll("input")) {
            element.value = "";
        }
        contenidoComprobantes.append(liCopia);
        for (const element of contenidoComprobantes.querySelectorAll(
            "select"
        )) {
            $(element).select2({
                theme: "bootstrap",
                width: "100%",
                placeholder: element.classList.contains("simple-select2") ? "Seleccione una opción" : "Seleccione o agregue una opción",
                tags: !element.classList.contains("simple-select2"),
            });
        }
    };
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
