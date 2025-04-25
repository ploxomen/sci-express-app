function loadPage() {
    let general = new General();
    let tablaGuias = $('#tabla-guias').DataTable({
        ajax: {
            url: 'guias',
            method: 'POST',
            headers: general.jsonToken,
            data: function (d) {
                d.fechaInicio = $('#fecha_inicio').val();
                d.fechaFin = $('#id_fecha_fin').val();
            }
        },
        columns: [{
                data: 'nro_guia'
            },
            {
                data: 'fecha_emision'
            },
            {
                data: 'fecha_traslado',
            },
            {
                data: 'nro_comp_cli',
                render : function(data){
                    if(data == null || data == ""){
                        return 'Sin comprobantes';
                    }
                    return data.replace(/;/i,"<br>");
                }
            },
            {
                data: 'dias_tran'
            },
            {
                data: 'nom_remi'
            },
            {
                data: 'nom_dest'
            },
            {
                data: 'ciudad_base_cluster'
            },
            {
                data: 'ciudad_final'
            },
            {
                data: 'id',
                searchable: false,
                orderable: false,
                name: 'id',
                render: function (data, type, full) {
                    return `<div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><button data-toggle="tooltip" title="Aprobar" data-guia="${data}" class="btn btn-sm btn-success aprobar-guia"><i class="fas fa-check"></i></button><button data-guia="${data}" class="btn btn-sm btn-danger desaprobar-guia"><i class="fas fa-ban"></i></button></div>`;
                }
            }
        ],
    });
    let idGuia = null;
    document.querySelector("#btn-denegar-modal").onclick = (e) => document.querySelector("#txt-enviar").click();
    for (const filtros of document.querySelectorAll('.filtro')) {
        filtros.onchange = e => tablaGuias.draw();
    }
    const contenidoDenegar = document.querySelector("#contenido-desaprobar");
    const formDenegar = document.querySelector("#formulario-denegar");
    const tablaGuiasHtml = document.querySelector("#tabla-detalle-guias");
    tablaGuiasHtml.onclick = async (e) => {
        if(e.target.classList.contains('desaprobar-guia')){
            let datos = new FormData();
            datos.append('guia',e.target.dataset.guia);
            const response = await general.funcfetch("guias/opciones-denegar",datos);
            let template = "";
            response.alternativas.forEach(alternativa => {
                template += `<li>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="opciones[]" value="${alternativa.id}" class="custom-control-input" id="alternativa-denegar-${alternativa.id}">
                    <label class="custom-control-label" for="alternativa-denegar-${alternativa.id}">${alternativa.opcion}</label>
                </div>
                </li>`
            });
            idGuia = e.target.dataset.guia;
            contenidoDenegar.innerHTML = !template ? `<li>Ninguna opcion encontrada</li>` : template;
            $('#denegar-guia').modal("show");
        }else if(e.target.classList.contains('aprobar-guia')){
            alertify.confirm("Alerta","¿Estas seguro de aprobar esta guía?",async () => {
                let datos = new FormData();
                datos.append('guia',e.target.dataset.guia);
                const response = await general.funcfetch("guias/aprobar",datos);
                if(response.success){
                    alertify.success(response.success);
                    tablaGuias.draw();
                }
            },() => {})            
        }
    }
    formDenegar.onsubmit = (e) => {
        e.preventDefault();
        alertify.confirm("Alerta","¿Estas seguro de denegar esta guía?",async () => {
            let datos = new FormData(e.target);
            datos.append('guia',idGuia);
            const response = await general.funcfetch("guias/denegar",datos);
            if(response.success){
                alertify.success(response.success);
                tablaGuias.draw();
                return $('#denegar-guia').modal("hide");
            }
        },() => {})
        
    }
}
window.addEventListener('DOMContentLoaded', loadPage);