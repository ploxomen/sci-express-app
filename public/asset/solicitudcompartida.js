function loadPage() {
    let general = new General();
    let contadorListaDetalle = 1;
    let nuevoProveedor = false;
    const listaDetalle = document.getElementById('lista-detalles-bultos');
    document.getElementById('agregar-datos-recojo').onclick = function(e){
        e.preventDefault();
        contadorListaDetalle++;
        $('#lista-detalles-bultos .cb-ubigeo').select2('destroy');
        const primerLugar = listaDetalle.children[0];
        const siguienteLugar = primerLugar.cloneNode(true);
        const listaCambio = siguienteLugar.querySelectorAll('label, select, input, textarea');
        for (const element of listaCambio) {
            const replace = element.dataset.replace + '_' + contadorListaDetalle;
            if (element.localName == 'label'){
                element.htmlFor = replace;
            }
            if(element.localName == 'select'){
                element.id = replace;
                if (element.dataset.consulta == 'provincia') {
                    element.value = "";
                    element.dataset.info = 'id_ciudad_cli_' + contadorListaDetalle;
                    element.dataset.infoDistrito = 'id_distrito_cli_' + contadorListaDetalle;
                } else if (element.dataset.consulta == 'distrito') {
                    element.innerHTML = "";
                    element.dataset.info = 'id_distrito_cli_' + contadorListaDetalle;
                }else{
                    element.innerHTML = "";
                }
            }
            if(element.localName == 'input' || element.localName == 'textarea'){
                element.value = "";
                element.id = replace;
            }
        }
        listaDetalle.append(siguienteLugar);
        calcularBultos(siguienteLugar.querySelectorAll('.calcular-bultos'));
        general.selectUbigeoCli(siguienteLugar);
        $('#lista-detalles-bultos .cb-ubigeo').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        });
        alertify.success('detalle agregado correctamente');
    }
    document.getElementById('eliminar-ultimo-dato-recojo').onclick = function(event){
        event.preventDefault();
        if (listaDetalle.children.length === 1){
            return alertify.error('se debe de tener al menos 1 dato de recojo');
        }
        alertify.confirm('Alerta','¿Desea eliminar la última fila de los datos del recojo?',()=>{
            listaDetalle.children[listaDetalle.children.length - 1].remove();
            alertify.success('ultimo dato eliminado');
            document.getElementById('id_cant_bulto').value = general.totalBultos(listaDetalle, '.calcular-bultos');
        },()=>{})
    }
    function calcularBultos($bultos){
        for (const calculo of $bultos) {
            calculo.addEventListener('change', function (e) {
                document.getElementById('id_cant_bulto').value = general.totalBultos(listaDetalle, '.calcular-bultos');
            });
        }
    }
    $('#id_nom_soli').on('select2:select', async function (event) {
        let datos = new FormData();
        datos.append('id_proveedor', $(this).val());
        try {
            let response = await general.funcfetch(window.location.origin +  '/intranet/solicitud/ver-proveedor', datos);
            if (response.error) {
                alertify.alert('Error', response.error);
                return
            }
            if (!response.proveedor) {
                nuevoProveedor = true;
                document.getElementById('id_doc_iden_soli').value = "";
                document.getElementById('id_nro_doc_soli').value = "";
                return
            }
            nuevoProveedor = false;
            document.getElementById('id_doc_iden_soli').value = response.proveedor.tipo_docu_prov;
            document.getElementById('id_nro_doc_soli').value = response.proveedor.nro_docu_prov;
            alertify.confirm('Atención', '¿Deseas sustituir la informacion de recojo?', () => {
                for (const key in response.proveedor) {
                    if (Object.hasOwnProperty.call(response.proveedor, key)) {
                        const value = response.proveedor[key];
                        const nodo = document.querySelector(`[data-proveedor='${key}']`);
                        if (!nodo) {
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
                $('#id_depart_cli_recojo').trigger('change');
                general.validarMinMaxTipoDocumento($('#id_doc_iden_soli').data('input'), $('#id_doc_iden_soli').val());
            }, () => { });
            $('.cb-tipo-doc').trigger('change');

        } catch (error) {
            alertify.alert('Error', error.message);
        }
    });
    
    calcularBultos(listaDetalle.querySelectorAll('.calcular-bultos'))

}
window.addEventListener('DOMContentLoaded', loadPage);