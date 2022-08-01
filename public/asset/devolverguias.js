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

    frmCargos.addEventListener('submit',async function(e){
        e.preventDefault();
        let datos = new FormData(this);
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
        alertify.success(response.success);
        window.location.reload();
    });
}
window.addEventListener('DOMContentLoaded',loadPage);