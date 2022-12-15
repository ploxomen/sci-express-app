function loadPage(){
    let general = new General();
    let datosCli = new FormData();
    let listProv = document.getElementById('list-proveedores');
    datosCli.append('nro_doc',document.getElementById('id_nro_doc_cli').value);
    general.funcfetch('obtener-editar',datosCli).then(data => {
        if(data.info){
            alertify.alert('Alerta',data.info,() => {window.history.back()});
            return
        }
        if(data.proveedores.length){
            $('#list-proveedores .cb-ubigeo, #list-proveedores .cb-mult-nuevo').select2('destroy');
            general.primerProveedor(false,listProv);
            data.proveedores.forEach(prov => {
                let liPrimero = listProv.children[0];
                let liSegundo = liPrimero.cloneNode(true);
                liSegundo.dataset.tipo = 'antiguo';
                liSegundo.dataset.proveedor = prov.id;
                let modificar = liSegundo.querySelectorAll(`[data-selected="true"]`);
                let n = 0;
                for (const element of modificar) {
                    if(element.tagName == 'SELECT' || element.tagName == 'INPUT'){
                        element.id = 'id_antiguo_' + element.dataset.name + "_" + prov.id;
                        modificar[n-1].setAttribute('for',element.id);
                        switch (element.dataset.name) {
                            case 'depa_prov':
                                general.transforData(prov.listDepa,element);
                            break;
                            case 'prov_prov':
                                general.transforData(prov.listProv,element);
                            break;
                            case 'dist_prov':
                                general.transforData(prov.listDist,element);
                            break;
                        }
                        if(element.dataset.name == 'tele_prov' || element.dataset.name == 'email_prov'){
                            let tempOpt = '';
                            if(!prov[element.dataset.name]){
                                continue;
                            }
                            prov[element.dataset.name].split(';').map((v) => {
                                tempOpt += `<option value="${v}" selected>${v}</option>`;
                            });
                            element.innerHTML = tempOpt;
                        }else{
                            element.value = prov[element.dataset.name];
                        }
                    }
                    n++;
                }
                modificar[7].dataset.info = modificar[9].id;
                modificar[7].dataset.infoDistrito = modificar[11].id;
                modificar[9].dataset.info = modificar[11].id;
                listProv.append(liSegundo);
                general.selectUbigeoCli(liSegundo);
            });
            $('#list-proveedores .cb-ubigeo').select2({
                theme : 'bootstrap',
                width: '100%',
                placeholder: 'Seleccione una opción'
            });
            $('#list-proveedores .cb-mult-nuevo').select2({
                theme: 'bootstrap',
                width: '100%',
                tags: true
            });
            listProv.children[0].remove();
        }
        for (const key in data.ubigeos) {
            if (Object.hasOwnProperty.call(data.ubigeos, key)) {
                const element = data.ubigeos[key];
                if(element){
                    let template = '';
                    for (let i = 0; i < element.length; i++) {
                        template += `<option value="${element[i].id_ubigeo}">${element[i].nombre_ubigeo}</option>`;
                    }
                    document.getElementById('id_'+key).innerHTML = template;
                }
            }
        }
        for (const key in data.cliente) {
            if (Object.hasOwnProperty.call(data.cliente, key)) {
                const element = data.cliente[key];
                const dom = document.getElementById('id_' + key);
                if(!dom){
                    continue;
                }
                if(key == 'dist_sa' || key == 'dist_so' || key == 'dpto_so' || key == 'dpto_sa' || key == 'prov_so' || key == 'prov_sa'){
                    $(dom).val(element).trigger("change");
                }
                if(key == 'aprob_file' || key == 'aprob_cred' || key == 'alta_clien'){
                    dom.checked = element ? true : false;
                    continue;
                }
                if (key == 'centros_costo' && element != null && element != ''){
                    let optNroCentroCosto = "";
                    element.split(';').forEach(opt => {
                        optNroCentroCosto += `<option value="${opt}" selected>${opt}</option>`;
                    });
                    $(dom).html(optNroCentroCosto);
                    $(dom).trigger("change");
                    continue;
                }
                dom.value = element;
            }
        }
        $('.simple-select2').trigger('change');
        for (const el of document.querySelectorAll('.cb-tipo-doc')) {
            general.validarMinMaxTipoDocumento(el.dataset.input,$(el).val());
        }
    }).catch( error => {
        console.error(error);
        alertify.alert('Error',error.message);
    });
}
window.addEventListener('DOMContentLoaded',loadPage);