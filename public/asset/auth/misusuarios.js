function loadPage(){
    let general = new General();
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
                    <button class="btn btn-primary btn-editar" data-usuario="${data}" title="Ver más detalles"><i class="fas fa-user-edit"></i></button>
                    <button class="btn btn-info btn-restore" data-usuario="${data}" title="Restaurar contraseña"><i class="fas fa-undo"></i></button>
                    <button class="btn btn-primary btn-editar-roles" data-usuario="${data}" title="Editar Roles"><i class="fas fa-dice"></i></i></button>
                    <button class="btn btn-danger btn-delete" data-usuario="${data}" title="Eliminar cuenta"><i class="far fa-trash-alt"></i></button>
                </div>`;
            }}
        ]
    });
    const tbodyUsuarios = document.getElementById("lista-usuarios");
    let usuarioid = null;
    tbodyUsuarios.addEventListener('click',async function(e){
        if(e.target.classList.contains('btn-delete')){
            const usuario = e.target.dataset.usuario;
            alertify.confirm('Alerta','¿Deseas eliminar este usuario?<br>Si acepta ya no podrá restaurarlo',async function(){
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
            usuarioid = response.success.id;
            for (const key in response.success) {
                if (Object.hasOwnProperty.call(response.success, key)) {
                    const valor = response.success[key];
                    const dom = document.getElementById('modal_' + key);
                    if(!dom){
                        continue;
                    }
                    dom.value = valor;
                }
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
            });
            $('#modal-usuario-roles').modal("show");
        }
    });
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
    });
    $('#modal-usuario-editar').on('hidden.bs.modal', function (e) {
        usuarioid = null;
        document.getElementById('frm-edid-user').reset();
    });
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
        let datos = new FormData();
        datos.append('id_usuario',usuarioid);
        datos.append('roles',JSON.stringify(roles));
        datos.append('accion','actualizar_rol');
        let response = await general.funcfetch('modificar',datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        alertify.success(response.success);
        $('#modal-usuario-roles').modal("hide");
    });

}
window.addEventListener('DOMContentLoaded',loadPage);