function loadPage() {
    const general = new General();
    let seleccionados = new Set();
    $(general.$loader).hide();
    const tablaGuias = $("#tabla-guias").DataTable({
        ajax: {
            url: "/intranet/comprobante/sunat/lista",
            method: "GET",
            headers: general.jsonToken,
            data: function (d) {
                d.tipo = $("#tipo_comprobantes").val();
                d.fechaInicio = $("#fecha_inicio").val();
                d.fechaFin = $("#id_fecha_fin").val();
                d.aceptados = 'true';
            },
        },
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    // Verifica si el ID está seleccionado
                    let checked = seleccionados.has(row.comprobante_sunat) ? "checked" : "";

                    return `
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ${checked} value="${row.comprobante_sunat}" id="checkboxGuias${row.comprobante_sunat}}">
                        <label class="custom-control-label" for="checkboxGuias${row.comprobante_sunat}}"></label>
                    </div>
                `;
                },
            },
            {
                data: "comprobante_sunat",
            },
            {
                data: "fech_emis",
            },
            {
                data: "nro_guia",
            },
            {
                data: "ruc_destinatario",
            },
            {
                data: "nombre_destinatario",
            },
            {
                data: "tipo_comprobante_sunat",
            },
        ],
    });
    document
        .querySelector("#frm-aplicar-filtros")
        .addEventListener("submit", function (e) {
            e.preventDefault();
            tablaGuias.ajax.reload();
        });
    document.querySelector("#tabla-detalle-guias").onclick = (evento) => {
        if (evento.target.classList.contains("custom-control-input")) {
            const comprobante = evento.target.value;
            if (evento.target.checked) {
                seleccionados.add(comprobante);
            } else {
                seleccionados.delete(comprobante);
            }
            viewComprobanteSeleccionado();
        }
    };
    tablaGuias.on("draw", function () {
        for (const element of document.querySelectorAll(
            "#tabla-detalle-guias .custom-control-input",
        )) {
            const comprobante = element.value;
            if (seleccionados.has(comprobante)) {
                element.cheched = true;
            }
        }
    });
    const contenidoComprobante = document.querySelector(
        "#comprobantes-seleccionados",
    );
    const viewComprobanteSeleccionado = () => {
        contenidoComprobante.innerHTML = "";
        seleccionados.forEach((seleccionado) => {
            let badge = document.createElement("div");
            badge.className =
                "!text-xs font-weight-bold text-slate-500 bg-slate-100 rounded-0 px-2 py-1 my-2";
            badge.innerText = seleccionado;
            contenidoComprobante.append(badge);
        });
    };
    const generarPDFs = (event) => {
        const ids = [...seleccionados];
        if (!ids.length) {
            return alertify.alert(
                "Mensaje",
                "Debe seleccionar al menos un comprobante",
            );
        }
        const params = new URLSearchParams();

        ids.forEach((id) => {
            params.append("ids[]", id);
        });
        params.append("tipo_impresion", event.target.dataset.type)

        window.open(`/intranet/comprobante/sunat/impresion/masivo?${params.toString()}`, "_blank");
    };
    document.querySelector("#imprimir-comp-tic").onclick = generarPDFs;
    document.querySelector("#imprimir-comp-a4").onclick = generarPDFs;

}
window.addEventListener("DOMContentLoaded", loadPage);
