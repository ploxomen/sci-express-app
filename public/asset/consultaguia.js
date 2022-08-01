function loadPage(){
    function tipoConsulta(event){
        let active = event.target.value == 'remitente' ? false : true;
        for (const elementRemitente of document.querySelectorAll('.remitente-required')) {
            elementRemitente.required = event.target.value == 'remitente' ? true : false;
        }
        for (const box of document.querySelectorAll('.ocultar-box')) {
            box.hidden = true;
        }
        document.getElementById('nro_guia_sci').required = active;
        document.getElementById('seleccionar-documentos').hidden = active;
    }
    for (const element of document.querySelectorAll('.tipo-consulta')) {
        element.addEventListener('change',tipoConsulta);
    }
    document.getElementById('consulta-guia').addEventListener('submit',function(event){
        event.preventDefault();
        for (const box of document.querySelectorAll('.ocultar-box')) {
            box.hidden = true;
        }
        for (const textos of document.querySelectorAll('.text-proceso')) {
            textos.textContent = '';
        }
        for (const textoSpan of document.querySelectorAll('.text-guias')) {
            textoSpan.innerHTML = '<span>No establecido</span>';
        }
        const datos = new FormData(this);
        let request = new XMLHttpRequest();
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                if(!request.response.result){
                    alertify.alert('ALERTA','NO SE ENCONTRARON RESULTADOS, FAVOR VERIFICAR LA INFORMACION CON SU ALMACEN');
                    return false;
                }
                for (const key in request.response.result) {
                    if (Object.hasOwnProperty.call(request.response.result, key)) {
                        const valor = request.response.result[key];
                        const dom = document.getElementById('info_'+key);
                        if(key == 'comprobantes'){
                            for (const keyc in valor) {
                                if (Object.hasOwnProperty.call(valor, keyc)) {
                                    const element = valor[keyc];
                                    if(element.length > 0){
                                        const domguias = document.getElementById('info_' + keyc);
                                        if(domguias){
                                            domguias.innerHTML = `<span class='d-block'>` + element.join(`</span><span class="d-block">`) + "</span>";
                                        }
                                    }
                                }
                            }
                        }
                        if(key == 'seguimiento'){
                            for (let i = 0; i < valor.length; i++) {
                                const domse = document.getElementById('info_fecha_rastreo_' + valor[i].id);
                                if(!domse){
                                    continue;
                                }
                                domse.textContent = valor[i].fecha_cambio;
                            }
                        }
                        if(!dom){
                            continue;
                        }
                        dom.textContent = valor;
                    }
                }
                for (const box of document.querySelectorAll('.ocultar-box')) {
                    box.hidden = false;
                }
            }
        }
        request.open('POST','guia/datos');
        request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        request.responseType = 'json';
        request.send(datos);
    });
}
window.addEventListener('DOMContentLoaded',loadPage);