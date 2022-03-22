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
    })
    $('#id_remi_nomb').on('select2:select',async function(event){
        newCliCli = false;
        clearCliDeCli();
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
            let tempCli = "<option></option>";
            if(response.cli.length > 0){
                response.cli.forEach(c => {
                    tempCli += `<option value="${c.id}">${c.nombres}</option>`;
                });
            }
            document.getElementById('id_dest_nomb').innerHTML = tempCli;
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
            let deta = e.querySelectorAll('input, select');
            for (const ele2 of deta) {
                if(ele2.value && ele2.value != '0.00'){
                    objeto[ele2.dataset.name] = ele2.value;
                }
            }
            if(!general.isObjEmpty(objeto)){
                detalles.push(objeto);
            }
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
            }).set('labels',{ok : 'SI', cancel: 'NO'});
        }else{
            addGuia(datos);
        }
        
    });
    async function addGuia(datos) {
        try {
            let response  = await general.funcfetch('guias/agregar-guia',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            newCliCli = false;
            alertify.alert('Mensaje',response.success,()=>{window.location.reload()});
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
        $('#id_dest_prov').html("");
        $('#id_dest_dist').html("");
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
    fila.children[9].children[0].value = total.toFixed(2);
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