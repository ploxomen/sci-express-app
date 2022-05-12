function loadPage() {
    let viewAllProv = document.getElementById('id_view_all_prov');
    let general = new General();
    let btnBuscProv = document.getElementById('searc_guias');
    let frmMani = document.getElementById('frm_manifiesto');
    let tblGuias = document.getElementById('table-guias');
    let cbTra = document.getElementById('id_trayecto');
    let cbSelectAll = document.getElementById("id_check_todo");
    async function buscarGuias() {
        if($('#id_prov_dest').val() != "" && $('#id_prov_orig').val() != "" && $('#id_id_cluster').val() != ""){
            viewAllProv.checked = false;
        }
        cbSelectAll.checked = false;
        let datos = new FormData();
        datos.append('prov_dest',viewAllProv.checked ? 'todos' : $('#id_prov_dest').val());
        datos.append('prov_orig',viewAllProv.checked ? 'todos' : $('#id_prov_orig').val());
        datos.append('cluster_id',viewAllProv.checked ? 'todos' : $('#id_id_cluster').val());
        try {
            let response = await general.funcfetch('ver-guias',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            let template = ``;
            if(response.success.length){
                response.success.forEach(ele => {
                    template += `<tr class="${ele.id_asignado ? 'table-success' : 'table-warning'}">
                    <td>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input ${ele.id_asignado ? 'select-guias' : 'disabled'}" ${!ele.id_asignado ? 'disabled' : ''} id="id_check_${ele.id}" data-id="${ele.id}">
                            <label class="custom-control-label" for="id_check_${ele.id}"></label>
                        </div>
                    </td>
                    <td>${ele.nro_guia}</td>
                    <td>${ele.fcha_emi}</td>
                    <td>${ele.remi_prov}</td>
                    <td>${ele.dest_prov}</td>
                    <td>${ele.orde_comp}</td>
                    <td>${ele.fech_venc_orde_comp}</td>
                    <td>${ele.nom_cluster}</td>
                    <td>${ele.ciudad_base_cluster}</td>
                    <td>${ele.ciudad_final}</td>
                    <td>${ele.remi_nomb}</td>
                    <td>${ele.dest_nomb}</td>
                    <td>${ele.tot_cant}</td>
                    <td>${ele.cant_comp}</td>
                    </tr>`;
                });
            }else{
                template = '<tr><td colspan="14" class="text-center">No se encontráron guias para ser manifestadas</td></tr>'
            }
            tblGuias.innerHTML = template;
        } catch (error) {
            alertify.alert('Error',error.message);
            console.error(error);
        }
    }
    buscarGuias();
    btnBuscProv.addEventListener('click',function(e){
        e.preventDefault();
        buscarGuias();
    })
    frmMani.addEventListener('submit',async function(e){
        e.preventDefault();
        let arrGuias = new Array();
        let trGuias = tblGuias.querySelectorAll('.select-guias');
        if($('#id_prov_orig').val() == "" || $('#id_prov_dest').val() == ""){
            alertify.error('seleccione las provincias de origen y destino');
            return
        }
        if($('#id_id_cluster').val() == ""){
            alertify.error('seleccione un cluster');
            return
        }
        for (const element of trGuias) {
            if(element.checked){
                arrGuias.push({id_guia: element.dataset.id});
            }
        }
        if(!arrGuias.length){
            alertify.error('seleccione una o más guias');
            return
        }
        document.getElementById('prov_view_origen').value = $('#id_prov_orig option:selected').text();
        document.getElementById('prov_view_destino').value = $('#id_prov_dest option:selected').text();
        document.getElementById('id_cluster_modal').value = $('#id_id_cluster option:selected').text();
        document.getElementById('nro_selecionadas').textContent = arrGuias.length;
        $('#manifestar-guias').modal("show");
        
    });
    document.getElementById("btn-generar-mani").addEventListener("click",async function(event){
        event.preventDefault();
        let datos = new FormData(frmMani);
        datos.append("fech_mani",document.getElementById("id_fech_mani").value);
        let arrGuias = new Array();
        let trGuias = tblGuias.querySelectorAll('.select-guias');
        for (const element of trGuias) {
            if(element.checked){
                arrGuias.push({id_guia: element.dataset.id});
            }
        }
        if($('#id_id_cluster').val() == ""){
            alertify.error('selecione un cluster');
            return
        }
        datos.append('guias',JSON.stringify(arrGuias));
        datos.append('id_cluster',$('#id_id_cluster').val());
         try {
            let response = await general.funcfetch('agregar',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            alertify.alert('Mensaje',response.success);
            $('#manifestar-guias').modal("hide");
            buscarGuias();
        } catch (error) {
            alertify.alert('Error',error.message);
            console.error(error);
        }
    })
    function calcProv(val_tra){
        $('#id_prov_orig').val("").trigger("change");
        $('#id_prov_dest').val("").trigger("change");
        switch (val_tra) {
            case '1':
                $('#id_prov_orig').val("3927").trigger("change");
                $('#id_id_cluster').html("<option></option>");
            break;
            case '2':
                $('#id_prov_dest').val("3927").trigger("change");
            break;
            case '3':
                $('#id_prov_orig').val("3927").trigger("change");
                $('#id_prov_dest').val("3927").trigger("change"); 
            break;
            case '4':
                $('#id_id_cluster').html("<option></option>");
            break;
        }
        detectProv();
    }
    calcProv(cbTra.value);
    cbTra.addEventListener('change',function(){
        calcProv(this.value);

    });
    $('.cb-prov').select2({
        theme : 'bootstrap',
        placeholder: 'Seleccione una opción',
        width: '100%'
    }).on("select2:select",detectProv);
    $('#id_id_cluster').on('select2:select',buscarGuias);
    $('#id_prov_dest').on('change.select2',async function(e){
        if($(this).val() == ""){
            return;
        }
        let nd = new FormData();
        nd.append("provincia",$('#id_prov_dest').val());
        let response = await general.funcfetch("./../cluster/obtener-provincia",nd);
        if(response.error){
            alertify.alert("Error",response.error);
            return
        }
        if(!response.cluster.length){
            alertify.alert("Alerta",'No se encontraron cluster para la provincia de destino ' + $('#id_prov_dest option:selected').text());
            $('#id_id_cluster').html("<option></option>");
            return
        }
        let template = `<option></option>`;
        response.cluster.forEach(c => {
            template += `<option value="${c.id}">${c.name} ${c.last_name}</option>`;
        });
        $('#id_id_cluster').html(template);
        $('#id_id_cluster').val("").trigger("change");
    });
    function detectProv(){
        if($('#id_prov_orig').val() != "" && $('#id_prov_dest').val() != ""){
            viewAllProv.checked = false;
        }else{
            viewAllProv.checked = true;
        }
    }
    viewAllProv.addEventListener('change',function(e){
        if(this.checked){
            $('#id_id_cluster').val("").trigger("change");
        }
    });
    cbSelectAll.addEventListener("change",function () {  
        const activo = this.checked;
        for (const tr of tblGuias.children) {
            if(tr.querySelector(`.select-guias`)){
                tr.querySelector(`.select-guias`).checked = activo;
            }
        }
    });
}
window.addEventListener('DOMContentLoaded',loadPage);