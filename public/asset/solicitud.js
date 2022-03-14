function loadPage(){
    let cbProvincias = document.getElementById('id_ciudad_cli');
    let cbDistritos = document.getElementById('id_distrito_cli');
    let cbProveedores = document.getElementById('id_nom_soli');
    let nuevoProveedor = false;
    let general = new General();
    $(cbProveedores).select2({
        theme: 'bootstrap',
        width: '100%',
        tags: true,
        placeholder : 'Seleccione un proveedor'
    })
    $('#id_documento_cli').on('select2:select',async function(event){
        let dato = new FormData();
        dato.append('id',$(this).val());
        try {
            let response = await general.funcfetch('obtener-cliente',dato);
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
            cbProveedores.innerHTML = template;
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
            $('#id_depar_cli').trigger('change');
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
    $(cbProveedores).on('select2:select',async function(event){
        console.log(event);
        let datos = new FormData();
        datos.append('id_proveedor',$(this).val());
        try {
            let response = await general.funcfetch('ver-proveedor',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            if(!response.proveedor){
                nuevoProveedor = true;
                document.getElementById('id_doc_iden_soli').value = "";
                document.getElementById('id_nro_doc_soli').value = "";
                return
            }
            nuevoProveedor = false;
            document.getElementById('id_doc_iden_soli').value = response.proveedor.tipo_docu_prov;
            document.getElementById('id_nro_doc_soli').value = response.proveedor.nro_docu_prov;
            alertify.confirm('Atención','¿Deseas sustituir la informacion de recojo?',()=>{
                for (const key in response.proveedor) {
                    if (Object.hasOwnProperty.call(response.proveedor, key)) {
                        const value = response.proveedor[key];
                        const nodo = document.querySelector(`[data-proveedor='${key}']`);
                        if(!nodo){
                            continue;
                        }
                        switch (key) {
                            case 'prov_prov':
                                let template = `<option value=""></option>`;
                                response.proveedor.listProv.forEach(prov => {
                                    template += `<option value="${prov.id_ubigeo}">${prov.nombre_ubigeo}</option>`;
                                });
                                nodo.innerHTML = template;
                                nodo.value = value;
                            break;
                            case 'dist_prov':
                                let template2 = `<option value=""></option>`;
                                response.proveedor.listDist.forEach(prov => {
                                    template2 += `<option value="${prov.id_ubigeo}">${prov.nombre_ubigeo}</option>`;
                                });
                                nodo.innerHTML = template2;
                                nodo.value = value;
                            break;
                            default:
                                nodo.value = value;
                            break;
                        }
                    }
                }
                $('#id_depar_cli').trigger('change');
            },()=>{});

        } catch (error) {
            alertify.alert('Error',error.message);
            
        } 
    })
    $('#id_documento_cli').select2({
        theme : 'bootstrap',
        width: '100%',
        templateResult: general.tempSeleCli,
        placeholder: 'Seleccione un cliente'
    });
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
