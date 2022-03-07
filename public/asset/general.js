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
    async obtenerSolicitud(datos){
        let template = ``;
        await this.funcfetch(window.location.origin + '/intranet/solicitud/obtener',datos).then(data => {
            if(!data.solicitudes.length){
                template = `<tr><td colspan="11" class="text-center">Sin registros para mostrar</td></tr>`;
            }else{
                data.solicitudes.forEach(e => {
                    let cbEstados = document.createElement('select');
                    cbEstados.classList.add('form-control','form-control-sm','change-estado');
                    cbEstados.dataset.idSolicitud = e.id;
                    let tmpEst = ``;
                    data.estados.forEach(est => {
                        tmpEst += `<option value="${est.id}" ${est.id == e.estado ? 'selected' : ''}>${est.estado}</option>`;
                    });
                    cbEstados.innerHTML = tmpEst;
                    template += `<tr>
                    <td>${e.id.toString().padStart(5,'0')}</td>
                    <td>${e.fecha_soli}</td>
                    <td>${e.hr_estimado}</td>
                    <td>${e.nom_cli}</td>
                    <td>${e.direccion_cli}</td>
                    <td>${cbEstados.outerHTML}</td>
                    <td>${e.cant_bulto}</td>
                    <td>${e.nom_soli}</td>
                    <td>${e.tipo}</td>
                    <td>${e.dia_recojo}</td>
                    <td><button class='btn btn-primary btn-sm btn_editar' title="Editar recojo" data-idsolicitud="${e.id}"><i class="fas fa-edit"></i></button></td>
                    </tr>`
                });
            }
        });
        return template;
    }
    tempSeleCli(cliente) {
        if (!cliente.id) {
            return cliente.text;
        }
        let span = document.createElement('span');
        span.style.fontSize = '13px';
        let template = `<b>${cliente.id}</b><br><span>${cliente.text}</span>`;
        span.innerHTML = template;
        return span;
    }
}