function loadPage() {
    let general = new General();
    let cbSelectAll = document.getElementById("id_check_todo");
    let tblMani = document.getElementById("tabla-manifiestos");
    let frmEmbarque = document.getElementById('add-embarque');
    let frmListEmb = document.getElementById('frm-lista-fletes');
    general.obtenerFlestes();
    let dataTableMani = $('#tabla-embarque').DataTable({
        ajax : {
            url: 'manifiesto/pendientes',
            method: 'POST',
            headers: general.jsonToken,
            data: function(d){
                d.ciudad_base = $('#id_prov_dest').val();
                d.tipo = $('#id_prov_dest').find(':selected').attr("data-tipo");
            }
        },
        columns: [
            {data: 'id',searchable: false,orderable: false, render : function(data,type,full){
                return `<div class="custom-control custom-checkbox">
                    <input type="checkbox" data-manifiesto="${data}" data-bulto="${full.totBulto}" class="custom-control-input ${full.id_asignado ? 'check-embarque' : 'disabled'}" ${!full.id_asignado ? 'disabled' : ''} id="check_manifiesto_${data}">
                    <label class="custom-control-label" for="check_manifiesto_${data}"></label>
                </div>`
            }},
            {data: 'idExtendMani'},
            {data: 'format_fecha',searchable: false,orderable: false},
            {data: 'trayectoName'},
            {data: 'envio'},
            {data: 'prov_orig'},
            {data: 'prov_dest'},
            {data: 'nombre_ciudad_base'},
            {data: 'nom_cluster'},
            {data: 'totBulto'},
            {data: 'totguias'}
        ],
        createdRow : function(row,data){
            let bgTable = !data.id_asignado ? 'table-warning' : 'table-success';
            $(row).addClass(bgTable);
        },
    });
    $('#id_prov_dest').on('select2:select',function(){
        dataTableMani.draw();
    });
    $('#id_id_deal').on('select2:select', function () {
        $("#id_id_deal2 option").prop('disabled', false);
        const valDealer2 = $(this).val();
        $("#id_id_deal2 option[value=" + valDealer2 + "]").prop('disabled', true);
        $("#id_id_deal2").val('NINGUNO').trigger('change');
    });
    $('#id_id_deal2').on('change', function () {
        const valDealer2 = $(this).val();
        $("#id_resp_emba option[value='Dealer 2']").prop('disabled', valDealer2 == 'NINGUNO' ? true : false);
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
            alertify.alert('Error',response.error);
            return
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
                alertify.alert('Error',response.error);
                return
            }
            alertify.alert('Mensaje',response.success);
            $('#modal-difinir-fletes').modal("hide");
            dataTableMani.draw();
            $('.simple-select2').val("").trigger('change');
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
    dataTableMani.on('draw',function(){
        for (const c of document.querySelectorAll('.check-embarque')) {
            c.addEventListener('change',function(){
                obtenerBulto(this);
                let checked = tblMani.querySelectorAll('input:checked').length;
                if(checked == tblMani.children.length){
                    cbSelectAll.checked = true;
                }else{
                    cbSelectAll.checked = false;
                }
            });
        }
    });
}
window.addEventListener('DOMContentLoaded',loadPage);