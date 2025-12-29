function loadPage() {
    let general = new General();
    const tablaEmbarque = document.getElementById("tabla-embarques");
    const tablaMani = document.getElementById("tabla-mani");
    const cbCluster = document.getElementById("cb-clusters");
    let nro_embarque = null,
        nro_guia = null,
        id_cluster = null,
        nro_mani = null,
        nroguiainfo = null,
        nroFlete = null,
        trayectoEmbarque = null;
    async function obtenerEmbarquesPorLlegar(
        embarque,
        guia,
        tipo,
        validarFuncion,
        valorCluster,
        trayecto
    ) {
        try {
            let datos = new FormData();
            datos.append("tipo", tipo);
            if (embarque) {
                datos.append("embarque", embarque);
            }
            if (cbCluster) {
                datos.append(
                    "id_cluster",
                    tipo == "manifiestos" ||
                        tipo == "detalle-embarque" ||
                        tipo == "detalle-guia"
                        ? valorCluster
                        : $("#cb-clusters").val()
                );
            }
            if (guia) {
                datos.append("guia", guia);
            }
            if (trayecto) {
                datos.append("trayecto", trayecto);
            }
            let response = await general.funcfetch(
                window.location.origin +
                    "/intranet/pedidos/llegada/embarques/arribar",
                datos
            );
            validarFuncion(response);
        } catch (error) {
            console.error(error.message);
            alertify.alert("Error", error.message);
        }
    }
    function llenarTablaEmbarque(recorrer) {
        const valEmbarque = document.getElementById("valor-embarques");
        if (!recorrer.resultados.length) {
            tablaEmbarque.innerHTML = `<tr><td colspan="9" class="text-center">No se encontraron resultados</td></tr>`;
            valEmbarque.value = "0";
            return;
        }
        let template = ``;
        recorrer.resultados.forEach((r) => {
            let iconoTrayecto =
                r.tipo_trayecto == "TERRESTRE"
                    ? `<i class="fas fa-truck text-info"></i>`
                    : `<i class="fas fa-plane text-primary"></i>`;
            template += `<tr>
            <td>${iconoTrayecto} - ${r.nro_embarque}</td>
            <td>${r.fecha_embarque}</td>
            <td>${r.empresa}</td>
            <td>${r.format_fch_llegada}</td>
            <td>${r.nom_cluster}</td>
            <td>${r.comprobantes}</td>
            <td>${r.nro_comprobantes}</td>
            <td>${r.total_bultos_paquete}</td>
            <td>
                <button class="btn btn-primary btn-sm ver-detalles" title="Ver más detalles" data-cluster="${r.idCluster}" data-embarque="${r.id}"><i class="fas fa-eye"></i></button>
                <button class="btn btn-success btn-sm ver-manifiestos-guias" title="Ver manifiestos - guias" data-trayecto="${r.tipo_trayecto}" data-flete="${r.fletes_id}" data-cluster="${r.idCluster}" data-embarque="${r.id}"><i class="fas fa-location-arrow"></i></button>
            </td>
            </tr>`;
        });
        valEmbarque.value = recorrer.resultados.length;
        tablaEmbarque.innerHTML = template;
    }
    obtenerEmbarquesPorLlegar(0, 0, "embarques", llenarTablaEmbarque);
    if (cbCluster) {
        $("#cb-clusters")
            .select2({
                theme: "bootstrap",
                templateResult: general.tempSeleCli,
                width: "100%",
                placeholder: "Seleccione un cliente",
            })
            .on("select2:select", function (e) {
                tablaMani.innerHTML = `<tr><td colspan="9" class="text-center">Seleccione un embarque</td></tr>`;
                obtenerEmbarquesPorLlegar(
                    0,
                    0,
                    "embarques",
                    llenarTablaEmbarque
                );
            });
    }
    tablaEmbarque.addEventListener("click", function (event) {
        nro_embarque = null;
        nroFlete = null;
        if (event.target.classList.contains("ver-manifiestos-guias")) {
            nro_embarque = event.target.dataset.embarque;
            nroFlete = event.target.dataset.flete;
            trayectoEmbarque = event.target.dataset.trayecto;
            if (cbCluster) {
                id_cluster = event.target.dataset.cluster;
            }
            obtenerEmbarquesPorLlegar(
                nro_embarque,
                0,
                "manifiestos",
                llenarTablaMani,
                id_cluster,
                trayectoEmbarque
            );
        }
        if (event.target.classList.contains("ver-detalles")) {
            nro_embarque = event.target.dataset.embarque;
            obtenerEmbarquesPorLlegar(
                nro_embarque,
                0,
                "detalle-embarque",
                detalleEmbarque,
                event.target.dataset.cluster
            );
        }
    });
    function detalleEmbarque(datos) {
        if (!datos.resultados.length) {
            alertify.error("no se encontró detalles de este embarque");
            return;
        }
        for (const key in datos.resultados[0]) {
            if (Object.hasOwnProperty.call(datos.resultados[0], key)) {
                const valor = datos.resultados[0][key];
                const dom = document.getElementById("id_modal_" + key);
                if (!dom) {
                    continue;
                }
                dom.textContent = !valor ? "-" : valor;
            }
        }
        $("#detalle-embarque").modal("show");
    }
    function llenarTablaMani(datos) {
        const valorMani = document.getElementById("valor-manifiesto");
        if (!datos.resultados.length) {
            alertify.error("no se encontraron manifiestos para este embarque");
            tablaMani.innerHTML = `<tr><td colspan="9" class="text-center">No se encontraron resultados</td></tr>`;
            valorMani.value = "0";
            return;
        }
        let total = 0;
        let template = ``;
        datos.resultados.forEach((r) => {
            if (r.guias.length) {
                template += `<tr>
                <td rowspan="${r.guias.length}">
                    <a href="javascript:void(0)" data-llegada="${r.id_ciudad_llegada}" class="ver-imagenes">
                        ${r.nro_mani}
                    </a>
                </td>
                <td rowspan="${r.guias.length}">${r.format_fecha_mani}</td>
                <td rowspan="${r.guias.length}">${r.prov_orig}</td>
                <td rowspan="${r.guias.length}">${r.prov_dest}</td>
                <td rowspan="${r.guias.length}">${r.total_guias}</td>
                <td rowspan="${r.guias.length}">${r.tipo}</td>`;
                total += r.guias.length;
                template += `<td>${r.guias[0].nro_guia}</td><td>${r.guias[0].fecha_reparto}</td>
                <td>
                    <button class="btn btn-primary btn-sm ver-detalle-guias" data-cluster="${r.idCluster}" title="Ver detalle de guía" data-manifiesto="${r.id}" data-nroguia="${r.guias[0].nro_guia}" data-guia="${r.guias[0].id}"><i class="fas fa-eye"></i></button>
                </td>
                </tr>`;
                for (let i = 1; i < r.guias.length; i++) {
                    template += `<tr>
                        <td>${r.guias[i].nro_guia}</td>
                        <td>${r.guias[i].fecha_reparto}</td>
                        <td>
                            <button class="btn btn-primary btn-sm ver-detalle-guias" data-cluster="${r.idCluster}" title="Ver detalle de guía" data-manifiesto="${r.id}" data-nroguia="${r.guias[i].nro_guia}" data-guia="${r.guias[i].id}"><i class="fas fa-eye"></i></button>
                        </td>
                    </tr>`;
                }
            }
        });
        tablaMani.innerHTML = template;
        alertify.success("manifiestos cargados correctamente");
        tablaMani.scrollIntoView({ behavior: "smooth" });
        valorMani.value = total;
    }
    let viewer = null;

    tablaMani.addEventListener("click", function (event) {
        nro_guia = null;
        nro_mani = null;
        nroguiainfo = null;
        if (event.target.classList.contains("ver-imagenes")) {
            fetch("imagenes/" + event.target.dataset.llegada) // tu ruta que devuelve un JSON con imágenes
                .then((res) => res.json())
                .then((data) => {
                    if(!data.imagenes.length){
                        return alertify.alert("Mensaje","No se adjuntaron imagenes");
                    }
                    const cont = document.getElementById("mi-galeria");

                    cont.innerHTML = ""; // limpiar

                    // insertar imágenes invisibles
                    data.imagenes.forEach((img) => {
                        cont.innerHTML += `<img src="${window.location.origin}/storage/${img.ruta}">`;
                    });

                    if (!viewer) {
                        viewer = new Viewer(cont);
                    } else {
                        viewer.update(); // si ya existía, recarga las nuevas imágenes
                    }

                    viewer.show();
                })
                .catch((err) => console.error(err));
        }
        if (event.target.classList.contains("ver-detalle-guias")) {
            nro_mani = event.target.dataset.manifiesto;
            nro_guia = event.target.dataset.guia;
            nroguiainfo = event.target.dataset.nroguia;
            obtenerEmbarquesPorLlegar(
                nro_embarque,
                nro_guia,
                "detalle-guia",
                verDetalleGuia,
                event.target.dataset.cluster
            );
        }
    });
    function verDetalleGuia(data) {
        if (!data.resultados[0]) {
            return;
        }
        for (const key in data.resultados[0]) {
            if (Object.hasOwnProperty.call(data.resultados[0], key)) {
                const value = data.resultados[0][key];
                const dom = document.getElementById("id_" + key);
                if (!dom) {
                    continue;
                }
                if (
                    (key == "fech_venc_orde_comp" || key == "orde_comp") &&
                    value !== "No definido"
                ) {
                    dom.innerHTML = `<span class="text-danger bold font-weight-boldinforme_completado">${value}</span>`;
                    continue;
                }
                dom.textContent = value;
            }
        }
        let template = "";
        if (data.resultados[0].detalles_guias) {
            data.resultados[0].detalles_guias.forEach((dg) => {
                template += `
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-6 col-lg-3"><b class="d-block text-secondary">Grupo Envio</b><span>${
                                dg.grupo_envio
                            }</span></div>
                            <div class="col-6 col-lg-3"><b class="d-block text-secondary">Tipo Envio</b><span>${
                                dg.tipo_envio
                            }</span></div>
                            <div class="col-6 col-lg-3"><b class="d-block text-secondary">Embase o Embalaje</b><span>${
                                dg.embase_embalaje
                            }</span></div>
                            <div class="col-6 col-lg-3"><b class="d-block text-secondary">Cantidad</b><span>${
                                dg.cantidad
                            }</span></div>
                            <div class="col-12"><b class="d-block text-secondary">Descripción</b><span>${
                                !dg.descripcion ? "No definido" : dg.descripcion
                            }</span></div>
                        </div>
                    </div>
                </div>`;
            });
            document.getElementById("detalle_guias").innerHTML = template;
            $(".ocultar-situacion").prop(
                "hidden",
                data.resultados[0].situ != 5 ? true : false
            );
        }
        $("#detalle-guias-llegada").modal("show");
    }
    document
        .getElementById("btn-guardar-guias")
        .addEventListener("click", function (e) {
            e.preventDefault();
            if ($("#id_situacion_envio").val() == "") {
                alertify.error("por favor seleccione la situacion de envio");
                return;
            }
            if (
                $("#id_situacion_envio").val() == "INCIDENCIA" &&
                $("#id_incidencia_envio").val() == ""
            ) {
                alertify.error("por favor seleccione la incidencia de envio");
                return;
            }
            if (
                $("#id_situacion_envio").val() == "INCIDENCIA" &&
                $("#id_incidencia_envio").val() != ""
            ) {
                if (document.getElementById("text-inconforme").value == "") {
                    alertify.error("por favor detalle la inconformidad");
                    return;
                }
            }
            const largo =
                $("#id_incidencia_envio").val() != ""
                    ? " tiene algún problema."
                    : " esta todo conforme.";
            const msg =
                "Al continuar usted esta afirmando que la guía N° <b>" +
                nroguiainfo +
                "</b>" +
                largo +
                " <br>¿Deseas continuar?";
            let datos = new FormData();
            datos.append("guia", nro_guia);
            datos.append("embarque", nro_embarque);
            datos.append("flete", nroFlete);
            datos.append("manifiesto", nro_mani);
            datos.append("situacion", $("#id_situacion_envio").val());
            datos.append("incidencia", $("#id_incidencia_envio").val());
            datos.append(
                "conforme",
                $("#id_incidencia_envio").val() == "DAÑO TOTAL" ||
                    $("#id_incidencia_envio").val() == "PERDIDA TOTAL"
                    ? 14
                    : 7
            );
            datos.append(
                "texto",
                document.getElementById("text-inconforme").value
            );
            alertify.confirm(
                "Alerta",
                msg,
                async () => {
                    try {
                        let response = await general.funcfetch(
                            window.location.origin +
                                "/intranet/pedidos/llegada/arribar-guias",
                            datos
                        );
                        if (response.error) {
                            alertify.alert("Error", response.error);
                            return;
                        }
                        const valorManifiesto =
                            document.getElementById("valor-manifiesto").value;
                        alertify.alert("Mensaje", response.success);
                        if (parseInt(valorManifiesto) - 1 === 0) {
                            obtenerEmbarquesPorLlegar(
                                0,
                                0,
                                "embarques",
                                llenarTablaEmbarque
                            );
                        }
                        obtenerEmbarquesPorLlegar(
                            nro_embarque,
                            0,
                            "manifiestos",
                            llenarTablaMani,
                            id_cluster,
                            trayectoEmbarque
                        );
                        nro_guia = null;
                        nro_mani = null;
                        $("#detalle-guias-llegada").modal("hide");
                    } catch (error) {
                        console.error(error.message);
                        alertify.alert("Error", error.message);
                    }
                },
                () => {}
            );
        });
    $("#detalle-guias-llegada").on("hidden.bs.modal", function () {
        document.getElementById("text-inconforme").value = "";
        document.getElementById("text-inconforme").disabled = true;
        $("#detalle-guias-llegada .simple-select2").val("").trigger("change");
    });
    $("#id_incidencia_envio").on("select2:select", function (e) {
        document.getElementById("text-inconforme").disabled =
            $(this).val() != "" ? false : true;
    });
    $("#id_situacion_envio").on("select2:select", function (e) {
        if ($(this).val() == "INCIDENCIA") {
            $("#id_incidencia_envio").val("").trigger("change");
            $("#id_incidencia_envio").prop("disabled", false);
        } else {
            $("#id_incidencia_envio").prop("disabled", true);
        }
    });
}
window.addEventListener("DOMContentLoaded", loadPage);
