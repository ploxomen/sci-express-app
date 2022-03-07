window.addEventListener('DOMContentLoaded',loadPage);
function loadPage(){
    let externo = new General();
    let frm = document.getElementById('frm-add-user');
    //AGREGAR CLIENTES  
    frm.addEventListener('submit',function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            let result = externo.funcfetch('agregar',datos);
            if(result.success){
                alertify.alert('Mensaje','Usuario creado correctamente');
            }else{
                alertify.alert('Error',result.error);
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