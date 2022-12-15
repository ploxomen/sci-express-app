function loadPage(){
    let general = new General();
    let cbProvincias = document.getElementById('id_ciudad_cli_recojo');
    let cbDistritos = document.getElementById('id_distrito_cli_recojo');
    let cbCentroCostos = document.getElementById('id_centro_costo');
    let cbCuentaInvitado = document.getElementById('id_cuenta_invitado');
    let cbProveedores = document.getElementById('id_nom_soli');
    $('#id_documento_cli').select2({
        theme: 'bootstrap',
        width: '100%',
        templateResult: general.tempSeleCli,
        placeholder: 'Seleccione un cliente'
    });
    $('#id_documento_cli').on('select2:select', async function (event) {
        let dato = new FormData();
        dato.append('id', $(this).val());
        try {
            let response = await general.funcfetch(window.location.origin + '/intranet/solicitud/obtener-cliente-invitado', dato);
            if (response.error) {
                alertify.error('Error', response.error);
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
            template = '';
            response.centrosCostos.cc.forEach((c,k) => {
                template += `<option value="${c}" ${k === 0 ? "selected" : ''}>${c}</option>`;
            });
            cbCentroCostos.innerHTML = template;
            template = '<option value="Ninguno">Ninguno</option>';
            response.centrosCostos.cuenta.forEach((c, k) => {
                template += `<option value="${c}" ${k === 0 ? "selected" : ''}>${c}</option>`;
            });
            cbCuentaInvitado.innerHTML = template;
            template = `<option value=""><option>`;
            if (response.proveedores.length) {
                // $('.limitar-proveedor').prop('required', true);
                response.proveedores.forEach(prov => {
                    template += `<option value="${prov.id}">${prov.nomb_prov}</option>`;
                });
            } else {
                // $('.limitar-proveedor').prop('required', false);
            }
            cbProveedores.innerHTML = template;
            for (const key in response.cliente) {
                if (Object.hasOwnProperty.call(response.cliente, key)) {
                    const element = response.cliente[key];
                    const domelement = document.querySelector(`[data-cliente="${key}"]`);
                    if (!domelement) {
                        continue;
                    }
                    domelement.value = element;
                }
            }
            $('#id_depart_cli_recojo').trigger('change');
            $('#id_centro_costo').trigger('change');
            $('.cb-tipo-doc').trigger('change');
            general.validarMinMaxTipoDocumento($('#id_doc_iden_cli').data('input'), $('#id_doc_iden_cli').val());
        } catch (error) {
            console.error(error);
            alertify.alert('Error', error.message);
        }
    });
    function limpiarFrm() {
        for (const element of document.querySelectorAll('.text-clear')) {
            element.value = "";
        }
    }
    let frmSoli = document.getElementById('agregar-solicitud');
    frmSoli.addEventListener('submit', async function (event) {
        event.preventDefault();
        let datos = new FormData(this);
        datos.append('cliente_id', $('#id_documento_cli').val());
        datos.append('doc_cliente', $('#id_doc_iden_cli').val());
        datos.append('nro_doc_cliente', $('#id_nro_doc_cli').val());
        datos.append('doc_proveedor', $('#id_doc_iden_soli').val());
        datos.append('nro_proveedor', $('#id_nro_doc_soli').val());
        datos.append('departamento', $('#id_depart_cli_recojo').val());
        datos.append('provincia', $('#id_ciudad_cli_recojo').val());
        datos.append('distrito', $('#id_distrito_cli_recojo').val());
        datos.append('direccion', $('#id_dir_sede_so').val());
        let response = await general.funcfetch(window.location.origin +'/intranet/solicitud/nuevo-cliente', datos);
        if(response.success){
            alertify.alert('Mensaje',response.success,()=>{window.location.reload()});
        }
    });


}
window.addEventListener('DOMContentLoaded',loadPage);