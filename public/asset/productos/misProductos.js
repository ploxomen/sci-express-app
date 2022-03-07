window.onload = loadPage;
function loadPage(){
    let gen = new General();
    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    $('.select2').select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: 'Selecciona las opciones',
        tags: true
    })

    let inpImg = document.getElementById('upload-img');
    let iptImgForm = document.getElementById('imagenes');
    let ulViewImg = document.getElementById('prev-img');
    let listImg = new DataTransfer();
    inpImg.addEventListener('change',function(e){
        e.preventDefault();
        let files = e.target.files;
        for (let i = 0; i < files.length; i++) {
            let imagen = new Image();
            let li = document.createElement('li'),
                name = document.createElement('span'),
                btnClose = document.createElement('button');
            li.classList.add('item-img');
            name.textContent = files[i].name;
            btnClose.classList.add('boton-close');
            btnClose.title = 'Eliminar imagen';
            btnClose.dataset.itemId = files[i].lastModified;
            btnClose.innerHTML = `<span class='material-icons' style='color:white;'>close</span>`
            imagen.src = URL.createObjectURL(files[i]);
            li.append(imagen,name,btnClose);
            ulViewImg.append(li);
            listImg.items.add(files[i]);
        }
    });
    ulViewImg.addEventListener('click',function(event){
        if(event.target.classList.contains('boton-close')){
            const id = event.target.dataset.itemId;
            let index = null;
            for (let i = 0; i < listImg.files.length; i++) {
                if(listImg.files[i].lastModified == id){
                    index = i;
                    break;
                }
            }
            if(index != null){
                listImg.items.remove(index);
                event.target.parentElement.remove();
                alertify.success('Imagen eliminada');
            }
        }
    })
    document.getElementById('send-form').addEventListener('submit',function(e){
        e.preventDefault();
        iptImgForm.files = listImg.files;
        let datos = new FormData(this);
        let xhr = new XMLHttpRequest();
        
        xhr.open('POST','productos/add');
        xhr.setRequestHeader('X-CSRF-TOKEN',token);
        xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
        // xhr.setRequestHeader("Content-Type","multipart/form-data");
        xhr.onreadystatechange = ()=>{
            if(xhr.readyState == 4 && xhr.status == 200){
                console.log('si');
            }
        }
        xhr.send(datos);
    })
    let btnLoadImg = document.getElementById('btn-load-img');
    btnLoadImg.onclick = ()=>{inpImg.click();}
}
