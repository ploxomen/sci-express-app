function loadPage(){
    let general = new General();
    let tablaFacturacion = null;
    function changeFuctionCb() {
        thSelectAll.hidden = true;
        if (tablaFacturacion){
            tablaFacturacion.draw();
            return
        }
        tablaFacturacion = $('#tabla-guias').DataTable({
            ajax: {
                url: 'ver',
                method: 'POST',
                headers: general.jsonToken,
                data: function (d) {
                    d.tipo = "consulta";
                    d.estado = $('#id_estado_estado').val();
                    d.cliente = $('#id_filtro_cliente').val();
                    d.trayecto = $('#id_tray_env').val();
                    d.fechaInicio = $('#fecha_inicio').val();
                    d.fechaFin = $('#id_fecha_fin').val();
                    d.response = "dataTable";
                }
            },
            columns: [{
                data: 'fechaEmision'
            },
            {
                data: 'nroGuia'
            },
            {
                data: 'destinatario'
            },
            {
                data: 'comp_clie'
            },
            {
                data: 'nro_comp_cli'
            },
            {
                data: 'baseCluster'
            },
            {
                data: 'ciudadFinal'
            },
            {
                data: 'esAlejado'
            },
            {
                data: 'tipoEnvio',
                render: function (data) {
                    return defaultDescripcion(data);
                }
            },
            {
                data: 'descripcionGuia',
                render: function(data){
                    return defaultDescripcion(data);
                }
            },
            {
                data: 'pesoGuia',
                render : function(data){
                    return !isNaN(parseFloat(data)) ? parseFloat(data) : 0;
                }
            },
            {
                data: 'pesoVolumen',
                render: function(data,tipo,full){
                    return general.pesoVolumen(data, tipo, full);
                }
            }, {
                data: 'valor_complejo_distante',
                render : function(data,tipo,full){
                    return general.valorComplejoDist(full.esAlejado,data);
                }
            }, {
                data: 'fact_final',
                render : function(data){
                    return general.moneyFormat(data);
                }
            }
            ],

        });
    }
    const thSelectAll = document.querySelector("#boxCheck");
    const frmFiltros = document.querySelector("#frm-aplicar-filtros");
    function defaultDescripcion(data){
        let template = "No definido";
        if (data && data.length > 0) {
            template = "";
            data.forEach(d => {
                if (d != 'null' && d != null) {
                    template += `<p class="mb-1">${d}</p>`;
                }
            });
            if (template == "") {
                template = "No definido";
            }
        }
        return template;
    }
    const boxInfoCostos = document.querySelector("#boxInfoCostos");
    const boxNunGuias = document.querySelector("#boxNumGuias");
    function changeValue(valor){
        if (valor == "11") {
            if (tablaFacturacion) {
                tablaFacturacion.destroy();
                tablaFacturacion = null;
            }
            changeFacturacionCb();
            boxInfoCostos.hidden = false;
            boxNunGuias.hidden = false;
        } else {
            selectorAll.checked = false;
            boxNunGuias.hidden = true;
            boxInfoCostos.hidden = true;
            tablaConsulta.innerHTML = "";
            changeFuctionCb();
        }
    }
    const tablaConsulta = document.querySelector("#tabla-guias").querySelector("tbody");
    let datosCostos = {
        subtotal : 0.00,
        igv : 0.00,
        total : 0.00
    }
    document.querySelector("#btn-facturar").onclick = function(e){
        if (datosCostos.total == 0){
            return alertify.alert("Mensaje","Para poder facturar el costo a ser mayor que S/ 0.00");
        }
        let guias = [];
        for (const check of tablaConsulta.querySelectorAll(`input[type="checkbox"]:checked`)) {
            guias.push({
                idGuia : check.dataset.guia
            });
        }
        alertify.confirm("Mensaje", "Estas a punto de facturar " + guias.length + " guias. ¿Estás seguro de continuar?<br>" + `<div class="text-right"><div class="mb-1"><b>SUBTOTAL: </b>${general.moneyFormat(datosCostos.subtotal)}</div><div class="mb-1"><b>I.G.V: </b>${general.moneyFormat(datosCostos.igv)}</div><div class="mb-1"><b>TOTAL: </b>${general.moneyFormat(datosCostos.total)}</div></div>`,async ()=>{
            let datos = new FormData();
            datos.append("tipo","facturar");
            datos.append("cliente", $('#id_filtro_cliente').val());
            datos.append("guias",JSON.stringify(guias));
            datos.append("subtotal", datosCostos.subtotal);
            datos.append("total", datosCostos.total);
            datos.append("igv", datosCostos.igv);
            try {
                const response = await general.funcfetch("ver",datos);
                if(response.error){
                    return alertify.alert("Error",response.error);
                }
                changeValue($('#id_estado_estado').val());
                return alertify.success(response.success);
            } catch (error) {
                console.error(error);
                alertify.error("error al facturar las guias");
            }

        },()=>{})
    }
    function calcularValores(){
        datosCostos.subtotal = 0.00;
        datosCostos.igv = 0.00;
        datosCostos.total = 0.00;
        for (const check of tablaConsulta.querySelectorAll(`input[type="checkbox"]:checked`)) {
            let value = parseFloat(check.dataset.valor)
            datosCostos.total += !isNaN(value) ? value : 0;
        }
        datosCostos.igv = ((datosCostos.total * 18)/100).toFixed(2);
        datosCostos.subtotal = datosCostos.total - datosCostos.igv;
        document.querySelector("#moneySubTotal").textContent = general.moneyFormat(datosCostos.subtotal);
        document.querySelector("#moneyIgv").textContent = general.moneyFormat(datosCostos.igv);
        document.querySelector("#moneyTotal").textContent = general.moneyFormat(datosCostos.total);
    }
    const selectorAll = document.querySelector("#selectAll");
    selectorAll.addEventListener("change", event => marcarTodosCheckbox(event.target.checked));
    function marcarTodosCheckbox(checked){
        for(const check of tablaConsulta.querySelectorAll(`input[type="checkbox"]`)) {
            check.checked = checked;
        }
        calcularValores();
    }
    const totalGuiasFacturar = document.querySelector("#totalFacturar");
    const guiasMostradas = document.querySelector("#guiasMostradas");
    async function changeFacturacionCb(){
        thSelectAll.hidden = false;
        let datos = new FormData(frmFiltros);
        datos.append("tipo", "consulta");
        datos.append("response","normal");
        try {
            const response = await general.funcfetch("ver",datos);
            totalGuiasFacturar.textContent = response.totalGuias;
            guiasMostradas.textContent = response.guias.length;
            let template = "";
            response.guias.forEach(g => {
                template += `<tr>
                    <td>
                         <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input input-check" id="selectCheckbox${g.id}" data-guia="${g.id}" data-valor="${g.fact_final}">
                            <label class="custom-control-label" for="selectCheckbox${g.id}"></label>
                        </div>
                    </td>
                    <td>
                        ${g.fechaEmision}
                    </td>
                    <td>
                        ${g.nroGuia}
                    </td>
                    <td>
                        ${g.destinatario}
                    </td>
                    <td>
                        ${g.comp_clie}
                    </td>
                    <td>
                        ${g.nro_comp_cli}
                    </td>
                    <td>
                        ${g.baseCluster}
                    </td>
                    <td>
                        ${g.ciudadFinal}
                    </td>
                    <td>
                        ${g.esAlejado}
                    </td>
                    <td>
                        ${defaultDescripcion(g.tipoEnvio)}
                    </td>
                    <td>
                        ${defaultDescripcion(g.descripcionGuia)}
                    </td>
                    <td>
                        ${!isNaN(parseFloat(g.pesoGuia)) ? parseFloat(g.pesoGuia) : 0}
                    </td>
                    <td>
                        ${general.pesoVolumen(g.pesoGuia, null, g)}
                    </td>
                    <td>
                        ${general.valorComplejoDist(g.esAlejado,g.valor_complejo_distante)}
                    </td>
                    <td>
                        ${general.moneyFormat(g.fact_final)}
                    </td>
                </tr>`
            });
            tablaConsulta.innerHTML = template == "" ? `<tr><td colspan="100%" class="text-center">Ningún dato disponible en esta tabla</td></tr>` : template;
            if(template){
                selectorAll.checked = true;
            }
            marcarTodosCheckbox(true);
            for (const input of document.querySelectorAll(".input-check")) {
                input.addEventListener("change", changeValueInput);
            }
        } catch (error) {
            alertify.error("error al obtener las guias en cd");
            console.error(error);
        }
    }
    function changeValueInput(e){
        console.log(tablaConsulta.querySelectorAll(`input[type="checkbox"]:checked`).length,tablaConsulta.querySelectorAll(`input[type="checkbox"]`).length);
        if (tablaConsulta.querySelectorAll(`input[type="checkbox"]:checked`).length != tablaConsulta.querySelectorAll(`input[type="checkbox"]`).length){
            selectorAll.checked = false;
        }else{
            selectorAll.checked = true;
        }
        calcularValores();
    }
    changeFacturacionCb();
    document.getElementById('aplicar-filtros-btn').onclick = function () {  
        changeValue($('#id_estado_estado').val())
    }
    for (const filtro of document.querySelectorAll('.filtros')) {
        $(filtro).on('select2:select', e => {
            changeValue($('#id_estado_estado').val());
        });
    }
    document.querySelector("#recalcular-costos").addEventListener("click",async function(e){
        let datos = new FormData(frmFiltros);
        datos.append("tipo","calcular");
        try {
            const response = await general.funcfetch("ver",datos);
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            changeValue($('#id_estado_estado').val());
            return alertify.success(response.success);
        } catch (error) {
            console.error(error);
            alertify.error("error al calcular los montos");
        }
    });
}
window.addEventListener('DOMContentLoaded',loadPage);