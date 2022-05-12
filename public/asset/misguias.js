function loadPage(){
    let general = new General();
    const tblDetalleGuia = document.getElementById('tabla-detalle-guias');
    let tablaGuias = $('#tabla-guias').DataTable({
        ajax : {
            url: 'mis-guias',
            method: 'POST',
            headers: general.jsonToken,
            data : function(d){d.estado = $('#estado_guias').val(); d.tipo = $('#id_tipo').val()}
        },
        columns: [
            {data: 'nro_guia'},
            {data: 'fecha_emision'},
            {data: 'fecha_traslado'},
            {data: 'dias_tran'},
            {data: 'nom_remi'},
            {data: 'nom_dest'},
            {data: 'ciudad_base_cluster'},
            {data: 'ciudad_final'},
            {data: 'listClusters',render : function(data,type,row){
                let tempClust = `<option></option>`;
                if(data){
                    data.forEach(c => {
                        tempClust += `<option value="${c.id_cluster}" ${row.id_cluster == c.id_cluster ? 'selected' : ''}>${c.nombres_completos}</option>`;
                    });
                }
                return `<select data-guia="${row.id}" class="form-control form-control-sm cb-clustes">${tempClust}</select>`;
            }},
            {data: 'id_asignado', searchable: false, render: function(data,type,full){
                if(full.guia_tipo == 'INACTIVA' || full.guia_tipo == 'ANULADA'){
                    return '<span class="text-danger">Sin acciones</span>'
                }
                let tempUsu = `<option></option>`;
                for (let i = 0; i < full.listUsu.length; i++) {
                    tempUsu += `<option value="${full.listUsu[i].id}" ${full.listUsu[i].status != 1 ? 'disabled' : ''} ${data == full.listUsu[i].id ? 'selected' : ''}>${full.listUsu[i].name} ${full.listUsu[i].last_name}</option>`;
                }
                return `<select data-guia="${full.id}" class="form-control form-control-sm cb-usuarios">${tempUsu}</select>`;
            }},
            {data: 'situ',searchable: false, orderable: false, render: function(data,type,full){
                if(full.guia_tipo == 'INACTIVA' || full.guia_tipo == 'ANULADA'){
                    return '<span class="text-danger">Sin acciones</span>'
                }
                let template = ``;
                full.listSoli.forEach(sol => {
                    template += `<option value="${sol.id}" ${data == sol.id ? 'selected' : ''}>${sol.tipo}</option>`;
                });
                return `<select data-guia="${full.id}" class="form-control form-control-sm cb-estado-change">${template}</select>`;
            }},
            {data: 'guia_tipo'},
            {data: 'id', searchable: false, orderable: false, name: 'id', render: function(data,type,full){
                let temButtons = `<div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><button data-toggle="tooltip" title="Visualizar solicitud" data-guia="${data}" class="btn btn-sm btn-primary view-guia"><i class="far fa-eye"></i></button>`;
                if(full.guia_tipo != 'ANULADA'){
                    temButtons += `<button data-toggle="tooltip" title="Anular guia" data-guia="${data}" class="btn btn-sm btn-danger delete-guia"><i class="fas fa-trash-alt"></i></button></div>`;
                }else{
                    temButtons += '</div>';
                }
                return temButtons;
            }}
        ],
        createdRow : function(row,data){
            let bgTable = !data.id_asignado && data.guia_tipo == 'ACTIVA' ? 'table-warning' : '';
            $(row).addClass(bgTable);
        }
    });
    tblDetalleGuia.addEventListener('click',async function(e){
        if(e.target.classList.contains('delete-guia')){
            const guia = e.target.dataset.guia;
            alertify.confirm('Mensaje','¿Deseas anular esta guía?',async ()=>{
                let datos = new FormData();
                datos.append('id_guia',guia);
                let response = await general.funcfetch('anular-guia',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return 
                }
                alertify.success(response.success);
                tablaGuias.draw();
            },()=>{});
        }
        if(e.target.classList.contains('view-guia')){
            let datos = new FormData();
            datos.append('guia',e.target.dataset.guia);
            let response = await general.funcfetch('obtener-detalles',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return 
            }
            verDetalleGuia(response.datos);
        }
    });
    function verDetalleGuia(data) {
        if(!data){
            return
        }
        for (const key in data) {
            if (Object.hasOwnProperty.call(data, key)) {
                const value = data[key];
                const dom = document.getElementById('id_' + key);
                if(!dom){
                    continue;
                }
                dom.textContent = value;
            }
        }
        let template = '';
        if(data.detalles_guias){
            data.detalles_guias.forEach(dg => {
                template += `
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Grupo Envio</b>${dg.grupo_envio}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Tipo Envio</b>${dg.tipo_envio}</div>
                            <div class="col-12 col-lg-4"><b class="d-block text-secondary">Embase o Embalaje</b>${dg.embase_embalaje}</div>
                            <div class="col-12"><b class="d-block text-secondary">Descripción</b>${!dg.descripcion ? 'No definido' : dg.descripcion}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Cantidad</b>${dg.cantidad}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Kg</b>${dg.kg}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Largo</b>${dg.largo}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Ancho</b>${dg.ancho}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Altura</b>${dg.altura}</div>
                            <div class="col-6 col-lg-4"><b class="d-block text-secondary">Volumen</b>${dg.volumen}</div>
                        </div>
                    </div>
                </div>`;
            });
            document.getElementById('detalle_guias').innerHTML = template;
        }
        $('#detalle-guias-todo').modal('show');
    }
    
    tablaGuias.on('responsive-display', function ( e, datatable, row, showHide, update ) {
        const tabla = e.target.children[0].children[0];
        if(tabla.children[8].style.display == 'none'){
            $('.cb-clustes').off('change');
            $('.cb-clustes').attr('data-responsive','true');
            $('.cb-clustes').on('change',async function(){
                let datos = new FormData();
                datos.append('id_guia',$(this).attr('data-guia'));
                datos.append('id_cluster',$(this).val());
                let response = await general.funcfetch('cambiar-cluster',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return 
                }
                alertify.success(response.success);
            });
        }
        if(tabla.children[9].style.display == 'none'){
            $('.cb-usuarios').off('change');
            $('.cb-usuarios').attr('data-responsive','true');
            $('.cb-usuarios').on('change',async function(event){
                let datos = new FormData();
                datos.append('id_guia',event.target.dataset.guia);
                datos.append('id_user',$(this).val());
                let response  = await general.funcfetch('asignar-personal',datos);
                if(response.error){
                    alertify.alert('Mensaje',response.error);
                    return
                }
                document.getElementById('total-por-procesar-guias').textContent = response.procesar;
                alertify.success(response.success);
            });
        }
        if(tabla.children[10].style.display == 'none'){
            $('.cb-estado-change').off('change');
            $('.cb-estado-change').attr('data-responsive','true');
            $('.cb-estado-change').on('change',async function(){
                let datos = new FormData();
                datos.append('id_guia',$(this).attr('data-guia'));
                datos.append('estado',$(this).val());
                let response = await general.funcfetch('cambiar-estado',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return 
                }
                alertify.success(response.success);
            })
        }
    });
    tablaGuias.on('draw.dt', function () {
        $('.cb-usuarios').select2({
            theme : 'bootstrap',
            width: '100%',
            placeholder : 'Seleccione una opción'
        }).on('select2:select',async function(event){
            let datos = new FormData();
            datos.append('id_guia',event.target.dataset.guia);
            datos.append('id_user',$(this).val());
            let response  = await general.funcfetch('asignar-personal',datos);
            if(response.error){
                alertify.alert('Mensaje',response.error);
                return
            }
            document.getElementById('total-por-procesar-guias').textContent = response.procesar;
            alertify.success(response.success);
        });
        $('.cb-clustes').select2({
            theme : 'bootstrap',
            width: '100%',
            placeholder : 'Seleccione una opción'
        }).on('select2:select',async function(event){
            let datos = new FormData();
            datos.append('id_guia',$(this).attr('data-guia'));
            datos.append('id_cluster',$(this).val());
            let response = await general.funcfetch('cambiar-cluster',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return 
            }
            alertify.success(response.success);
        });
        $('.cb-estado-change').select2({
            theme : 'bootstrap',
            width: '100%',
            placeholder : 'Seleccione una opción'
        }).on('select2:select',async function(event){
            let datos = new FormData();
            datos.append('id_guia',$(this).attr('data-guia'));
            datos.append('estado',$(this).val());
            let response = await general.funcfetch('cambiar-estado',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return 
            }
            alertify.success(response.success);
        });
    });
    tablaGuias.on('responsive-resize', function ( e, datatable, columns ) {
        if(columns[8] && $('.cb-clustes').attr('data-responsive') == 'true'){
            $('.cb-clustes').off('change');
            $('.cb-clustes').attr('data-responsive','false');
        }
        if(columns[9] && $('.cb-usuarios').attr('data-responsive') == 'true'){
            $('.cb-usuarios').off('change');
            $('.cb-usuarios').attr('data-responsive','false');
        }
        if(columns[10] && $('.cb-estado-change').attr('data-responsive') == 'true'){
            $('.cb-estado-change').off('change');
            $('.cb-estado-change').attr('data-responsive','false');
        }
    });
    for (const filtros of document.querySelectorAll('.cb-filtros')) {
        filtros.addEventListener('change',function(){
            tablaGuias.draw();
        });
    }

}
window.addEventListener('DOMContentLoaded',loadPage);