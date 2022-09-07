function loadPage() {
    let general = new General();
    let cbSelectAll = document.getElementById("id_check_todo");
    let tblMani = document.getElementById("tabla-manifiestos");
    let frmEmbarque = document.getElementById('add-embarque');
    let frmListEmb = document.getElementById('frm-lista-fletes');
    let viewData = 'todos';
    general.obtenerFlestes();
    async function obtenerManifiestos() {
        let datos = new FormData();
        datos.append('ciudad_base', $('#id_prov_dest').val());
        datos.append('tipo', $('#id_prov_dest').find(':selected').attr("data-tipo"));
        const response = await general.funcfetch('manifiesto/pendientes',datos);
        if (response && $('#id_prov_dest').val() == 'todos'){
            selectFilterAll(response);
        } else if (response && $('#id_prov_dest').val() != 'todos'){
            selectOneCity(response);
        }
    }
    function selectOneCity(response) {
        if (viewData == 'todos'){
            tblMani.innerHTML = "";
        }
        response.forEach(manifiesto => {
            const tr = document.createElement("tr");
            tr.classList.add(!manifiesto.id_asignado ? 'table-warning' : 'table-success');
            tr.dataset.selected = $('#id_prov_dest').find(':selected').attr("data-position");
            tr.innerHTML = `
                <td>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" data-manifiesto="${manifiesto.id}" data-bulto="${manifiesto.totBulto}" class="custom-control-input ${manifiesto.id_asignado ? 'check-embarque' : 'disabled'}" ${!manifiesto.id_asignado ? 'disabled' : ''} id="check_manifiesto_${manifiesto.id}">
                        <label class="custom-control-label" for="check_manifiesto_${manifiesto.id}"></label>
                    </div>
                </td>
                <td>${manifiesto.idExtendMani}</td>
                <td>${manifiesto.format_fecha}</td>
                <td>${manifiesto.trayectoName}</td>
                <td>${manifiesto.envio}</td>
                <td>${manifiesto.prov_orig}</td>
                <td>${manifiesto.prov_dest}</td>
                <td>${manifiesto.nombre_ciudad_base}</td>
                <td>${manifiesto.nom_cluster}</td>
                <td>${manifiesto.totBulto}</td>
                <td>${manifiesto.totguias}</td>
                `
            tblMani.append(tr);
        });
        viewData = $('#id_prov_dest').val();
    }
    function selectFilterAll(response){
        let template = "";
        let ciudadesFiltros = [];
        response.forEach(manifiesto => {
            const existeProv = ciudadesFiltros.findIndex(provincia => provincia.ciudad_base.toLowerCase() == manifiesto.nombre_ciudad_base.toLowerCase());
            if (existeProv >= 0) {
                ciudadesFiltros[existeProv].nro_manifiesto++;
            } else {
                ciudadesFiltros.push({
                    nro_manifiesto: 1,
                    ciudad_base: manifiesto.nombre_ciudad_base
                });
            }
            template += `<tr class="${!manifiesto.id_asignado ? 'table-warning' : 'table-success'}">
            <td>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" data-manifiesto="${manifiesto.id}" data-bulto="${manifiesto.totBulto}" class="custom-control-input ${manifiesto.id_asignado ? 'check-embarque' : 'disabled'}" ${!manifiesto.id_asignado ? 'disabled' : ''} id="check_manifiesto_${manifiesto.id}">
                    <label class="custom-control-label" for="check_manifiesto_${manifiesto.id}"></label>
                </div>
            </td>
            <td>${manifiesto.idExtendMani}</td>
            <td>${manifiesto.format_fecha}</td>
            <td>${manifiesto.trayectoName}</td>
            <td>${manifiesto.envio}</td>
            <td>${manifiesto.prov_orig}</td>
            <td>${manifiesto.prov_dest}</td>
            <td>${manifiesto.nombre_ciudad_base}</td>
            <td>${manifiesto.nom_cluster}</td>
            <td>${manifiesto.totBulto}</td>
            <td>${manifiesto.totguias}</td>
            </tr>`
        });
        let totalGuias = 0;
        let templateManifiesto = "";
        ciudadesFiltros.forEach(prov => {
            totalGuias = totalGuias + prov.nro_manifiesto;
            templateManifiesto += `<b class="text-info">${prov.ciudad_base.toUpperCase()}: ${prov.nro_manifiesto}</b>`;
        });
        document.getElementById('detalle-provincia-destino').innerHTML = ciudadesFiltros.length === 0 ? '<b>No se encontraron guias pendientes para contabilizar</b>' : templateManifiesto;
        document.getElementById('total_resultamos_mostrados').textContent = response.length;
        tblMani.innerHTML = !response.length ? '<tr><td colspan="11" class="text-center">No se encontraron manifiestos para ser embarcados</td></tr>' : template;
        document.getElementById('filtro-section').innerHTML = "";
        viewData = 'todos';
    }
    obtenerManifiestos();
    // let dataTableMani = $('#tabla-embarque').DataTable({
    //     ajax : {
    //         url: 'manifiesto/pendientes',
    //         method: 'POST',
    //         headers: general.jsonToken,
    //         data: function(d){
    //             d.ciudad_base = ;
    //             d.tipo = ;
    //         }
    //     },
    //     columns: [
    //         {data: 'id',searchable: false,orderable: false, render : function(data,type,full){
    //             return `<div class="custom-control custom-checkbox">
    //                 <input type="checkbox" data-manifiesto="${data}" data-bulto="${full.totBulto}" class="custom-control-input ${full.id_asignado ? 'check-embarque' : 'disabled'}" ${!full.id_asignado ? 'disabled' : ''} id="check_manifiesto_${data}">
    //                 <label class="custom-control-label" for="check_manifiesto_${data}"></label>
    //             </div>`
    //         }},
    //         {data: 'idExtendMani'},
    //         {data: 'format_fecha',searchable: false,orderable: false},
    //         {data: 'trayectoName'},
    //         {data: 'envio'},
    //         {data: 'prov_orig'},
    //         {data: 'prov_dest'},
    //         {data: 'nombre_ciudad_base'},
    //         {data: 'nom_cluster'},
    //         {data: 'totBulto'},
    //         {data: 'totguias'}
    //     ],
    //     createdRow : function(row,data){
    //         let bgTable = !data.id_asignado ? 'table-warning' : 'table-success';
    //         $(row).addClass(bgTable);
    //     },
    // });

    $('#id_prov_dest').on('select2:select',async function(){
        await obtenerManifiestos();
        const listFiltro = document.getElementById('filtro-section');
        if ($('#id_prov_dest').val() == 'todos'){
            listFiltro.innerHTML = "";
            $('#id_prov_dest option').prop("disabled",false);
        }else{
            const filtro = document.createElement("div");
            filtro.classList.add('rectangulo-filtro');
            filtro.innerHTML = `<span class="icono-delete" data-select="${$('#id_prov_dest').find(':selected').attr("data-position")}"><i class="fas fa-times"></i></span><span>${$('#id_prov_dest option:selected').text()}</span>`;
            listFiltro.append(filtro);
            $(`#id_prov_dest [data-position='${$('#id_prov_dest').find(':selected').attr("data-position")}']`).prop('disabled',true);
            $(this).val("");
        }
        $("#id_prov_dest").trigger("change");
    });
    document.getElementById("filtro-section").onclick = function(event){
        if (event.target.classList.contains("icono-delete")){
            $(tblMani).find(`tr[data-selected="${event.target.dataset.select}"]`).remove();
            $(`#id_prov_dest [data-position='${event.target.dataset.select}']`).prop('disabled', false);
            $("#id_prov_dest").trigger("change");
            event.target.parentElement.remove();
            if (!document.getElementById('filtro-section').children.length){
                $("#id_prov_dest").val("todos").trigger("change");
                obtenerManifiestos();
            }
            
        }
    }
    $('#id_id_deal').on('select2:select', function () {
        general.validarDealerEmbarque('id_id_deal2',$(this).val());
    });
    $('#id_id_deal2').on('change', function () {
        general.validarResponsableEmbarque('id_resp_emba',$(this).val());
    });
    cbSelectAll.addEventListener("change",function () {  
        const activo = this.checked;
        for (const tr of tblMani.children) {
            if(tr.querySelector(`.check-embarque`)){
                tr.querySelector(`.check-embarque`).checked = activo;
                obtenerBulto(tr.querySelector(`.check-embarque`));
            }
        }
    });
    frmEmbarque.addEventListener('submit',async function(event){
        event.preventDefault();
        let datos = new FormData();
        let arrMani = new Array();
        let trMani = tblMani.querySelectorAll('.check-embarque');
        for (const element of trMani) {
            if(element.checked){
                arrMani.push(element.dataset.manifiesto);
            }
        }
        if(!arrMani.length){
            alertify.error('seleccione uno o más manifiestos');
            return
        }
        datos.append('lista_manifiestos',JSON.stringify(arrMani));
        let response = await general.funcfetch('manifiesto/embarcar-fletes',datos);
        if(response.error){
            return alertify.alert('Error',response.error);
        }
        general.visualizarFletes(response, 'lista-ciudades-base', 'lista-ciudades-base-aereo');

        $('#modal-difinir-fletes').modal("show");
        $('#modal-difinir-fletes .calc-total').on('change',function(e){
            general.calcularBultosFletes('#modal-difinir-fletes', e.target.parentElement.parentElement, $(this).attr('data-base'), $(this).attr('data-transfer'));
        });
        $('#modal-difinir-fletes .sub-flete').on('change', general.calcularSubFlete);
    });
    frmEmbarque.addEventListener('reset',function(){
        $('.simple-select2').val("").trigger('change');
    });
    function addFletes(li){
        if(!li.dataset.cluster){
            return false;
        }
        return {
            id_cluster: li.dataset.cluster,
            cajas: li.querySelector(`[data-search="cajas"]`).value == "" ? 0 : li.querySelector(`[data-search="cajas"]`).value,
            jabas: li.querySelector(`[data-search="jabas"]`).value == "" ? 0 : li.querySelector(`[data-search="jabas"]`).value,
            cajones: li.querySelector(`[data-search="cajones"]`).value == "" ? 0 : li.querySelector(`[data-search="cajones"]`).value,
            sobres: li.querySelector(`[data-search="sobres"]`).value == "" ? 0 : li.querySelector(`[data-search="sobres"]`).value,
            paquetes: li.querySelector(`[data-search="paquetes"]`).value == "" ? 0 : li.querySelector(`[data-search="paquetes"]`).value,
            otros: li.querySelector(`[data-search="parihuelas"]`).value == "" ? 0 : li.querySelector(`[data-search="parihuelas"]`).value,
            detalle_otros: li.querySelector(`[data-search="detalle_otros"]`).value,
            sub_total_bultos: li.querySelector(`.sub-total`).value == "" ? 0 : li.querySelector(`.sub-total`).value,
            subtotal_fletes: li.querySelector(`.sub-flete`).value == "" ? 0 : li.querySelector(`.sub-flete`).value
        }
    }
    frmListEmb.addEventListener('submit',async function (e) {  
        e.preventDefault();
        let datos = new FormData(frmEmbarque);
        let arrMani = new Array();
        let trMani = tblMani.querySelectorAll('.check-embarque');
        let arrBase = new Array();
        for (const element of trMani) {
            if(element.checked){
                arrMani.push({id_manifiesto:element.dataset.manifiesto});
            }
        }
        if(!arrMani.length){
            alertify.error('seleccione uno o más manifiestos');
            return
        }
        for (const li of document.querySelectorAll('.lista-fletes')) {
            const fletes = addFletes(li);
            const existCiudad = arrBase.findIndex(ciudad => ciudad.tipo_base == li.dataset.tipoCiudad && ciudad.transfer == li.dataset.transfer && ciudad.id_ciudad == li.dataset.ciudad && ciudad.ubigeo == li.dataset.distrito);
            if(existCiudad === -1){
                arrBase.push({
                    tipo_base: li.dataset.tipoCiudad,
                    transfer: li.dataset.transfer,
                    id_ciudad: li.dataset.ciudad,
                    ubigeo: li.dataset.distrito,
                    nombreCiudad : li.dataset.nombreCiudad,
                    fletes: [fletes]
                });
            }else{
                arrBase[existCiudad].fletes.push(fletes);
            }
        }
        datos.append('flet_esti',$('#id_flet_esti').val());
        datos.append('total_bultos_paquete',$('#id_tota_bult').val());
        datos.append('flestes_cluster',JSON.stringify(arrBase));
        datos.append('lista_manifiestos',JSON.stringify(arrMani));
        try {
            let response = await general.funcfetch('manifiesto/embarcar',datos);
            if(response.error){
                return alertify.alert('Error',response.error);
            }
            alertify.alert('Mensaje',response.success);
            $('#modal-difinir-fletes').modal("hide");
            $('.simple-select2').val("").trigger('change');
            if (response.ciudades){
                let tempCiudad = `<option value="todos">TODOS</option>`;
                response.ciudades.forEach((ciudad,key) => {
                    tempCiudad += `<option data-position="${key}" value="${ciudad.value}" data-tipo="${ciudad.tipo_base}">${ciudad.nombre}</option>`
                });
                document.getElementById('id_prov_dest').innerHTML = tempCiudad;
                $('#id_prov_dest').val("todos").trigger("change");
            }
            obtenerManifiestos();
        } catch (error) {
            alertify.alert('Error',error.message);
            console.error(error)
        }
    });
    $('#modal-difinir-fletes').on('hidden.bs.modal',function(){
        $('#id_flet_esti').val(0);
        $('#id_tota_bult').val(0);
    });
    function obtenerBulto(element){
        const totBult = document.getElementById('id_tota_bult');
        let totBulVal = isNaN(totBult.value) ? 0 : parseFloat(totBult.value);
        let total = element.checked ? totBulVal + parseFloat(element.dataset.bulto) : totBulVal - parseFloat(element.dataset.bulto)        
        totBult.value = total < 0 ? 0 : total.toFixed(2);
    }
    document.getElementById('siguiente-fletes').onclick = ()=>{
        document.getElementById('enviar-fletes').click();
    }
    // dataTableMani.on('draw',function(){
    //     for (const c of document.querySelectorAll('.check-embarque')) {
    //         c.addEventListener('change',function(){
    //             obtenerBulto(this);
    //             let checked = tblMani.querySelectorAll('input:checked').length;
    //             if(checked == tblMani.children.length){
    //                 cbSelectAll.checked = true;
    //             }else{
    //                 cbSelectAll.checked = false;
    //             }
    //         });
    //     }
    // });
}
window.addEventListener('DOMContentLoaded',loadPage);