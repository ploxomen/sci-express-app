function loadPage() {
    let viewAllProv = document.getElementById('id_view_all_prov');
    let general = new General();
    let btnBuscProv = document.getElementById('searc_guias');
    let frmMani = document.getElementById('frm_manifiesto');
    let tblGuias = document.getElementById('table-guias');
    let cbTra = document.getElementById('id_trayecto');
    let cbSelectAll = document.getElementById("id_check_todo");
    async function buscarGuias() {
        cbSelectAll.checked = false;
        let datos = new FormData();
        datos.append('prov_dest',viewAllProv.checked ? 'todos' : $('#id_prov_dest').val());
        datos.append('prov_orig',viewAllProv.checked ? 'todos' : $('#id_prov_orig').val());
        try {
            let response = await general.funcfetch('ver-guias',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            let template = ``;
            if(response.success.length){
                response.success.forEach(ele => {
                    template += `<tr>
                    <td>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="id_check_${ele.id}" data-id="${ele.id}">
                            <label class="custom-control-label" for="id_check_${ele.id}"></label>
                        </div>
                    </td>
                    <td>${ele.id.toString().padStart(5,'0')}</td>
                    <td>${ele.fcha_emi}</td>
                    <td>${ele.remi_prov}</td>
                    <td>${ele.dest_prov}</td>
                    <td>${ele.orde_comp}</td>
                    <td>${ele.fech_venc_orde_comp}</td>
                    <td>${ele.remi_nomb}</td>
                    <td>${ele.dest_nomb}</td>
                    <td>${ele.tot_cant}</td>
                    <td>${ele.cant_comp}</td>
                    </tr>`;
                });
            }else{
                template = '<tr><td colspan="11" class="text-center">No se encontráron guias para ser manifestadas</td></tr>'
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
        let trGuias = tblGuias.querySelectorAll('.custom-control-input');
        for (const element of trGuias) {
            if(element.checked){
                arrGuias.push({id_guia: element.dataset.id});
            }
        }
        if(!arrGuias.length){
            alertify.error('seleccione una o más guias');
            return
        }
        if($('#id_prov_orig').val() == "" || $('#id_prov_dest').val() == ""){
            alertify.error('seleccione las provincias de origen y destino');
            return
        }
        document.getElementById('prov_view_origen').value = $('#id_prov_orig option:selected').text();
        document.getElementById('prov_view_destino').value = $('#id_prov_dest option:selected').text();
        try {
            let nd = new FormData();
            nd.append("provincia",$('#id_prov_dest').val());
            let response = await general.funcfetch("./../cluster/obtener-provincia",nd);
            if(response.error){
                alertify.alert("Error",response.error);
                return
            }
            if(!response.cluster.length){
                alertify.alert("Alerta",'No se encontraron cluster para la provincia de destino ' + $('#id_prov_dest option:selected').text());
                return
            }
            let template = `<option></option>`;
            response.cluster.forEach(c => {
                template += `<option value="${c.id}">${c.name} ${c.last_name}</option>`;
            });
            document.getElementById('nro_selecionadas').textContent = arrGuias.length;
            $('#id_cluster').html(template);
            $('#id_cluster').val("").trigger("change");
            $('#manifestar-guias').modal("show");
        } catch (error) {
            alertify.alert("Error",error.message);
            console.error(error);
        }
    });
    document.getElementById("btn-generar-mani").addEventListener("click",async function(event){
        event.preventDefault();
        let datos = new FormData(frmMani);
        datos.append("fech_mani",document.getElementById("id_fech_mani").value);
        let arrGuias = new Array();
        let trGuias = tblGuias.querySelectorAll('.custom-control-input');
        for (const element of trGuias) {
            if(element.checked){
                arrGuias.push({id_guia: element.dataset.id});
            }
        }
        if($('#id_cluster').val() == ""){
            alertify.error('selecione un cluster');
            return
        }
        datos.append('guias',JSON.stringify(arrGuias));
        datos.append('id_cluster',$('#id_cluster').val());
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
            break;
            case '2':
                $('#id_prov_dest').val("3927").trigger("change"); 
            break;
            case '3':
                $('#id_prov_orig').val("3927").trigger("change");
                $('#id_prov_dest').val("3927").trigger("change"); 
            break;
        }
        detectProv();
    }
    calcProv(cbTra.value);
    cbTra.addEventListener('change',function(){calcProv(this.value)});
    $('.cb-prov').select2({
        theme : 'bootstrap',
        placeholder: 'Seleccione una opción',
        width: '100%'
    }).on("select2:select",detectProv);
    function detectProv(){
        if($('#id_prov_orig').val() != "" && $('#id_prov_dest').val() != ""){
            viewAllProv.checked = false;
        }else{
            viewAllProv.checked = true;
        }
    }
    cbSelectAll.addEventListener("change",function () {  
        const activo = this.checked;
        for (const tr of tblGuias.children) {
            if(tr.querySelector(`input[type="checkbox"]`)){
                tr.querySelector(`input[type="checkbox"]`).checked = activo;
            }
        }
    });
}
window.addEventListener('DOMContentLoaded',loadPage);