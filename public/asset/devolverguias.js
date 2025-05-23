function loadPage() {
    let general = new General();
    let cbSelectAll = document.getElementById("id_check_todo");
    let tblGuia = document.getElementById("tabla-cuerpo-guias");

    let tabalGuias = $("#tabla-retorno-guia").DataTable({
        ajax: {
            url: "por-retornar",
            method: "POST",
            headers: general.jsonToken,
        },
        columns: [
            {
                data: "id",
                searchable: false,
                orderable: false,
                render: function (data) {
                    return `<div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input check-guia" id="id_check_guia_${data}" value="${data}">
                    <label class="custom-control-label" for="id_check_guia_${data}"></label>
                </div>`;
                },
            },
            {
                data: "nro_guia",
                render: function (data, type, row) {
                    return row.retorno_rechazo === 1
                        ? `<span>${data}</span><button class="btn btn-sm btn-danger" style="font-size:8px;" data-guia="${row.id}"><i class="fas fa-info"></i></button>`
                        : data;
                },
            },
            { data: "remi_nro_docu" },
            { data: "nom_cli_remitente" },
            { data: "dest_nro_docu" },
            { data: "nom_cli_destinatario" },
            { data: "trayecto_envio" },
            { data: "remi_prov" },
            { data: "dest_prov" },
        ],
        createdRow: function (row, data) {
            let bgTable = "";
            if (data.retorno_rechazo === 1) {
                bgTable = "table-danger";
            }
            if (data.situ == 14) {
                bgTable = "table-warning";
            }
            $(row).addClass(bgTable);
        },
    });
    document.querySelector("#tabla-retorno-guia").onclick = async (event) => {
        if(event.target.classList.contains('btn-danger')){
            try {
                const response = await fetch("guias-rechazadas/" + event.target.dataset.guia).then(response => response.json());
                if(!response.data) {
                    return alertify.alert("Mensaje","No se encontraron rechazos para mostrar");
                }
                renderMotivosRechazos(response.data);
                $("#embarque-rechazos").modal("show")
            } catch (error) {
                alertify.error("error al mostrar los rechazos");
                console.error(error)
            }
        }
    }
    function renderMotivosRechazos(rechazos){
        let template = "";
        rechazos.forEach(rechazo => {
            template += `<div class="card" style="font-size:0.75rem;">
                <div class="card-body p-2">
                <h5 style="font-size:1rem;" class="text-primary"> Rechazado por ${rechazo.responsable}</h5>
                <h6 style="font-size:0.75rem;" class="card-subtitle mb-2 text-muted">${rechazo.fecha_hora}</h6>
                <strong>Motivos:</strong>
                <ul class="card-text">
                    <li>
                    ${rechazo.detalles.join("</li><li>")}
                    </li>
                </ul>`
            template += rechazo.otros_detalles ? `<p>${rechazo.otros_detalles}</p>` : '';
            template += '</div></div>'
        });
        return document.getElementById("motivos-rechazo").innerHTML = template;
    }
    cbSelectAll.addEventListener("change", function () {
        const activo = this.checked;
        for (const tr of tblGuia.children) {
            if (tr.querySelector(`.check-guia`)) {
                tr.querySelector(`.check-guia`).checked = activo;
            }
        }
    });
    tabalGuias.on("draw", function () {
        for (const c of document.querySelectorAll(".check-guia")) {
            c.addEventListener("change", function () {
                let checked = tblGuia.querySelectorAll("input:checked").length;
                if (checked == tblGuia.children.length) {
                    cbSelectAll.checked = true;
                } else {
                    cbSelectAll.checked = false;
                }
            });
        }
    });
    for (const e of document.querySelectorAll(".calc-total")) {
        e.addEventListener("change", function (e) {
            document.getElementById("total_bultos").value = general.totalBultos(
                this.parentElement.parentElement,
                ".calc-total"
            );
        });
    }
    const btnFinalizarEmbarque = document.getElementById(
        "btn-guardar-guias-embarcar"
    );
    const frmCargos = document.getElementById("retornar-cargos");
    const btnGuias = document.getElementById("guardar_guias");
    const btnEmbarcarGuias = document.getElementById("embarcar_guias");
    btnFinalizarEmbarque.addEventListener("click", function (e) {
        document.getElementById("btn-terminar-embarque").click();
    });
    btnEmbarcarGuias.addEventListener("click", async function (e) {
        e.preventDefault();
        let datos = new FormData();
        datos.append("tipo", "consultas_guias");
        let response = await general.funcfetch("retornar", datos);
        if (response.error) {
            alertify.alert("Error", response.error);
            return;
        }
        const bodyModal = document.getElementById("tabla-guias-modal");
        bodyModal.onclick = async (e) => {
            if (e.target.classList.contains("btn-danger")) {
                let datos = new FormData();
                datos.append("tipo", "eliminar-guias-guardadas");
                datos.append("guia", e.target.dataset.guia);
                const response = await general.funcfetch(
                    "/intranet/cluster/embarcar/retornar",
                    datos
                );
                if (response.success) {
                    e.target.closest("tr").remove();
                    return alertify.success("guia eliminada correctamente");
                }
            }
        };
        if (!response.guias.length) {
            bodyModal.innerHTML = `<tr><td colspan="5">No se encontró guias guardadas</td></tr>`;
        } else {
            let template = "";
            response.guias.forEach((g) => {
                template += `<tr data-type="old" class="table-primary" data-guia="${g.id}">
                    <td>${g.nro_guia}</td>
                    <td>${g.remi_nro_docu}</td>
                    <td>${g.nom_cli_remitente}</td>
                    <td>${g.dest_nro_docu}</td>
                    <td>${g.nom_cli_destinatario}</td>
                    <td><button type='button' data-guia="${g.id}" class='btn btn-sm btn-danger'><i class="fas fa-trash"></i></button></td>
                </tr>`;
            });
            bodyModal.innerHTML = template;
        }
        if (tblGuia.querySelectorAll("input:checked").length > 0) {
            if (!response.guias.length) {
                bodyModal.innerHTML = "";
            }
            for (const elemento of tblGuia.querySelectorAll("input:checked")) {
                const tr = elemento.parentElement.parentElement.parentElement;
                const createTr = document.createElement("tr");
                createTr.dataset.type = "new";
                createTr.className = "table-warning";
                createTr.dataset.guia = elemento.value;
                const template2 = `
                    <td>${tr.children[1].textContent}</td>
                    <td>${tr.children[2].textContent}</td>
                    <td>${tr.children[3].textContent}</td>
                    <td>${tr.children[4].textContent}</td>
                    <td>${tr.children[5].textContent}</td>
                    <td>-</td>
                `;
                createTr.innerHTML = template2;
                bodyModal.append(createTr);
            }
        }
        if (
            !response.guias.length &&
            !tblGuia.querySelectorAll("input:checked").length
        ) {
            alertify.error(
                "no se encontro ninguna guia guardada ni seleccionada"
            );
            return;
        }
        $("#guardar-detalles-nuevo").modal("show");
    });
    btnGuias.addEventListener("click", function (e) {
        e.preventDefault();
        let datos = new FormData();
        let guias = new Array();
        if (!tblGuia.querySelectorAll("input:checked").length) {
            alertify.error("por favor seleccione una o más guias");
            return;
        } else {
            for (const elemento of tblGuia.querySelectorAll("input:checked")) {
                guias.push({
                    id_guia: elemento.value,
                });
            }
            datos.append("guias", JSON.stringify(guias));
        }
        alertify.confirm(
            "Mensaje",
            "¿Deseas guardar estas guías?",
            async () => {
                datos.append("tipo", "guardar_guias");
                let response = await general.funcfetch("retornar", datos);
                if (response.error) {
                    alertify.alert("Error", response.error);
                    return;
                }
                alertify.success(response.success);
                tabalGuias.draw();
            },
            () => {}
        );
    });
    const btnAgregarImg = document.querySelector("#btn-new-img");
    const fileImg = document.querySelector("#input-img-new");
    const contenidoImg = document.querySelector("#list-img");
    let newImages = [];
    btnAgregarImg.onclick = (e) => fileImg.click();
    function ImageDelivery(nomFile, nomIdFile, idImg = null) {
        const contenedor = document.createElement("div");
        contenedor.classList.add("img-delivery");
        contenedor.innerHTML = `<button type="button" class="eliminar-img" title="Eliminar esta imagen" data-id="${idImg}" data-name="${nomFile}"><i class="far fa-trash-alt"></i></button>
            <img src="${nomIdFile}"
            title="${nomFile}"
            alt="Imagen de entrega de la guía">
        </button>`;
        return contenedor;
    }
    let deleteImages = [];
    contenidoImg.onclick = (e) => {
        console.log(e.target.dataset.id);
        if (e.target.classList.contains("eliminar-img")) {
            if (e.target.dataset.id === "null") {
                newImages = newImages.filter(
                    (img) => img.name !== e.target.dataset.name
                );
            } else {
                deleteImages.push(e.target.dataset.id);
            }
            e.target.closest(".img-delivery").remove();
        }
    };
    fileImg.onchange = function (event) {
        const files = Array.from(event.target.files);
        files.forEach((file) => {
            newImages.push(file);
            const reader = new FileReader();
            reader.onload = function (e) {
                contenidoImg.append(ImageDelivery(file.name, e.target.result));
            };
            reader.readAsDataURL(file);
        });
    };
    $("#guardar-detalles-nuevo").on("hidden.bs.modal", function (event) {
        document.querySelector("#btn-guardar-guias-embarcar").textContent =
            "Embarcar";
        frmCargos.reset();
        $("#id_nro_guias").empty();
        $(".simple-select2, .cb-mult-nuevo").trigger("change");
        if (window.retorno) {
            delete window.retorno;
        }
        Array.from(contenidoImg.children).forEach((li, key) => {
            if (key > 1) {
                li.remove();
            }
        });
        newImages = [];
        deleteImages = [];
    });
    function retornarGuias() {
        let guiasGuardadas = new Array();
        let guiasNuevas = new Array();
        for (const elemento of document.getElementById("tabla-guias-modal")
            .children) {
            if (elemento.dataset.type == "old") {
                guiasGuardadas.push({ id_guia: elemento.dataset.guia });
            }
            if (elemento.dataset.type == "new") {
                guiasNuevas.push({ id_guia: elemento.dataset.guia });
            }
        }
        return { guiasGuardadas, guiasNuevas };
    }
    frmCargos.addEventListener("submit", async function (e) {
        e.preventDefault();
        let datos = new FormData(this);
        datos.append(
            "tipo_comprobantes",
            !$("#id_tipo_comprobantes").val()
                ? ""
                : $("#id_tipo_comprobantes").val().join(";")
        );
        datos.append(
            "nro_guias",
            !$("#id_nro_guias").val() ? "" : $("#id_nro_guias").val().join(";")
        );
        if (window.retorno) {
            datos.append("tipo", "editar-retorno");
            datos.append("id_retorno", window.retorno);
            datos.append("deleteImages", deleteImages.join(";"));
        } else {
            datos.append("tipo", "retornar_guias");
            const { guiasGuardadas, guiasNuevas } = retornarGuias();
            if (!guiasGuardadas.length && !guiasNuevas.length) {
                alertify.error("no se encontraron guias para ser embarcadas");
                return;
            }
            datos.append("guias_guardadas", JSON.stringify(guiasGuardadas));
            datos.append("guias_nuevas", JSON.stringify(guiasNuevas));
        }
        if (!general.totalBultos(this, ".calc-total")) {
            alertify.alert(
                "Alerta",
                "El total de bulto debe de ser mayor que 0"
            );
            return;
        }
        newImages.forEach((file) => {
            datos.append("new_images[]", file);
        });
        datos.append("total_bultos", general.totalBultos(this, ".calc-total"));
        let response = await general.funcfetch("retornar", datos);
        if (response.error) {
            alertify.alert("Error", response.error);
            return;
        }
        $("#guardar-detalles-nuevo").modal("hide");
        alertify.alert("Mensaje", response.success, () => {
            window.location.reload();
        });
    });
}
window.addEventListener("DOMContentLoaded", loadPage);
