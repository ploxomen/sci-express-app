function loadedPage() {
    let general = new General();
    let tablaDestinatarios = document.getElementById("contenido-destinatario");
    let dataTableDestinatario = $("#tabla-destinatario").DataTable({
        ajax: {
            url: "obtener",
            method: "POST",
            headers: general.jsonToken,
            // data: function (d) {
            //     d.estadoDoc = $("#id_estado_doc_cli").val();
            // },
        },
        columns: [
            { data: "id", name: "index" },
            {
                data: "nro_doc",
                name: "nro_doc",
                render: function (data, type, full, meta) {
                    return full.tipo && full.nro_doc
                        ? `<span>${full.tipo} - ${full.nro_doc}</span>`
                        : "";
                },
            },
            { data: "nombres", name: "nombres" },
            { data: "direccion", name: "direccion" },
            {
                data: "activo",
                name: "activo",
                render: function (data, type, full, meta) {
                    return `<span class="${
                        data == 0 ? "text-danger" : "text-success"
                    }">${data == 0 ? "INACTIVO" : "ACTIVO"}</span>`;
                },
            },
            {
                data: "id",
                searchable: false,
                orderable: false,
                name: "id",
                render: function (data) {
                    return `<div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><a href="agregar-modificar/${data}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Editar destinatario"><i class="fas fa-user-edit"></i></a><button data-toggle="tooltip" title="Eliminar destinatario" data-destinatario="${data}" class="btn btn-sm btn-danger delete-destinatario"><i class="fas fa-user-times"></i></button></div>`;
                },
            },
        ],
    });
    tablaDestinatarios.addEventListener("click", function (event) {
        if (event.target.classList.contains("delete-destinatario")) {
            let nameCli =
                event.target.parentElement.parentElement.parentElement
                    .children[1].textContent;

            alertify.confirm(
                "Mensaje",
                "¿Deseas eliminar al destinatario <b>" + nameCli + "</b>?",
                async () => {
                    try {
                        let response = await general.funcfetch(
                            `eliminar/${event.target.dataset.destinatario}`,
                            null,
                            "DELETE"
                        );
                        if (response.success) {
                            dataTableDestinatario.draw();
                            alertify.success(response.success);
                            return;
                        }
                        alertify.alert("Error", response.error);
                    } catch (error) {
                        console.log(error);
                    }
                },
                () => {}
            );
        }
    });
    dataTableDestinatario.on("draw.dt", function () {
        let info = dataTableDestinatario.page.info();
        dataTableDestinatario
            .column(0, { search: "applied", order: "applied", page: "applied" })
            .nodes()
            .each(function (cell, i) {
                cell.innerHTML = i + 1 + info.start;
            });
    });
    // $('.cb-filter').change(function(){
    //     dataTableDestinatario.draw();
    // });
}
window.addEventListener("DOMContentLoaded", loadedPage);
