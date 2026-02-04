function loadPage() {
    const general = new General();
    const tablaGuias = $("#tabla-guias").DataTable({
        ajax: {
            url: "sunat/lista",
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
                    `
                },
            },
        ],
    });
    document.querySelector("#frm-aplicar-filtros").addEventListener("submit", function (e) {
        e.preventDefault();
        tablaGuias.ajax.reload();
    });
}
window.addEventListener("DOMContentLoaded", loadPage);
