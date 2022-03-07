window.addEventListener('DOMContentLoaded',loadPage);
function loadPage(){
    let externo = new General();
    let frmLogin = document.getElementById('login');
    frmLogin.addEventListener('submit',async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        let result = await externo.funcfetch('usuarios/autenticacion',datos);
        if(result.success){
            return window.location.reload();
        }else if(result.not_user){
            alertify.alert('Mensaje','El usuario y/o contraseña no son los correctos.');
        }
    })
}
