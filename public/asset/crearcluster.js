function loadPage(){
    let general = new General();
    let frmAddCluster = document.getElementById('frm_cluster');
    frmAddCluster.addEventListener('submit',async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        datos.append('type_document','3')
        try {
            let response = await general.funcfetch('agregar',datos);
            alertify.alert('Mensaje',response.error ? response.error : response.success);
            if(response.success){
                resetForm(this)
            }
        } catch (error) {
            alertify.alert('Error',error.message);
            console.error(error);
        }
    });
    function resetForm(form){
        form.reset();
        $('.cb-ubigeo').val("").trigger('change');
        $('.simple-select2').val("").trigger('change');
    }
    frmAddCluster.addEventListener('reset',function(e){
        resetForm(this);
    });
}
window.addEventListener('DOMContentLoaded',loadPage);