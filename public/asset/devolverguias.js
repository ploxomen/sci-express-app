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
            document.getElementById('total_bultos').value = general.totalBultos('.calc-total');;
        });
    }
    const frmCargos = document.getElementById('retornar-cargos');
    frmCargos.addEventListener('submit',async function(e){
        e.preventDefault();
        let datos = new FormData(this);
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
        if(!general.totalBultos('.calc-total')){
            alertify.alert('Alerta','El total de bulto debe de ser mayor que 0');
            return
        }
        let response = await general.funcfetch('retornar',datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        alertify.success(response.success);
        tabalGuias.draw();
        this.reset();
    })
}
window.addEventListener('DOMContentLoaded',loadPage);