function loadPage(){
    
    let general = new General();
    let usuarioClienteExterno = false;
    let idSelectClient = 1;
    const clientesInvitado = {
        listaClietnes: "#lista-contenedora-clientes",
        listSelectCliente: ".simple-select2-cliente",
        oneListaCliente: '#lista-cliente'
    }
    const limiteCliente = document.querySelectorAll('#documento_cli_1 option').length;
    $(clientesInvitado.listSelectCliente).select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: 'Seleccione un cliente'
    });
    let tablaUsuarios = $('#tabla-usuarios').DataTable({
        ajax : {
            url: 'obtener',
            method: 'POST',
            headers: general.jsonToken,
        },
        columns: [
            {data: 'n_document'},
            {data: 'name', render : function(data,type,full){
                return data + ' ' + full.last_name;
            }},
            {data: 'phone'},
            {data: 'email'},
            {data : 'area'},
            {data: 'status', render: function(data,type,full){
                let estado = '';
                switch (data) {
                    case 0:
                        estado = `<span class="text-danger">Anulado</span>`;
                    break;
                    case 1:
                        estado = `<span class="text-success">Activo</span>`;
                    break;
                    case 2:
                        estado = `<span class="text-warning">Por activar</span>`;
                    break;
                     case 3:
                        estado = `<span class="text-primary">Restaurado</span>`;
                    break;
                    default:
                        estado = `<span class="text-danger">No definido</span>`;
                    break;
                }
                return estado;
            }},
            {data : 'id', searchable : false, orderable : false, render : function(data){
                return `
                <div class='d-flex justify-content-center align-items-center flex-wrap' style="gap:5px;">
                    <button class="btn btn-sm btn-primary btn-editar" data-usuario="${data}" title="Ver más detalles"><i class="fas fa-user-edit"></i></button>
                    <button class="btn btn-sm btn-info btn-restore" data-usuario="${data}" title="Restaurar contraseña"><i class="fas fa-undo"></i></button>
                    <button class="btn btn-sm btn-sm btn-secondary btn-editar-roles" data-usuario="${data}" title="Editar Roles"><i class="fas fa-dice"></i></i></button>
                    <button class="btn btn-sm btn-danger btn-delete" data-usuario="${data}" title="Eliminar cuenta"><i class="far fa-trash-alt"></i></button>
                </div>`;
            }}
        ]
    });
    const tbodyUsuarios = document.getElementById("lista-usuarios");
    let usuarioid = null;
    tbodyUsuarios.addEventListener('click',async function(e){
        if(e.target.classList.contains('btn-delete')){
            const usuario = e.target.dataset.usuario;
            alertify.confirm('Alerta','¿Deseas anular este usuario?<br>Para restaurarlo deberá de resturar su contraseña',async function(){
                let datos = new FormData();
                datos.append('id_usuario',usuario);
                datos.append('accion','eliminar');
                let response = await general.funcfetch('modificar',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                alertify.success(response.success);
                tablaUsuarios.draw();
            },()=>{});
        }
        if(e.target.classList.contains('btn-restore')){
            const usuario = e.target.dataset.usuario;
            usuarioid = usuario;
            $('#modal-usuario-password').modal("show");
        }
        if(e.target.classList.contains('btn-editar')){
            const usuario = e.target.dataset.usuario;
            let datos = new FormData();
            datos.append('id_usuario',usuario);
            datos.append('accion','mostrar_editar');
            let response = await general.funcfetch('modificar',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            if(response.datos){
                usuarioid = response.datos.id;
                for (const key in response.datos) {
                    if (Object.hasOwnProperty.call(response.datos, key)) {
                        const valor = response.datos[key];
                        const dom = document.getElementById('modal_' + key);
                        if (!dom) {
                            continue;
                        }
                        dom.value = valor;
                    }
                }
            }
            if(response.ccInvitado){
                usuarioClienteExterno = true;
                changeEstadoCentroCostoDOM(false);
                response.ccInvitado.forEach(dato => {
                    idSelectClient = general.agregarNuevaListCliente(clientesInvitado, limiteCliente, idSelectClient, dato.cod_cliente, dato.centro_costo, dato.adicional.cc, dato.adicional.cuenta, response.ccInvitado,true,dato.id);
                });
                desactivarPrimeraLinia(true);
            }
            
            $('#modal-usuario-editar').modal("show");
        }
        if(e.target.classList.contains('btn-editar-roles')){
            const usuario = e.target.dataset.usuario;
            let datos = new FormData();
            datos.append('id_usuario',usuario);
            datos.append('accion','mostrar_roles');
            let response = await general.funcfetch('modificar',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            usuarioid = response.success.usuario;
            response.success.roles.forEach(r => {
                const listaRol = document.getElementById('usuario_rol_' + r.id);
                if(listaRol){
                    listaRol.checked = true;
                }
                if(r.id === 10 && r.active){
                    usuarioClienteExterno = true;
                }
            });
            $('#modal-usuario-roles').modal("show");
        }
    });
    function changeEstadoCentroCostoDOM(estado){
        [...document.querySelectorAll('#lista-contenedora-clientes select')].forEach((v,k) =>{
            if(k > 0){
                v.disabled = estado;
            }
        });
        for (const ocultar of document.querySelectorAll("#modal-usuario-editar .change_centro_costo")) {
            ocultar.hidden = estado;
        }
    }
    changeEstadoCentroCostoDOM(true);
    document.getElementById('agregar-nuevo-cliente').onclick = function () {
        desactivarPrimeraLinia(false);
        idSelectClient = general.agregarNuevaListCliente(clientesInvitado, limiteCliente, idSelectClient, null, null, null,null,null,true,null)
        desactivarPrimeraLinia(true);
    };
    const btnRestaurarPassword = document.getElementById('cambiar-password');
    btnRestaurarPassword.addEventListener('click',function(e){
        e.preventDefault();
        if(usuarioid == null){
            alertify.error('no se encontró el id del usuario');
            return
        }
        if($('#nuevo_password').val() == ""){
            alertify.error('por favor ingrese una contraseña');
            return
        }
        alertify.confirm('Alerta','¿Deseas restaurar la contraseña de este usuario?',async ()=>{
            let datos = new FormData();
            datos.append('id_usuario',usuarioid);
            datos.append('password',$('#nuevo_password').val());
            datos.append('accion','restaurar');
            let response = await general.funcfetch('modificar',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            alertify.alert('Alerta',response.success);
            tablaUsuarios.draw();
            $('#modal-usuario-password').modal("hide");
        },()=>{});
    });
    $('#modal-usuario-password').on('hidden.bs.modal', function (e) {
        usuarioid = null;
        $('#nuevo_password').val($('#nuevo_password').data('default'));
    });
    $('#modal-usuario-roles').on('hidden.bs.modal', function (e) {
        $('.checkbox-modal-rol').prop('checked',false);
        usuarioid = null;
        usuarioClienteExterno = false;
    });
    $('#modal-usuario-editar').on('hidden.bs.modal', function (e) {
        usuarioid = null;
        document.getElementById('frm-edid-user').reset();
        desactivarPrimeraLinia(false);
        general.eliminarListaCentroCostoModal(document.querySelector(clientesInvitado.listaClietnes).children);
        changeEstadoCentroCostoDOM(true);
    });
    document.querySelector(clientesInvitado.listaClietnes).onclick = function(event){
        if (event.target.classList.contains('btn-outline-danger')){
            const li = event.target.parentElement.parentElement.parentElement;
            if(li.dataset.type == 'old'){
                alertify.confirm("Mensaje","¿Estás seguro de eliminar este centro de costo?",async ()=>{
                    try {
                        let dato = new FormData();
                        dato.append('accion','eliminar_cc');
                        dato.append('id_usuario',usuarioid);
                        dato.append('id_cc',li.dataset.cliente);
                        const response = await general.funcfetch("modificar",dato);
                        if(response.success){
                            alertify.success('centro de costo eliminado correctamente');
                            general.eliminarListaCentroCosto(this,event,clientesInvitado);
                        }
                    } catch (error) {
                        console.error(error);
                        alertify.error('ocurrió un error al eliminar el centro de costo');
                    }
                },()=>{})
            }else{
                general.eliminarListaCentroCosto(this,event,clientesInvitado);
            }
        }
    }
    document.getElementById('actualizar-usuario').addEventListener('click',()=>{document.getElementById('btn-send-edid-user').click()});
    document.getElementById('frm-edid-user').addEventListener('submit',async function(e){
        e.preventDefault();
        if(usuarioid == null){
            alertify.error('no se encontró el id del usuario');
            return
        }
        let datos = new FormData(this);
        datos.append('id_usuario',usuarioid);
        datos.append('accion','actualizar');
        let datosNew = [];
        let datosUpdate = [];
        [...document.querySelector(clientesInvitado.listaClietnes).children].forEach(li => {
            if(li.dataset.type){
                let valor = {
                    idInvitado : null,
                    cliente : $(li.querySelector('.simple-select2-cliente')).val(),
                    centroCosto : $(li.querySelector('.simple-select2')).val(),
                    cuenta : $(li.querySelector('.cb-mult-nuevo')).val() ? $(li.querySelector('.cb-mult-nuevo')).val().join(";") : null
                }
                if(li.dataset.type == "new"){
                    datosNew.push(valor);
                }else{
                    valor.idInvitado = li.dataset.cliente;
                    datosUpdate.push(valor);
                }
            }
        });
        datos.delete('centro_costo[]');
        datos.delete('id_documento_cli[]');
        datos.delete('centro_cuenta[]');
        if(datosNew.length){
            datos.append('lista_agregar_cc',JSON.stringify(datosNew));
        }
        if(datosUpdate.length){
            datos.append('lista_actualizar_cc',JSON.stringify(datosUpdate));
        }
        let response = await general.funcfetch('modificar',datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        alertify.success(response.success);
        tablaUsuarios.draw();
        $('#modal-usuario-editar').modal("hide");
    });
    document.getElementById('btn-actualizar-rol').addEventListener('click',async function(e){
        let roles = new Array();
        for (const e of document.querySelectorAll('.checkbox-modal-rol')) {
            if(!e.checked){
                continue;
            }
            roles.push({
                id_rol : e.value
            });
            
        }
        if(roles.length === 0){
            alertify.error('debe de seleccionar al menos un rol');
            return
        }
        if(usuarioClienteExterno && typeof roles.find(rol => rol.id_rol == 10) === 'undefined'){
            alertify.confirm("Mensaje","Se detectó que se quitó el rol <b>Cliente - Operario</b>, recuerde que el usuario ya no tendrá acceso a los modulos correspondientes a ese rol, además se eliminarán los registros de los centros de costo.<br>¿Deseas continuar de todas formas?",()=>{
                modificarRolUsuario(usuarioid, roles,true);
            },()=>{})
            return
        }
        modificarRolUsuario(usuarioid, roles, false);
    });
    function desactivarPrimeraLinia(valor) {
        const listClientesInvitado = document.querySelector(clientesInvitado.listaClietnes);
        for (const select of listClientesInvitado.children[0].querySelectorAll('select')) {
            $(select).prop("disabled",valor);
        }
        listClientesInvitado.children[0].hidden = valor;
    }
    async function modificarRolUsuario(usuarioid,roles,eliminarRol) {  
        let datos = new FormData();
        datos.append('id_usuario', usuarioid);
        datos.append('roles', JSON.stringify(roles));
        datos.append('accion', 'actualizar_rol');
        if (eliminarRol){
            datos.append('elimiarRol', true);
        }
        let response = await general.funcfetch('modificar', datos);
        if (response.error) {
            alertify.alert('Error', response.error);
            return
        }
        alertify.success(response.success);
        $('#modal-usuario-roles').modal("hide");
    }

}
window.addEventListener('DOMContentLoaded',loadPage);