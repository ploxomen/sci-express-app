function loadPage(){
    let general = new General();
    let datatableSoli = $('#tabla-solicitud').DataTable({
        ajax : {
            url: 'obtener',
            method: 'POST',
            headers: general.jsonToken,
            data : function(d){d.estadoSol = $('#estado_soli').val()}
        },
        columns: [
            {data: 'nro_soli'},
            {data: 'fecha_soli', name: 'fecha_soli'},
            {data: 'dia_recojo', name: 'dia_recojo'},
            {data: 'hr_estimado', name: 'hr_estimado'},
            {data: 'nom_cli', name: 'nom_cli'},
            {data: 'direccion_cli', name: 'direccion_cli'},
            {data: 'cant_bulto', name: 'cant_bulto'},
            {data: 'nom_soli', name: 'nom_soli'},
            {data: 'tipo', name: 'tipo'},
            {data: 'id_user', searchable: false, render: function(data,type,full){
                let tempUsu = `<option></option>`;
                for (let i = 0; i < full.listUsu.length; i++) {
                    tempUsu += `<option value="${full.listUsu[i].id}" ${data == full.listUsu[i].id ? 'selected' : ''}>${full.listUsu[i].name} ${full.listUsu[i].last_name}</option>`;
                }
                return `<select data-solicitud="${full.id}" class="form-control form-control-sm cb-usuarios">${tempUsu}</select>`;
            }},
            {data: 'estado',searchable: false, orderable: false, name: 'estado', render: function(data,type,full){
                let template = ``;
                full.listSoli.forEach(sol => {
                    template += `<option value="${sol.id}" ${data == sol.id ? 'selected' : ''}>${sol.estado}</option>`;
                });
                return `<select data-solicitud="${full.id}" class="form-control form-control-sm cb-estado-change">${template}</select>`;
            }},
            {data: 'id', searchable: false, orderable: false, name: 'id', render: function(data,type,full){return`<div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><button data-toggle="tooltip" title="Visualizar solicitud" data-solicitud="${data}" class="btn btn-sm btn-primary view-solicitud"><i class="far fa-eye"></i></button><button data-toggle="tooltip" title="Eliminar cliente" data-solicitud="${data}" class="btn btn-sm btn-danger delete-solicitud"><i class="fas fa-trash-alt"></i></button></div>`}}
        ],
    });
    datatableSoli.on('draw.dt', function () {
        $('.cb-usuarios').select2({
            theme : 'bootstrap',
            width: '100%',
            placeholder : 'Seleccione una opción'
        }).on('select2:select',async function(event){
            let datos = new FormData();
            datos.append('id_solicitud',event.target.dataset.solicitud);
            datos.append('id_user',$(this).val());
            let response  = await general.funcfetch('asignar-personal',datos);
            if(response.error){
                alertify.alert('Mensaje',response.error);
                return
            }
            document.getElementById('total-por-procesar').textContent = response.procesar;
            alertify.success(response.success);
        });
        for (const cbos of document.querySelectorAll('.cb-estado-change')) {
            cbos.addEventListener('change',async function(event){
                let datos = new FormData();
                datos.append('id_solicitud',this.dataset.solicitud);
                datos.append('estado',this.value);
                let response = await general.funcfetch('cambiar-estado',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return 
                }
                alertify.success(response.success);
            });
        }
    });
    let tablaSoli = document.getElementById('tabla-solicitudes');
    tablaSoli.addEventListener('click',async function(e){
        if(e.target.classList.contains('view-solicitud')){
            let datos = new FormData();
            datos.append('id_solicitud',e.target.dataset.solicitud);
            let response = await general.funcfetch('ver-detalle',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }else if(response.info){
                alertify.alert('Alerta',response.info);
                return
            }
            document.getElementById('nro_solicitud').textContent =  response.success.id.toString().padStart(5,'0');
            for (const key in response.success) {
                if (Object.hasOwnProperty.call(response.success, key)) {
                    const valor = response.success[key];
                    const dom = document.getElementById('id_text_' + key);
                    if(!dom){
                        continue;
                    }
                    dom.textContent = valor;
                }
            }
            $('#detalle-solicitud').modal('show');
        }
        if(e.target.classList.contains('delete-solicitud')){
            alertify.confirm('Alerta','¿Deseas eliminar esta solicitud?',async ()=>{
                let datos = new FormData();
                datos.append('id_solicitud',e.target.dataset.solicitud);
                let response = await general.funcfetch('eliminar-solicitud',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                alertify.success(response.success);
                datatableSoli.draw();
            },()=>{});
        }
    });
    for (const cb of document.querySelectorAll('.cb-filtros')) {
        cb.addEventListener('change',function(e){
            datatableSoli.draw();
        });
    }
    
}
window.addEventListener('DOMContentLoaded',loadPage);