function loadPage() {  
    $('.cb-prov').select2({
        theme : 'bootstrap',
        placeholder: 'Seleccione una opción',
        width: '100%'
    });
    let general = new General();
    let btnBuscProv = document.getElementById('searc_guias');
    let frmMani = document.getElementById('frm_manifiesto');
    let tblGuias = document.getElementById('table-guias');
    async function buscarGuias() {  
        let viewAllProv = document.getElementById('id_view_all_prov');
        let datos = new FormData();
        datos.append('prov_dest',viewAllProv.checked ? 'todos' : $('#id_prov_dest').val());
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
                    <td>${ele.dest_prov}</td>
                    <td>${ele.orde_comp}</td>
                    <td>${ele.fech_venc_orde_comp}</td>
                    <td>${ele.remi_nomb}</td>
                    <td>${ele.dest_nomb}</td>
                    <td>${ele.cant_bulto}</td>
                    <td>${ele.cant_comp}</td>
                    </tr>`;
                });
            }else{
                template = '<tr><td colspan="10" class="text-center">No se encontráron guias para ser manifestadas</td></tr>'
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
        let datos = new FormData(this);
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
        datos.append('guias',JSON.stringify(arrGuias));
         try {
            let response = await general.funcfetch('agregar',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            alertify.alert('Mensaje',response.success);
            buscarGuias();
        } catch (error) {
            alertify.alert('Error',error.message);
            console.error(error);
        }
    })

}
window.addEventListener('DOMContentLoaded',loadPage);