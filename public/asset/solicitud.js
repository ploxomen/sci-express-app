function loadPage(){
    let cbProvincias = document.getElementById('id_ciudad_cli');
    let cbDistritos = document.getElementById('id_distrito_cli');
    let general = new General();
    $('#id_documento_cli').on('select2:select',async function(event){
        let dato = new FormData();
        dato.append('nro_doc',$(this).val());
        try {
            let response = await general.funcfetch('obtener-cliente',dato);
            if(response.error){
                alertify.error('Error',response.error);
                return
            }
            let template = ``;
            response.provincias.forEach(prov => {
                template += `<option value="${prov.id_ubigeo}">${prov.nombre_ubigeo}</option>`;
            });
            cbProvincias.innerHTML = template;
            template = '';
            response.distritos.forEach(dist => {
                template += `<option value="${dist.id_ubigeo}">${dist.nombre_ubigeo}</option>`;
            });
            cbDistritos.innerHTML = template;
            for (const key in response.cliente) {
                if (Object.hasOwnProperty.call(response.cliente, key)) {
                    const element = response.cliente[key];
                    const domelement = document.querySelector(`[data-cliente="${key}"]`);
                    console.log(domelement);
                    if(!domelement){
                        continue;
                    }
                    domelement.value = element;
                }
            }
        } catch (error) {
            console.error(error);
            alertify.alert('Error',error.message);
        }
    })
    $('#id_documento_cli').select2({
        theme : 'bootstrap',
        templateResult: general.tempSeleCli,
        placeholder: 'Seleccione un cliente'
    });
    let frmSoli = document.getElementById('agregar-solicitud');
    frmSoli.addEventListener('submit',async function(event){
        event.preventDefault();
        let datos = new FormData(this);
        datos.append('nom_cli_nro_doc',$('#id_documento_cli').val());
        try {
            let response = await general.funcfetch('nuevo',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            alertify.alert('Mensaje',response.success,()=>{window.location.reload();});
        } catch (error) {
            alertify.alert('Error',error.message);
            console.error(error);
        }
    })
}
window.addEventListener('DOMContentLoaded',loadPage);
