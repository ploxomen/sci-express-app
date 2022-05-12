function loadPage() {
    let general = new General();
    let cbSelectAll = document.getElementById("id_check_todo");
    let tblMani = document.getElementById("tabla-manifiestos");
    let frmEmbarque = document.getElementById('add-embarque');
    let frmListEmb = document.getElementById('frm-lista-fletes');
    let fletes = null;
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
        }
    });
    $('#id_prov_dest').on('select2:select',function(){
        dataTableMani.draw();
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
    async function obtenerFlestes(){
        let response = await general.funcfetch('obtener-fletes');
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        fletes = response.fletes;
    }
    obtenerFlestes();
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
        let template = ``;
        response.ciudades.forEach(c => {
            template += `
                <li data-cluster="${c.id_cluster}">
                    <div class="form-row">
                        <div class="form-group col-12 col-md-6 col-lg-5">
                            <b>Ciudad Base:</b><span> ${c.nombreciudad}</span>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-7">
                            <b>Cluster:</b><span> ${c.nom_cluster}</span>
                        </div>
                        <div class="form-group col-6 col-lg-3">
                            <label for="total_bultos_${c.id_cluster}">Sub Cant</label>
                            <input type="number" id="total_bultos_${c.id_cluster}" min="1" class="form-control form-control-sm sub-total" readonly required>
                        </div>
                        <div class="form-group col-6 col-lg-3">
                            <label for="sub_feletes_${c.id_cluster}">Sub Flete</label>
                            <input type="number" step="0.01" id="sub_feletes_${c.id_cluster}" min="1" class="form-control form-control-sm sub-flete" required>
                        </div>
                        <div class="col-12"></div>
                        <div class="form-group col-6 col-lg-2">
                            <label for="id_nro_cajas_${c.id_cluster}">Cajas</label>
                            <input type="number" name="nro_cajas" data-search="cajas" data-base="${c.nombreciudad.toUpperCase()}" id="id_nro_cajas_${c.id_cluster}" class="form-control form-control-sm calc-total" min="0">
                        </div>
                        <div class="form-group col-6 col-lg-2">
                            <label for="id_nro_jabas_${c.id_cluster}">Jabas</label>
                            <input type="number" name="nro_jabas" data-search="jabas" data-base="${c.nombreciudad.toUpperCase()}" id="id_nro_jabas_${c.id_cluster}" class="form-control form-control-sm calc-total" min="0">
                        </div>
                        <div class="form-group col-6 col-lg-2">
                            <label for="id_nro_cajones_${c.id_cluster}">Cajones</label>
                            <input type="number" name="nro_cajones" data-search="cajones" data-base="${c.nombreciudad.toUpperCase()}" id="id_nro_cajones_${c.id_cluster}" class="form-control form-control-sm calc-total" min="0">
                        </div>
                        <div class="form-group col-6 col-lg-2">
                            <label for="id_nro_sobres_${c.id_cluster}">Sobres</label>
                            <input type="number" name="nro_sobres" data-search="sobres" data-base="${c.nombreciudad.toUpperCase()}" id="id_nro_sobres_${c.id_cluster}" class="form-control form-control-sm calc-total" min="0">
                        </div>
                        <div class="form-group col-6 col-lg-2">
                            <label for="id_nro_paquetes_${c.id_cluster}">Paquetes</label>
                            <input type="number" name="nro_paquetes" data-search="paquetes" data-base="${c.nombreciudad.toUpperCase()}" id="id_nro_paquetes_${c.id_cluster}" class="form-control form-control-sm calc-total" min="0">
                        </div>
                        <div class="form-group col-6 col-lg-2">
                            <label for="id_num_otros_${c.id_cluster}">Otros</label>
                            <input type="number" name="num_otros" data-search="parihuelas" data-base="${c.nombreciudad.toUpperCase()}" id="id_num_otros_${c.id_cluster}" class="form-control form-control-sm calc-total" min="0">
                        </div>
                        <div class="form-group col-12">
                            <label for="id_detalle_otro_${c.id_cluster}">Detallar otro</label>
                            <textarea name="detalle_otro" maxlength="255" data-search="detalle_otros" id="id_detalle_otro_${c.id_cluster}" class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                </li>`;
        });
        document.getElementById('lista-ciudades-base').innerHTML = template;
        $('#modal-difinir-fletes').modal("show");
        $('#modal-difinir-fletes .calc-total').on('change',function(e){
            const subTotalBulto = e.target.parentElement.parentElement;
            subTotalBulto.querySelector('.sub-total').value = general.totalBultos(subTotalBulto,'.calc-total');
            let totalBulto = 0;
            $('#modal-difinir-fletes .calc-total').map(function(k,v){
                const vtotal = $(v).val() == "" || $(v).val() == null ? 0 : $(v).val();
                totalBulto += parseInt(vtotal);
            });
            $('#id_tota_bult').val(totalBulto);
            if(!fletes){
                return
            }
            fletes.forEach(f => {
                if(f.ciudad_base == $(this).attr('data-base')){
                    let total = 0;
                    $(subTotalBulto).find('.calc-total').map(function(k,v){
                        const fle = parseFloat(f[$(v).attr('data-search')]);
                        const mivalor = $(this).val() == "" || $(this).val() == null ? 0 : $(this).val();
                        total += isNaN(fle) ? 0 : fle * mivalor;
                    });
                    subTotalBulto.querySelector('.sub-flete').value = total.toFixed(2);
                    $(subTotalBulto).find('.sub-flete').trigger("change");
                    return
                }
            });
        });
        $('#modal-difinir-fletes .sub-flete').on('change',function(e){
            let total = 0;
            $('#lista-ciudades-base .sub-flete').map(function(k,v){
                const valor = $(v).val() == "" || $(v).val() == null ? 0 : $(v).val();
                total += parseFloat(valor);
            });
            $('#id_flet_esti').val(total.toFixed(2));
        });       
    });
    frmEmbarque.addEventListener('reset',function(){
        $('.simple-select2').val("").trigger('change');
    });
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
        for (const li of document.getElementById('lista-ciudades-base').children) {
            arrBase.push({
                id_cluster: li.dataset.cluster,
                cajas: li.querySelector(`[data-search="cajas"]`).value == "" ? 0 : li.querySelector(`[data-search="cajas"]`).value,
                jabas : li.querySelector(`[data-search="jabas"]`).value == "" ? 0 : li.querySelector(`[data-search="jabas"]`).value,
                cajones : li.querySelector(`[data-search="cajones"]`).value == "" ? 0 : li.querySelector(`[data-search="cajones"]`).value,
                sobres : li.querySelector(`[data-search="sobres"]`).value == "" ? 0 : li.querySelector(`[data-search="sobres"]`).value,
                paquetes : li.querySelector(`[data-search="paquetes"]`).value == "" ? 0 : li.querySelector(`[data-search="paquetes"]`).value,
                otros : li.querySelector(`[data-search="parihuelas"]`).value == "" ? 0 : li.querySelector(`[data-search="parihuelas"]`).value,
                detalle_otros: li.querySelector(`[data-search="detalle_otros"]`).value,
                sub_total_bultos: li.querySelector(`.sub-total`).value == "" ? 0 : li.querySelector(`.sub-total`).value, 
                subtotal_fletes: li.querySelector(`.sub-flete`).value == "" ? 0 : li.querySelector(`.sub-flete`).value
            })
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