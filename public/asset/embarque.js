function loadPage() {
    let general = new General();
    let cbSelectAll = document.getElementById("id_check_todo");
    let tblMani = document.getElementById("tabla-manifiestos");
    let frmEmbarque = document.getElementById('add-embarque');
    let dataTableMani = $('#tabla-embarque').DataTable({
        ajax : {
            url: 'manifiesto/pendientes',
            method: 'POST',
            headers: general.jsonToken,
            data: function(d){d.provincia = $('#id_prov_dest').val()}
        },
        columns: [
            {data: 'id',searchable: false,orderable: false, render : function(data,type,full){
                return `<div class="custom-control custom-checkbox">
                    <input type="checkbox" data-manifiesto="${data}" data-bulto="${full.totBulto}" class="custom-control-input check-embarque" id="check_manifiesto_${data}">
                    <label class="custom-control-label" for="check_manifiesto_${data}"></label>
                </div>`
            }},
            {data: 'idExtendMani'},
            {data: 'format_fecha',searchable: false,orderable: false},
            {data: 'trayectoName'},
            {data: 'prov_orig'},
            {data: 'prov_dest'},
            {data: 'nom_cluster'},
            {data: 'totBulto'},
            {data: 'totguias'}
        ]
    });
    $('#id_prov_dest').on('select2:select',function(){
        dataTableMani.draw();
    });
    cbSelectAll.addEventListener("change",function () {  
        const activo = this.checked;
        for (const tr of tblMani.children) {
            if(tr.querySelector(`input[type="checkbox"]`)){
                tr.querySelector(`input[type="checkbox"]`).checked = activo;
                obtenerBulto(tr.querySelector(`input[type="checkbox"]`));
            }
        }
    });
    frmEmbarque.addEventListener('submit',async function(event){
        event.preventDefault();
        let datos = new FormData(this);
        let arrMani = new Array();
        let trMani = tblMani.querySelectorAll('.check-embarque');
        for (const element of trMani) {
            if(element.checked){
                arrMani.push({id_manifiesto: element.dataset.manifiesto});
            }
        }
        if(!arrMani.length){
            alertify.error('seleccione uno o más manifiestos');
            return
        }
        datos.append('lista_manifiestos',JSON.stringify(arrMani));
        try {
            let response = await general.funcfetch('manifiesto/embarcar',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            alertify.alert('Mensaje',response.success);
            dataTableMani.draw();
            $('.simple-select2').val("").trigger('change');
        } catch (error) {
            alertify.alert('Error',error.message);
            console.error(error)
        }
    });
    frmEmbarque.addEventListener('reset',function(){
        $('.simple-select2').val("").trigger('change');
    })
    function obtenerBulto(element){
        const totBult = document.getElementById('id_tota_bult');
        let totBulVal = isNaN(totBult.value) ? 0 : parseFloat(totBult.value);
        let total = element.checked ? totBulVal + parseFloat(element.dataset.bulto) : totBulVal - parseFloat(element.dataset.bulto)        
        totBult.value = total < 0 ? 0 : total.toFixed(2);
    }
    dataTableMani.on('draw',function(){
        for (const c of document.querySelectorAll('.check-embarque')) {
            c.addEventListener('change',function(){
                obtenerBulto(this);
                let checked = tblMani.querySelectorAll('input:checked').length;
                if(checked == tblMani.children.length){
                    cbSelectAll.checked = true;
                }else{
                    cbSelectAll.checked = false;
                }
            });
        }
    });
}
window.addEventListener('DOMContentLoaded',loadPage);