function loadPage(){
    let general = new General();
    let idusuario = null;
    async function getDatosUsuario(){
        const datos = new FormData();
        datos.append('tipo','obtener')
        const response = await general.funcfetch('miperfil/modificar',datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        idusuario = response.usuario.id;
        for (const key in response.usuario) {
            if (Object.hasOwnProperty.call(response.usuario, key)) {
                const valor = response.usuario[key];
                const dom = document.getElementById('id_' + key);
                if(!dom){
                    continue;
                }
                dom.value = valor;
            }
        }
    }
    getDatosUsuario();
    document.getElementById('frm_editar_usuario').addEventListener('submit',async function(e){
        e.preventDefault();
        if(!idusuario){
            alertify.alert('Alerta','No se encontró el usuario');
            return
        }
        let datos = new FormData(this);
        datos.append('tipo','actualizar');
        datos.append('id_usuario',idusuario);
        const response = await general.funcfetch('miperfil/modificar',datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        alertify.success(response.success);
    })

}
window.addEventListener('DOMContentLoaded',loadPage);