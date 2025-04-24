function loadPage(){
    let general = new General();
    let cbSelectAll = document.getElementById("id_check_todo");
    let tblGuia = document.getElementById("tabla-cuerpo-guias");

    let tabalGuias = $('#tabla-retorno-guia').DataTable({
        ajax : {
            url: 'por-retornar',
            method: 'POST',
            headers: general.jsonToken,
        },
        columns: [
            {data: 'id',searchable: false, orderable: false,render : function(data){
                return `<div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input check-guia" id="id_check_guia_${data}" value="${data}">
                    <label class="custom-control-label" for="id_check_guia_${data}"></label>
                </div>`
            }},
            {data: 'nro_guia'},
            {data: 'remi_nro_docu'},
            {data: 'nom_cli_remitente'},
            {data: 'dest_nro_docu'},
            {data: 'nom_cli_destinatario'},
            {data: 'trayecto_envio'},
            {data: 'remi_prov'},
            {data: 'dest_prov'}
        ],
        createdRow: function (row, data) {
            let bgTable = data.situ == 14 ? 'table-danger' : '';
            $(row).addClass(bgTable);
        }
    });
    cbSelectAll.addEventListener("change",function () {  
        const activo = this.checked;
        for (const tr of tblGuia.children) {
            if(tr.querySelector(`.check-guia`)){
                tr.querySelector(`.check-guia`).checked = activo;
            }
        }
    });
    tabalGuias.on('draw',function(){
        for (const c of document.querySelectorAll('.check-guia')) {
            c.addEventListener('change',function(){
                let checked = tblGuia.querySelectorAll('input:checked').length;
                if(checked == tblGuia.children.length){
                    cbSelectAll.checked = true;
                }else{
                    cbSelectAll.checked = false;
                }
            });
        }
    });
    for (const e of document.querySelectorAll('.calc-total')) {
        e.addEventListener('change',function(e){
            document.getElementById('total_bultos').value = general.totalBultos(this.parentElement.parentElement,'.calc-total');;
        });
    }
    const btnFinalizarEmbarque = document.getElementById('btn-guardar-guias-embarcar');
    const frmCargos = document.getElementById('retornar-cargos');
    const btnGuias = document.getElementById('guardar_guias');
    const btnEmbarcarGuias = document.getElementById('embarcar_guias');
    btnFinalizarEmbarque.addEventListener('click',function(e){
        document.getElementById('btn-terminar-embarque').click();
    });
    btnEmbarcarGuias.addEventListener('click',async function(e){
        e.preventDefault();
        let datos = new FormData();
        datos.append('tipo','consultas_guias');
        let response = await general.funcfetch('retornar',datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        const bodyModal = document.getElementById('tabla-guias-modal');
        bodyModal.onclick = async (e) => {
            if(e.target.classList.contains('btn-danger')){
                let datos = new FormData();
                datos.append('tipo','eliminar-guias-guardadas');
                datos.append('guia',e.target.dataset.guia);
                const response = await general.funcfetch('/intranet/cluster/embarcar/retornar',datos);
                if(response.success){
                    e.target.closest('tr').remove();
                    return alertify.success('guia eliminada correctamente')
                }
            }
        }
        if(!response.guias.length){
            bodyModal.innerHTML = `<tr><td colspan="5">No se encontró guias guardadas</td></tr>`;
        }else{
            let template = "";
            response.guias.forEach(g => {
                template += `<tr data-type="old" class="table-primary" data-guia="${g.id}">
                    <td>${g.nro_guia}</td>
                    <td>${g.remi_nro_docu}</td>
                    <td>${g.nom_cli_remitente}</td>
                    <td>${g.dest_nro_docu}</td>
                    <td>${g.nom_cli_destinatario}</td>
                    <td><button type='button' data-guia="${g.id}" class='btn btn-sm btn-danger'><i class="fas fa-trash"></i></button></td>
                </tr>`;
            });
            bodyModal.innerHTML = template;
        }
        if(tblGuia.querySelectorAll('input:checked').length > 0){
            if(!response.guias.length){
                bodyModal.innerHTML = "";
            }
            for (const elemento of tblGuia.querySelectorAll('input:checked')) {
                const tr = elemento.parentElement.parentElement.parentElement;
                const createTr = document.createElement("tr");
                createTr.dataset.type = "new";
                createTr.className = 'table-warning';
                createTr.dataset.guia = elemento.value;
                console.log(tr.children[1].textContent);
                const template2 = `
                    <td>${tr.children[1].textContent}</td>
                    <td>${tr.children[2].textContent}</td>
                    <td>${tr.children[3].textContent}</td>
                    <td>${tr.children[4].textContent}</td>
                    <td>${tr.children[5].textContent}</td>
                `;
                createTr.innerHTML = template2;
                bodyModal.append(createTr);
            }
        }
        if(!response.guias.length && !tblGuia.querySelectorAll('input:checked').length){
            alertify.error('no se encontro ninguna guia guardada ni seleccionada');
            return
        }
        $('#guardar-detalles-nuevo').modal("show");
    });
    btnGuias.addEventListener('click',function(e){
        e.preventDefault();
        let datos = new FormData();
        let guias = new Array();
        if(!tblGuia.querySelectorAll('input:checked').length){
            alertify.error('por favor seleccione una o más guias');
            return
        }else{
            for (const elemento of tblGuia.querySelectorAll('input:checked')) {
                guias.push({
                    id_guia : elemento.value
                });
            }
            datos.append('guias',JSON.stringify(guias));
        }
        alertify.confirm('Mensaje','¿Deseas guardar estas guías?',async ()=>{
            datos.append('tipo','guardar_guias');
            let response = await general.funcfetch('retornar',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            alertify.success(response.success);
            tabalGuias.draw();
        },()=>{});
    });
    const btnAgregarImg = document.querySelector("#btn-new-img");
    const fileImg = document.querySelector("#input-img-new");
    const contenidoImg = document.querySelector("#list-img");
    let newImages = [];
    btnAgregarImg.onclick = (e) => fileImg.click();
    function ImageDelivery(nomFile,nomIdFile,idImg = null){
        const contenedor = document.createElement('div');
        contenedor.classList.add('img-delivery');
        contenedor.innerHTML = `<button type="button" class="eliminar-img" title="Eliminar esta imagen" data-id="${idImg}" data-name="${nomFile}"><i class="far fa-trash-alt"></i></button>
            <img src="${nomIdFile}"
            title="${nomFile}"
            alt="Imagen de entrega de la guía">
        </button>`;
        return contenedor;
    }
    contenidoImg.onclick = (e) => {
        if(e.target.classList.contains('eliminar-img')){
            newImages = newImages.filter(img => img.name !== e.target.dataset.name);
            e.target.closest('.img-delivery').remove();
            console.log(newImages)
        }
    }
    fileImg.onchange = function (event) {
        const files = Array.from(event.target.files);
        files.forEach((file) => {
            newImages.push(file);
            const reader = new FileReader();
            reader.onload = function (e) {
                contenidoImg.append(ImageDelivery(file.name,e.target.result));
            };
            reader.readAsDataURL(file);
        });
    };
    frmCargos.addEventListener('submit',async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        datos.append('tipo_comprobantes',!$('#id_tipo_comprobantes').val() ? "" : $('#id_tipo_comprobantes').val().join(";"));
        datos.append('nro_guias',!$('#id_nro_guias').val() ? "" : $('#id_nro_guias').val().join(";"));
        let guiasGuardadas = new Array();
        let guiasNuevas = new Array();
        for (const elemento of document.getElementById('tabla-guias-modal').children) {
            if(elemento.dataset.type == 'old'){
                guiasGuardadas.push(
                    {id_guia : elemento.dataset.guia}
                );
            }
            if(elemento.dataset.type == 'new'){
                guiasNuevas.push({id_guia : elemento.dataset.guia})
            }
        }
        if(!guiasGuardadas.length && !guiasNuevas.length){
            alertify.error('no se encontraron guias para ser embarcadas');
            return
        }
        if(!general.totalBultos(this,'.calc-total')){
            alertify.alert('Alerta','El total de bulto debe de ser mayor que 0');
            return
        }
        newImages.forEach(file => {
            datos.append('new_images[]', file);
        });
        datos.append('tipo','retornar_guias');
        datos.append('guias_guardadas',JSON.stringify(guiasGuardadas));
        datos.append('total_bultos',general.totalBultos(this,'.calc-total'));
        datos.append('guias_nuevas',JSON.stringify(guiasNuevas));
        let response = await general.funcfetch('retornar',datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        $('#guardar-detalles-nuevo').modal("hide");
        alertify.alert("Mensaje", response.success, () => { window.location.reload() });
    });
}
window.addEventListener('DOMContentLoaded',loadPage);