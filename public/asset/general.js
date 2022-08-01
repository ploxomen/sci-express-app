class General{
    token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fletes = null;
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
    verDetalleGuia(data) {
        if (!data) {
            return
        }
        $('#detalle-guias-todo').modal('show');
        for (const key in data) {
            if (Object.hasOwnProperty.call(data, key)) {
                const value = data[key];
                const dom = document.getElementById('id_' + key);
                if (!dom) {
                    continue;
                }
                dom.textContent = value;
            }
        }
        $('#galery-img').empty();
        $('#galery-img').nanogallery2('destroy');
        
        if (data.img_list.length > 0) {
            let arrayImg = new Array();
            data.img_list.forEach(img => {
                arrayImg.push({
                    src: img.nom_id_file,
                    srct: img.nom_id_file,
                    title: img.nom_file
                })
            });
            $('#galery-img').nanogallery2({
                thumbnailHeight: 100,
                thumbnailWidth: 100,
                thumbnailAlignment: 'center',
                thumbnailGutterWidth: 5,
                thumbnailGutterHeight: 5,
                thumbnailBorderHorizontal: 1,
                thumbnailBorderVertical: 1,
                itemsBaseURL: window.location.origin + "/intranet/storage/img/entrega/",
                items: arrayImg,
                thumbnailLabel: {
                    display: false
                }
            });
        } else {
            $('#galery-img').html(`<span class="text-danger d-block text-center">No se encontró imagenes de entrega para esta guía</span>`)
        }
        let template = '';
        if (data.detalles_guias) {
            data.detalles_guias.forEach(dg => {
                template += `
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Grupo Envio</b>${dg.grupo_envio}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Tipo Envio</b>${dg.tipo_envio}</div>
                            <div class="col-12 col-lg-4"><b class="d-block text-secondary">Embase o Embalaje</b>${dg.embase_embalaje}</div>
                            <div class="col-12"><b class="d-block text-secondary">Descripción</b>${!dg.descripcion ? 'No definido' : dg.descripcion}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Cantidad</b>${dg.cantidad}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Kg</b>${dg.kg}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Largo</b>${dg.largo}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Ancho</b>${dg.ancho}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Altura</b>${dg.altura}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Volumen</b>${dg.volumen}</div>
                        </div>
                    </div>
                </div>`;
            });
            document.getElementById('detalle_guias').innerHTML = template;
        }
    }
    totalBultos(domparent,domtotal){
        let total = 0;
        for (const element of domparent.querySelectorAll(domtotal)) {
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
    validarMinMaxTipoDocumento(text_data_input,valor){
        const nro = document.getElementById(text_data_input);
        let minMax = 15;
        switch (valor) {
            case '1':
                minMax = 12;
            break;
            case '3':
                minMax = 8;
            break;
            case '4':
                minMax = 11;
            break;
             case '5':
                minMax = 12;
            break;
        }
        nro.setAttribute('minlength',minMax);
        nro.setAttribute('maxlength',minMax);
    }
    calcularBultosFletes(modal,subTotalBulto,dataBase,dataTransfer) {
        subTotalBulto.querySelector('.sub-total').value = this.totalBultos(subTotalBulto, '.calc-total');
        let totalBulto = 0;
        $(`${modal} .calc-total`).map(function (k, v) {
            const vtotal = $(v).val() == "" || $(v).val() == null ? 0 : $(v).val();
            totalBulto += parseInt(vtotal);
        });
        $('#id_tota_bult').val(totalBulto);
        if (!this.fletes) {
            return
        }
        this.fletes.forEach(f => {
            if (f.ciudad_base == dataBase && dataTransfer == "TERRESTRE") {
                let total = 0;
                $(subTotalBulto).find('.calc-total').map(function (k, v) {
                    const fle = parseFloat(f[$(v).attr('data-search')]);
                    const mivalor = $(v).val() == "" || $(v).val() == null ? 0 : $(v).val();
                    total += isNaN(fle) ? 0 : fle * mivalor;
                });
                subTotalBulto.querySelector('.sub-flete').value = total.toFixed(2);
                $(subTotalBulto).find('.sub-flete').trigger("change");
                return
            }
        });
    }
   async obtenerFlestes(){
        let response = await this.funcfetch('obtener-fletes');
        if (response.error) {
            alertify.alert('Error', response.error);
            return
        }
        this.fletes = response.fletes;
    }
    calcularSubFlete(){
        let total = 0;
        $('#lista-ciudades-base .sub-flete').map(function (k, v) {
            const valor = $(v).val() == "" || $(v).val() == null ? 0 : $(v).val();
            total += parseFloat(valor);
        });
        $('#id_flet_esti').val(total.toFixed(2));
    }
    visualizarFletes(response, $terrestre, $aereo) {
        let template = ``,
            templateLand = ``,
            templateAerial = ``;
        let valoresFletes = {
            cajas: "",
            jabas: "",
            cajones: "",
            sobres: "",
            paquetes: "",
            otros: "",
            detalle_otros: "",
            sub_total_bultos: "",
            subtotal_fletes: ""
        }
        response.ciudades.forEach(c => {
            for (const key in c) {
                if (Object.hasOwnProperty.call(c, key)) {
                    const valores = c[key];
                    if (valoresFletes[key] == '' && valores !== null) {
                        valoresFletes[key] = valores;
                    }
                }
            }
            template = `
                <li data-cluster="${c.id_cluster}" data-transfer="${c.via_envio}">
                    <div class="form-row">
                        <div class="form-group col-12 col-md-6 col-lg-5">
                            <b>Ciudad Base:</b><span> ${c.nombreciudad}</span>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-7">
                            <b>Cluster:</b><span> ${c.nom_cluster}</span>
                        </div>
                        <div class="form-group col-6 col-lg-3">
                            <label for="total_bultos_${c.id_cluster}">Sub Cant <small class="text-danger">*</small></label>
                            <input type="number" id="total_bultos_${c.id_cluster}" min="1" class="form-control form-control-sm sub-total" readonly required value="${valoresFletes.sub_total_bultos}">
                        </div>
                        <div class="form-group col-6 col-lg-3">
                            <label for="sub_feletes_${c.id_cluster}">Sub Flete <small class="text-danger">*</small></label>
                            <input type="number" step="0.01" id="sub_feletes_${c.id_cluster}" readonly min="1" class="form-control form-control-sm sub-flete" required value="${valoresFletes.subtotal_fletes}">
                        </div>
                        <div class="col-12"></div>
                        <div class="form-group col-6 col-lg-2">
                            <label for="id_nro_cajas_${c.id_cluster}">Cajas</label>
                            <input type="number" name="nro_cajas" data-search="cajas" data-base="${c.nombreciudad.toUpperCase()}" data-transfer="${c.via_envio}" id="id_nro_cajas_${c.id_cluster}" class="form-control form-control-sm calc-total" min="0" value="${valoresFletes.cajas}">
                        </div>
                        <div class="form-group col-6 col-lg-2">
                            <label for="id_nro_jabas_${c.id_cluster}">Jabas</label>
                            <input type="number" name="nro_jabas" data-search="jabas" data-base="${c.nombreciudad.toUpperCase()}" data-transfer="${c.via_envio}" id="id_nro_jabas_${c.id_cluster}" class="form-control form-control-sm calc-total" min="0" value="${valoresFletes.jabas}">
                        </div>
                        <div class="form-group col-6 col-lg-2">
                            <label for="id_nro_cajones_${c.id_cluster}">Cajones</label>
                            <input type="number" name="nro_cajones" data-search="cajones" data-base="${c.nombreciudad.toUpperCase()}" data-transfer="${c.via_envio}" id="id_nro_cajones_${c.id_cluster}" class="form-control form-control-sm calc-total" min="0" value="${valoresFletes.cajones}">
                        </div>
                        <div class="form-group col-6 col-lg-2">
                            <label for="id_nro_sobres_${c.id_cluster}">Sobres</label>
                            <input type="number" name="nro_sobres" data-search="sobres" data-base="${c.nombreciudad.toUpperCase()}" data-transfer="${c.via_envio}" id="id_nro_sobres_${c.id_cluster}" class="form-control form-control-sm calc-total" min="0" value="${valoresFletes.sobres}">
                        </div>
                        <div class="form-group col-6 col-lg-2">
                            <label for="id_nro_paquetes_${c.id_cluster}">Paquetes</label>
                            <input type="number" name="nro_paquetes" data-search="paquetes" data-base="${c.nombreciudad.toUpperCase()}" data-transfer="${c.via_envio}" id="id_nro_paquetes_${c.id_cluster}" class="form-control form-control-sm calc-total" min="0" value="${valoresFletes.paquetes}">
                        </div>
                        <div class="form-group col-6 col-lg-2">
                            <label for="id_num_otros_${c.id_cluster}">Otros</label>
                            <input type="number" name="num_otros" data-search="parihuelas" data-base="${c.nombreciudad.toUpperCase()}" data-transfer="${c.via_envio}" id="id_num_otros_${c.id_cluster}" class="form-control form-control-sm calc-total" min="0" value="${valoresFletes.otros}">
                        </div>
                        <div class="form-group col-12">
                            <label for="id_detalle_otro_${c.id_cluster}">Detallar otro</label>
                            <textarea name="detalle_otro" maxlength="255" data-search="detalle_otros" id="id_detalle_otro_${c.id_cluster}" class="form-control form-control-sm">${valoresFletes.detalle_otros}</textarea>
                        </div>
                    </div>
                </li>`;
            if (c.via_envio == 'TERRESTRE') {
                templateLand += template;
            } else {
                templateAerial += template;
            }
            for (const key2 in valoresFletes) {
                valoresFletes[key2] = '';
            }
        });
        document.getElementById($terrestre).innerHTML = !templateLand ? '<li class="text-center">No se encontraron Envios Terrestres    </li>' : templateLand;
        document.getElementById($aereo).innerHTML = !templateAerial ? '<li class="text-center">No se encontraron Envios Aereos</li>' : templateAerial;
    }
}