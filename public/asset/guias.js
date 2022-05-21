function pageLoaded(){
    let general = new General();
    let newCliCli = false;
    let tipoCbRemi = null;
    $('#id_comp_clie').select2({
        theme : 'bootstrap',
        width: '100%'
    });
    $('#id_nro_comp_cli').select2({
        theme : 'bootstrap',
        width: '100%',
        tags: true
    });
    $('#id_dest_dist').on('select2:select',async function(e){
        let datosn = new FormData();
        datosn.append('distrito',$(this).val());
        let response = await general.funcfetch(window.location.origin + '/intranet/ubigeo/ciudades',datosn);
        let template = `<option></option>`;
        response.ciudades.forEach(c => {
            template += `<option value="${c.id}">${c.nombre_ciudad}</option>`;
        });
        document.querySelector($(this).attr('data-ciudades')).innerHTML = template;
    });
    $('#id_nro_comp_cli').on('change.select2',function(e){
        $('#id_cant_comp').val($(this).val().length);
    });
    $('#id_remi_nomb').on('select2:select',async function(event){
        // newCliCli = false;
        // clearCliDeCli();
        let datos = new FormData();
        tipoCbRemi = this.selectedOptions[0].getAttribute('data-type');
        datos.append('tipo',tipoCbRemi);
        datos.append('id',$(this).val());
        if(tipoCbRemi == 'proveedor'){
            datos.append('idcliente',this.selectedOptions[0].getAttribute('data-id-cliente'));
        }
        try {
            let response = await general.funcfetch('guias/obtener-clipro',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            if(response.prov.length > 0){
                addCbUbigeo(response.prov,'id_remi_prov');
            }
            if(response.dist.length > 0){
                addCbUbigeo(response.dist,'id_remi_dist');
            }
            for (const key in response.datos) {
                if (Object.hasOwnProperty.call(response.datos, key)) {
                    const valor = response.datos[key];
                    const element = document.querySelector(tipoCbRemi == 'proveedor' ? `[data-valor-proveedor="${key}"]` : `[data-valor-cliente="${key}"]`);
                    if(!element){
                        continue;
                    }
                    element.value = valor;
                }
            }
            $('#id_remi_depa').trigger('change');
            $('#id_remi_tipo_docu').trigger('change');
        } catch (error) {
            console.log(error);
            alertify.alert('Error',error.message);
        }

    });
    let frmGuia = document.getElementById('frm_guia');
    frmGuia.addEventListener('submit',async function(event){
        event.preventDefault();
        let datos = new FormData(this);
        let tablaDetalle = document.getElementById('tabla-detalle');
        datos.append('tipo_guia',tipoCbRemi);
        let detalles = new Array();
        for (const e of tablaDetalle.children) {
            let objeto = new Object();
            let deta = e.querySelectorAll('input, select, textarea');
            for (const ele2 of deta) {
                if(ele2.value && ele2.value != '0.00'){
                    objeto[ele2.dataset.name] = ele2.value;
                }
            }
            if(!general.isObjEmpty(objeto)){
                detalles.push(objeto);
            }
        }
        if(!detalles.length){
            alertify.alert('Mensaje','Por favor agregue detalles a la guía');
            return
        }
        datos.append('detalle_guia',JSON.stringify(detalles));
        if(newCliCli){
            alertify.confirm('Mensaje','Se a detectado un nuevo cliente del cliente. <br> ¿Deseas guardarlo para la proxima vez?',()=>{
                datos.append('add_cli_bool',true);
                datos.append('add_cli_cli',tipoCbRemi == 'proveedor' ? document.getElementById('id_remi_nomb').selectedOptions[0].getAttribute('data-id-cliente') : $('#id_remi_nomb').val());
                addGuia(datos);
            },()=>{
                datos.append('add_cli_bool',false);
                addGuia(datos);
            }).set({
                'invokeOnCloseOff': true,
                'closable': false,
                'labels': {ok:'Si', cancel:'No'}
            });
        }else{
            addGuia(datos);
        }
    });
    $('.calculo-dia').on('select2:select',async function(){
        let datos = new FormData();
        datos.append('departamento_origen',$('#id_remi_depa').find("option:selected").text());
        datos.append('departamento_destino',$('#id_dest_depa').find("option:selected").text());
        datos.append('provincia_destino',$('#id_dest_prov').find("option:selected").text());
        datos.append('ciudad_final',$(this).find("option:selected").text());
        let response  = await general.funcfetch('guias/obtener-dias',datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        if(!response.dias){
            document.getElementById('id_dias_tran').value = "";
            alertify.error('no se encontró los dias de transito para este destino');
            return
        }
        calcularDiasTransito('id_dias_tran',response.dias.total_origen_base_hr_aereo,response.dias.total_hr_origen_destino);
        
    });
    function calcularDiasTransito(domId,aereo,terrestre){
        let diasEnvio = 0;
        switch ($('#id_via_envi').val()) {
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
        document.getElementById(domId).value = diasEnvio;
    }
    async function addGuia(datos) {
        try {
            let response  = await general.funcfetch('guias/agregar-guia',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            if(response.alerta){
                alertify.alert('Alerta',response.alerta);
                return
            }
            newCliCli = false;
            alertify.alert('Mensaje',response.success,()=>{
                window.location.reload();
            });
        } catch (error) {
            console.error(error);
            alertify.alert('Error',error.message);
        }
    }
    document.getElementById('add-new-detalle').addEventListener('click',function(e){
        $('.simple-select2').select2('destroy');
        let tr = document.getElementById('tabla-detalle').children[0];
        const cloneTr = tr.cloneNode(true);
        for (const i2 of cloneTr.querySelectorAll('input, select')) {
            console.dir(i2);
           i2.value = i2.localName == "input" ? "0.00" : "";
        }
        document.getElementById('tabla-detalle').append(cloneTr);
        $('.simple-select2').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        });
        alertify.success('Se agregó otra fila');
    });
    $('#id_dest_nomb').on('select2:select',async function(event){
        let datos = new FormData();
        datos.append('id',$(this).val());
        datos.append('departamentoOrigen',$('#id_remi_depa').find("option:selected").text());
        try {
            let response  = await general.funcfetch('guias/detalle-cli-de-cli',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            if(!response.datos){
                newCliCli = true;
                clearCliDeCli();
                return
            }
            if(response.prov.length > 0){
                addCbUbigeo(response.prov,'id_dest_prov');
            }
            if(response.dist.length > 0){
                addCbUbigeo(response.dist,'id_dest_dist');
            }
            if(response.ciudades.length > 0){
                let template2 = "<option></option>";
                response.ciudades.forEach(c => {
                    template2 += `<option value="${c.id}">${c.nombre_ciudad}</option>`;
                });
                document.getElementById('id_ciudad_destino_final').innerHTML = template2;
            }
            for (const key in response.datos) {
                if (Object.hasOwnProperty.call(response.datos, key)) {
                    const valor = response.datos[key];
                    const dom = document.querySelector(`[data-cli-cli="${key}"]`);
                    if(!dom){
                        continue;
                    }
                    dom.value = valor;
                }
            }
            $('#id_dest_depa').trigger('change');
            $('#id_dest_tipo_docu').trigger('change');
            $('#id_ciudad_destino_final').trigger('change');
            if(!response.dias){
                document.getElementById('id_dias_tran').value = "";
                alertify.error('no se encontró los dias de transito para este destino');
                return
            }
            calcularDiasTransito('id_dias_tran',response.dias.total_origen_base_hr_aereo,response.dias.total_hr_origen_destino);
        } catch (error) {
            console.error(error);
            alertify.alert('Error',error.message);
        }
    });
    function clearCliDeCli(){
        for (const e of document.querySelectorAll(`[data-cli-cli]`)) {
            e.value = "";
        }
        $('.cb-dest').val("").trigger("change");
        $('#id_dest_tipo_docu').val("").trigger("change");
        $('#id_dest_prov').html("");
        $('#id_dest_dist').html("");
        $('#id_ciudad_destino_final').html("");
    }
    function addCbUbigeo(listUbigeo,dom){
        let template = "<option></option>";
        listUbigeo.forEach(p => {
            template += `<option value="${p.id_ubigeo}">${p.nombre_ubigeo}</option>`;
        });
        document.getElementById(dom).innerHTML = template;
    }
    
}
function calcVolumen(event){
    if(parseFloat(event.target.value) < 0 || isNaN(event.target.value)){
        event.target.value = 0;
    }
    const fila = event.target.parentElement.parentElement;
    let total = 1;
    for (let i = 6; i < 9; i++) {
        total = total * parseFloat(fila.children[i].children[0].value);
    }
    fila.children[9].children[0].value = (total / 6000).toFixed(2);
    const e = new Event("change");
    fila.children[9].children[0].dispatchEvent(e);
}
function calcTotal(event,num,dom){
    if(parseFloat(event.target.value) < 0 || isNaN(event.target.value)){
        event.target.value = 0;
    }
    const filaTotal = document.getElementById('tabla-detalle');
    let total = 0;
    for (let i = 0; i < filaTotal.children.length; i++) {
        total += parseFloat(filaTotal.children[i].children[num].children[0].value);
    }
    document.getElementById(dom).value = total.toFixed(2);
    
}
window.addEventListener('DOMContentLoaded',pageLoaded);