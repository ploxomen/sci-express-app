window.addEventListener('DOMContentLoaded',loadPage);
function loadPage(){
    let general = new General();
    let frm = document.getElementById('frm-add-user');
    //AGREGAR CLIENTES  
    frm.addEventListener('submit',async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            let result = await general.funcfetch('agregar',datos);
            if(result.error){
                alertify.alert('Error',result.error);
            }
            if(result.alerta){
                alertify.alert('Alerta',result.alerta);
            }
            if(result.success){
                alertify.alert('Mensaje',`Usuario creado correctamente.<br>Recuerda que la contraseña temporal es : <b>${datos.get('password')}</b>`);
                this.reset();
            }
        } catch (error) {
            console.error(error);
        }
    });
    $('#roles, #areas').select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: 'Seleccione una opción'
    })
}