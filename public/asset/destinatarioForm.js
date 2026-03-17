function loadPage(){
    $('.selected-two').on('select2:select',function(e){
        const idCb = $(this).data('selected-two');
        $('#' + idCb).val($(this).val()).trigger("change");
    });
    const formDestinatario = document.querySelector("#form-destinatarios");
    formDestinatario.addEventListener('submit',async function(e){
        e.preventDefault();
        let data = new FormData(this);
        try {
            const response = await fetch(data.has('destinatario') ? `../actualizar/${data.get('destinatario')}` : 'agregar',{
                method: 'POST',
                body: data,
                headers: {
                    'Accept': 'application/json',
                },
            });
            const dataResponse = await response.json();
            if(!data.has('destinatario')){
                formDestinatario.reset();
                $('.simple-select2, .cb-ubigeo').val("").trigger("change");
            }
            return alertify.alert("Mensaje",dataResponse.success,() => {
                window.location = "/intranet/destinatario/mostrar";
            });
        } catch (error) {
            console.error(error);
        } 
    })
    $('#id_tipo_doc').trigger("change");
}
window.addEventListener('DOMContentLoaded',loadPage);