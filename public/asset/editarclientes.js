function loadPage(){
    let general = new General();
    let datosCli = new FormData();
    datosCli.append('nro_doc',document.getElementById('id_nro_doc_cli').value);
    general.funcfetch('obtener-editar',datosCli).then(data => {
        if(data.info){
            alertify.alert('Alerta',data.info,() => {window.history.back()});
            return
        }
        $('.cb-ubigeo').select2('destroy');
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
        $('.cb-ubigeo').select2({
            theme : 'bootstrap',
            width : '100%',
            placeholder: 'Seleccione una opción'
        });
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
                dom.value = element;
            }
        }
    }).catch( error => {
        console.error(error);
        alertify.alert('Error',error.message);
    });
}
window.addEventListener('DOMContentLoaded',loadPage);