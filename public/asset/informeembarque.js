function loadPage(){
    let general = new General();
    let listaEmbarque = document.getElementById('listas-embarque');
    let embarque = null;
    let frmEmbarque = document.getElementById('frm-informar-embarques');
    let tablaEmbarquesPendi = $('#embarques-pendientes').DataTable({
        ajax : {
            url: 'obtener-informe',
            method: 'POST',
            headers: general.jsonToken
        },
        columns: [
            {data: 'nro_embarque'},
            {data: 'format_fecha'},
            {data: 'hr_inicio'},
            {data: 'hr_estimado'},
            {data: 'hr_fin'},
            {data: 'tota_bult'},
            {data: 'kpi'},
            {data: 'flet_esti'},
            {data: 'tot_mani'},
            {data: 'id', searchable: false, orderable: false, name: 'id', render: function(data){return`<button class="btn btn-danger btn-sm obtener-detalle" title="Agregar informe del embarque" data-embarque="${data}"><i class="fas fa-edit"></i></button>`}}
        ],
    });
    listaEmbarque.addEventListener('click',async function(event){
        const dom = event.target;
        if(dom.classList.contains('obtener-detalle')){
            let datos = new FormData();
            datos.append('id_embarque',dom.dataset.embarque);
            try {
                let response = await general.funcfetch('obtener-informe-detalle',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                if(response.embarque){
                    embarque = response.embarque.id;
                    general.verInformeEmbarque(response);
                }
            } catch (error) {
                alertify.alert('Error',error.message);
                console.error(error);
            }
        }
    });
    document.getElementById('actualizar-embarque-informe').onclick = function () {
        document.getElementById('enviar-informe').click();
    }
    frmEmbarque.addEventListener('submit',async function(event){
        event.preventDefault();
        if(!embarque){
            return
        }
        let ciudadesBase = new Array();
        for (const ciudad of document.querySelectorAll('#informes-embarques .informe-ciudades')) {
            const idCiudad = ciudad.dataset.ciudad;
            ciudadesBase.push({
                ciudadBase : ciudad.dataset.ciudad,
                trayecto: ciudad.dataset.trayecto,
                comprobantes: $(ciudad).find('#id_comprobantes_' + idCiudad).val(),
                nroComprobantes: $(ciudad).find('#id_nro_comprobantes_' + idCiudad).val(),
                importeFactura: $(ciudad).find('#id_importe_factura_' + idCiudad).val(),
                fechaLlegada: $(ciudad).find('#id_fecha_llegada_' + idCiudad).val(),
                hrLlegada: $(ciudad).find('#id_hr_llegada_' + idCiudad).val(),
                guiasEmbarque: ciudad.dataset.trayecto == 'AEREO' ? null : $(ciudad).find('#id_guia_embarque_' + idCiudad).val()
            });
        }
        let datos = new FormData(this);
        datos.append('id_embarque',embarque);
        datos.append('ciudades_base',JSON.stringify(ciudadesBase));
        try {
            let response = await general.funcfetch('agregar-informe', datos);
            console.log(response);
            if(response.success){
                alertify.success(response.success);
                tablaEmbarquesPendi.draw();
                $('#modal-informe-embarque').modal('hide');
                return
            }
            if (response.error){
                alertify.alert('Alerta',response.error);
            }
        } catch (error) {
            alertify.alert('Error',error.message);
            console.error(error);
        }
    });
    $('#insertar-guias-embarque').on('hidden.bs.modal', function (e) {
        document.getElementById('lista-guias-embarque').innerHTML = "";
    })
    
}
window.addEventListener('DOMContentLoaded',loadPage);