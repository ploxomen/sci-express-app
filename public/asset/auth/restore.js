function loadPage(){
    let general = new General();
    document.getElementById('frm-add-user').addEventListener('submit',async function(e){
        e.preventDefault();
        if($('#id_password_1').val() !== $('#id_password_2').val()){
            alertify.error('las contraseñas no coinciden, intentelo de nuevo');
            return
        }
        let datos = new FormData(this);
        let response = await general.funcfetch('login/restore',datos);
        if(response.error){
            alertify.error(response.error);
            return
        }
        window.location.reload();
    });
    document.getElementById('cancelar-restore').addEventListener('click',async function(e){
        e.preventDefault();
        let response = await general.funcfetch('login/restore-cancelar');
        if(response.success){
            window.location.reload();
        }
        alertify.error('no se pudo cancelar la restauracion');
    })
}
window.addEventListener('DOMContentLoaded',loadPage);