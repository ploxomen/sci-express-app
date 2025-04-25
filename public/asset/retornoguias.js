function loadPage() {
    let general = new General();
    const renderListas = function (data) {
        let template = `<div>`;
        if (data) {
            const dataCount = data.split(";");
            if (dataCount.length === 1) {
                return `<span class="text-info d-block">${data}</span>`;
            }
            data.split(";").map(function (v, k) {
                template += `<span class="text-info d-block">${v}</span>`;
            });
        } else {
            template += `<span class="text-danger">Por definir</span>`;
        }
        template += `</div>`;
        return template;
    };
    let columnsTable = [
        { data: "nro_embarque" },
        { data: "fecha_embarque" },
        { data: "empresa" },
        { data: "fecha_hr_llegada" },
    ];
    columnsTable.push(
        { data: "importe_embarque" },
        { data: "tipo_comprobantes", render: renderListas },
        { data: "nro_guias", render: renderListas },
        { data: "lista_guias", render: renderListas }
    );
    
    columnsTable.push(
        { data: "total_bultos" },
        {
            data: "id_embarque",
            render: function (ndata, type, row) {
                if (ndata != 0) {
                    return `<button data-nroembarque="${
                        row.nro_embarque
                    }" data-cajas="${
                        !row.nro_cajas ? "-" : row.nro_cajas
                    }" data-cajones="${
                        !row.nro_cajones ? "-" : row.nro_cajones
                    }" data-paquetes="${
                        !row.nro_paquetes ? "-" : row.nro_paquetes
                    }" data-otros="${
                        !row.num_otros ? "-" : row.num_otros
                    }" data-sobres="${
                        !row.nro_sobres ? "-" : row.nro_sobres
                    }" data-detalle="${
                        !row.detalle_otro ? "-" : row.detalle_otro
                    }" data-jabas="${
                        !row.nro_jabas ? "-" : row.nro_jabas
                    }" class="btn btn-primary btn-sm btn-bultos" data-embarque="${ndata}" title="Ver detalle de bultos"><i class="fas fa-eye"></i></button><button title="Editar retorno" type='button' data-embarque="${ndata}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></button>`;
                } else {
                    return "Sin acciones";
                }
            },
        }
    );
    let tablaRetorno = $("#tabla-retorno").DataTable({
        ajax: {
            url:
                window.location.origin +
                "/intranet/comprobante/guias/retornadas",
            method: "POST",
            headers: general.jsonToken,
            data: function (d) {
                d.tipo = "mostrar-guias";
            },
        },
        columns: columnsTable,
    });
    const contenidoImg = document.querySelector("#list-img");
    function ImageDelivery(nomFile,nomIdFile,idImg){
        const contenedor = document.createElement('div');
        contenedor.classList.add('img-delivery');
        contenedor.innerHTML = `<button type="button" class="eliminar-img" title="Eliminar esta imagen" data-id="${idImg}" data-name="${nomFile}"><i class="far fa-trash-alt"></i></button>
            <img src="${nomIdFile}"
            title="${nomFile}"
            alt="Imagen de entrega de la guía">
        </button>`;
        return contenedor;
    }
    
    const bodyModal = document.getElementById('tabla-guias-modal');
    bodyModal.onclick = async (e) => {
        if(e.target.classList.contains('btn-danger')){
            let datos = new FormData();
            datos.append('tipo','eliminar-guias-retornadas');
            datos.append('guia',e.target.dataset.guia);
            const response = await general.funcfetch('/intranet/cluster/embarcar/retornar',datos);
            if(response.success){
                e.target.closest('tr').remove();
                return alertify.success('guia eliminada correctamente')
            }
        }
    }
    const tablaDetalleGuiasRetorno = document.querySelector("#tabla-guias-modal");
    const eliminarImg = () => {
        Array.from(contenidoImg.children).forEach((li,key) => {
            if(key > 1){
                li.remove();
            }
        });
    }
    document
        .getElementById("cuerpo-detalle-guias")
        .addEventListener("click", async function (e) {
            if (e.target.classList.contains("btn-bultos")) {
                document.getElementById("id_modal_nro_cajas").textContent =
                    e.target.dataset.cajas;
                document.getElementById("id_modal_nro_jabas").textContent =
                    e.target.dataset.jabas;
                document.getElementById("id_modal_nro_cajones").textContent =
                    e.target.dataset.cajones;
                document.getElementById("id_modal_nro_sobres").textContent =
                    e.target.dataset.sobres;
                document.getElementById("id_modal_nro_paquetes").textContent =
                    e.target.dataset.paquetes;
                document.getElementById("id_modal_num_otros").textContent =
                    e.target.dataset.otros;
                document.getElementById("id_modal_detalle_otro").textContent =
                    e.target.dataset.detalle;
                document.getElementById("id_modal_nro_embarque").textContent =
                    e.target.dataset.nroembarque;
                $("#detalle-embarque").modal("show");
            }else if(e.target.classList.contains("btn-info")){
                eliminarImg()
                let datos = new FormData();
                datos.append('tipo','mostrar-retorno');
                datos.append('id',e.target.dataset.embarque);
                let response = await general.funcfetch('/intranet/comprobante/guias/retornadas',datos);
                if(response.error){
                    return alertify.alert("Alerta","Error al obtener los resultados");
                }
                window.retorno = e.target.dataset.embarque;
                for (const key in response) {
                    if (Object.prototype.hasOwnProperty.call(response, key)) {
                        const value = response[key];
                        const dom = document.querySelector(`#guardar-detalles-nuevo [name="${key}"]`)
                        if(key === "imagenes"){
                            value.forEach(img => {
                                contenidoImg.append(ImageDelivery(img.nombre,`/intranet/storage/img/retorno/${img.url}`,img.id));
                            });
                            continue;
                        }
                        if(key === "guias"){
                            let template = "";
                            value.forEach(guia => {
                                template += `
                                <tr>
                                   <td>${guia.prefijo?.toString().padStart(3,0)} - ${guia.nro_guia?.toString().padStart(7,0)}</td>
                                   <td>${guia.remi_nro_docu}</td>
                                   <td>${guia.tipo_guia === "proveedor" ? guia.nomb_prov : guia.nom_cli}</td>
                                   <td>${guia.dest_nro_docu}</td>
                                   <td>${guia.destinatario}</td>
                                    <td><button type='button' data-guia="${guia.id}" class='btn btn-sm btn-danger'><i class="fas fa-trash"></i></button></td>
                                </tr>
                                `
                            });
                            tablaDetalleGuiasRetorno.innerHTML = template;
                            continue;
                        }
                        if(key === "nro_guias"){
                            $('#id_nro_guias').empty();
                            value.split(";").forEach(nro => {
                                $('#id_nro_guias').append(new Option(nro,nro,true,true));
                            })
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = value;
                    }
                }
                document.querySelector("#btn-guardar-guias-embarcar").textContent = "Actualizar";
                $('.simple-select2, .cb-mult-nuevo').trigger("change");
                $('#guardar-detalles-nuevo').modal('show');
            }
        });
    $("#detalle-embarque").on("hidden.bs.modal", function (e) {
        $(".clear-text").text("-");
    });
}
window.addEventListener("DOMContentLoaded", loadPage);
