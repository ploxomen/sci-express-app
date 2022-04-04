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
            if(!files.files.length){
                alertify.alert('Mensaje','Debe de agregar como mínimo una imagen de referencia');
                return
            }
            alertify.confirm('Mensaje','Al continuar usted está afirmando que el pedido ya se entregó.',async ()=>{
                let datos = new FormData(formulario);
                datos.append('guia',this.dataset.guia);
                let response = await general.funcfetch('modificar-pedidos',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                alertify.alert('Mensaje',response.success,()=>{window.location.reload()})
            },()=>{});
        })
    }
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