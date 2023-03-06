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
                    d.cluster = $('#id_filtro_cluster').val();
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
                data: 'nom_cli'
            },
            {
                data: 'comp_clie'
            },
            {
                data: 'nro_comp_cli'
            },
            
            {data:'nomDest'},
            {
                data: 'baseCluster'
            },
            {
                data: 'nomCiudadFinal'
            },
            {
                data: 'tot_kg',
                render : function(data,tipo,full){
                    return general.pesoVolumenCluster(data,full.pesoVolumen);
                }
            }, 
            {
                data: 'fact_cluster_mov1',
                render : function(data){
                    return general.moneyFormat(data);
                }
            },
            {
                data: 'fact_cluster_mov2',
                render : function(data){
                    return general.moneyFormat(data);
                }
            },
            {
                data: 'fact_cluster_total',
                render : function(data){
                    return general.moneyFormat(data);
                }
            }
            ],

        });
    }
    const thSelectAll = document.querySelector("#boxCheck");
    const frmFiltros = document.querySelector("#frm-aplicar-filtros");
    const boxInfoCostos = document.querySelector("#boxInfoCostos");
    const boxInfoTable = document.querySelector("#boxNumGuias");
    function changeValue(valor){
        if (valor == "11") {
            if (tablaFacturacion) {
                tablaFacturacion.destroy();
                tablaFacturacion = null;
            }
            changeFacturacionCb();
            boxInfoCostos.hidden = false;
            boxInfoTable.hidden = false;
        } else {
            selectorAll.checked = false;
            boxInfoCostos.hidden = true;
            boxInfoTable.hidden = true;
            tablaConsulta.innerHTML = "";
            changeFuctionCb();
        }
    }
    const totalGuiasFacturar = document.querySelector("#totalFacturar");
    const guiasMostradas = document.querySelector("#guiasMostradas");
    const tablaConsulta = document.querySelector("#tabla-guias").querySelector("tbody");
    let datosCostos = {
        facturacion : 0.00,
        mov1 : 0.00,
        mov2 : 0.00,
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
                idGuia : check.dataset.guia,
                mov2 : check.dataset.mov2
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
        datosCostos.facturacion = 0.00;
        datosCostos.subtotal = 0.00;
        datosCostos.igv = 0.00;
        datosCostos.mov1 = 0.00;
        datosCostos.mov2 = 0.00;
        datosCostos.total = 0.00;
        for (const check of tablaConsulta.querySelectorAll(`input[type="checkbox"]:checked`)) {
            let value = isNaN(parseFloat(check.dataset.valor)) ? 0 : parseFloat(check.dataset.valor);
            let mov2 = !isNaN(parseFloat(check.dataset.mov2)) ? parseFloat(check.dataset.mov2) : 0;
            let mov1 = !isNaN(parseFloat(check.dataset.mov1)) ? parseFloat(check.dataset.mov1) : 0;
            datosCostos.mov2 += mov2;
            datosCostos.mov1 += mov1;
            datosCostos.facturacion += value;
        }
        document.querySelector("#moneyFacturacion").textContent = general.moneyFormat(datosCostos.facturacion);
        datosCostos.total = datosCostos.mov2 + datosCostos.mov1 + datosCostos.facturacion;
        datosCostos.igv = ((datosCostos.total * 18)/100).toFixed(2);
        datosCostos.subtotal = (datosCostos.total - datosCostos.igv).toFixed(2);
        document.querySelector("#moneySubTotal").textContent = general.moneyFormat(datosCostos.subtotal);
        document.querySelector("#moneyMov1").textContent = general.moneyFormat(datosCostos.mov1);
        document.querySelector("#moneyMov2").textContent = general.moneyFormat(datosCostos.mov2);
        document.querySelector("#moneyIgv").textContent = general.moneyFormat(datosCostos.igv);
        document.querySelector("#moneyTotal").textContent = general.moneyFormat(datosCostos.total);
    }
    function changeMov2(e) {
        const tr = e.target.parentElement.parentElement;
        const checkbox = tr.querySelector("input[type='checkbox']");
        let valor = isNaN(parseFloat(e.target.value)) ? 0 : parseFloat(e.target.value).toFixed(2);
        if(valor < 0){
            valor = 0;
            e.target.value = valor;
        }
        const subTotal = isNaN(parseFloat(checkbox.dataset.valor)) ? 0 : parseFloat(checkbox.dataset.valor);
        const mov1 = isNaN(parseFloat(checkbox.dataset.mov1)) ? 0 : parseFloat(checkbox.dataset.mov1);
        const total = subTotal + mov1 + valor;
        tr.children[12].textContent = general.moneyFormat(total);
        checkbox.dataset.mov2 = valor;
        calcularValores();
    }
    const selectorAll = document.querySelector("#selectAll");
    selectorAll.addEventListener("change", event => marcarTodosCheckbox(event.target.checked));
    function marcarTodosCheckbox(checked){
        for(const check of tablaConsulta.querySelectorAll(`input[type="checkbox"]`)) {
            check.checked = checked;
        }
        calcularValores();
    }
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
                let gbTr = "";
                if (g.tipoGuiaNom == "OS") {
                    gbTr = "table-warning";
                }else if(g.tipoGuiaNom == "GUIA" && g.cantidad == 1){
                    gbTr = "table-primary";
                }
                template += `<tr class="${gbTr}">
                    <td>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input input-check" id="selectCheckbox${g.id}" data-guia="${g.id}" data-valor="${g.fact_cluster_sub}" data-mov1="${g.fact_cluster_mov1}" data-mov2="${g.fact_cluster_mov2}">
                            <label class="custom-control-label" for="selectCheckbox${g.id}"></label>
                        </div>
                    </td>
                    <td>
                        ${g.fechaEmision}
                    </td>
                    <td>
                        <a href="javascript:void(0)" class="select-detalle" data-guia="${g.id}" data-tipoguia="${g.tipoGuiaNom}" data-nroguia="${g.nroGuia}">${g.nroGuia}</a>
                    </td>
                    <td>
                        ${g.nom_cli}
                    </td>
                    <td>
                        ${g.comp_clie}
                    </td>
                    <td>
                        ${!g.nro_comp_cli ? "NINGUNO" : g.nro_comp_cli}
                    </td>
                    <td>
                        ${g.nomDest}
                    </td>
                    <td>
                        ${g.baseCluster}
                    </td>
                    <td>
                        ${g.nomCiudadFinal}
                    </td>
                    <td>
                        ${general.pesoVolumenCluster(g.tot_kg,g.pesoVolumen)}
                    </td>
                    <td>
                        ${general.moneyFormat(g.fact_cluster_mov1)}
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm change-mov2" value="${!g.fact_cluster_mov2 ? "0.00" : g.fact_cluster_mov2}" step="0.01" min="0">
                    </td>
                    <td>
                        ${general.moneyFormat(g.fact_cluster_total)}
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
            for (const ea of document.querySelectorAll(".change-mov2")) {
                ea.addEventListener("change",changeMov2);
            }
            for (const verDetalle of document.querySelectorAll(".select-detalle")) {
                verDetalle.addEventListener("click",verDetalleGuia);
            }
        } catch (error) {
            alertify.error("error al obtener las guias en cd");
            console.error(error);
        }
    }
    $('#detalle-guia-modal').on("hidden.bs.modal",function(e){
        setTimeout(() => {
            if(tr){
                tr.classList.remove("table-active");
                tr = null;
            }
        }, 500);
    });
    let tr = null;
    async function verDetalleGuia(e){
        let datos = new FormData(frmFiltros);
        datos.append("tipo", "detalle");
        datos.append("guia", e.target.dataset.guia);
        tr = e.target.parentElement.parentElement;
        tr.classList.add("table-active");
        try {
            const response = await general.funcfetch("ver",datos);
            if(response.success){
                let template = "";
                response.success.forEach(dg => {
                    template +=`<div class="card mb-2">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Grupo Envio</b>${dg.grupo_envio}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Tipo Envio</b>${dg.tipo_envio}</div>
                            <div class="col-12 col-lg-4"><b class="d-block text-secondary">Embase o Embalaje</b>${dg.embase_embalaje}</div>
                            <div class="col-12"><b class="d-block text-secondary">Descripción</b>${!dg.descripcion ? 'No definido' : dg.descripcion}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Cantidad</b>${dg.cantidad}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Kg</b>${dg.kg}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Volumen</b>${dg.volumen}</div>
                        </div>
                    </div>
                </div>`
                });
                document.querySelector("#tituloDetalleModal").textContent = "Detalle de la "+ e.target.dataset.tipoguia +" - N° " + e.target.dataset.nroguia;
                document.querySelector("#bodyDetalleGuiaModal").innerHTML = template;
                return $('#detalle-guia-modal').modal("show");
            }
            alertify.error("no se encontro el detalle de la guía/os");
        } catch (error) {
            tr.classList.remove("table-active");
            console.error(error);
            alertify.error("error al obtener el detalle de la guía/os");
        }
    }
    function changeValueInput(e){
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