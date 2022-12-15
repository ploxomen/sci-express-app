function loadPage(){
    let cbProvincias = document.getElementById('id_ciudad_cli_recojo');
    let cbDistritos = document.getElementById('id_distrito_cli_recojo');
    let cbProveedores = document.getElementById('id_nom_soli');
    let cbInvitados = document.getElementById('id_solicitante');
    let cbCentroCostos = document.getElementById('id_centro_costo');
    let cbCuentaInvitado = document.getElementById('id_cuenta_invitado');
    let nuevoProveedor = false;
    let general = new General();
    $(cbProveedores).select2({
        theme: 'bootstrap',
        width: '100%',
        tags: true,
        placeholder : 'Seleccione un proveedor'
    }); 
    $('#id_documento_cli').select2({
        theme: 'bootstrap',
        width: '100%',
        templateResult: general.tempSeleCli,
        placeholder: 'Seleccione un cliente'
    });
    $(cbInvitados).on('select2:select',async function(e){
        let dato = new FormData();
        dato.append('usuario',$(this).val());
        dato.append('cliente', $('#id_documento_cli').val());
        try {
            let response = await general.funcfetch(window.location.origin + '/intranet/solicitud/obtener-cliente-cc', dato);
            let template = '';
            response.centrosCostos.cc.forEach((c, k) => {
                template += `<option value="${c}" ${k === 0 ? "selected" : ''}>${c}</option>`;
            });
            cbCentroCostos.innerHTML = template;
            template = "";
            response.centrosCostos.cuenta.forEach((c, k) => {
                template += `<option value="${c}" ${k === 0 ? "selected" : ''}>${c}</option>`;
            });
            cbCuentaInvitado.innerHTML = template;
            template = '';
        } catch (error) {
            alertify.error('error al obtener los cc del invitado');
            console.error(error);
        }
    });
    document.getElementById('id_responsable_solicitud').onchange = function(e){
        general.setAtributos('.datos-proveedor, .datos-invitado','required',false);
        switch (this.value) {
            case 'PROVEEDOR':
                general.setAtributos('.datos-proveedor', 'required', true);
                break;
            case 'INVITADO':
                general.setAtributos('.datos-invitado', 'required', true);
                break;
        }
    }
    $('#id_documento_cli').on('select2:select',async function(event){
        let dato = new FormData();
        dato.append('id',$(this).val());
        try {
            let response = await general.funcfetch(window.location.origin + '/intranet/solicitud/obtener-cliente',dato);
            if(response.error){
                alertify.error('Error',response.error);
                return
            }
            limpiarFrm();
            let template = ``;
            response.provincias.forEach(prov => {
                template += `<option value="${prov.id_ubigeo}">${prov.nombre_ubigeo}</option>`;
            });
            cbProvincias.innerHTML = template;
            template = '';
            response.distritos.forEach(dist => {
                template += `<option value="${dist.id_ubigeo}">${dist.nombre_ubigeo}</option>`;
            });
            cbDistritos.innerHTML = template;
            template = `<option value=""><option>`;
            if(response.proveedores.length){
                response.proveedores.forEach(prov => {
                    template += `<option value="${prov.id}">${prov.nomb_prov}</option>`;
                });
            }
            cbCentroCostos.innerHTML = "<option></option>";
            cbCuentaInvitado.innerHTML = "<option></option>";
            cbProveedores.innerHTML = template;
            template = '';
            if(response.invitados.length){
                template += "<option></option>";
                response.invitados.forEach(inv => {
                    template += `<option value="${inv.id}">${inv.name + ' ' + inv.last_name}</option>`;
                });
            }
            cbInvitados.innerHTML = template;
            template = '';
            for (const key in response.cliente) {
                if (Object.hasOwnProperty.call(response.cliente, key)) {
                    const element = response.cliente[key];
                    const domelement = document.querySelector(`[data-cliente="${key}"]`);
                    if(!domelement){
                        continue;
                    }
                    domelement.value = element;
                }
            }
            $('#id_depart_cli_recojo').trigger('change');
            $('.cb-tipo-doc').trigger('change');
            general.validarMinMaxTipoDocumento($('#id_doc_iden_cli').data('input'),$('#id_doc_iden_cli').val());
        } catch (error) {
            console.error(error);
            alertify.alert('Error',error.message);
        }
    });
    function limpiarFrm(){
        for (const element of document.querySelectorAll('.text-clear')) {
            element.value = "";
        }
    }
    let frmSoli = document.getElementById('agregar-solicitud');
    frmSoli.addEventListener('submit',async function(event){
        event.preventDefault();
        let datos = new FormData(this);
        datos.append('id_cliente',$('#id_documento_cli').val());
        datos.append('nuevo_prov',nuevoProveedor);
        try {
            if(nuevoProveedor){
                alertify.confirm('Alerta','Se a detectado un nuevo proveedor, ¿Deseas guardarlo para la proxima solicitud?',()=>{
                    datos.append('agregar_prov',true);
                    agregarProveedor(datos);
                },()=>{
                    datos.append('agregar_prov',false);
                    agregarProveedor(datos);
                }).set('labels',{ok : 'SI', cancel: 'NO'});
            }else{
                agregarProveedor(datos);
            }
            
        } catch (error) {
            alertify.alert('Error',error.message);
            console.error(error);
        }
    });
    async function agregarProveedor(datos){
        let response = await general.funcfetch('nuevo',datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        alertify.alert('Mensaje',response.success,()=>{window.location.reload();});
    }
}
window.addEventListener('DOMContentLoaded',loadPage);
