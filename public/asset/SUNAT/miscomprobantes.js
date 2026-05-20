function loadPage() {
    const general = new General();
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
            },
        },
        columns: [
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
            {
                data: "id",
                searchable: false,
                orderable: false,
                name: "id",
                render: function (data, type, full) {
                    return `
                    <div class="flex gap-2 justify-center">
                        <a href="/intranet/comprobante/sunat/consulta/001/${data}" class="btn btn-sm btn-primary" title="Ver Comprobante SUNAT A4" target="_blank">
                            <i class="fas fa-print"></i>
                        </a>
                        <a href="/intranet/comprobante/sunat/consulta/004/${data}" class="btn btn-sm btn-primary" title="Ver Comprobante SUNAT TICKET" target="_blank">
                            <i class="fas fa-print"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-eliminar-comprobante-sunat" data-id="${data}" title="Desasociar Comprobante SUNAT">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    `;
                },
            },
        ],
    });
    document
        .querySelector("#frm-aplicar-filtros")
        .addEventListener("submit", function (e) {
            e.preventDefault();
            tablaGuias.ajax.reload();
        });
    document.getElementById("asignacion-comprobante").onclick = function () {
        fetch("sunat/sin-asignar", {
            method: "GET",
            headers: general.jsonToken,
        })
            .then((response) => response.json())
            .then((data) => {
                if (!data || data.length === 0) {
                    alertify.error("No hay guías sin comprobante SUNAT.");
                    return;
                }
                let opciones = '<option value="">Seleccione una guía</option>';
                data.forEach((guia) => {
                    opciones += `<option value="${guia.id}">${guia.nro_guia}</option>`;
                });
                document.getElementById("nro_guia_sci").innerHTML = opciones;
                $("#comprobante-asignacion").modal("show");
            })
            .catch((error) => {
                console.error(
                    "Error al obtener las guías sin comprobante SUNAT:",
                    error,
                );
                alertify.error("Error al cargar las guías disponibles.");
            });
    };
    const $frmAsignacionComprobante = document.getElementById(
        "frm-asignacion-comprobante",
    );
    $frmAsignacionComprobante.onsubmit = function (e) {
        e.preventDefault();
        const formData = new FormData($frmAsignacionComprobante);
        $(general.$loader).show();
        fetch("sunat/asociar", {
            method: "POST",
            headers: general.jsonToken,
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alertify.success(data.message);
                    $("#comprobante-asignacion").modal("hide");
                    tablaGuias.ajax.reload();
                    return;
                }
                throw new Error(data.message || "Error desconocido");
            })
            .catch((error) => {
                console.error("Error al asignar el comprobante SUNAT:", error);
                alertify.error(error.message);
            })
            .finally(() => {
                $(general.$loader).hide();
            });
    };
    document.getElementById("tabla-guias").onclick = async function (e) {
        if (e.target.closest(".btn-eliminar-comprobante-sunat")) {
            const confirmar = await general.alertifyConfirmPromise(
                "Desasociar Comprobante SUNAT",
                "Para desasociar este comprobante asegurate de haberlo ANULADO en SUNAT y el proveedor de la API ¿Deseas continuar con la anulación?",
            );
            if (!confirmar) {
                return;
            }
            $(general.$loader).show();
            const idGuia = e.target.getAttribute("data-id");
            fetch(`sunat/desasociar/${idGuia}`, {
                method: "PUT",
                headers: general.jsonToken,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alertify.success(data.message);
                        tablaGuias.ajax.reload();
                    } else {
                        alertify.error(
                            data.message ||
                                "Error al anular el comprobante SUNAT.",
                        );
                    }
                })
                .catch((error) => {
                    console.error(
                        "Error al anular el comprobante SUNAT:",
                        error,
                    );
                    alertify.error("Error al anular el comprobante SUNAT.");
                })
                .finally(() => {
                    $(general.$loader).hide();
                });
        }
    };
    document.getElementById("consulta-guia-sci").onclick = async function (e) {
        e.preventDefault();
        const idGuia = document.getElementById("nro_guia_sci").value;
        if (!idGuia) {
            alertify.error("Por favor, seleccione la guia SCI.");
            return;
        }
        let datos = new FormData();
        datos.append("guia", idGuia);
        datos.append("tipo", "detalle");
        let response = await general.funcfetch(
            "/intranet/comprobante/guias/obtener-detalles",
            datos,
        );
        if (response.error) {
            alertify.alert("Error", response.error);
            return;
        }
        general.verDetalleGuia(response.datos);
    };
}
window.addEventListener("DOMContentLoaded", loadPage);
