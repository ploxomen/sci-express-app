function pageLoaded(){
    let general = new General();
    $('#id_comp_clie').select2({
        theme : 'bootstrap',
        width: '100%'
    });
    $('#id_nro_comp_cli').select2({
        theme : 'bootstrap',
        width: '100%',
        tags: true
    })
    let frmGuia = document.getElementById('frm_guia');
    frmGuia.addEventListener('submit',async function(event){
        event.preventDefault();
        let datos = new FormData(this);
        let tablaDetalle = document.getElementById('tabla-detalle');
        let detalles = new Array();
        for (const e of tablaDetalle.children) {
            let objeto = new Object();
            let deta = e.querySelectorAll('input, select');
            for (const ele2 of deta) {
                if(ele2.value && ele2.value != '0.00'){
                    objeto[ele2.dataset.name] = ele2.value;
                }
            }
            if(!general.isObjEmpty(objeto)){
                detalles.push(objeto);
            }
        }
        datos.append('detalle_guia',JSON.stringify(detalles));
        try {
            let response  = await general.funcfetch('agregar-guia',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            alertify.alert('Mensaje',response.success,()=>{window.location.reload()});
        } catch (error) {
            console.error(error);
            alertify.alert('Error',error.message);
        }
    })
}
window.addEventListener('DOMContentLoaded',pageLoaded);