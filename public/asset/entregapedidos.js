function loadPage(){

    let general = new General();
    let btnsAddImg = document.querySelectorAll('.btn-add-img');
    let inputsGuias = document.querySelectorAll('.imgs-guias');
    let btnSaveEntrega = document.querySelectorAll('.btn-save-entrega');
    for (const btn of btnSaveEntrega) {
        btn.addEventListener('click',function(e){
            e.preventDefault();
            const formulario = document.getElementById(this.dataset.formulario);
            const files = document.getElementById(this.dataset.files);
            if(!formulario){
                return
            }
            const situacionReparto = '#'+this.dataset.situacionReparto;
            const estadoEntrega = '#'+this.dataset.estadoEntrega;
            const incidenciaReparto = '#'+this.dataset.situacionIncidencia;
            const detalleIncidencia = '#'+this.dataset.situacionDetalle;
            const nroEntrega = '#'+this.dataset.nroVisitas;
            const motivoNoEntregado = '#'+this.dataset.motivoNoentregado;
            console.log(situacionReparto);
            if($(situacionReparto).val() == ""){
                alertify.error('por favor selecione la situacion del reparto');
                return
            }
            if($(situacionReparto).val() == "INCIDENCIA" && $(incidenciaReparto).val() == ""){
                alertify.error('por favor selecione la incidencia del reparto');
                return
            }
            if($(situacionReparto).val() == "INCIDENCIA" && $(detalleIncidencia).val() == ""){
                alertify.error('por favor detalle la incidencia del reparto');
                return
            }
            if($(estadoEntrega).val() == "" && $(situacionReparto).val() == "CONFORME"){
                alertify.error('por favor selecione el status de la entrega');
                return
            }
            if($(estadoEntrega).val() == "ENTREGADO" && $(nroEntrega).val() == ""){
                alertify.error('por favor selecione el numero de visitas');
                return
            }
            if($(estadoEntrega).val() == "NO ENTREGADO" && $(motivoNoEntregado).val() == ""){
                alertify.error('por favor selecione el motivo por el cual no se efectuo la entrega');
                return
            }

            if(!files.files.length || files.files.length > 5){
                alertify.alert('Mensaje','Debe de agregar como mínimo una imagen y como máximo cinco imagenes de referencia');
                return
            }
            alertify.confirm('Mensaje','¿Estas seguro de completar esta entrega?',async ()=>{
                let datos = new FormData(formulario);
                let datos_adicionales = {
                    motivo_no_ent : $(motivoNoEntregado).val(),
                    nro_visitas_reparto : $(nroEntrega).val(),
                    estado_entregado: $(estadoEntrega).val(),
                    text_durante_disconforme: $(detalleIncidencia).val(),
                    situacion_durante_envio: $(situacionReparto).val(),
                    incidencia_durante_envio: $(incidenciaReparto).val()
                }
                datos.append('guia',this.dataset.guia);
                datos.append('datos_adicionales',JSON.stringify(datos_adicionales));
                datos.append('nro_guia',this.dataset.nroguia);
                let response = await general.funcfetch('modificar-pedidos',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                alertify.alert('Mensaje',response.success,()=>{window.location.reload()})
            },()=>{});
        })
    }
    $('.situacion-reparto').on('select2:select',function(e){
        if($(this).val() == "INCIDENCIA"){
            $($(this).attr("data-status")).val("").trigger("change");
            $($(this).attr("data-status")).prop("disabled",true);
            $($(this).attr("data-incidencia")).prop("disabled",false);
            $($(this).attr("data-detalle")).prop("disabled",false);
        }else{
            $($(this).attr("data-status")).prop("disabled",false);
            $($(this).attr("data-incidencia")).val("").trigger("change");
            $($(this).attr("data-detalle")).val("");
            $($(this).attr("data-incidencia")).prop("disabled",true);
            $($(this).attr("data-detalle")).prop("disabled",true);
        }
    });
    $('.situacion-incidencia').on('select2:select',function(e){
        if($(this).val() == "DAÑO PARCIAL" || $(this).val() == "PERDIDA PARCIAL"){
            $($(this).attr("data-estado")).val("").trigger("change");
            $($(this).attr("data-estado")).prop("disabled",false);
        }else{
            $($(this).attr("data-estado")).prop("disabled",true);
        }
    });
    $('.status-entrega').on('change',function(e){
        const nroEntregas = $(this).attr("data-visitas");
        const noEntregado = $(this).attr("data-noentregado");
        console.log(nroEntregas,noEntregado);
        if($(this).val() == ""){
            $(nroEntregas).prop('disabled',true);
            $(nroEntregas).val('').trigger("change");
            $(noEntregado).prop('disabled',true);
            $(noEntregado).val('').trigger("change");
        }
        if($(this).val() == 'ENTREGADO'){
            $(nroEntregas).prop('disabled',false);
            $(noEntregado).val('').trigger("change");
            $(noEntregado).prop('disabled',true);
        }
        if($(this).val() == 'NO ENTREGADO'){
            $(nroEntregas).prop('disabled',true);
            $(nroEntregas).val('').trigger("change");
            $(noEntregado).prop('disabled',false);
        }
    });
    for (const btn of btnsAddImg) {
        btn.addEventListener('click',function(event){
            event.preventDefault();
            const inputFile = document.querySelector(this.dataset.input);
            if(!inputFile){
                return
            }
            inputFile.click();
        });
    }
    function coinsidenciaImg(files,name){
        for (let i = 0; i < files.length; i++) {
            if(files[i].name == name){
                return 1;
            }
        }
        return 0;
    }
    for (const img of inputsGuias) {
        img.addEventListener('change',function(e){
            const imgs = e.target.files;
            if(!imgs.length){
                return
            }
            const textTransfer = this.dataset.transfer;
            const inputTransfer = document.getElementById(textTransfer);
            const pattern = /image-*/;
            const transfer = new DataTransfer();
            for (let e = 0; e < inputTransfer.files.length; e++) {
                transfer.items.add(inputTransfer.files[e]);                
            }
            let countImg = 0;
            for (let i = 0; i < imgs.length; i++) {
                if(imgs[i].type.match(pattern)){
                    if(coinsidenciaImg(inputTransfer.files,imgs[i].name)){
                        countImg++;
                        continue;
                    }
                    const domparent = this.parentElement;
                    let render = new FileReader();
                    render.addEventListener('load',function () {  
                        const dom = document.createElement('div');
                        const btnDelete = document.createElement('button');
                        let img = new Image();
                        img.src = this.result;
                        img.classList.add('img-guias');
                        img.title = imgs[i].name;
                        dom.classList.add('form-group','col-12','col-xl-6','position-relative');
                        btnDelete.classList.add('img-btn-delete','rounded-circle','btn','btn-sm','btn-light');
                        btnDelete.dataset.file = imgs[i].name;
                        btnDelete.dataset.filessave = textTransfer;
                        btnDelete.innerHTML = `<i class="fas fa-trash-alt"></i>`;
                        btnDelete.addEventListener('click',deleteImg);
                        dom.append(img,btnDelete);
                        domparent.parentElement.insertBefore(dom,domparent);
                    },false);
                    render.readAsDataURL(imgs[i]);
                    transfer.items.add(imgs[i]);
                }
            }
            inputTransfer.files = transfer.files;
            if(countImg){
                alertify.alert('Mensaje','Se detectaron ' + countImg + ' imagen(es) que ya se cargaron, recuerda que no se perminte imágenes duplicadas.');
            }
        });
    }
    function deleteImg(){
        const nameImg = this.dataset.file;
        const newDataTrnasfer = new DataTransfer();
        const filesParent = document.getElementById(this.dataset.filessave);
        let deleteDom = false;
        for (let i = 0; i < filesParent.files.length; i++) {
            if(filesParent.files[i].name != nameImg){
                newDataTrnasfer.items.add(filesParent.files[i]);
            }else{
                deleteDom = true;
            }
        }
        filesParent.files = newDataTrnasfer.files;
        if(deleteDom){
            this.parentElement.remove();
            alertify.success('imagen eliminada');
        }
    }

    

}
window.addEventListener('DOMContentLoaded',loadPage);