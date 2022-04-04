class General{
    token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    jsonToken = {
        'X-CSRF-TOKEN' : this.token,
        'X-Requested-With' : 'XMLHttpRequest'
    }
    async funcfetch(url,dato){
        let response = await fetch(url,{
            headers: this.jsonToken,
            method: 'POST',
            body: dato
        })
        return response.json();
    }
    selectUbigeoCli(element){
        let $cbUbigeos = element.querySelectorAll('.cb-ubigeo');
        for (const element of $cbUbigeos) {
            $(element).on('select2:select',async (e)=>{
                let cbUbigeo = document.getElementById(e.target.dataset.info);
                if(!cbUbigeo){
                    return
                }
                let consulta = e.target.dataset.consulta;
                if(consulta == 'provincia'){
                    document.getElementById(e.target.dataset.infoDistrito).innerHTML = '';
                }
                try {
                    let response = consulta == 'provincia' ? await this.getProvincias($(element).val()) : await this.getDistritos($(element).val());
                    this.transforData(response,cbUbigeo);
                } catch (error) {
                    console.error(error);
                    alertify.alert('Error','Error al cargar los datos');
                }
            })
        }
    }
    totalBultos(domtotal){
        let total = 0;
        for (const element of document.querySelectorAll(domtotal)) {
            const valor = parseInt(element.value);
            total += isNaN(valor) ? 0 : valor;
        }
        return total;
    }
    primerProveedor(boleano,element){
        element.children[0].hidden = boleano;
        for (const ele of element.children[0].querySelectorAll(`[data-selected="true"]`)) {
            if(ele.tagName == 'SELECT' || ele.tagName == 'INPUT'){
                ele.value = "";
                ele.disabled = boleano;
                if(ele.classList.contains('cb-ubigeo') || ele.classList.contains('cb-mult-nuevo')){
                    $(ele).trigger('change');
                }
            }
        }
    }
    transforData(data,cbUbigeo){
        let template = `<option value=""></option>`;
        data.forEach(e => {
            template += `<option value="${e.id_ubigeo}">${e.nombre_ubigeo}</option>`;
        });
        cbUbigeo.innerHTML = template;
    }
    getDepartamentos(){
        return this.funcfetch(window.location.origin + '/intranet/ubigeo/departamentos',null);
    }
    getProvincias(id_ubigeo_departamento){
        let datos = new FormData();
        datos.append('id_ubigeo',id_ubigeo_departamento);
        return this.funcfetch(window.location.origin + '/intranet/ubigeo/provincias',datos);
    }
    getDistritos(id_ubigeo_provincia){
        let datos = new FormData();
        datos.append('id_ubigeo',id_ubigeo_provincia);
        return this.funcfetch(window.location.origin + '/intranet/ubigeo/distritos',datos);
    }
    detectSession(message) {  
        if(message == 'CSRF token mismatch.'){
            return 'La sesión caducó, por favor vuelva a iniciar sesíon o actualize la página';
        }else{
            return message;
        }
    }
    isObjEmpty(obj){
        for (let prop in obj) {
            if (obj.hasOwnProperty(prop)) return false;
        }
        return true;
    }
    tempSeleCli(cliente) {
        if (!cliente.id) {
            return cliente.text;
        }
        let span = document.createElement('span');
        span.style.fontSize = '13px';
        let template = `<b>${cliente.element.dataset.nro}</b><br><span>${cliente.text}</span>`;
        span.innerHTML = template;
        return span;
    }
}