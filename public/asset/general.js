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
                console.log(consulta);

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
                if (key == 'nro_guia' && data['prefijo'] === null){
                    dom.textContent = 'Orden de Servicio N° ' + value;
                    continue;
                }
                if(key == 'nro_guia' && data['prefijo'] !== null) {
                    dom.textContent = 'Guía N° ' + value;
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
    opcionesAlejadas = [
        {
            value :'Rural - Alejada',
            text : 'Rural - Alejada (S/)'
        },
        {
            value : 'Complejo - Distante',
            text: 'Complejo - Distante (%)'
        },
        {
            value: 'FAU',
            text: 'FAU (%)'
        }
    ];
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
    setAtributos(querySelector, atributo, valor) {
        [...document.querySelectorAll(querySelector)].forEach(element => element[atributo] = valor);
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
            const existIdFlete = c.id_embarque_flete ? `data-flete="${c.id_embarque_flete}"` : '';
            template = `
                <li class="lista-fletes" data-cluster="${c.id_cluster}" ${existIdFlete} data-nombre-ciudad="${c.nombreciudad}" data-transfer="${c.via_envio}" data-ciudad="${c.id_ciudad}" data-distrito="${c.ubigeo_distrito}" data-tipo-ciudad="${c.tipo_base}">
                    <div class="form-row">
                        <div class="form-group col-12 col-md-6 col-lg-5">
                            <b>Ciudad Base:</b><span> ${c.nombreciudad}</span>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-7">
                            <b>Cluster:</b><span> ${c.nom_cluster}</span>
                        </div>
                        <div class="form-group col-6 col-lg-3">
                            <label for="total_bultos_${c.id_cluster}">Sub Cant <small class="text-danger">*</small></label>
                            <input type="number" id="total_bultos_${c.id_cluster}" min="1" class="form-control form-control-sm sub-total" required value="${valoresFletes.sub_total_bultos}">
                        </div>
                        <div class="form-group col-6 col-lg-3">
                            <label for="sub_feletes_${c.id_cluster}">Sub Flete <small class="text-danger">*</small></label>
                            <input type="number" step="0.01" id="sub_feletes_${c.id_cluster}" readonly min="1" class="form-control form-control-sm sub-flete" value="${valoresFletes.subtotal_fletes}">
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
        document.getElementById($terrestre).innerHTML = !templateLand ? '<li class="text-center">No se encontraron Envios Terrestres</li>' : templateLand;
        document.getElementById($aereo).innerHTML = !templateAerial ? '<li class="text-center">No se encontraron Envios Aereos</li>' : templateAerial;
    }
    validarDealerEmbarque($cbDealer2,value){
        $(`#${$cbDealer2} option`).prop('disabled', false);
        if (value == $(`#${$cbDealer2}`).val()) {
            $(`#${$cbDealer2}`).val('NINGUNO').trigger('change');
        }
        $(`#${$cbDealer2} option[value=` + value + "]").prop('disabled', true);
    }
    validarResponsableEmbarque($cbResponsable,value){
        if(value == 'NINGUNO'){
            $(`#${$cbResponsable}`).val('Dealer 1').trigger('change');
        }
        $(`#${$cbResponsable} option[value='Dealer 2']`).prop('disabled', value == 'NINGUNO' ? true : false);
    }
    verInformeEmbarque(response){
        let nroBultos = {
            bulto_total: isNaN(parseInt(response.embarque.total_bultos_paquete)) ? 0 : parseInt(response.embarque.total_bultos_paquete),
            embarque_nro_cajas: 0,
            embarque_nro_jabas: 0,
            embarque_nro_cajones: 0,
            embarque_nro_sobres: 0,
            embarque_nro_paquetes: 0,
            embarque_num_otros: 0
        };
        let template = "";
        let cbComprobante = null,
            cbNroComprobantes = "";
        response.embarque.manfiestos.forEach(e => {
            for (const key in nroBultos) {
                if (Object.hasOwnProperty.call(nroBultos, key)) {
                    nroBultos[key] += isNaN(parseInt(e[key])) ? 0 : parseInt(e[key]);
                }
            }
            cbNroComprobantes = "";
            cbComprobante = e.comprobantes ? e.comprobantes.split(';') : null;
            if (e.nro_comprobantes) {
                e.nro_comprobantes = e.nro_comprobantes.split(';');
                e.nro_comprobantes.forEach(nro => {
                    cbNroComprobantes += `<option value="${nro}" selected>${nro}</option>`;
                });
            }
            let empresasTransporte = document.createElement(response.embarque.lista_empresas_transporte ? 'select' : 'input');
            empresasTransporte.classList.add('form-control','form-control-sm');
            empresasTransporte.id = 'empresa_trasporte_' + e.id;
            empresasTransporte.required = true;
            if (response.embarque.lista_empresas_transporte) {
                empresasTransporte.classList.add('simple-select2');
                let templateEmpre = "";
                response.embarque.lista_empresas_transporte.forEach(emp => {
                    templateEmpre += `<option value="${emp.id}" ${emp.id === e.id_empr_tran_emba ? 'selected' : ''}>${emp.empresa}</option>`;
                });
                empresasTransporte.innerHTML = templateEmpre;
            }else{
                empresasTransporte.setAttribute('value', e.empresa_trasporte);
                empresasTransporte.type = 'text';
                empresasTransporte.disabled = true;
            }
            const tipoAreos = ["AEREO","AEREA"];
            const requerido = tipoAreos.indexOf(e.tipo_trayecto) >= 0 ? '' : 'required';
            template += `
                        <li class='informe-ciudades' data-ciudad="${e.id}" data-trayecto="${e.tipo_trayecto}">
                            <div class="form-row">
                                <div class="form-group col-7 col-lg-4">
                                    <b>Ciudad Base:</b><span> ${e.nombreciudad}</span>
                                </div>
                                <div class="form-group col-5 col-lg-8">
                                    <b>Envio:</b><span> ${e.tipo_trayecto}</span>
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-5">
                                    <label for="id_comprobantes_${e.id}">Comprobantes ${requerido != '' ? '<small class="text-danger">*</small>' : ''}</label>
                                    <select id="id_comprobantes_${e.id}" class="form-control simple-select2 form-control-sm" multiple ${requerido}>
                                        <option value="GUIA TRANSPORTISTA" ${cbComprobante && cbComprobante.indexOf('GUIA TRANSPORTISTA') >= 0 ? 'selected' : ''}>GUIA TRANSPORTISTA</option>
                                        <option value="FACTURA" ${cbComprobante && cbComprobante.indexOf('FACTURA') >= 0 ? 'selected' : ''}>FACTURA</option>
                                        <option value="MANIFIESTO" ${cbComprobante && cbComprobante.indexOf('MANIFIESTO') >= 0 ? 'selected' : ''}>MANIFIESTO</option>
                                        <option value="ORDEN DE SERVICIO" ${cbComprobante && cbComprobante.indexOf('ORDEN DE SERVICIO') >= 0 ? 'selected' : ''}>ORDEN DE SERVICIO</option>
                                    </select>
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-5">
                                    <label for="id_nro_comprobantes_${e.id}">N° Comprobantes ${requerido != '' ? '<small class="text-danger">*</small>' : ''}</label>
                                    <select id="id_nro_comprobantes_${e.id}" ${requerido} multiple class="form-control cb-select2-tags form-control-sm">${cbNroComprobantes}</select>
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-2">
                                    <label for="id_importe_factura_${e.id}">Importe Factura ${requerido != '' ? '<small class="text-danger">*</small>' : ''}</label>
                                    <input type="number" value="${e.importe_factura}" ${requerido} step="0.01" id="id_importe_factura_${e.id}" class="form-control form-control-sm" step="0.01" min="0">
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-3">
                                    <label for="empresa_trasporte_${e.id}">Empresa <small class="text-danger">*</small></label>
                                    ${empresasTransporte.outerHTML}
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-3">
                                    <label for="id_embarque_fch_emba_${e.id}">Fecha embarque <small class="text-danger">*</small></label>
                                    <input type="date" id="id_embarque_fch_emba_${e.id}" value="${response.embarque.fch_emba}" required class="form-control form-control-sm" disabled>
                                </div>
                                <div class="form-group col-6 col-lg-3">
                                    <label for="id_fecha_llegada_${e.id}">Fecha llegada ${requerido != '' ? '<small class="text-danger">*</small>' : ''}</label>
                                    <input type="date" id="id_fecha_llegada_${e.id}" value="${e.fecha_llegada}" class="form-control form-control-sm" ${requerido}>
                                </div>
                                <div class="form-group col-6 col-lg-3">
                                    <label for="id_hr_llegada_${e.id}">Hr llegada ${requerido != '' ? '<small class="text-danger">*</small>' : ''}</label>
                                    <input type="time" id="id_hr_llegada_${e.id}" value="${e.hr_llegada}" class="form-control form-control-sm" ${requerido}>
                                </div>
                                `
                                if (e.guiasEmbarque) {
                                    const nroGuiaEmbarque = e.prefijo_guia_embarque == null && e.nro_guia_embarque == null ? '' : e.prefijo_guia_embarque.toString().padStart(3, '0') + '-' + e.nro_guia_embarque.toString().padStart(7, '0');
                                    template += `
                                                <div class="col-12 form-group border-top pt-2"></div>
                                                <div class="col-12 form-group col-md-6 col-lg-8">
                                                    <h6 class="text-info mb-0">GUÍA DE EMBARQUE - ${e.nombreciudad}</h6>
                                                </div>
                                                <div class="col-12 col-md-6 form-group col-lg-4">
                                                    <input type="text" required pattern="[0-9]{3}[-][0-9]{7}" id="id_guia_embarque_${e.id}" value="${nroGuiaEmbarque}" maxlength="11" class="form-control form-control-sm informe-embarque" placeholder="N° guía embarque">
                                                </div>
                                                <div class="form-group col-12">
                                                        `
                                    e.guiasEmbarque.forEach(g => {
                                        const nroGuia = g.prefijo !== null ? g.prefijo.toString().padStart(3, '0') + '-' + g.nro_guia.toString().padStart(7, '0') : g.nro_guia.toString().padStart(7, '0');
                                        template += `
                                            <span class="p-2">N° ${nroGuia}</span>
                                        `
                                    });
                                    template += `</div>`;
                                }
            template += "</div></li>";
        });
        for (const key in nroBultos) {
            if (Object.hasOwnProperty.call(nroBultos, key)) {
                if (key == 'bulto_total') {
                    document.getElementById('informe_' + key).textContent = nroBultos[key];
                    continue;
                }
                document.getElementById('informe_' + key).value = nroBultos[key];
            }
        }
        document.getElementById('informes-embarques').innerHTML = template;
        $('#modal-informe-embarque').modal('show');
        $('#modal-informe-embarque .simple-select2').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        });
        $('#modal-informe-embarque .cb-select2-tags').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione o agregue una opción',
            tags: true
        });
    }
    changeTipoGuia(valor){
        $('.data-anulada-oculta').prop('hidden', valor == '3' ? true : false);
        $('.data-anulada').prop('required', valor == '3' ? false : true);
        $('.data-anulada-int').prop('min', valor == '3' ? '0' : '1');
        $('.data-anulada-decimal').prop('min', valor == '3' ? '0' : '0.01');
    }
    async obtenerDetalleGuia(){
        return await this.funcfetch('obtener-detalle-tabla');
    }
    validarSeleccionComprobantes(valor,idComprobante,idNroComprobante){
        const valores = valor;
        if (valores) {
            const exist = valores.filter(opt => opt == 'NINGUNO');
            if (exist.length) {
                $(idComprobante).val(["NINGUNO"]).trigger("change");
                $(idNroComprobante).val("").trigger("change");
            }
            document.querySelector(idNroComprobante).required = exist.length === 0 ? true : false;
            document.querySelector(idNroComprobante).disabled = exist.length === 0 ? false : true;
        }
    }
    agregarNuevoDetalleGuia($estadoGuia,$cbDuplicado){
        $('#tabla-detalle ' + $cbDuplicado).select2('destroy');
        let tr = document.getElementById('tabla-detalle').children[document.getElementById('tabla-detalle').children.length - 1];
        const cloneTr = tr.cloneNode(true);
        let $estadoGuiaVal = document.getElementById($estadoGuia) || $estadoGuia;

        for (const i2 of cloneTr.querySelectorAll('input, select')) {
            i2.value = i2.localName === "input" && i2.getAttribute('step') == '0.01' ? "0.00" : "0";
            if ($estadoGuiaVal == '3') {
                i2.required = false;
            }
            if (i2.localName === 'select' || i2.localName === 'textarea'){
                i2.value = tr.querySelector(`[data-name="${i2.dataset.name}"]`).value;
            }
        }
        const buttonDelete = cloneTr.querySelector('button');
        if (buttonDelete){
            buttonDelete.dataset.tipo = 'new';
            buttonDelete.removeAttribute('data-detalle');
        }
        document.getElementById('tabla-detalle').append(cloneTr);
        $('#tabla-detalle ' +$cbDuplicado).select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        });
        alertify.success('Se agregó otro detalle');
    }
    changeTipoGuiaFormato(idNroGuia,value,max,condicion,textLabel,dataClient){
        const nroGuia = document.getElementById(idNroGuia);
        if(value){
            nroGuia.value = value;
        }
        nroGuia.setAttribute('maxlength', max);
        nroGuia.pattern = condicion;
        nroGuia.parentElement.querySelector('label').innerHTML = `${textLabel} <small class="text-danger">*</small>`;
        for (const d of document.querySelectorAll('.label-disabled-os')) {
            d.classList.remove('d-inline', 'd-none');
            d.classList.add(!dataClient ? 'd-none' : 'd-inline');
        }
        for (const inp of document.querySelectorAll('.input-disabled-os')) {
            inp.required = dataClient;
        }
    }
    async getCiudadesFinales(value,$cbCiudades){
        let datosn = new FormData();
        datosn.append('distrito', value);
        let response = await this.funcfetch(window.location.origin + '/intranet/ubigeo/ciudades', datosn);
        let template = `<option></option>`;
        response.ciudades.forEach(c => {
            template += `<option value="${c.id}">${c.nombre_ciudad}</option>`;
        });
        document.querySelector($cbCiudades).innerHTML = template;
    }
    async getCCInvitados(cbInvitado,$cbCliente) {
        const $ccCosto = $($(cbInvitado).attr('data-cuenta'));
        const $selectedCliente = $cbCliente.selectedOptions[0];
        if ($(cbInvitado).val() == 'ninguno') {
            $ccCosto.val("").empty().trigger('change');
            $ccCosto.prop('disabled', true);
        } else {
            let dato = new FormData();
            dato.append('usuario', $(cbInvitado).val());
            dato.append('cliente', $selectedCliente.getAttribute('data-type') == 'proveedor' ? $selectedCliente.getAttribute('data-id-cliente') : $cbCliente.value);
            let response = await this.funcfetch(window.location.origin + '/intranet/solicitud/obtener-cliente-cc', dato);
            let template = '';
            response.centrosCostos.cc.forEach((c, k) => {
                template += `<option value="${c}" ${k === 0 ? "selected" : ''}>${c}</option>`;
            });
            $ccCosto.html(template);
            $ccCosto.prop('disabled', false);
        }
    }
    async getDataRemitenteGuia(cbBox,idRemiProv,idRemiDist,idRemiDepa,idRemiTipoDocu,idInvitado){
        let datos = new FormData();
        const tipoCbRemi = cbBox.selectedOptions[0].getAttribute('data-type');
        datos.append('tipo', tipoCbRemi);
        datos.append('id', $(cbBox).val());
        if (tipoCbRemi == 'proveedor') {
            datos.append('idcliente', cbBox.selectedOptions[0].getAttribute('data-id-cliente'));
        }
        try {
            let response = await this.funcfetch(window.location.origin + '/intranet/comprobante/guias/obtener-clipro', datos);
            if (response.error) {
                alertify.alert('Error', response.error);
                return null;
            }
            const ccInvitado = $(idInvitado).attr('data-cuenta');
            $(ccInvitado).val(null).empty().trigger('change');
            $(ccInvitado).prop('disabled',true);
            $(idInvitado).val(null).empty();
            $(idInvitado).append(new Option('Ninguno','ninguno',true,false));
            if(response.invitados.length > 0){
                response.invitados.forEach(invitado => {
                    const optNew = new Option(invitado.name + ' ' + invitado.last_name,invitado.id,false,false);
                    $(idInvitado).append(optNew)
                });
            }
            $(idInvitado).trigger('change');
            if (response.prov.length > 0) {
                this.transforData(response.prov, document.querySelector(idRemiProv));
            }
            if (response.dist.length > 0) {
                this.transforData(response.dist, document.querySelector(idRemiDist));
            }
            for (const key in response.datos) {
                if (Object.hasOwnProperty.call(response.datos, key)) {
                    const valor = response.datos[key];
                    const element = document.querySelector(tipoCbRemi == 'proveedor' ? `[data-valor-proveedor="${key}"]` : `[data-valor-cliente="${key}"]`);
                    if (!element) {
                        continue;
                    }
                    element.value = valor;
                }
            }
            $(idRemiDepa).trigger('change');
            $(idRemiTipoDocu).trigger('change');
            return tipoCbRemi;
        } catch (error) {
            console.error(error);
            alertify.alert('Error', error.message);
            return tipoCbRemi;
        }
    }
    renderComplejoDistante(response, complejoDistante){
        if (response.complejo && response.complejo.ciudad_alejada != null && response.complejo.ciudad_alejada != 'Ninguno') {
            complejoDistante.hidden = false;
            document.querySelector(complejoDistante.dataset.texto).textContent = response.complejo.ciudad_alejada + ' ' + (response.complejo.ciudad_alejada == 'Rural - Alejada' ? '(S/)' : '(%)');
            document.querySelector(complejoDistante.dataset.input).value = response.complejo.costo_ciudad_alejada;
            document.querySelector(complejoDistante.dataset.input).disabled = false;
            document.querySelector(complejoDistante.dataset.input).dataset.tipo = response.complejo.ciudad_alejada;
        } else {
            complejoDistante.hidden = true;
            document.querySelector(complejoDistante.dataset.input).disabled = true;
        }
    }
    async consultaComplejoDistante(distrito, tipo, idComplejoDistante, ciudad = null){
        let datos = new FormData();
        datos.append("distrito", distrito);
        datos.append("tipo",tipo);
        if (ciudad != null){
            datos.append("ciudad", ciudad);
        }
        try {
            const response = await this.funcfetch(window.location.origin + "/intranet/comprobante/guias/consulta-distante",datos);
            if(response.error){
                return alertify.alert('Error', response.error);
            }
            const complejoDistante = document.querySelector(idComplejoDistante);
            this.renderComplejoDistante(response, complejoDistante);
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener el complejo distante");
        }
    }
    async getDataDestGuia(cbBox, idRemitDepa, idDestTipoDocu,idDestDepa ,idDestPorv, idDestDist, idCiudadFinal,idDiasTransito,idViaEnvio,idComplejoDistante, estado){
        let datos = new FormData();
        datos.append('id', $(cbBox).val());
        datos.append('departamentoOrigen', $(idRemitDepa).find("option:selected").text());
        try {
            let response = await this.funcfetch(window.location.origin +  '/intranet/comprobante/guias/detalle-cli-de-cli', datos);
            if (response.error) {
                alertify.alert('Error', response.error);
                return false;
            }
            if (!response.datos) {
                this.clearCliDeCli(idDestTipoDocu, idDestPorv, idDestDist, idCiudadFinal);
                return true;
            }
            const complejoDistante = document.querySelector(idComplejoDistante);
            if(response.complejo){
                this.renderComplejoDistante(response,complejoDistante);
            }
            if (response.prov.length > 0 && estado < 2) {
                this.transforData(response.prov, document.querySelector(idDestPorv));
            }
            if (response.dist.length > 0 && estado < 2) {
                this.transforData(response.dist, document.querySelector(idDestDist));
            }
            if (response.ciudades.length > 0 && estado < 2) {
                let template2 = "<option></option>";
                response.ciudades.forEach(c => {
                    template2 += `<option value="${c.id}">${c.nombre_ciudad}</option>`;
                });
                document.querySelector(idCiudadFinal).innerHTML = template2;
            }
            for (const key in response.datos) {
                if (Object.hasOwnProperty.call(response.datos, key)) {
                    const valor = response.datos[key];
                    const dom = document.querySelector(`[data-cli-cli="${key}"]`);
                    if (!dom) {
                        continue;
                    }
                    dom.value = valor;
                }
            }
            $(idDestTipoDocu).trigger('change');
            $(idDestDepa).trigger('change');
            $(idCiudadFinal).trigger('change');
            if (!response.dias) {
                document.querySelector(idDiasTransito).value = "";
                alertify.error('no se encontró los dias de transito para este destino');
            }else{
                this.calcularDiasTransito(idViaEnvio, idDiasTransito, response.dias.total_origen_base_hr_aereo, response.dias.total_hr_origen_destino);
            }
            return false;
        } catch (error) {
            console.error(error);
            alertify.alert('Error', error.message);
            return false;
        }
    }
    async calculoDiasTrancitoCb(idRemiDepa,idDestDepa,idDestProv,idDiasTran,idViaEnvi){
        let datos = new FormData();
        datos.append('departamento_origen', $(idRemiDepa).find("option:selected").text());
        datos.append('departamento_destino', $(idDestDepa).find("option:selected").text());
        datos.append('provincia_destino', $(idDestProv).find("option:selected").text());
        datos.append('ciudad_final', $(this).find("option:selected").text());
        let response = await this.funcfetch(window.location.origin + '/intranet/comprobante/guias/obtener-dias', datos);
        if (response.error) {
            alertify.alert('Error', response.error);
            return
        }
        if (!response.dias) {
            document.querySelector(idDiasTran).value = "";
            alertify.error('no se encontró los dias de transito para este destino');
            return
        }
        this.calcularDiasTransito(idViaEnvi, idDiasTran, response.dias.total_origen_base_hr_aereo, response.dias.total_hr_origen_destino);
    }
    clearCliDeCli(idDestTipoDocu,idDestPorv,idDestDist,idCiudadFinal) {
        for (const e of document.querySelectorAll(`[data-cli-cli]`)) {
            e.value = "";
        }
        $('.cb-dest').val("").trigger("change");
        $(idDestTipoDocu).val("").trigger("change");
        $(idDestPorv).html("");
        $(idDestDist).html("");
        $(idCiudadFinal).html("");
    }
    calcularDiasTransito(idViaEnvio,domId, aereo, terrestre) {
        let diasEnvio = 0;
        switch ($(idViaEnvio).val()) {
            case '1':
                diasEnvio = Math.ceil(terrestre / 24);
                break;
            case '2':
                diasEnvio = Math.ceil(aereo / 24);
                break;
            case '5':
                diasEnvio = Math.ceil(aereo / 24);
                break;
        }
        document.querySelector(domId).value = diasEnvio;
    }
    formaterFecha(fecha,separador){
        const fechaDestruc = fecha.split('-');
        return fechaDestruc[2] + separador + fechaDestruc[1] + separador + fechaDestruc[0];
    }
    formaterHora(hora) {
        const fechaDestruc = hora.split(':');
        let amorpm = "AM";

        if (parseInt(fechaDestruc[0]) >= 13){
            fechaDestruc[0] = (parseInt(fechaDestruc[0]) - 12).toString().padStart(2,'0');
            amorpm = "PM";
        } else if (parseInt(fechaDestruc[0]) === 0){
            fechaDestruc[0] = 12;
        } else if (parseInt(fechaDestruc[0]) === 12){
            fechaDestruc[0] = 12;
            amorpm = "PM";
        }
        return fechaDestruc[0] + ":" + fechaDestruc[1] + " " + amorpm;
    }
    agregarNuevaListCliente(clientesInvitado, limiteCliente, idTemp, valCliente, valCc,listCc,listNumeroCuenta,listaClientes,update,idInvitado){
        const listaClienteCC = document.querySelector(clientesInvitado.listaClietnes);
        if (listaClienteCC.children.length >= limiteCliente) {
            return alertify.error('el limite de clientes es de ' + (update ? limiteCliente - 1 : limiteCliente));
        }
        $(clientesInvitado.listSelectCliente).off("change");
        $(clientesInvitado.listSelectCliente + ', ' + clientesInvitado.listaClietnes + ' .simple-select2, ' + clientesInvitado.listaClietnes + ' .cb-mult-nuevo').select2('destroy');
        const idSelectClient = +idTemp + 1;
        const listaCopia = listaClienteCC.children[0].cloneNode(true);
        listaCopia.setAttribute("data-type",update && valCliente ? 'old' : 'new');
        listaCopia.setAttribute("data-cliente",update && idInvitado ? idInvitado : 'no definido');
        const $cbListCliente = listaCopia.querySelector(clientesInvitado.listSelectCliente);
        $cbListCliente.id = "documento_cli_" + idSelectClient;
        $cbListCliente.required = true;
        if (valCliente){
            for (const opt of $cbListCliente.options) {
                const exist = listaClientes.filter(cuenta => cuenta.cod_cliente === +opt.value);
                if (exist.length > 0 && +opt.value !== +valCliente){
                    opt.setAttribute('disabled','disabled');
                }
            }
            $cbListCliente.value = valCliente;
        }
        const optSelected = this.clienteSeleccionados(clientesInvitado);
        const $cbCentroCosto = listaCopia.querySelector(".simple-select2")
        $cbCentroCosto.id = "cc_cli_" + idSelectClient;
        $cbCentroCosto.required = true;
        let template = "";
        if (valCc){
            template = `<option value="Ninguno">Ninguno</option>`;
            if (listCc.length > 0){
                template += `<option value="CC" ${'CC' == valCc ? 'selected' : ''}>CC de acuerdo a envio</option>`;
            }
            listCc.forEach(cc => {
                template += `<option value="${cc}" ${cc == valCc ? 'selected' : ''}>${cc}</option>`;
            });
        }
        $cbCentroCosto.innerHTML = template;
        const $cbNroCuenta = listaCopia.querySelector(".cb-mult-nuevo");
        $cbNroCuenta.id = "cuenta_cli_" + idSelectClient;
        template = "";
        if (listNumeroCuenta){
            listNumeroCuenta.forEach(nro => {
                if(nro != 'Ninguno'){
                    template += `<option value="${nro}" selected>${nro}</option>`;
                }
            });
        }
        $cbNroCuenta.innerHTML = template;

        listaClienteCC.append(listaCopia);
        if (valCliente === null){
            listaClienteCC.querySelectorAll("#documento_cli_" + idSelectClient + " option").forEach(opt => {
                opt.disabled = optSelected.indexOf(opt.value) === -1 ? false : true;
            });
        }
        
        let configSelect2 = {
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione un cliente',
            tags : false
        }
        $(clientesInvitado.listSelectCliente).select2(configSelect2);
        configSelect2.placeholder = "Seleccione una opción";
        $(clientesInvitado.listaClietnes + ' .simple-select2').select2(configSelect2);
        configSelect2.placeholder = "";
        configSelect2.tags = true;
        $(clientesInvitado.listaClietnes + ' .cb-mult-nuevo').select2(configSelect2);
        
        $(clientesInvitado.listSelectCliente).change(event => {
            this.consultarCentroCosto($(event.target), $(event.target).parents('li').find('.simple-select2'), clientesInvitado);
        });
        return idSelectClient;
    }
    async consultarCentroCosto(elemenThis, cbCentroCosto, clientesInvitado){
        const idCliente = elemenThis.val();
        const seleccionClientes = this.clienteSeleccionados(clientesInvitado);
        for (const cbCliente of document.querySelectorAll(clientesInvitado.listSelectCliente)) {
            if (cbCliente != elemenThis) {
                cbCliente.querySelectorAll('option').forEach(opt => {
                    const valor = opt.selected ? opt.value : null;
                    const valorSelectAll = seleccionClientes.indexOf(opt.value);
                    opt.disabled = valorSelectAll >= 0 && valor != seleccionClientes[valorSelectAll] ? true : false;
                });
            }
        }
        let datos = new FormData();
        datos.append('cliente', idCliente);
        let result = await this.funcfetch('obtener-centro-costo', datos);
        let template = `<option value="Ninguno">Ninguno</option>`;
        if (result.success) {
            template += `<option value="CC">CC de acuerdo a envio</option>`;
            result.success.forEach(centroCosto => {
                template += `<option value="${centroCosto}">${centroCosto}</option>`;
            });
        }
        $(cbCentroCosto).html(template);
    }
    clienteSeleccionados(clientesInvitado){
        let idClientes = [];
        const optSelected = document.querySelector(clientesInvitado.listaClietnes).querySelectorAll(".simple-select2-cliente option:checked");
        optSelected.forEach(opt => {
            if (opt.value != '') {
                idClientes.push(opt.value);
            }
        });
        return idClientes;
    }
    nuevoCiuadaAlejada(contadorLi,ulListCiudad){
        $('#lista-destino-final .cb-ubigeo, #lista-destino-final .cb-select2-tags, #lista-destino-final .simple-select2').select2('destroy');
        const liPrimero = ulListCiudad.children[0];
        const liDuplicado = liPrimero.cloneNode(true);
        liDuplicado.setAttribute('data-type','new');
        liDuplicado.querySelector(".simple-select2").setAttribute("data-costo","#costo-new-alejado-" + contadorLi);
        liDuplicado.querySelector(".simple-select2").id = "opcion-alejado-new-destino-" + contadorLi;
        liDuplicado.querySelector(".cb-ubigeo").setAttribute('id','id_distrito_new_clu_' + contadorLi);
        liDuplicado.querySelector(".cb-ubigeo").setAttribute('data-ciudades','#id_ciudad_new_destino_' + contadorLi);
        liDuplicado.querySelector(".cb-select2-tags").setAttribute('id','id_ciudad_new_destino_' + contadorLi);
        liDuplicado.querySelector("input").id = "costo-new-alejado-" + contadorLi;
        liDuplicado.querySelector("input").value = "";
        liDuplicado.querySelector("input").disabled = true;
        liDuplicado.querySelector(".selection-base").checked = false;
        for (const element of liDuplicado.querySelectorAll("select")) {
            element.value = "";
            if(element.classList.contains('cb-select2-tags')){
                element.innerHTML = '<option></option>';
            }
            if (element.classList.contains('simple-select2')) {
                element.value = "Ninguno";
            }
        }
        ulListCiudad.append(liDuplicado);
        alertify.success('accion relizada');
        $('#lista-destino-final .cb-ubigeo').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        }).on('select2:select',async (e) => {
            let datosn = new FormData();
            datosn.append('distrito',$(e.target).val());
            let response = await this.funcfetch(window.location.origin + '/intranet/ubigeo/ciudades',datosn);
            let template = `<option></option>`;
            response.ciudades.forEach(c => {
                template += `<option value="${c.id}">${c.nombre_ciudad}</option>`;
            });
            document.querySelector($(e.target).attr('data-ciudades')).innerHTML = template;
        });
        $('#lista-destino-final .cb-select2-tags').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder : 'Seleccione o agregue una opción',
            tags: true,
        });
        $('#lista-destino-final .simple-select2').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        }).on('change',this.changeCiudadAlejada);
    }
    changeCiudadAlejada(e){
        if (e.target.value == 'Ninguno'){
            $(e.target.dataset.costo).prop('disabled',true);
            $(e.target.dataset.costo).prop('required', false);
            $(e.target.dataset.costo).val("");
        }else{
            $(e.target.dataset.costo).prop('disabled', false);
            $(e.target.dataset.costo).prop('required', true);
        }
    }
    eliminarListaCentroCostoModal($listaCentroCosto){
        [...$listaCentroCosto].forEach((l,k) =>{
            if(k > 0){
                l.remove();
            }
        });
    }
    eliminarListaCentroCosto(contenedor,evento,clientesInvitado){
        if (contenedor.children.length === 1){
            alertify.error('El usuario debe de tener al menos un cliente');
            return
        }
        evento.target.parentElement.parentElement.parentElement.remove();
        const seleccionClientes = this.clienteSeleccionados(clientesInvitado);
        for (const cbCliente of document.querySelectorAll(clientesInvitado.listSelectCliente)) {
            cbCliente.querySelectorAll('option').forEach(opt => {
                if (contenedor.children.length === 1) {
                    opt.disabled = false;
                }else{
                    const valor = opt.selected ? opt.value : null;
                    const valorSelectAll = seleccionClientes.indexOf(opt.value);
                    opt.disabled = valorSelectAll >= 0 && valor != seleccionClientes[valorSelectAll] ? true : false;
                }
            });
        }
    }
    templateDetalle(id, grupo_envio, tipo_envio, embase_embalaje, descripcion, cantidad, kg, largo, ancho, altura, volumen,detalleTabla) {
        const detalleId = id ? `data-detalle="${id}" data-tipo="old"` : `data-tipo="new"`;
        let templateGrupoEnvio = "<option></option>";
        detalleTabla.grupo_envio.forEach(ge => {
            templateGrupoEnvio += `<option value="${ge}" ${ge == grupo_envio ? 'selected' : ''}>${ge}</option>`;
        });
        let templateTipoEnvio = "<option></option>";
        detalleTabla.tipo_envio.forEach(ge => {
            templateTipoEnvio += `<option value="${ge}" ${ge == tipo_envio ? 'selected' : ''}>${ge}</option>`;
        });
        let templateEnbaseEmba = "<option></option>";
        detalleTabla.envase_embalaje.forEach(ge => {
            templateEnbaseEmba += `<option value="${ge}" ${ge == embase_embalaje ? 'selected' : ''}>${ge}</option>`;
        });
        return `<tr>
        <td>
            <select required data-name="grupo_envio" class="data-anulada form-control form-control-sm select-new-detalle">
            ${templateGrupoEnvio}
            </select>
        </td>
        <td>
            <select required data-name="tipo_envio" class="data-anulada form-control form-control-sm select-new-detalle">
            ${templateTipoEnvio}
            </select>
        </td>
        <td>
            <textarea data-name="descripcion" class="form-control form-control-sm clear-detalle-guia-text" rows="1">${descripcion}</textarea>
        </td>
        <td>
            <select required data-name="embase_embalaje" class="data-anulada form-control form-control-sm select-new-detalle">
            ${templateEnbaseEmba}
            </select>
        </td>
        <td>
            <input required data-name="cantidad" type="number" value="${cantidad}" class="clear-detalle-guia data-anulada-int data-anulada form-control form-control-sm" onchange="calcTotal(event,4,'id_editar_tot_cant')" min="1">
        </td>
        <td>
            <input required data-name="kg" type="number" value="${kg}" class="clear-detalle-guia-decimal data-anulada-decimal data-anulada form-control form-control-sm" onchange="calcTotal(event,5,'id_editar_tot_kg')" min="0.01" step="0.01">
        </td>
        <td>
            <input required data-name="largo" type="number" value="${largo}" class="clear-detalle-guia-decimal data-anulada-decimal data-anulada form-control form-control-sm" onchange="calcVolumen(event)" min="0.01" step="0.01">
        </td>
        <td>
            <input required data-name="ancho" type="number" value="${ancho}" class="clear-detalle-guia-decimal data-anulada-decimal data-anulada form-control form-control-sm" onchange="calcVolumen(event)" min="0.01" step="0.01">
        </td>
        <td>
            <input required data-name="altura" type="number" value="${altura}" class="clear-detalle-guia-decimal data-anulada-decimal data-anulada form-control form-control-sm" onchange="calcVolumen(event)" min="0.01" step="0.01">
        </td>
        <td>
            <input required data-name="volumen" type="number" value="${volumen}" class="clear-detalle-guia-decimal data-anulada-decimal data-anulada form-control form-control-sm" onchange="calcTotal(event,9,'id_editar_tot_vol')" min="0.01" step="0.01">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger eliminar-detalle-guia" ${detalleId}><i class="far fa-trash-alt"></i></button>
        </td>`
    }
    valoresFormatoGuia(valor){
        return {
            idTxtNroGuia: 'id_editar__nro_guia',
            nroGuia: null,
            max: valor === "1" ? 11 : 7,
            condicion: valor === "1" ? "[0-9]{3}[-][0-9]{7}" : "[0-9]{7}",
            textLabel: valor === "1" ? "N° Guía" : "N° OS",
            valid: valor === "1" ? true : false
        }
    }
    eliminarDetalleGuia(event,$tablaDetalle,valEditarEstado,idGuia){
        if (event.target.dataset.tipo == 'old') {
            if (valEditarEstado === '1' && $tablaDetalle.children.length === 1) {
                return alertify.alert('Alerta', 'Las guías activas deben de tener al menos un detalle');
            }
            alertify.confirm('Mensaje', '¿Estás seguro de eliminar este detalle de la guía?, una vez eliminado no se podrá recuperar', async () => {
                let datos = new FormData();
                datos.append('situacion_guia', valEditarEstado);
                datos.append('detalle', event.target.dataset.detalle);
                datos.append('guia', idGuia);
                const response = await this.funcfetch('eliminar-detalle-guia', datos);
                if (response.error) {
                    return response.error(response.error);
                }
                if ($tablaDetalle.children.length > 1) {
                    event.target.parentElement.parentElement.remove();
                } else {
                    this.clearDetalleGuia(this.children.length);
                }
                this.recalcularTabla();
                alertify.success(response.success);
            }, () => {});
        } else{
            if ($tablaDetalle.children.length > 1) {
                event.target.parentElement.parentElement.remove();
            } else {
                this.clearDetalleGuia($tablaDetalle.children.length);
            }
            this.recalcularTabla();
            alertify.success('Detalle de guía eliminada correctamente');
        }
    }
    recalcularTabla() {
        $(document.querySelector(`[data-name="cantidad"]`)).trigger('change');
        $(document.querySelector(`[data-name="kg"`)).trigger('change');
        $(document.querySelector(`[data-name="largo"]`)).trigger('change');
    }

    clearDetalleGuia(cantidad) {
        if (cantidad === 1){
            $('#tabla-detalle tr td button').removeAttr('data-detalle');
            $('#tabla-detalle tr td button').attr('data-tipo','new');
        }
        $('.clear-detalle-guia').val('0');
        $('.clear-detalle-guia-text').val('');
        $('.clear-detalle-guia-decimal').val('0.00');
        $('.select-new-detalle').val("").trigger('change');
    }
    actualizarDatos($formularioEditar,tipoCbRemi,idGuia,idEstado,newCliCli,url,modal,$cbCliente,tablaGuias){
        let datos = new FormData($formularioEditar);
        let tablaDetalle = document.getElementById('tabla-detalle');
        const idCliente = tipoCbRemi == 'proveedor' ? $cbCliente.selectedOptions[0].getAttribute('data-id-cliente') : $($cbCliente).val();
        datos.append('tipo_guia', tipoCbRemi);
        datos.append('id_guia', idGuia);
        datos.append('id_cliente_facturacion', idCliente);
        let detalleUpdate = new Array();
        let detalleNew = new Array();
        for (const e of tablaDetalle.children) {
            let objeto = new Object();
            let deta = e.querySelectorAll('input, select, textarea');
            const newOrOld = e.querySelector('button');
            for (const ele2 of deta) {
                if (ele2.value && (ele2.value != '0.00' || ele2.value != '0')) {
                    objeto[ele2.dataset.name] = ele2.value;
                }
            }
            if (!this.isObjEmpty(objeto)) {
                if (newOrOld && newOrOld.getAttribute('data-tipo') == 'new'){
                    detalleNew.push(objeto);
                }
                if (newOrOld && newOrOld.getAttribute('data-tipo') == 'old') {
                    objeto.idDetalle = newOrOld.getAttribute('data-detalle'); 
                    detalleUpdate.push(objeto);
                }
            }
        }
        if ((!detalleNew.length && !detalleUpdate.length) && idEstado != '3') {
            alertify.alert('Mensaje', 'Por favor agregue detalles a la guía');
            return
        }
        if (detalleNew.length) {
            datos.append('detalle_guia_new', JSON.stringify(detalleNew));
        }
        if (detalleUpdate.length) {
            datos.append('detalle_guia_update', JSON.stringify(detalleUpdate));
        }
        if (newCliCli) {
            const nuevoCliente = alertify.confirm('Mensaje', 'Se a detectado un nuevo cliente del cliente. <br> ¿Deseas guardarlo para la proxima vez?', null, null);
            nuevoCliente.set({
                'invokeOnCloseOff': true,
                'closable': false,
                'labels': {
                    ok: 'Si',
                    cancel: 'No'
                }
            });
            nuevoCliente.set('onok', function () {
                datos.append('add_cli_bool', true);
                datos.append('add_cli_cli',true);
                this.updateGuia(datos,url,modal,tablaGuias);
            });
            nuevoCliente.set('oncalcel', function () {
                datos.append('add_cli_bool', false);
                this.updateGuia(datos,url,modal,tablaGuias);
            });
        } else {
            this.updateGuia(datos,url,modal,tablaGuias);
        }
    }
    async updateGuia(datos,url,modalEditar,tablaGuias) {
        try {
            let response = await this.funcfetch(url, datos);
            if (response.error) {
                alertify.alert('Error', response.error);
                return
            }
            if (response.alerta) {
                alertify.alert('Alerta', response.alerta);
                return
            }
            alertify.success(response.success);
            tablaGuias.draw();
            $(modalEditar).modal('hide'); 
        } catch (error) {
            console.error(error);
            alertify.alert('Error', error.message);
        }
    }
}