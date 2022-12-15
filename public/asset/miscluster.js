function loadPage(){
    let contadorLi = 0;
    const listRemoteCity = [
        {
            value :'Rural - Alejada',
            text : 'Rural - Alejada (S/)'
        },
        {
            value : 'Complejo - Distante',
            text: 'Complejo - Distante (%)'
        },
        {
            value: 'FAU',
            text: 'FAU (%)'
        }
    ];
    let general = new General();
    let bodyCluster = document.getElementById('lista-clusters');
    const frmEdidCluster = document.getElementById('edid_cluster');
    const ulListCiudad = document.getElementById('lista-destino-final');
    let dataTableCluster = $('#tabla-cluster').DataTable({
        ajax : {
            url: 'obtener',
            method: 'POST',
            headers: general.jsonToken,
        },
        columns: [
            {data: null,searchable: false, orderable: false,},
            {data: 'n_document'},
            {data: 'nomcompleto'},
            {data: 'ruc_clu'},
            {data: 'razon_clu'},
            {data: 'departamento_clu'},
            {data: 'provincia_clu'},
            {data: 'ciudad_base'},
            {data: 'cluster_id', searchable: false, orderable: false, name: 'id', 
            render: function(data){
                return`<div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><button class="btn btn-sm btn-primary update-cluster" data-toggle="tooltip" title="Editar cluster" data-cluster="${data}"><i class="fas fa-user-edit"></i></button><button data-toggle="tooltip" title="Eliminar cluster" data-cluster="${data}" class="btn btn-sm btn-danger delete-cluster"><i class="fas fa-user-times"></i></button></div>`
            }}
        ],
    });
    dataTableCluster.on('draw.dt', function () {
        let info = dataTableCluster.page.info();
        dataTableCluster.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });
    bodyCluster.addEventListener('click',async function(e){
        if(e.target.classList.contains('update-cluster')){
            let datos = new FormData();
            datos.append('id',e.target.dataset.cluster);
            let response = await general.funcfetch('obtener',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            let template = ``;
            response.cluster.ciudades.forEach(h => {
                let templateDist = `<option></option>`;
                response.cluster.distritos.forEach(d => {
                    templateDist+= `<option value="${d.id_ubigeo}" ${d.id_ubigeo == h.ubigeo_distrito ? 'selected' : ''}>${d.nombre_ubigeo}</option>`;
                });
                let tempCity = `<option></option>`;
                h.listCity.forEach(li => {
                    tempCity += `<option value="${li.id}" ${li.id == h.id_ciudad ? 'selected' : ''}>${li.nombre_ciudad}</option>`;
                });
                let tempRemoteCity = `<option></option><option value='Ninguno' ${h.ciudad_alejada === null || h.ciudad_alejada == '' || h.ciudad_alejada == 'Ninguno' ? 'selected' : ''}>Ninguno</option>`;
                listRemoteCity.forEach(city => {
                    tempRemoteCity += `<option value="${city.value}" ${city.value == h.ciudad_alejada && 'selected'}>${city.text}</option>`;
                });
                template += `
                <li data-type="old" data-ciudad="${h.id}">
                    <div class="form-row">
                        <div class="form-group col-6 col-lg-3">
                            <select class="form-control cb-ubigeo" required data-ciudades="#id_ciudad_old_destino_${h.id}" data-idciudad="${h.id}" id="id_distrito_old_clu_${h.id}">${templateDist}</select>
                        </div>
                        <div class="form-group col-6 col-lg-3">
                            <select class="form-control cb-select2-tags" id="id_ciudad_old_destino_${h.id}">${tempCity}</select>
                        </div>
                        <div class="form-group col-6 col-sm-6 col-lg-3">
                            <select required class="form-control simple-select2" data-costo="#costo-alejado-${h.id}">${tempRemoteCity}</select>
                        </div>
                        <div class="form-group col-6 col-sm-4 col-lg-2">
                            <input type="number" placeholder="S/ ó %" id="costo-alejado-${h.id}" step="0.01" min="0" class="form-control form-control-sm" value="${h.costo_ciudad_alejada}">
                        </div>
                        <div class="form-group col-12 col-sm-2 col-lg-1 text-right">
                            <input type="radio" name="destino_final" class="selection-base" ${h.base ? 'checked' : ''}>
                            <button type="button" class="btn btn-danger btn-sm btn-delete-destino"><i class="far fa-trash-alt"></i></button>
                        </div>
                    </div> 
                </li>
                `
            });
            ulListCiudad.innerHTML = template;
            general.transforData(response.cluster['lista_provincia_clu'],document.querySelector('#id_provincia_clu'));
            for (const key in response.cluster) {
                if (Object.hasOwnProperty.call(response.cluster, key)) {
                    const valor = response.cluster[key];
                    const dom = document.getElementById('id_'+key);
                    if(!dom){
                        continue;
                    }
                    if(key == 'consignados'){
                        let optConsig = `<option></option>`;
                        if(valor){
                            valor.split(';').forEach(v => {
                                optConsig += `<option value="${v}" selected>${v}</option>`;
                            });
                            dom.innerHTML = optConsig;
                            $(dom).val(valor.split(';')).trigger('change');
                        }
                    }
                    dom.value = valor;
                }
            }
            $('.cb-ubigeo, .simple-select2').trigger('change');
            $('#lista-destino-final .cb-select2-tags').select2({
                theme: 'bootstrap',
                width: '100%',
                placeholder : 'Seleccione o agregue una opción',
                tags: true
            });
            $('#lista-destino-final .simple-select2').select2({
                theme: 'bootstrap',
                width: '100%',
                placeholder: 'Seleccione una opción'
            }).on('change',(e) => {
                if (e.target.value == 'Ninguno'){
                    $(e.target.dataset.costo).prop('disabled',true);
                    $(e.target.dataset.costo).prop('required', false);
                    $(e.target.dataset.costo).val("");
                }else{
                    $(e.target.dataset.costo).prop('disabled', false);
                    $(e.target.dataset.costo).prop('required', true);
                }
            });
            $('#lista-destino-final .cb-ubigeo').select2({
                theme: 'bootstrap',
                width: '100%',
                placeholder: 'Seleccione una opción'
            }).on('select2:select',async function(e){
                let datosn = new FormData();
                datosn.append('distrito',$(this).val());
                let response = await general.funcfetch(window.location.origin + '/intranet/ubigeo/ciudades',datosn);
                let template = `<option></option>`;
                response.ciudades.forEach(c => {
                    template += `<option value="${c.id}">${c.nombre_ciudad}</option>`;
                });
                document.querySelector($(this).attr('data-ciudades')).innerHTML = template;
            });
            $('#lista-destino-final .simple-select2').trigger('change');
            $('#modal-editar-cluster').modal("show");
        }
        if(e.target.classList.contains('delete-cluster')){
            alertify.confirm('Mensaje','¿Deseas eliminar a este cluster?',async ()=>{
                let datos = new FormData();
                datos.append('id',e.target.dataset.cluster);
                let response = await general.funcfetch('eliminar',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                alertify.alert('Mensaje',response.success,()=>{dataTableCluster.draw()});
            },()=>{});
        }
    });
    let ciudadesEliminadas = [];
    ulListCiudad.addEventListener('click',async function(e){
        if(e.target.classList.contains('btn-delete-destino')){
            const myLi = e.target.parentElement.parentElement.parentElement;
            if(ulListCiudad.children.length === 1){
                alertify.error('debe haber como mínimo una ciudad de destino');
                return
            }
            if(myLi.getAttribute('data-type') == 'old'){
                ciudadesEliminadas.push(myLi.getAttribute('data-ciudad'));
            }
            myLi.remove();
            alertify.success('accion relizada');
        }
    });
    $('#id_provincia_clu').on('select2:select',async function(e){
        let response = await general.getDistritos($(this).val());
        for (const element of ulListCiudad.children) {
            general.transforData(response,element.querySelector('.cb-ubigeo'));
            element.querySelector('.cb-select2-tags').innerHTML = '<option></option>';
            element.querySelector('.selection-base').checked = false;
        }
        $('#lista-destino-final .cb-ubigeo, #lista-destino-final .cb-select2-tags, #lista-destino-final .simple-select2').val("").trigger("change");
    });
    const btnAddCiudad = document.getElementById('btn-add-ciudad');
    btnAddCiudad.addEventListener('click',function(e){
        e.preventDefault();
        contadorLi++;
        $('#lista-destino-final .cb-ubigeo, #lista-destino-final .cb-select2-tags, #lista-destino-final .simple-select2').select2('destroy');
        const liPrimero = ulListCiudad.children[0];
        const liDuplicado = liPrimero.cloneNode(true);
        liDuplicado.setAttribute('data-type','new');
        liDuplicado.querySelector(".cb-ubigeo").setAttribute('id','id_distrito_new_clu_' + contadorLi);
        liDuplicado.querySelector(".cb-ubigeo").setAttribute('data-ciudades','#id_ciudad_new_destino_' + contadorLi);
        liDuplicado.querySelector(".cb-select2-tags").setAttribute('id','id_ciudad_new_destino_' + contadorLi);
        liDuplicado.querySelector(".selection-base").checked = false;
        for (const element of liDuplicado.querySelectorAll("select")) {
            element.value = "";
            if(element.classList.contains('cb-select2-tags')){
                element.innerHTML = '<option></option>';
            }
            if (element.classList.contains('simple-select2')) {
                element.value = "Ninguno";
            }
        }
        ulListCiudad.append(liDuplicado);
        alertify.success('accion relizada');
        $('#lista-destino-final .cb-ubigeo').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        }).on('select2:select',async function(e){
            let datosn = new FormData();
            datosn.append('distrito',$(this).val());
            let response = await general.funcfetch(window.location.origin + '/intranet/ubigeo/ciudades',datosn);
            let template = `<option></option>`;
            response.ciudades.forEach(c => {
                template += `<option value="${c.id}">${c.nombre_ciudad}</option>`;
            });
            document.querySelector($(this).attr('data-ciudades')).innerHTML = template;
        });
        $('#lista-destino-final .cb-select2-tags').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder : 'Seleccione o agregue una opción',
            tags: true,
        });
        $('#lista-destino-final .simple-select2').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        });
    });
    frmEdidCluster.addEventListener('submit',function(e){
        e.preventDefault();
        const selecionarBase = ulListCiudad.querySelectorAll(`input:checked`).length;
        if(!selecionarBase){
            alertify.error('favor de elegir la ciudad base');
            return
        }
        let agregarCiudades = [];
        let actualizarCiudades = [];
        for (const ciudades of ulListCiudad.children) {
            if(ciudades.dataset.type){
                const valores = {
                    ciudad: $(ciudades).find('.cb-select2-tags').val(),
                    distrito : $(ciudades).find('.cb-ubigeo').val(),
                    ciudad_base : $(ciudades).find('.selection-base').prop("checked") ? 1 : 0,
                    id_ciudad : $(ciudades).attr('data-type') == 'old' ? $(ciudades).attr('data-ciudad') : null,
                    ciudad_alejada : $(ciudades).find('.simple-select2').val(),
                    costo_ciudad_alejada: $(ciudades).find('.form-control-sm').val()
                }
                ciudades.dataset.type == 'new' ? agregarCiudades.push(valores) : actualizarCiudades.push(valores);
            }
        }
        alertify.confirm('Alerta','¿Estás seguro de actualizar los datos de este cluster?<br>Recuerda que una vez aceptado no habra modo de recuperación de los datos modificados.',async ()=>{
            let datos = new FormData(this);
            datos.append('agregar_ciudades',JSON.stringify(agregarCiudades));
            datos.append('actualizar_ciudades',JSON.stringify(actualizarCiudades));
            datos.append('eliminar_ciudades',JSON.stringify(ciudadesEliminadas));
            try {
                let response = await general.funcfetch('update',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                alertify.alert('Mensaje',response.success);
                $('#modal-editar-cluster').modal("hide");
                dataTableCluster.draw();
            } catch (error) {
                alertify.alert('Error',error.message);
                console.error(error);
            }
        },()=>{});
        
    })
    const btnActualizar = document.getElementById('btn-actualizar-cluster');
    btnActualizar.addEventListener('click',function(e){
        document.getElementById(this.dataset.click).click();
    });
    $('#modal-editar-cluster').on('hidden.bs.modal', function (e) {
        ciudadesEliminadas = [];
    });
}
window.addEventListener('DOMContentLoaded',loadPage);