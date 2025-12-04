window.addEventListener('DOMContentLoaded',loadPage);
function loadPage(){
    let general = new General();
    let frm = document.getElementById('frm-add-user');
    let idSelectClient = 1;
    const clientesInvitado = {
        listaClietnes: "#lista-contenedora-clientes",
        listSelectCliente: ".simple-select2-cliente",
        oneListaCliente: '#lista-cliente'
    }
    const limiteCliente = document.querySelectorAll('#documento_cli_1 option').length - 1;
    $(clientesInvitado.listSelectCliente).select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: 'Seleccione un cliente'
    });
    
    document.getElementById('agregar-nuevo-cliente').onclick = function(){
        idSelectClient = general.agregarNuevaListCliente(clientesInvitado, limiteCliente, idSelectClient,null,null,null,null,null,false,null);
    };

    document.querySelector(clientesInvitado.listaClietnes).onclick = function(event){
        console.log(event);
        if (event.target.classList.contains('btn-outline-danger')){
            general.eliminarListaCentroCosto(this,event,clientesInvitado);
        }
    }
    $('#documento_cli_1').change( event => {
        general.consultarCentroCosto($(event.target), $(event.target).parents('.form-row').find('.simple-select2'), clientesInvitado);
    });

    //AGREGAR CLIENTES  
    frm.addEventListener('submit',function(e){
        e.preventDefault();
        if (document.getElementById('id_trabajador_cliente').checked){
            return $('#modal-usuario-cliente').modal('show');
        }
        let datos = new FormData(this);
        if($('#roles').val().some(role => role === '6') && !datos.get('driver_license')){
            return alertify.error('debe de completar el campo de licencia de conducir')
        }
        alertify.confirm('Mensaje', '¿Estás seguro de crear al usuario ' + datos.get('name') + ' ' + datos.get('last_name') + '?', () => { agregarNuevoUsuario(datos) },()=>{});
    });
    document.getElementById('id_trabajador_cliente').onchange = function(event){
        $('#areas').val(this.checked ? "6" : "");
        $('#roles').val(this.checked ? "10" : "");
        $('#areas, #roles').trigger("change");
    }
    async function agregarNuevoUsuario(datos){
        try {
            let result = await general.funcfetch('agregar', datos);
            if (result.error) {
                alertify.alert('Error', result.error);
            }
            if (result.alerta) {
                alertify.alert('Alerta', result.alerta);
            }
            if (result.success) {
                alertify.alert('Mensaje', `Usuario creado correctamente.<br>Recuerda que la contraseña temporal es : <b>${datos.get('password')}</b>`);
                $('#modal-usuario-cliente').modal('hide');
                frm.reset();
                $('#id_trabajador_cliente').prop("checked", false).trigger("change");
                $("#id_documento_cli").val("").trigger("change");
            }
        } catch (error) {
            console.error(error);
        }
    }
    document.getElementById('form-usuario-cliente').onsubmit = function(e){
        e.preventDefault();
        let datos = new FormData(this);
        let datos2 = new FormData(frm);
        [...document.querySelector(clientesInvitado.listaClietnes).children].forEach(li => {
            const nnCuenta = li.querySelector('.cb-mult-nuevo');
            datos.append('centro_cuenta[]', $(nnCuenta).val() ? $(nnCuenta).val().join(';') : "");
        });
        for (const pair of datos2.entries()) {
            datos.append(pair[0],pair[1]);
        }
        alertify.confirm('Mensaje', '¿Estás seguro de crear al usuario ' + datos.get('name') + ' ' + datos.get('last_name') + '?', () => { agregarNuevoUsuario(datos) }, () => { });
    }
    document.getElementById('agregar-usuario-cliente').onclick = ever => { document.getElementById('enviar-usuario-cliente').click()}
    $('#roles, #areas').select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: 'Seleccione una opción'
    });
    $('#modal-usuario-cliente').on('hidden.bs.modal', function (e) {
        $(clientesInvitado.listSelectCliente).off("change");
        $(clientesInvitado.listSelectCliente + ', ' + clientesInvitado.listaClietnes + ' .simple-select2, ' + clientesInvitado.listaClietnes + ' .cb-mult-nuevo').select2('destroy');

        [...document.querySelector(clientesInvitado.listaClietnes).children].forEach((v,k) => {
            if(k === 0){
                v.querySelector(clientesInvitado.listSelectCliente).value = "";
                v.querySelector('.simple-select2').value = "";
                v.querySelector('.cb-mult-nuevo').value = "";
            }
            if(k > 0){
                v.remove();
            }
        });
        let configSelect2 = {
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione un cliente',
            tags: false
        }
        $(clientesInvitado.listSelectCliente).select2(configSelect2);
        configSelect2.placeholder = "Seleccione una opción";
        $(clientesInvitado.listaClietnes + ' .simple-select2').select2(configSelect2);
        configSelect2.placeholder = "";
        configSelect2.tags = true;
        $(clientesInvitado.listaClietnes + ' .cb-mult-nuevo').select2(configSelect2);

        $(clientesInvitado.listSelectCliente).change( event => {
            general.consultarCentroCosto($(event.target), $(event.target).parents('.form-row').find('.simple-select2'), clientesInvitado);
        });
    })
}