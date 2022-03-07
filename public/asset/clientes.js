function loadPage(){
    let general = new General();
    let frmCli = document.getElementById('frm_cliente');
    let idCli = document.getElementById('id_nro_doc_cli') ? document.getElementById('id_nro_doc_cli').value : false;
    frmCli.addEventListener('submit',async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        if(idCli){
            datos.append('id_cliente', idCli);
        }
        try {
            let response = await general.funcfetch('agregar',datos);
            let message = general.detectSession(response.message);
            alertify.alert('Mensaje',message,() => {
                if(response.action === false){
                    window.location.href = 'mostrar';
                }else{
                    frmCli.reset();
                    $('.cb-ubigeo').val('').trigger("change");
                }
            });
        } catch (error) {
            console.log(error);
        }
    });
}
window.addEventListener('DOMContentLoaded',loadPage);