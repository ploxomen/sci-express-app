function loadPage(){
    let general = new General();
    const tablaEmbarque = document.getElementById('tabla-embarques');
    const tablaMani = document.getElementById('tabla-mani');
    let nro_embarque = null;
    let nro_guia = null;
    let nro_mani = null;
    async function obtenerEmbarquesPorLlegar(embarque,guia,tipo,validarFuncion){
        try {
            let datos = new FormData();
            datos.append('tipo',tipo);
            if(embarque){
                datos.append('embarque',embarque);
            }
            if(guia){
                datos.append('guia',guia);
            }
            let response = await general.funcfetch('llegada/embarques/arribar',datos);
            validarFuncion(response);
        } catch (error) {
            console.error(error.message);
            alertify.alert('Error',error.message);
        }
    }
    function llenarTablaEmbarque(recorrer) {
        const valEmbarque = document.getElementById('valor-embarques');
        if(!recorrer.resultados.length){
            tablaEmbarque.innerHTML = `<tr><td colspan="7" class="text-center">No se encontraron resultados</td></tr>`;
            valEmbarque.value = '0';
            return
        }
        let template = ``;
        recorrer.resultados.forEach(r => {
            template+= `<tr>
            <td>${r.nro_embarque}</td>
            <td>${r.empresa}</td>
            <td>${r.format_fch_llegada}</td>
            <td>${r.format_hr_llegada}</td>
            <td>${r.total_bultos_paquete}</td>
            <td>${r.nro_mani}</td>
            <td>
                <button class="btn btn-primary btn-sm ver-detalles" title="Ver más detalles" data-embarque="${r.id}"><i class="fas fa-eye"></i></button>
                <button class="btn btn-success btn-sm ver-manifiestos-guias" title="Ver manifiestos - guias" data-embarque="${r.id}"><i class="fas fa-location-arrow"></i></button>
            </td>
            </tr>`;
        });
        valEmbarque.value = recorrer.resultados.length;
        tablaEmbarque.innerHTML = template;
    }
    obtenerEmbarquesPorLlegar(0,0,'embarques',llenarTablaEmbarque);
    tablaEmbarque.addEventListener('click',function(event){
        nro_embarque = null;
        if(event.target.classList.contains('ver-manifiestos-guias')){
            nro_embarque = event.target.dataset.embarque;
            obtenerEmbarquesPorLlegar(nro_embarque,0,'manifiestos',llenarTablaMani);
        }
        if(event.target.classList.contains('ver-detalles')){
            nro_embarque = event.target.dataset.embarque;
            obtenerEmbarquesPorLlegar(nro_embarque,0,'detalle-embarque',detalleEmbarque);
        }
    });
    function detalleEmbarque(datos) {  
        if(!datos.resultados.length){
            alertify.error('no se encontró detalles de este embarque')
            return
        }
        for (const key in datos.resultados[0]) {
            if (Object.hasOwnProperty.call(datos.resultados[0], key)) {
                const valor = datos.resultados[0][key];
                const dom = document.getElementById('id_modal_' + key);
                if(!dom){
                    continue;
                }
                dom.textContent = !valor ? '-' : valor;
            }
        }
        $('#detalle-embarque').modal('show');
    }
    function llenarTablaMani(datos) {
        const valorMani = document.getElementById('valor-manifiesto'); 
        if(!datos.resultados.length){
            tablaMani.innerHTML = `<tr><td colspan="7"  class="text-center">No se encontraron resultados</td></tr>`;
            valorMani.value = '0';
            return
        }
        let total = 0;
        let template = ``;
        datos.resultados.forEach(r => {
            if(r.guias.length){
                template+= `<tr>
                <td rowspan="${r.guias.length}">${r.nro_mani}</td>
                <td rowspan="${r.guias.length}">${r.format_fecha_mani}</td>
                <td rowspan="${r.guias.length}">${r.prov_orig}</td>
                <td rowspan="${r.guias.length}">${r.prov_dest}</td>
                <td rowspan="${r.guias.length}">${r.total_guias}</td>
                <td rowspan="${r.guias.length}">${r.tipo}</td>`;
                total += r.guias.length;
                template += `<td>${r.guias[0].id.toString().padStart(5,'0')}</td>
                <td>
                    <button class="btn btn-primary btn-sm ver-detalle-guias" title="Ver detalle de guía" data-manifiesto="${r.id}" data-guia="${r.guias[0].id}"><i class="fas fa-eye"></i></button>
                </td>
                </tr>`;
                for (let i = 1; i < r.guias.length; i++) {
                    template += `<tr>
                        <td>${r.guias[i].id.toString().padStart(5,'0')}</td>
                        <td>
                            <button class="btn btn-primary btn-sm ver-detalle-guias" title="Ver detalle de guía" data-manifiesto="${r.id}" data-guia="${r.guias[i].id}"><i class="fas fa-eye"></i></button>
                        </td>
                    </tr>`;
                }
            }
            
        });
        tablaMani.innerHTML = template;
        valorMani.value = total;
    }
    tablaMani.addEventListener('click',function(event){
        nro_guia = null;
        nro_mani = null;
        if(event.target.classList.contains('ver-detalle-guias')){
            nro_mani = event.target.dataset.manifiesto;
            nro_guia = event.target.dataset.guia;
            obtenerEmbarquesPorLlegar(nro_embarque,nro_guia,'detalle-guia',verDetalleGuia);
        }
    });
    function verDetalleGuia(data) {
        if(!data.resultados[0]){
            return
        }
        for (const key in data.resultados[0]) {
            if (Object.hasOwnProperty.call(data.resultados[0], key)) {
                const value = data.resultados[0][key];
                const dom = document.getElementById('id_' + key);
                if(!dom){
                    continue;
                }
                dom.textContent = value;
            }
        }
        let template = '';
        if(data.resultados[0].detalles_guias){
            data.resultados[0].detalles_guias.forEach(dg => {
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
        $('#detalle-guias-llegada').modal('show');
    }
     function verDetalleGuiaCluster(data) {
        if(!data.resultados[0]){
            return
        }
        for (const key in data.resultados[0]) {
            if (Object.hasOwnProperty.call(data.resultados[0], key)) {
                const value = data.resultados[0][key];
                const dom = document.getElementById('id_modal2_' + key);
                if(!dom){
                    continue;
                }
                dom.textContent = value;
            }
        }
        let template = '';
        if(data.resultados[0].detalles_guias){
            data.resultados[0].detalles_guias.forEach(dg => {
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
            document.getElementById('id_modal2_detalle_guias').innerHTML = template;
        }
        $('#detalle-guias-llegada-cluster').modal('show');
    }
    let checkConforme = document.getElementById('activar_todo_bien');
    checkConforme.addEventListener('change',function (event){
        document.getElementById('text-inconforme').disabled = this.checked;
    })
    document.getElementById('btn-guardar-guias').addEventListener('click',function (e) {  
        e.preventDefault();
        if(!checkConforme.checked){
            if(document.getElementById('text-inconforme').value == ""){
                alertify.error('por favor detalle la inconformidad');
                return
            }
        }
        const largo = !checkConforme.checked ? ' tiene algún problema.' : ' esta todo conforme.';
        const msg = 'Al continuar usted esta afirmando que la guía N° - <b>' + nro_guia.toString().padStart(5,'0') + '</b>'  +  largo + ' <br>¿Deseas continuar?';
        let datos = new FormData();
        datos.append('guia',nro_guia);
        datos.append('embarque',nro_embarque);
        datos.append('manifiesto',nro_mani);
        datos.append('conforme',!checkConforme.checked ? 14 : 7);
        datos.append('texto',document.getElementById('text-inconforme').value);
        alertify.confirm('Alerta',msg,async ()=>{
            try {
                let response = await general.funcfetch('llegada/arribar-guias',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                alertify.alert('Mensaje',response.success);
                obtenerEmbarquesPorLlegar(0,0,'embarques',llenarTablaEmbarque);
                obtenerEmbarquesPorLlegar(nro_embarque,0,'manifiestos',llenarTablaMani);
                nro_guia = null;
                nro_mani = null;
                $('#detalle-guias-llegada').modal('hide');
            } catch (error) {
                console.error(error.message);
                alertify.alert('Error',error.message);
            }
        },()=>{})
    });
    $('#detalle-guias-llegada').on('hidden.bs.modal',function(){
        document.getElementById('text-inconforme').value = "";
        document.getElementById('text-inconforme').disabled = true;
        checkConforme.checked = true;
    });
    
    //MIS GUIAS
    const tablaGuiaClus = document.getElementById('tabla-guias-cluster');
    let dataGuias = $('#tabla-asigancion-guias').DataTable({
        ajax : {
            url: 'llegada/mis-guias',
            method: 'POST',
            headers: general.jsonToken,
            data : function(d){d.estado = $('#estado_guias').val()}
        },
        columns: [
            {data: 'nro_guia'},
            {data: 'fecha_emi_guia'},
            {data: 'nro_manifiesto'},
            {data: 'fecha_emi_mani'},
            {data: 'prov_orig'},
            {data: 'prov_dest'},
            {data: 'guia_tipo'},
            {data: 'situ',render : function(data,type,full){
                if(full.guia_tipo == 'ANULADA' || full.guia_tipo=='INACTIVA'){
                    return `<span class='text-danger'>Sin acciones</span>`;
                }
                let tempEstado = '';
                full.lista_estados.forEach(estado => {
                    tempEstado += `<option value="${estado.id}" ${estado.id == data ? 'selected' : ''}>${estado.tipo}</option>`;
                });
                return `<select class="form-control form-control-sm change-estado" data-guia="${full.id}">${tempEstado}</select>`;
            }},
            {data: 'id', searchable: false, orderable: false,render: function(data){
                return `<button class="btn btn-primary btn-sm ver-detalles-cluster-guia" title="Ver más detalles de la guia" data-guia="${data}"><i class="fas fa-eye"></i></button>`
            }}
        ],
    });
    for (const filtro of document.querySelectorAll('.cb-filtros-guias')) {
        filtro.addEventListener('change',function(e){
            dataGuias.draw();
        });
    }
    tablaGuiaClus.addEventListener('click',async function(event){
        if(event.target.classList.contains('ver-detalles-cluster-guia')){
            let datos = new FormData();
            datos.append('id_guia',event.target.dataset.guia);
            let response = await general.funcfetch('llegada/guia-detalle-cluster',datos);
            verDetalleGuiaCluster(response);
        }
    });
    dataGuias.on('draw.dt', function () {
        for (const cbos of document.querySelectorAll('.change-estado')) {
            cbos.addEventListener('change',async function(event){
                let datos = new FormData();
                datos.append('id_guia',this.dataset.guia);
                datos.append('estado',this.value);
                let response = await general.funcfetch('llegada/cambiar-estado-guias',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return 
                }
                alertify.success(response.success);
            });
        }
    });
}
window.addEventListener('DOMContentLoaded',loadPage);