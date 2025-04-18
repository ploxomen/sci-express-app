function loadPage(){
    let general = new General();
    let frmCli = document.getElementById('frm_cliente');
    let idCli = document.getElementById('id_nro_doc_cli') ? document.getElementById('id_nro_doc_cli').value : false;
    let listProv = document.getElementById('list-proveedores');
    let id_li = parseInt(listProv.dataset.time);
    frmCli.addEventListener('submit',async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        let provModi = new Array();
        let provAdd = new Array();
        for (const li of listProv.children) {
            if(li.hidden){
               continue; 
            }
            let newObj = new Object();
            for (const ele of li.querySelectorAll(`[data-selected="true"]`)) {
                if(ele.tagName == 'SELECT' || ele.tagName == 'INPUT'){
                    if(ele.multiple && $(ele).val() != null && $(ele).val().length > 0){
                        newObj[ele.dataset.name] = $(ele).val().join(';');
                    }else{
                        newObj[ele.dataset.name] = ele.value;
                    }
                }
            }
            if(li.dataset.tipo == 'nuevo'){
                provAdd.push(newObj);
            }else if(li.dataset.tipo == 'antiguo'){
                newObj['id_prove'] = li.dataset.proveedor;
                provModi.push(newObj);
            }
        }
        if(idCli){
            datos.append('id_cliente', idCli);
            datos.append('prov_modi',JSON.stringify(provModi));
        }
        datos.append('prov_add',JSON.stringify(provAdd));
        try {
            let response = await general.funcfetch('agregar',datos);
            let message = general.detectSession(response.message);
            alertify.alert('Mensaje',message,() => {
                if(response.action === false){
                    window.location.href = 'mostrar';
                }else{
                    frmCli.reset();
                    $('.cb-ubigeo').val('').trigger("change");
                }
            });
        } catch (error) {
            console.log(error);
        }
    });
    let btnAddProv = document.getElementById('btn-add-prov');
    btnAddProv.addEventListener('click',function(e){
        e.preventDefault();
        addProveedor();
    });
    listProv.addEventListener('click',function(event){
        if(event.target.classList.contains('btn-delete-proveedor')){
            alertify.confirm('Alerta','¿Deseas eliminar a este proveedor?',async ()=>{
                let parentLi = event.target.parentElement.parentElement.parentElement;
                if(parentLi.dataset.tipo == 'antiguo' && idCli){
                    let datos = new FormData();
                    datos.append('cod_cli',idCli);
                    datos.append('cod_prov',parentLi.dataset.proveedor);
                    let response = await general.funcfetch('eliminar-proveedor',datos);
                    if(response.error){
                        alertify.alert('Error',response.error);
                        return
                    }
                    if(listProv.children.length === 1){
                        parentLi.dataset.tipo = 'nuevo';
                    }
                }
                if(listProv.children.length === 1 && listProv.children[0].dataset.tipo == 'nuevo' && !listProv.children[0].hidden){
                    general.primerProveedor(true,listProv);
                    alertify.success('proveedor eliminado correctamente');
                    return
                }
                parentLi.remove();
                alertify.success('proveedor eliminado correctamente');
            },()=>{});
        }
    });
    $('#id_doc_ident').on("change.select2",function(e){
        $('.require-empresa').prop("required",$(this).val() == "4" ? true : false);
        
    });
    function addProveedor() {
        if(listProv.children.length === 1 && listProv.children[0].dataset.tipo == 'nuevo' && listProv.children[0].hidden){
            general.primerProveedor(false,listProv);
            return
        }
        id_li++;
        $('#list-proveedores .cb-ubigeo, #list-proveedores .cb-mult-nuevo').select2('destroy');
        let liPrimero = listProv.children[0];
        let liSegundo = liPrimero.cloneNode(true);
        liSegundo.dataset.tipo = 'nuevo';
        liSegundo.removeAttribute('data-proveedor');
        let modificar = liSegundo.querySelectorAll(`[data-selected="true"]`);
        let n = 0;
        for (const element of modificar) {
           if(element.tagName == 'SELECT' || element.tagName == 'INPUT'){
               element.value = "";
               element.id = 'id_' + element.dataset.name + "_" + id_li;
               modificar[n-1].setAttribute('for',element.id);
           }
           n++;
        }
        modificar[7].dataset.info = modificar[9].id;
        modificar[7].dataset.infoDistrito = modificar[11].id;
        modificar[9].dataset.info = modificar[11].id;
        modificar[9].innerHTML = '<option value=""></option>';
        modificar[11].innerHTML = '<option value=""></option>';
        listProv.append(liSegundo);
        $('#list-proveedores .cb-ubigeo').select2({
            theme : 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        });
        general.selectUbigeoCli(liSegundo);
        $('#list-proveedores .cb-mult-nuevo').select2({
            theme: 'bootstrap',
            width: '100%',
            tags: true
        });
        alertify.success('se agregó un nuevo proveedor');
    }
    for (const element of document.querySelectorAll('.clone-value')) {
        element.addEventListener('input',function(e){
            const clone = this.dataset.clone;
            document.querySelector(clone).value = this.value;
        });
    }
    $('.selected-two').on('select2:select',function(e){
        const idCb = $(this).data('selected-two');
        $('#' + idCb).val($(this).val()).trigger("change");
    });
    const btnAgregarDetalle = document.querySelector("#btn-add-detalle-envio");
    const listDetalle = document.querySelector("#list-detalle-envio");
    listDetalle.onclick = (e) => {
        if(e.target.classList.contains("btn-delete-detalle")){
            alertify.confirm("Mensaje","¿Deseas eliminar este detalle de envío?",() => {
                e.target.closest('li').remove();
            }, () => {})
        }
    }
    btnAgregarDetalle.onclick = () => {
        listDetalle.children[0].hidden = false;
        $("#list-detalle-envio .simple-select2").select2('destroy');
        const elementoListaDetalle = listDetalle.children[0].cloneNode(true);
        limpiarListaDetalle(elementoListaDetalle,false);
        listDetalle.append(elementoListaDetalle);
        $('#list-detalle-envio .simple-select2').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        });
        listDetalle.children[0].hidden = true;
    }
    function limpiarListaDetalle(elemento,valor){
        elemento.hidden = valor;
        for (const element of elemento.querySelectorAll("select, input")) {
            element.disabled = valor;
            element.value = "";
        }
    }
}
window.addEventListener('DOMContentLoaded',loadPage);